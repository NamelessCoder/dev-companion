<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Project;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;

/**
 * The one place this server reads something other than its own knowledge base,
 * and the rules that keep that from happening by accident.
 */
final class InstanceTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Test]
    public function withoutAnEntrypointHandingInADirectoryThereIsNoInstance(): void
    {
        // The HTTP case: a request-serving endpoint never calls discoverFrom(),
        // so no caller can be answered from whatever installation the document
        // root happens to sit in.
        Instance::discoverFrom(null);

        self::assertFalse(Instance::isAvailable());
        self::assertNull(Instance::root());
        self::assertSame([], Instance::packages());
    }

    #[Test]
    public function aCoreCheckoutIsFoundFromAnyDirectoryInsideIt(): void
    {
        $root = $this->coreCheckout();
        Instance::discoverFrom($root . '/typo3/sysext/backend/Classes/Controller');

        $instance = Instance::describe();
        self::assertNotNull($instance);
        self::assertSame(realpath($root), $instance['root']);
        self::assertSame(Instance::KIND_CORE_CHECKOUT, $instance['kind']);
        self::assertSame(['backend', 'core'], array_keys(Instance::packages()));
    }

    #[Test]
    public function aComposerProjectIsFoundThroughItsInstalledPackages(): void
    {
        $root = $this->composerProject();
        Instance::discoverFrom($root);

        $instance = Instance::describe();
        self::assertNotNull($instance);
        self::assertSame(Instance::KIND_COMPOSER_PROJECT, $instance['kind']);

        // A project's own extension registers icons and ships labels exactly
        // like a system extension does, so it has to be in the list.
        self::assertSame(['core', 'my_sitepackage'], array_keys(Instance::packages()));
    }

    #[Test]
    public function theTypo3VersionIsReadFromTheCorePackageRatherThanAskedOfTheConsole(): void
    {
        // It has to be available exactly when the console is not: an
        // installation whose database has no schema still has a version, and
        // the version is what decides whether an answer holds for it.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        self::assertSame('13.4.33', Instance::typo3Version());
        self::assertSame(13, Instance::typo3Major());
    }

    #[Test]
    public function anInstallationThatStatesNoVersionIsNotGuessedAt(): void
    {
        Instance::discoverFrom($this->composerProject());

        self::assertNull(Instance::typo3Version());
        self::assertNull(Instance::typo3Major());
    }

    #[Test]
    public function aProjectThatMovedItsVendorDirectoryIsFoundThereRatherThanMissed(): void
    {
        // The layout the TYPO3 extension testing setup produces. Reading the
        // default vendor/ instead walked past the installation entirely, and
        // every question only it can answer came back as if nothing existed.
        $root = $this->composerProject('.build/vendor');
        Instance::discoverFrom($root);

        self::assertSame(Instance::KIND_COMPOSER_PROJECT, Instance::describe()['kind']);
        self::assertSame(['core', 'my_sitepackage'], array_keys(Instance::packages()));
    }

    #[Test]
    public function theExtensionBeingWorkedOnIsAmongThePackagesAlthoughComposerListsOnlyDependencies(): void
    {
        $root = $this->composerProject('.build/vendor');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'config' => ['vendor-dir' => '.build/vendor'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        // Without this the one package the agent is editing is the only one
        // missing from the answers about its own installation.
        self::assertSame(realpath($root), Instance::packages()['bootstrap_package'] ?? null);
    }

    #[Test]
    public function aPackageBelowTestsIsTheTestSetupsRatherThanTheOneBeingWorkedOn(): void
    {
        $root = $this->composerProject('.build/vendor');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'config' => ['vendor-dir' => '.build/vendor'],
            'repositories' => ['tests' => ['type' => 'path', 'url' => 'Tests/Packages/*']],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/Tests/Packages/demo_package', 0o777, true);
        file_put_contents($root . '/Tests/Packages/demo_package/composer.json', json_encode([
            'name' => 'acme/demo-package',
            'type' => 'typo3-cms-extension',
            'extra' => ['typo3/cms' => ['extension-key' => 'demo_package']],
        ], JSON_THROW_ON_ERROR));
        $installed = json_decode(
            (string) file_get_contents($root . '/.build/vendor/composer/installed.json'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $installed['packages'][] = [
            'name' => 'acme/demo-package',
            'type' => 'typo3-cms-extension',
            'install-path' => '../../../Tests/Packages/demo_package',
            'extra' => ['typo3/cms' => ['extension-key' => 'demo_package']],
        ];
        file_put_contents(
            $root . '/.build/vendor/composer/installed.json',
            json_encode($installed, JSON_THROW_ON_ERROR),
        );
        Instance::discoverFrom($root);

        $origins = array_column(Project::describe()['extensions'], 'origin', 'key');

        // Calling the fixture the project's own says "this is what is being
        // worked on" about a package that exists to be loaded by a suite, and
        // a review then audits it as if it were shipped.
        self::assertSame(Project::ORIGIN_FIXTURE, $origins['demo_package'] ?? null);
        self::assertSame(Project::ORIGIN_PROJECT, $origins['bootstrap_package'] ?? null);
        self::assertSame(Project::ORIGIN_THIRD_PARTY, Project::origin('/app/.build/vendor/b13/container'));
    }

    #[Test]
    public function aRepositoryWithNoInstallationAroundItIsNotReportedAsOne(): void
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 'acme/bootstrap-package', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        Instance::discoverFrom($root);

        // An extension checkout whose dependencies were never installed has
        // nothing to answer from, and saying so beats reporting an
        // installation that holds a single package and no console.
        self::assertFalse(Instance::isAvailable());
    }

    #[Test]
    public function anInstallationNamedOutrightIsReadWithoutAnySearch(): void
    {
        // The way out of every layout this server cannot walk to: a stack it
        // has never heard of, an installation in a subdirectory, a client that
        // starts the server beside the checkout rather than inside it.
        $root = $this->composerProject();
        putenv(Instance::ROOT_VARIABLE . '=' . $root);
        Instance::discoverFrom(sys_get_temp_dir());

        $instance = Instance::describe();
        self::assertSame(realpath($root), $instance['root']);
        self::assertSame(Instance::VIA_ENVIRONMENT, $instance['via']);
        self::assertSame('', Instance::misconfiguration());
    }

    #[Test]
    public function aNamedInstallationThatDoesNotExistIsReportedRatherThanSearchedPast(): void
    {
        $root = $this->composerProject();
        putenv(Instance::ROOT_VARIABLE . '=' . $root . '/nowhere');
        Instance::discoverFrom($root);

        // Falling back to the discoverable one would answer about an
        // installation other than the one the caller named, and the setting
        // that was ignored would never be mentioned again.
        self::assertFalse(Instance::isAvailable());
        self::assertStringContainsString(Instance::ROOT_VARIABLE, Instance::misconfiguration());
    }

    #[Test]
    public function anInstallationThatAppearsDuringTheSessionIsFound(): void
    {
        // The stdio process lives as long as the agent session, and an agent
        // that is told there is nothing to read runs composer install or starts
        // the containers — because of that answer. Remembering the "nothing"
        // outlives its reason, and the caller would have to restart the client
        // to be given an answer that has been true for ten minutes.
        $root = $this->temporaryDirectory();
        Instance::discoverFrom($root);
        self::assertFalse(Instance::isAvailable());

        $this->installPackagesInto($root);

        self::assertTrue(Instance::isAvailable());
        self::assertSame(['core'], array_keys(Instance::packages()));
    }

    #[Test]
    public function aDirectoryOutsideAnyInstallationFindsNothing(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());

        self::assertFalse(Instance::isAvailable());
    }

    #[Test]
    public function theAnswerSaysWhereItLookedSoAWrongInstanceIsVisible(): void
    {
        $root = $this->coreCheckout();
        $startedFrom = $root . '/typo3/sysext/core';
        Instance::discoverFrom($startedFrom);

        self::assertSame(realpath($startedFrom), Instance::describe()['startedFrom']);
    }

    private function installPackagesInto(string $root): void
    {
        mkdir($root . '/vendor/typo3/cms-core', 0o777, true);
        mkdir($root . '/vendor/composer', 0o777, true);
        file_put_contents($root . '/vendor/composer/installed.json', json_encode(['packages' => [[
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'install-path' => '../typo3/cms-core',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ]]], JSON_THROW_ON_ERROR));
    }
}
