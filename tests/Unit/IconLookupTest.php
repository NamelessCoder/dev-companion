<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Icons;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Installation\Typo3Runtime;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

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
        Icons::forget();
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }

    #[Test]
    #[DataProvider('theThreeShapesAnIconAnswerTakes')]
    public function everyAnswerSaysTheIdentifiersAreTheBackendRegistrys(string $query): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $result = Registry::call('typo3_icon_lookup', ['query' => $query]);

        self::assertStringContainsString('backend icon registry', $result->text);
        self::assertStringContainsString('backend icon registry', (string) ($result->data['scope'] ?? ''));
    }

    /** @return array<string, array{0: string}> */
    public static function theThreeShapesAnIconAnswerTakes(): array
    {
        return [
            'a hit' => ['acme-product'],
            'a miss' => ['quantumflux'],
            'the browsing answer, with no query at all' => [''],
        ];
    }

    #[Test]
    public function theRoutingEntrySendsCallersThereForBackendWorkOnly(): void
    {
        // "About to reference an icon identifier" read as if it held for any
        // icon in any context, which is where the wrong use came from.
        $entries = array_values(array_filter(
            Coverage::read()['routing'],
            static fn(array $entry): bool => $entry['call'] === 'typo3_icon_lookup'
        ));

        self::assertCount(1, $entries);
        self::assertStringContainsString('backend', $entries[0]['when']);
    }

    #[Test]
    public function aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist(): void
    {
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $categoryOnly = Registry::call('typo3_icon_lookup', [
            'query' => 'actions-definitely-does-not-exist',
        ]);
        self::assertSame(0, $categoryOnly->data['matchCount']);
        self::assertSame(0, $categoryOnly->data['suggestionCount']);

        $missing = Registry::call('typo3_icon_lookup', [
            'query' => 'actions-open-definitely-does-not-exist',
        ]);

        self::assertFalse($missing->data['exactMatch']);
        self::assertSame(0, $missing->data['matchCount']);
        self::assertGreaterThan(0, $missing->data['suggestionCount']);
        self::assertStringContainsString('suggestions, not the answer', $missing->text);

        $exact = Registry::call('typo3_icon_lookup', ['query' => 'actions-open']);
        self::assertTrue($exact->data['exactMatch']);
        self::assertSame(1, $exact->data['matchCount']);
    }

    #[Test]
    public function aRegistryReadFromTheFilesSaysThatInTheAnswerItself(): void
    {
        // The registry the installation assembles is the one that knows what a
        // package builds in a loop. This project has no console to boot it
        // with, so the files are read — and an answer that does not say so is
        // read as the whole registry by a review that then reports icons as
        // unregistered because it could not see them.
        Instance::discoverFrom($this->installationWithItsOwnIcon());

        $result = Registry::call('typo3_icon_lookup', ['query' => 'acme-product']);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertStringContainsString('read from the package files', $result->text);
        self::assertStringContainsString('has no TYPO3 console', $result->text, 'the reason travels with it');
        self::assertStringContainsString('builds in a loop', $result->text);
        // The answer itself still stands: what was read is right, not complete.
        self::assertTrue($result->data['exactMatch']);
    }

    /** A Composer project whose own extension registers an icon. */
    private function installationWithItsOwnIcon(): string
    {
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        mkdir($extension . '/Configuration', 0o777, true);
        file_put_contents(
            $extension . '/Configuration/Icons.php',
            "<?php\nreturn [\n"
            . "    'acme-product' => ['provider' => 'x', 'source' => 'y'],\n"
            . "    'actions-open' => ['provider' => 'x', 'source' => 'y'],\n"
            . "    'actions-close' => ['provider' => 'x', 'source' => 'y'],\n"
            . "];\n"
        );
        Icons::forget();

        return $root;
    }
}
