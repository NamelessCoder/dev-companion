<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Domains;
use Typo3CmsMcp\TaskIntents;
use Typo3CmsMcp\TestSuiteHints;

final class HintsTest extends TestCase
{
    #[Test]
    public function aPhpPathIsNeverAnsweredWithFrontendConventions(): void
    {
        $result = ArchitectureHints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], '', 6);

        self::assertContains(Domains::PHP, $result['domains']);
        self::assertNotContains(Domains::FRONTEND, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertContains($hint['category'], ['PHP', 'General']);
        }
    }

    #[Test]
    public function aSassPathReachesTheCssHints(): void
    {
        $result = ArchitectureHints::find(['Build/Sources/Sass/component/_badge.scss'], '', 6);

        self::assertContains(Domains::FRONTEND, $result['domains']);
        self::assertNotSame([], $result['matchedHints']);
    }

    #[Test]
    public function proseIsOnlyAFallbackWhenNoStructuredHintMatched(): void
    {
        $matched = ArchitectureHints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], 'DataHandler', 6);
        self::assertNotSame([], $matched['matchedHints']);
        self::assertSame([], $matched['knowledgeSections'], 'prose would only bury the hints it restates');
    }

    #[Test]
    public function hintsAreGroupedWithGeneralFirst(): void
    {
        $hints = ArchitectureHints::load();
        $categories = array_column(ArchitectureHints::groupByCategory($hints), 'category');

        self::assertSame('PHP', $categories[0]);
        self::assertContains('General', $categories);
    }

    #[Test]
    public function everyHintCarriesItsSectionAndAtLeastOneHint(): void
    {
        foreach (ArchitectureHints::load() as $hint) {
            self::assertNotSame('', $hint['id']);
            self::assertNotSame([], $hint['hints'], $hint['id'] . ' has no hints');
            self::assertNotSame('', $hint['category']);
        }
    }

    #[Test]
    public function aSuiteIsFoundByItsName(): void
    {
        $hints = TestSuiteHints::find('phpstan');

        self::assertSame('phpstan', $hints[0]['suite']);
        self::assertStringContainsString('runTests.sh', $hints[0]['command']);
    }

    #[Test]
    public function aPhpTaskIsNeverRecommendedASassBuild(): void
    {
        $suites = array_column(TestSuiteHints::find('unit tests', [Domains::PHP]), 'suite');

        self::assertContains('unit', $suites);
        self::assertNotContains('lintScss', $suites);
        self::assertNotContains('build-css', $suites);
    }

    #[Test]
    public function withoutAQueryEverySuiteIsListedForBrowsing(): void
    {
        self::assertSame(count(TestSuiteHints::load()), count(TestSuiteHints::find(null)));
    }

    #[Test]
    public function theInvocationNotesApplyToEverySuite(): void
    {
        $invocation = TestSuiteHints::invocation();

        self::assertNotSame([], $invocation['notes']);
        self::assertNotSame([], $invocation['options']);
        self::assertNotSame([], $invocation['examples']);
    }

    #[Test]
    public function aDeprecationTaskIsRecognizedAsOne(): void
    {
        $intents = TaskIntents::detect('Deprecate GeneralUtility::getUrl()');

        self::assertContains('deprecation', array_column($intents, 'id'));
    }

    #[Test]
    public function aRecognizedIntentPullsTheRulesThatApply(): void
    {
        $rules = TaskIntents::rules(TaskIntents::detect('deprecate a method'));

        self::assertNotSame([], $rules);
        foreach ($rules as $rule) {
            self::assertNotSame('', $rule['heading']);
        }
    }

    #[Test]
    public function withoutAnySignalTheDomainIsPhp(): void
    {
        self::assertSame([Domains::PHP], Domains::detect([], 'do something'));
    }

    #[Test]
    public function aLabelChangeAlwaysInvolvesPhpAsWell(): void
    {
        $domains = Domains::detect(['typo3/sysext/backend/Resources/Private/Language/locallang.xlf'], '');

        self::assertContains(Domains::XLIFF, $domains);
        self::assertContains(Domains::PHP, $domains);
    }
}
