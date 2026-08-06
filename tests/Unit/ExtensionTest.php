<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Extension;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;

/**
 * What the extension answer says about the registration files it lists.
 *
 * Both deprecations behind this turn on a file the extension ships rather than
 * on anything its code calls, so nothing a caller would search for reaches
 * them and the tool that lists the file is the only thing that can — D-ANS-009.
 * The predicates are read at their trigger sites in .checkouts/14.3:
 * `Configuration\Extension\ExtTablesFactory` for #109438 and
 * `Package\PackageManager::getComposerManifest()` for #108345.
 */
final class ExtensionTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function shippingBothFilesWithoutTheComposerFieldsCostsBothDeprecations(): void
    {
        $found = $this->deprecatedFilesOf(['ext_tables.php', 'ext_emconf.php'], []);

        self::assertSame(['ext_tables.php', 'ext_emconf.php'], array_column($found, 'file'));
        self::assertSame(['#109438', '#108345'], array_column($found, 'changelog'));
    }

    #[Test]
    public function declaringBothFieldsIsWhatSilencesTheEmconfOne(): void
    {
        $found = $this->deprecatedFilesOf(['ext_emconf.php'], [
            'extra' => ['typo3/cms' => ['version' => '1.0.0', 'Package' => ['providesPackages' => []]]],
        ]);

        // An empty providesPackages is what an extension shipping no Composer
        // packages of its own writes, and isComposerOnlyCapable() accepts it.
        self::assertSame([], $found);
    }

    #[Test]
    public function declaringOneOfTheTwoFieldsStillReadsTheFile(): void
    {
        $version = $this->deprecatedFilesOf(['ext_emconf.php'], ['version' => '1.0.0']);
        $provides = $this->deprecatedFilesOf(['ext_emconf.php'], [
            'extra' => ['typo3/cms' => ['Package' => ['providesPackages' => []]]],
        ]);

        // The predicate is both fields rather than either, which is the half
        // an extension author reads past: composer.json gains a version, the
        // deprecation stays, and nothing says why.
        self::assertSame(['#108345'], array_column($version, 'changelog'));
        self::assertSame(['#108345'], array_column($provides, 'changelog'));
    }

    #[Test]
    public function theTopLevelVersionFieldCountsAsMuchAsTheTypo3One(): void
    {
        $found = $this->deprecatedFilesOf(['ext_emconf.php'], [
            'version' => '1.0.0',
            'extra' => ['typo3/cms' => ['Package' => ['providesPackages' => []]]],
        ]);

        self::assertSame([], $found);
    }

    #[Test]
    public function aFrameworkPackageIsExemptFromBoth(): void
    {
        $found = $this->deprecatedFilesOf(
            ['ext_tables.php', 'ext_emconf.php'],
            ['type' => 'typo3-cms-framework'],
        );

        // Core exempts a framework package from #109438 outright, and a system
        // extension derives its version from Typo3Version rather than from
        // composer.json. The v13 system extensions all ship an ext_emconf.php
        // and none of them is the caller's to migrate.
        self::assertSame([], $found);
    }

    #[Test]
    public function anExtensionShippingNeitherFileIsNotToldItIsClean(): void
    {
        $found = $this->deprecatedFilesOf(['ext_localconf.php'], []);

        // Present and empty, never absent: the field is a contract, and what
        // an empty list does not say is in its schema description.
        self::assertSame([], $found);
    }

    /**
     * An extension shipping exactly those files, described.
     *
     * @param array<int, string> $files
     * @param array<string, mixed> $manifest what its composer.json declares beside the key
     * @return array<int, array{file: string, changelog: string, predicate: string, cost: string}>
     */
    private function deprecatedFilesOf(array $files, array $manifest): array
    {
        $root = $this->composerProject();
        $path = $root . '/packages/my_sitepackage';
        file_put_contents($path . '/composer.json', json_encode(
            $manifest + ['name' => 'acme/my-sitepackage', 'type' => 'typo3-cms-extension'],
            JSON_THROW_ON_ERROR,
        ));
        foreach ($files as $file) {
            file_put_contents($path . '/' . $file, "<?php\n");
        }
        Instance::discoverFrom($root);

        $extension = Extension::describe('my_sitepackage');
        self::assertNotNull($extension);
        self::assertSame($files, array_intersect($files, $extension['files']));

        return $extension['deprecatedFiles'];
    }
}
