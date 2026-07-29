<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Instance;
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

    private function coreCheckout(): string
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', '{"name": "typo3/cms", "type": "typo3-cms-core"}');
        foreach (['core' => 'core', 'backend' => 'backend'] as $directory => $key) {
            $path = $root . '/typo3/sysext/' . $directory;
            mkdir($path . '/Classes/Controller', 0o777, true);
            file_put_contents($path . '/composer.json', json_encode([
                'name' => 'typo3/cms-' . $directory,
                'type' => 'typo3-cms-framework',
                'extra' => ['typo3/cms' => ['extension-key' => $key]],
            ], JSON_THROW_ON_ERROR));
        }

        return $root;
    }

    private function composerProject(string $vendorDirectory = 'vendor'): string
    {
        $root = $this->temporaryDirectory();
        $vendor = $root . '/' . $vendorDirectory;
        if ($vendorDirectory !== 'vendor') {
            file_put_contents($root . '/composer.json', json_encode(
                ['name' => 'acme/extension', 'config' => ['vendor-dir' => $vendorDirectory]],
                JSON_THROW_ON_ERROR
            ));
        }
        mkdir($vendor . '/typo3/cms-core', 0o777, true);
        mkdir($root . '/packages/my_sitepackage', 0o777, true);
        mkdir($vendor . '/composer', 0o777, true);
        file_put_contents($vendor . '/composer/installed.json', json_encode(['packages' => [
            [
                'name' => 'typo3/cms-core',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
                'extra' => ['typo3/cms' => ['extension-key' => 'core']],
            ],
            [
                'name' => 'acme/my-sitepackage',
                'type' => 'typo3-cms-extension',
                // Composer writes install-path relative to the vendor
                // directory it actually used, so a moved one is deeper.
                'install-path' => str_repeat('../', substr_count($vendorDirectory, '/') + 2)
                    . 'packages/my_sitepackage',
                'extra' => ['typo3/cms' => ['extension-key' => 'my_sitepackage']],
            ],
            ['name' => 'symfony/console', 'type' => 'library', 'install-path' => '../symfony/console'],
        ]], JSON_THROW_ON_ERROR));

        return $root;
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
