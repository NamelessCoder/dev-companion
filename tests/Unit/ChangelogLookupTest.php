<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Reaching a changelog entry by what a caller holds rather than by its title:
 * the identifier its body names, and the issue number it was filed under.
 *
 * The identifier corpus is three entries written in the two markups the core's
 * own changelog uses for one, because what that holds is a rule about markup —
 * `D-ANS-042`. The number is read off the file name, and `D-VER-009` is what a
 * sweep asks it for.
 */
final class ChangelogLookupTest extends TestCase
{
    use TemporaryInstallation;

    /**
     * Nothing here reaches docs.typo3.org. The changelog lookup reads the
     * versions above the installed major from the manual, and a unit test that
     * let it would be measuring the host — `R-COD-003`.
     */
    #[Before]
    public function sealTheManual(): void
    {
        CoreChangelog::useReader(static fn(string $url): ?string => null);
    }

    #[After]
    public function forgetTheInstance(): void
    {
        CoreChangelog::useReader(null);
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    #[Test]
    public function aRemovedMethodReachesTheEntriesNamingItInTheirBody(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText']);

        // Breaking-101955 is titled about image generation and carries the
        // method in a list of what it removed; Deprecation-46770 is the older
        // markup, single backticks and no :php: role.
        self::assertSame(2, $result->data['matchCount']);
        self::assertStringContainsString('13.0/Breaking-101955-', (string) $result->data['entries'][0]['file']);
        self::assertStringContainsString('7.1/Deprecation-46770-', (string) $result->data['entries'][1]['file']);
        self::assertSame('body', $result->data['matchedIn']);
        self::assertStringContainsString('not the same as being about it', $result->text);
    }

    #[Test]
    public function aQueryTheNamesAnswerIsNotWidenedByTheBodies(): void
    {
        // Breaking-101955 names GraphicalFunctions 44 times in the corpus this
        // was measured against while being titled about image generation, which
        // is the answer this order exists to keep out: the names answer, and
        // the bodies are read only where they answered nothing.
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'GraphicalFunctions']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertStringContainsString('Deprecation-46770', (string) $result->data['entries'][0]['file']);
        self::assertSame('name', $result->data['matchedIn']);
    }

    #[Test]
    public function aWordThatIsAlsoWrittenAsCodeIsNotAnIdentifier(): void
    {
        // `crop()` is a real method and the index does not carry it: an
        // identifier is a name written with a hump or an underscore, and
        // without that rule every entry writing `preview` or `file` would
        // answer the word.
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'crop']);

        self::assertSame(0, $result->data['matchCount']);
    }

    #[Test]
    public function anIdentifierIsReachedInEverySpellingACallerHasIt(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        foreach ([
            'qualified by its class' => 'GraphicalFunctions::getTemporaryImageWithText()',
            'fully qualified' => '\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->getTemporaryImageWithText',
        ] as $spelling => $query) {
            $result = Registry::call('typo3_changelog_lookup', ['query' => $query]);

            self::assertSame(2, $result->data['matchCount'], $spelling);
        }
    }

    /**
     * The only entry that carries the word is in a system extension the query
     * never mentioned, and the answer says so.
     *
     * `extendToSubpages` is the TCA column and the natural word for inherited
     * frontend access restriction, and the changelog answers it with one 12.0
     * Breaking removing an Indexed Search option that happens to spell it. The
     * answer is arguably correct — that area was never reworked, and a
     * changelog records change events — but returned flat beside nothing it
     * reads as evidence about the area (`feedback/2026-08-07-233553`).
     */
    #[Test]
    public function anAnswerThatComesWholeFromOneSystemExtensionSaysSo(): void
    {
        Instance::discoverFrom($this->installationWithTheIndexedSearchEntry());

        $answered = Registry::call('typo3_changelog_lookup', ['query' => 'extendToSubpages']);

        self::assertSame(1, $answered->data['matchCount']);
        self::assertStringContainsString('Every one of these is in ext:indexed_search', $answered->text);
        self::assertStringContainsString('the place that happens to spell the word', $answered->text);

        // Not where the caller asked about that extension. Word by word,
        // because nobody types the key with its underscore.
        $asked = Registry::call('typo3_changelog_lookup', ['query' => 'indexed search']);
        self::assertSame(1, $asked->data['matchCount']);
        self::assertStringNotContainsString('Every one of these is in', $asked->text);
    }

    /**
     * And never for `ext:core`, which most of what the changelog records is in:
     * "every one of these is in ext:core" is a statement about the corpus
     * rather than about the query.
     */
    #[Test]
    public function theCoresOwnTagIsNotSomebodyElsesSubject(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText']);

        self::assertStringNotContainsString('Every one of these is in', $result->text);
    }

    /**
     * The second question a dual-major sweep asks of every deprecation it got
     * back, and the call that answers it — `D-VER-009`.
     *
     * The number is a word of every file name filed under it, so the siblings
     * come back off the names and the version each carries is what says whether
     * the replacement is on the lower declared major.
     */
    #[Test]
    public function anIssueNumberReachesEveryEntryFiledUnderIt(): void
    {
        Instance::discoverFrom($this->installationWithTheEntriesOfTwoIssues());

        $result = Registry::call('typo3_changelog_lookup', ['query' => '108557']);

        self::assertSame(3, $result->data['matchCount']);
        self::assertSame(
            ['Deprecation', 'Feature', 'Important'],
            array_column($result->data['entries'], 'type'),
        );
        self::assertSame(['14.2', '14.2', '14.2'], array_column($result->data['entries'], 'version'));
        // Off the names, so nothing was opened to answer it.
        self::assertSame('name', $result->data['matchedIn']);
    }

    /**
     * What the core filed under two issue numbers, each with the entry that
     * announced the replacement beside the deprecation.
     *
     * The five are read off `.checkouts/14.3` on 2026-08-21, as excerpts: the
     * three of #108557 in 14.2 and the two of #108524 in 14.1, which is what
     * keeps the count above a corpus that only holds one number.
     */
    private function installationWithTheEntriesOfTwoIssues(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach ([
            '14.2/Deprecation-108557-TCAOptionAllowedRecordTypesForPageTypes.rst' => 'Deprecation: #108557 - TCA '
                . "option allowedRecordTypes for page types\n\nDescription\n===========\n\n"
                . "The registry option is deprecated and will be removed in TYPO3 v15.0.\n",
            '14.2/Feature-108557-TCAOptionAllowedRecordTypesForPageTypes.rst' => 'Feature: #108557 - TCA option '
                . "allowedRecordTypes for page types\n\nDescription\n===========\n\n"
                . "Page types now declare the record types they allow in TCA.\n",
            '14.2/Important-108557-DropPageDoktypeRegistryOnlyAllowedTablesOption.rst' => 'Important: #108557 - Drop '
                . "PageDoktypeRegistry option allowedTables\n\nDescription\n===========\n\n"
                . "The option is gone from the registry.\n",
            '14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst' => 'Deprecation: #108524 - Fluid '
                . "namespaces in TYPO3_CONF_VARS\n\nDescription\n===========\n\n"
                . "Registering namespaces in TYPO3_CONF_VARS is deprecated.\n",
            '14.1/Feature-108524-ConfigurationFileToRegisterGlobalFluidNamespaces.rst' => 'Feature: #108524 - '
                . "Configuration file to register global Fluid namespaces\n\nDescription\n===========\n\n"
                . "Configuration/Fluid/Namespaces.php registers them instead.\n",
        ] as $path => $contents) {
            if (!is_dir($changelog . '/' . dirname($path))) {
                mkdir($changelog . '/' . dirname($path), 0o777, true);
            }
            file_put_contents($changelog . '/' . $path, $contents);
        }

        return $root;
    }

    private function installationWithTheIndexedSearchEntry(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog/12.0';
        mkdir($changelog, 0o777, true);
        file_put_contents(
            $changelog . '/Breaking-97530-IndexedSearchOptionSearchSkipExtendToSubpagesCheckingRemoved.rst',
            "Breaking: #97530 - Indexed Search option searchSkipExtendToSubpagesChecking removed\n\n"
            . "Description\n===========\n\nThe option has been removed.\n\n"
            . ".. index:: TypoScript, NotScanned, ext:indexed_search\n",
        );

        return $root;
    }

    /**
     * A project whose core ships the three entries around one removed method.
     *
     * Each is an excerpt of the entry it is named after, kept in the markup
     * that entry writes: the `:php:` role of 13.0, the single backticks of 7.1,
     * and a feature that only mentions the class in passing.
     */
    private function installationWithTheImageGenerationEntries(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach ([
            '13.0/Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst' => 'Breaking: #101955 - '
                . "Removed public methods related to Image Generation\n\nDescription\n===========\n\n"
                . "The following public methods from :php:`GraphicalFunctions` have been removed:\n\n"
                . "- :php:`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->crop()`\n"
                . "- :php:`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->getTemporaryImageWithText()`\n\n"
                . ".. index:: PHP-API, FullyScanned, ext:core\n",
            '7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions.rst' => 'Deprecation: #46770 - Deprecate '
                . "LocalImageProcessor::getTemporaryImageWithText\n\nDescription\n===========\n\n"
                . 'The public method `LocalImageProcessor::getTemporaryImageWithText()` has been marked as '
                . "deprecated, it is directly\nreplaced by "
                . "`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions::getTemporaryImageWithText()`.\n",
            '13.0/Feature-102755-ImprovedGetImageResourceFunctionality.rst' => 'Feature: #102755 - Improved '
                . "getImageResource functionality\n\nDescription\n===========\n\n"
                . "The instruction is handed to :php:`GraphicalFunctions` unchanged.\n",
        ] as $path => $contents) {
            if (!is_dir($changelog . '/' . dirname($path))) {
                mkdir($changelog . '/' . dirname($path), 0o777, true);
            }
            file_put_contents($changelog . '/' . $path, $contents);
        }

        return $root;
    }
}
