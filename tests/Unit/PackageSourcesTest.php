<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\FluidNamespaces;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

/**
 * What the installed packages can answer when the console cannot.
 *
 * Booting TYPO3 needs a migrated database, and a project spends a lot of its
 * life without one. Where the answer is a file in a package rather than the
 * container's assembled state, the question does not have to end there.
 */
final class PackageSourcesTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function fluidNamespacesAreReadFromThePackagesThatDeclareThem(): void
    {
        $root = $this->coreCheckout();
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        $this->namespaceFile($root . '/typo3/sysext/backend', ['f' => ['TYPO3\\CMS\\Backend\\ViewHelpers']]);
        Instance::discoverFrom($root);

        self::assertSame(
            ['core' => ['TYPO3\CMS\Core\ViewHelpers'], 'f' => ['TYPO3\CMS\Backend\ViewHelpers']],
            FluidNamespaces::all()
        );
    }

    #[Test]
    public function withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne(): void
    {
        $root = $this->coreCheckout();
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        Instance::discoverFrom($root);
        Typo3Cli::forget();

        $result = Registry::call('typo3_fluid_namespace_list', []);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('core', $result->data['namespaces'][0]['prefix']);
        self::assertStringContainsString('not what the container assembled', $result->text);
    }

    #[Test]
    public function aPackageThatDeclaresNothingContributesNothing(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame([], FluidNamespaces::all());
    }

    #[Test]
    public function theChangelogOfTheInstalledCoreIsSearchable(): void
    {
        // What a version deprecated is a list, not a convention, and every
        // installation has it on disk.
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-107208-FdebugrenderViewHelper', 'Deprecation: #107208 - <f:debug.render> ViewHelper', ['Fluid', 'NotScanned', 'ext:fluid']);
        $this->changelogEntry($root, '13.4', 'Breaking-101392-GetIdentifierRemoved', 'Breaking: #101392 - getIdentifier() removed', ['PHP-API', 'FullyScanned']);
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'viewhelper']);

        self::assertSame(1, $result->data['matchCount']);
        $entry = $result->data['entries'][0];
        self::assertSame('Deprecation', $entry['type']);
        self::assertSame('14.0', $entry['version']);
        self::assertSame('107208', $entry['issue']);
        self::assertSame('<f:debug.render> ViewHelper', $entry['title'], 'the type and the issue are fields of their own');
        self::assertSame(['Fluid', 'NotScanned', 'ext:fluid'], $entry['tags']);
        self::assertSame(['14.0', '13.4'], $result->data['versions'], 'newest first');
    }

    /**
     * The shape two sessions reported from two checkouts, and the one a caller
     * actually types: the thing has a name with separators in it, and the
     * changelog file spells that name apart. Every one of these has an entry in
     * `.checkouts/14.3` and reached none of them.
     */
    #[Test]
    public function anIdentifierReachesTheEntryTitledInWords(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.3', 'Deprecation-109438-ExtTablesPhpInExtensions', 'Deprecation: #109438 - ext_tables.php in extensions', []);
        $this->changelogEntry($root, '14.0', 'Deprecation-98453-SchedulerTaskRegistrationViaSCOPTIONS', 'Deprecation: #98453 - Scheduler task registration via SC_OPTIONS', []);
        $this->changelogEntry($root, '14.0', 'Breaking-107784-RemoveBackendLayoutDataProviderRegistration', 'Breaking: #107784 - Remove backend layout data provider registration', []);
        Instance::discoverFrom($root);

        $reaches = static function (string $query): array {
            return array_column(Registry::call('typo3_changelog_lookup', ['query' => $query])->data['entries'], 'issue');
        };

        self::assertSame(['109438'], $reaches('ext_tables.php'));
        self::assertSame(['98453'], $reaches('SC_OPTIONS'));
        self::assertSame(['107784'], $reaches('backend_layout'));
        // The words themselves still reach it, and every term still has to be
        // carried: nothing that matched before matches less.
        self::assertSame(['109438'], $reaches('ext tables extensions'));
        self::assertSame([], $reaches('ext_tables.php scheduler'));
    }

    #[Test]
    public function theChangelogIsNarrowedByTypeAndVersion(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-1-SomethingOld', 'Deprecation: #1 - Something old', []);
        $this->changelogEntry($root, '14.0', 'Feature-2-SomethingNew', 'Feature: #2 - Something new', []);
        $this->changelogEntry($root, '13.4', 'Feature-3-SomethingOlder', 'Feature: #3 - Something older', []);
        Instance::discoverFrom($root);

        $byType = Registry::call('typo3_changelog_lookup', ['type' => 'feature']);
        self::assertSame(['2', '3'], array_column($byType->data['entries'], 'issue'));

        // A prefix, so "14" reaches 14.0 through 14.3.x.
        $byVersion = Registry::call('typo3_changelog_lookup', ['version' => '14']);
        self::assertSame(['1', '2'], array_column($byVersion->data['entries'], 'issue'));
    }

    #[Test]
    public function anInstallationWithoutAChangelogSaysSoRatherThanAnsweringEmpty(): void
    {
        Instance::discoverFrom($this->composerProject());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'anything']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertArrayHasKey('unsupported', $result->data);
    }

    /** @param array<int, string> $tags */
    private function changelogEntry(string $root, string $version, string $name, string $title, array $tags): void
    {
        $path = $root . '/vendor/typo3/cms-core/Documentation/Changelog/' . $version;
        if (!is_dir($path)) {
            mkdir($path, 0o777, true);
        }
        file_put_contents($path . '/' . $name . '.rst', implode("\n", [
            '.. include:: /Includes.rst.txt',
            '',
            str_repeat('=', mb_strlen($title)),
            $title,
            str_repeat('=', mb_strlen($title)),
            '',
            'Description',
            '',
            $tags === [] ? '' : '..  index:: ' . implode(', ', $tags),
        ]));
    }

    /** @param array<string, array<int, string>> $namespaces */
    private function namespaceFile(string $packagePath, array $namespaces): void
    {
        $path = $packagePath . '/Configuration/Fluid';
        mkdir($path, 0o777, true);

        $entries = '';
        foreach ($namespaces as $prefix => $phpNamespaces) {
            $entries .= sprintf("    '%s' => [\n", $prefix);
            foreach ($phpNamespaces as $phpNamespace) {
                $entries .= sprintf("        '%s',\n", str_replace('\\', '\\\\', $phpNamespace));
            }
            $entries .= "    ],\n";
        }

        file_put_contents($path . '/Namespaces.php', "<?php\n\nreturn [\n" . $entries . "];\n");
    }
}
