<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\InstalledIcons;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Scope;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;

/**
 * Where the identifiers this tool answers with may be used.
 *
 * The registry is the backend's. An answer that does not say so is usable in a
 * frontend template, where none of it resolves.
 */
final class IconLookupTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
        InstalledIcons::forget();
    }

    #[Test]
    public function everyAnswerSaysTheIdentifiersAreTheBackendRegistrys(): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        // A hit, a miss, and the browsing answer with no query at all.
        foreach (['acme-product', 'quantumflux', ''] as $query) {
            $result = Tools::call('typo3_icon_lookup', ['query' => $query]);

            self::assertStringContainsString('backend icon registry', $result->text, 'query: ' . $query);
            self::assertStringContainsString('backend icon registry', (string) ($result->data['scope'] ?? ''));
        }
    }

    #[Test]
    public function theRoutingEntrySendsCallersThereForBackendWorkOnly(): void
    {
        // "About to reference an icon identifier" read as if it held for any
        // icon in any context, which is where the wrong use came from.
        $entries = array_values(array_filter(
            Scope::read()['routing'],
            static fn(array $entry): bool => $entry['call'] === 'typo3_icon_lookup'
        ));

        self::assertCount(1, $entries);
        self::assertStringContainsString('backend', $entries[0]['when']);
    }

    /** A Composer project whose own extension registers an icon. */
    private function installationWithItsOwnIcon(): string
    {
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        mkdir($extension . '/Configuration', 0o777, true);
        file_put_contents(
            $extension . '/Configuration/Icons.php',
            "<?php\nreturn ['acme-product' => ['provider' => 'x', 'source' => 'y']];\n"
        );
        InstalledIcons::forget();

        return $root;
    }
}
