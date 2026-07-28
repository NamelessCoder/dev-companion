<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Domains;
use Typo3CmsMcp\TaskIntents;
use Typo3CmsMcp\TestSuiteHints;
use Typo3CmsMcp\Tools;

final class HintsTest extends TestCase
{
    #[Test]
    public function aPhpPathIsNeverAnsweredWithFrontendConventions(): void
    {
        $result = ArchitectureHints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], '', 6);

        self::assertContains(Domains::PHP, $result['domains']);
        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertContains($hint['category'], ['PHP', 'General']);
        }
    }

    #[Test]
    public function aSassPathReachesTheCssHints(): void
    {
        $result = ArchitectureHints::find(['Build/Sources/Sass/component/_badge.scss'], '', 6);

        self::assertContains(Domains::CSS, $result['domains']);
        self::assertNotSame([], $result['matchedHints']);
    }

    #[Test]
    public function aSassPathIsNeverAnsweredWithTypeScriptConventions(): void
    {
        $result = ArchitectureHints::find(['Build/Sources/Sass/component/_card.scss'], 'card component styling', 8);

        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotContains($hint['category'], ['TypeScript', 'JavaScript'], $hint['id']);
        }
    }

    #[Test]
    public function aTypeScriptPathIsNeverAnsweredWithCssConventions(): void
    {
        $result = ArchitectureHints::find(
            ['Build/Sources/TypeScript/backend/form-editor/inspector-component.ts'],
            'field label override per record type',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame('CSS', $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function aFluidPathReachesTheFluidHintsAndNoOthers(): void
    {
        $result = ArchitectureHints::find(
            [
                'typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html',
                'typo3/sysext/core/Classes/ViewHelpers/IconViewHelper.php',
            ],
            'Fluid template ViewHelper conventions escaping namespace',
            6
        );

        $categories = array_column($result['matchedHints'], 'category');
        self::assertContains('Fluid', $categories);
        self::assertNotContains('TypeScript', $categories);
        self::assertNotContains('CSS', $categories);
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
    public function noHintStatesSomethingThatOnlyHoldsOnOneBranch(): void
    {
        // The server does not know the caller's branch, so a hint has to hold on
        // every one of them. A version number, a concrete changelog file, or a
        // count taken from a single checkout is a snapshot: it reads as a fact
        // long after it stopped being one. Where the answer really is
        // branch-specific, the hint says how to look it up in the checkout.
        $snapshots = [
            'a version number' => '/\bv\d+\b|\b\d+\.\d+\b|\bsince \d/i',
            'a concrete changelog file' => '/\b(Breaking|Deprecation|Feature|Important|Task)-\d+/i',
            'a count taken from a checkout' => '/\b\d{2,}\b/',
        ];

        foreach (ArchitectureHints::load() as $hint) {
            // PSR numbers name an interface, they do not date the statement.
            $text = (string) preg_replace(
                '/\bPSR-\d+/i',
                'PSR',
                $hint['title'] . "\n" . implode("\n", $hint['hints'])
            );

            foreach ($snapshots as $what => $pattern) {
                self::assertDoesNotMatchRegularExpression(
                    $pattern,
                    $text,
                    $hint['id'] . ' states ' . $what . ', which only holds on the branch it was written from'
                );
            }
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
    public function aPathNamedInTheQueryNarrowsTheSuitesAsAnExplicitPathWould(): void
    {
        $suites = array_column(Tools::call('typo3_test_run_guide', [
            'query' => 'Only a Sass change in Build/Sources/Sass/component/_card.scss; recommend the narrow '
                . 'iteration check and the review-ready checks, without unrelated PHP or TypeScript suites',
        ])->data['suites'], 'suite');

        self::assertSame(['build', 'build-css', 'lintScss'], $suites);
    }

    #[Test]
    public function aNegatedDomainInTheQueryIsNotReadAsASignal(): void
    {
        // "without unrelated PHP or TypeScript suites" names both domains it
        // rules out, which is why only paths narrow the answer.
        self::assertSame([], Domains::fromPaths(['without unrelated PHP or TypeScript suites']));
        self::assertSame([Domains::CSS], Domains::fromPaths(['Build/Sources/Sass/component/_card.scss']));
    }

    #[Test]
    public function aQueryThatMatchesNoSuiteNameStillAnswersWithinItsDomain(): void
    {
        $suites = TestSuiteHints::find('recommend the review-ready checks', [Domains::CSS]);

        self::assertNotSame([], $suites, 'a narrowed list beats an empty answer');
        foreach ($suites as $suite) {
            self::assertContains(Domains::CSS, $suite['domains']);
        }
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
    public function anXlfOnlyChangeStaysAnXlfChange(): void
    {
        // A label patch that touches no PHP must not pull the PHP suites in:
        // unit and functional runs cost minutes and cannot fail on an XLF edit.
        $domains = Domains::detect(['typo3/sysext/backend/Resources/Private/Language/locallang.xlf'], '');

        self::assertSame([Domains::XLIFF], $domains);
        self::assertSame(
            ['checkIntegrityXliff', 'normalizeXliff'],
            array_column(TestSuiteHints::find(null, $domains), 'suite')
        );
    }

    #[Test]
    public function aFluidTemplateIsItsOwnDomain(): void
    {
        $domains = Domains::detect(['typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html'], '');

        self::assertContains(Domains::FLUID, $domains);
        self::assertNotContains(Domains::TYPESCRIPT, $domains);
        self::assertNotContains(Domains::CSS, $domains);
    }
}
