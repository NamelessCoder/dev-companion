<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;

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
        $this->alsoInstalled($root . '/.build/vendor', [
            'name' => 'acme/demo-package',
            'type' => 'typo3-cms-extension',
            'install-path' => '../../../Tests/Packages/demo_package',
            'extra' => ['typo3/cms' => ['extension-key' => 'demo_package']],
        ]);
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
    public function aRootThatIsAlsoInstalledIntoTheVendorDirectoryIsOnePackageAtTheRoot(): void
    {
        // The extension checkout that requires itself through a path
        // repository: Composer symlinks the root into the vendor directory and
        // lists it there, so the same extension arrives twice. Both entries
        // resolve to one realpath under one key, which is what makes them
        // collapse — the vendor path would report the extension being edited as
        // a dependency of the repository it is.
        $root = $this->composerProject();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'repositories' => ['self' => ['type' => 'path', 'url' => '.']],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/vendor/acme', 0o777, true);
        symlink($root, $root . '/vendor/acme/bootstrap-package');
        $this->alsoInstalled($root . '/vendor', [
            'name' => 'acme/bootstrap-package',
            'type' => 'typo3-cms-extension',
            'install-path' => '../acme/bootstrap-package',
        ]);
        Instance::discoverFrom($root);

        self::assertSame(['bootstrap_package', 'core', 'my_sitepackage'], array_keys(Instance::packages()));
        self::assertSame(realpath($root), Instance::packages()['bootstrap_package']);

        $origins = array_column(Project::describe()['extensions'], 'origin', 'key');
        self::assertSame(Project::ORIGIN_PROJECT, $origins['bootstrap_package'] ?? null);
    }

    #[Test]
    public function aMonorepoRootIsCountedBesideThePackagesItHoldsRatherThanInsteadOfThem(): void
    {
        // A repository that holds extensions rather than being one, and still
        // declares a TYPO3 package type at its root. Nothing in the metadata
        // says the root is a container, so it is counted like any other root —
        // but under its own key, and the extension actually being edited keeps
        // the directory Composer installed it in.
        $root = $this->composerProject();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/typo3-extensions',
            'type' => 'typo3-cms-extension',
            'repositories' => ['packages' => ['type' => 'path', 'url' => 'packages/*']],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame(['core', 'my_sitepackage', 'typo3_extensions'], array_keys(Instance::packages()));
        self::assertSame(realpath($root) . '/packages/my_sitepackage', Instance::packages()['my_sitepackage']);
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
        self::assertSame([], Instance::packages());

        // What the same manifest is enough for: saying which extension this
        // repository is, which places the work and reports nothing
        // (`D-SCO-012`). The key is derived, because this one declares none.
        self::assertSame('bootstrap_package', Instance::startedInPackage());
        self::assertSame(Instance::KIND_EXTENSION_REPOSITORY, Instance::startedIn());
    }

    /**
     * @param array<string, mixed> $manifest what the root's composer.json declares
     */
    #[Test]
    #[DataProvider('manifests')]
    public function aProjectRootIsRecognisedByWhatItsOwnManifestDeclares(array $manifest, bool $isProjectRoot): void
    {
        // The state the installation workflow starts in: a clone nobody has run
        // composer install in. Everything the project answer reads is in these
        // files, and the walk goes up twelve directories — so what identifies a
        // root has to be a declaration of TYPO3 rather than the presence of a
        // composer.json, or the answer reports a TYPO3 project for whatever PHP
        // repository the caller happens to be standing below (`D-ANS-085`) —
        // `D-DIS-019`.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame($isProjectRoot ? realpath($root) : null, Instance::project()['root'] ?? null);

        // And in neither case is it an installation. Nothing is installed below
        // it, so the icon, label and package answers keep saying so rather than
        // speaking for a checkout with no packages and no console.
        self::assertFalse(Instance::isAvailable());
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: bool}>
     */
    public static function manifests(): array
    {
        return [
            'an extension repository' => [[
                'name' => 't3g/blog',
                'type' => 'typo3-cms-extension',
                'require' => ['php' => '^8.2', 'typo3/cms-core' => '^13.4.15 || ^14.3'],
                'extra' => ['typo3/cms' => ['extension-key' => 'blog']],
            ], true],
            // The base distribution every environment below .environments/ is
            // built from: no package type and no extra block, and the packages
            // it requires are the whole of what says TYPO3 at all.
            'a site distribution' => [[
                'name' => 'typo3/cms-base-distribution',
                'type' => 'project',
                'require' => ['typo3/cms-backend' => '^13.4', 'typo3/cms-core' => '^13.4'],
            ], true],
            'a package that installs TYPO3 for its tests alone' => [[
                'name' => 'acme/fluid-components',
                'type' => 'library',
                'require-dev' => ['typo3/cms-core' => '^14.3'],
            ], true],
            'a project that declares only the installer keys' => [[
                'name' => 'acme/site',
                'type' => 'project',
                'extra' => ['typo3/cms' => ['web-dir' => 'public']],
            ], true],
            'a PHP library' => [[
                'name' => 'acme/toolkit',
                'type' => 'library',
                'require' => ['php' => '>=8.2', 'symfony/finder' => '^7.4'],
            ], false],
            // Requiring one of TYPO3's tools is not requiring TYPO3, and it is
            // the shape a rule matching the vendor name alone would claim.
            'a repository that requires TYPO3 tooling' => [[
                'name' => 'acme/toolkit',
                'type' => 'library',
                'require-dev' => ['typo3/coding-standards' => '^0.8', 'typo3/tailor' => '^1.5'],
            ], false],
        ];
    }

    #[Test]
    public function aPackageInsideAnInstalledProjectIsNotTheProjectRoot(): void
    {
        // The installation is looked for over the whole walk before any
        // declaration is, so the project a session stands in stays the answer
        // from inside one of its own packages — where the manifest declares a
        // TYPO3 package type and the environment, the sites and the document
        // root are one directory up — `D-DIS-019`.
        $root = $this->composerProject();
        file_put_contents($root . '/packages/my_sitepackage/composer.json', json_encode(
            ['name' => 'acme/my-sitepackage', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        Instance::discoverFrom($root . '/packages/my_sitepackage');

        self::assertSame(realpath($root), Instance::project()['root'] ?? null);
    }

    #[Test]
    public function aNamedInstallationThatDoesNotExistIsNotWalkedPastForAProjectEither(): void
    {
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 't3g/blog', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR
        ));
        putenv(Instance::ROOT_VARIABLE . '=' . $root . '/nowhere');
        Instance::discoverFrom($root);

        // Describing the repository the walk finds would answer about something
        // other than what the caller named, which is the failure the variable is
        // reported through rather than searched past.
        self::assertNull(Instance::project());
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

    /**
     * Adds one more package to what a vendor directory reports as installed.
     *
     * @param array<string, mixed> $package as Composer writes it into installed.json
     */
    private function alsoInstalled(string $vendor, array $package): void
    {
        $file = $vendor . '/composer/installed.json';
        $installed = json_decode((string) file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
        $installed['packages'][] = $package;
        file_put_contents($file, json_encode($installed, JSON_THROW_ON_ERROR));
    }
}
