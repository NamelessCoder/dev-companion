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
            self::assertNotContains($hint['category'], [ArchitectureHints::CATEGORY_TYPESCRIPT, 'JavaScript'], $hint['id']);
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
            self::assertNotSame(ArchitectureHints::CATEGORY_CSS, $hint['category'], $hint['id']);
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
        self::assertNotContains(ArchitectureHints::CATEGORY_TYPESCRIPT, $categories);
        self::assertNotContains(ArchitectureHints::CATEGORY_CSS, $categories);
    }

    #[Test]
    public function aTypoScriptPathReachesTheTypoScriptHintsAndNotTheCssOnes(): void
    {
        // .typoscript and .tsconfig used to fall into the generic frontend
        // bucket, which answered a site set with the CSS browser baseline.
        $result = ArchitectureHints::find(
            [
                'typo3/sysext/fluid_styled_content/Configuration/Sets/FluidStyledContent/setup.typoscript',
                'typo3/sysext/form/Configuration/page.tsconfig',
            ],
            '',
            6
        );

        self::assertContains(Domains::TYPOSCRIPT, $result['domains']);
        $categories = array_column($result['matchedHints'], 'category');
        self::assertContains('TypoScript', $categories);
        self::assertNotContains(ArchitectureHints::CATEGORY_CSS, $categories);
    }

    #[Test]
    public function aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions(): void
    {
        $result = ArchitectureHints::find(
            ['Resources/Public/Scss/bootstrap.scss', 'Build/Sources/Sass/_variables.scss'],
            'Sass architecture, variables and build pipeline for a Bootstrap 5 based frontend theme',
            8
        );

        self::assertContains(ArchitectureHints::CATEGORY_CSS, $result['withheldCategories']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(ArchitectureHints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
        self::assertSame([], $result['knowledgeSections'], 'the CSS document describes the same backend');
    }

    #[Test]
    public function stylingABackendModuleStillReachesTheBackendCssHints(): void
    {
        $result = ArchitectureHints::find(
            ['Resources/Public/Css/backend-icon-search.css'],
            'styling for the backend module of a site package',
            8
        );

        self::assertSame([], $result['withheldCategories']);
        self::assertContains(ArchitectureHints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    #[Test]
    public function aPhpClassNameThatCarriesTheWordScssIsStillPhp(): void
    {
        $result = ArchitectureHints::find(
            ['Classes/ViewHelpers/Format/ScssViewHelper.php', 'Configuration/Services.yaml'],
            '',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(ArchitectureHints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function aQueryAboutLanguageFilesReachesTheLanguageFilesHint(): void
    {
        $result = ArchitectureHints::find([], 'Language files, XLF labels and how to reference them in TCA', 6);

        self::assertContains('language-files', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function aHintCanBeAskedForByItsIdInsteadOfGuessedAt(): void
    {
        $result = ArchitectureHints::find([], '', 6, 'language-files');

        self::assertSame(['language-files'], array_column($result['matchedHints'], 'id'));
        self::assertSame([], $result['domains'], 'nothing was inferred, so nothing is claimed');
        self::assertSame([], $result['availableHints']);
    }

    #[Test]
    public function anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo(): void
    {
        $result = ArchitectureHints::find([], '', 6, 'language-file');

        self::assertSame([], $result['matchedHints']);
        self::assertContains('language-files', array_column($result['availableHints'], 'id'));
    }

    #[Test]
    public function aMissNamesWhatThereWouldHaveBeenToFind(): void
    {
        $hit = ArchitectureHints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], 'DataHandler', 6);
        self::assertSame([], $hit['availableHints'], 'an index would only bury the hints it lists');

        $miss = ArchitectureHints::find([], 'how do I write a good sonnet', 6);
        self::assertSame([], $miss['matchedHints']);
        self::assertContains('language-files', array_column($miss['availableHints'], 'id'));
        foreach ($miss['availableHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
        }
    }

    #[Test]
    public function aPathAloneReachesTheHintForTheSubsystemItIsIn(): void
    {
        // Both were subsystems with no hint at all, and an extension
        // maintenance task got generic TCA and Fluid advice for them.
        $reached = static fn(string $path): array => array_column(
            ArchitectureHints::find([$path], '', 6)['matchedHints'],
            'id'
        );

        self::assertContains('upgrade-wizards', $reached('Classes/Updates/AccordionElementUpdate.php'));
        self::assertContains('frontend-dataprocessors', $reached('Classes/DataProcessing/CsvFileProcessor.php'));
    }

    #[Test]
    public function theFrontendRenderingPathIsAnsweredAsWellAsTheBackendOne(): void
    {
        // Every Fluid hint was about backend modules, and the mechanism every
        // site is built on — how a page template is found and how it reaches
        // its content — was not written down at all.
        $result = ArchitectureHints::find([], 'Fluid templates frontend', 6);

        self::assertContains('frontend-page-rendering', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function anExtbasePluginHasAHintOfItsOwn(): void
    {
        // There was none at all: the task returned datahandler-persistence,
        // which is about DataHandler, and asking by id returned the index.
        $result = ArchitectureHints::find(
            [],
            'Extbase plugin in a project extension: domain model, repository, controller, plugin registration, '
            . 'persistence mapping to a custom table, pagination and search',
            6
        );
        self::assertContains('extbase', array_column($result['matchedHints'], 'id'));

        // The failures are the half that cost the session, and each of them
        // answers with a wrong page rather than with an error.
        $text = implode("\n", array_column((array) ArchitectureHints::byId('extbase')['hints'], 'text'));
        self::assertStringContainsString('cacheHash', $text);
        self::assertStringContainsString('allowProperties', $text);
    }

    #[Test]
    public function registeringSomethingSoTheCoreFindsItIsCovered(): void
    {
        // "How do I register X so the core actually finds it" fell between the
        // component catalog and the subsystem conventions, and was answered by
        // reading the core sources by hand.
        $element = ArchitectureHints::find([], 'register a new content element with its own CType', 6);
        self::assertContains('content-elements', array_column($element['matchedHints'], 'id'));

        $di = ArchitectureHints::byId('dependency-injection-services');
        self::assertNotNull($di);
        self::assertStringContainsString(
            'public: true',
            implode("\n", array_column($di['hints'], 'text')),
            'a provider the container resolves by class name is not found unless it is public'
        );
    }

    #[Test]
    public function aProductSectionInASitepackageIsAnsweredWithHowItIsBuilt(): void
    {
        // The task that produced nothing usable: a list, a detail view and a
        // teaser element for records of an own table. It was answered with two
        // hints about backend forms and shipping content, and the mechanism the
        // whole task is made of was not written down anywhere.
        $result = ArchitectureHints::find(
            [],
            'Add a product list and product detail rendering plus a product teaser element to a sitepackage '
            . 'extension: custom database table, TCA, frontend content elements, routing for the detail view',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('frontend-records', $ids);
        self::assertContains('sitepackage-layout', $ids);
        self::assertContains('frontend-page-rendering', $ids, 'a sitepackage is frontend work');
        self::assertNotContains(ArchitectureHints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    #[Test]
    public function theSeedingAdviceCarriesTheStepsItAsksFor(): void
    {
        // "Seed with DataHandler, then export" named the way in and stopped
        // where the work starts. Getting a DataHandler at all is a hand-written
        // boot, and each of its steps is a trap.
        $hint = ArchitectureHints::byId('sitepackage-initial-content');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));

        self::assertStringContainsString('Bootstrap::init', $text);
        self::assertStringContainsString('initializeBackendUser', $text);
        self::assertStringContainsString('--table', $text);
    }

    #[Test]
    public function aNavigationIsAnsweredWhereMenusAreActuallyConfigured(): void
    {
        // excludeDoktypes replaces the default list instead of extending it,
        // which puts every storage folder into the menu. The hint that says so
        // has to be reachable from the word the question is asked with.
        $result = ArchitectureHints::find([], 'main navigation of the site, menu levels and which pages it shows', 6);

        self::assertContains('frontend-page-rendering', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function aMenuQuestionThatReadsAsFrontendWorkStillReachesTheMenuTrap(): void
    {
        // The statement was there and was re-reported as missing: it sat in the
        // PHP category, and a question phrased as sitepackage work has that
        // whole category withheld from it. Where a statement lives decides who
        // can see it.
        $result = ArchitectureHints::find(
            [],
            'the main navigation of my sitepackage shows storage folders, menu dataProcessing excludeDoktypes',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');
        self::assertContains('frontend-page-rendering', $ids);

        $text = implode("\n", array_column((array) ArchitectureHints::byId('frontend-page-rendering')['hints'], 'text'));
        self::assertStringContainsString('excludeDoktypes', $text);
    }

    #[Test]
    public function aSitepackageIsAnsweredWithTheLayoutTheCoreItselfShips(): void
    {
        // A layout was invented for a sitepackage and rejected afterwards,
        // because the core ships a theme extension that establishes one and
        // nothing here pointed at it.
        $result = ArchitectureHints::find([], 'directory structure of a sitepackage extension', 6);
        self::assertContains('sitepackage-layout', array_column($result['matchedHints'], 'id'));

        $hint = ArchitectureHints::byId('sitepackage-layout');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('theme_camino', $text);
        self::assertStringContainsString('Content/Default', $text, 'the layout name collision is the load-bearing half');
    }

    #[Test]
    public function theTemplateTrapsThatFailWithoutAnErrorAreNamed(): void
    {
        // Both produce a wrong page rather than a failure: a variable assigned
        // outside a section of a template that declares a layout is never
        // executed, and an HTML comment is rendered into the response with its
        // expressions resolved. Neither is logged, so neither is searchable.
        $hint = ArchitectureHints::byId('fluid-templates');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));

        self::assertStringContainsString('<f:section>', $text);
        self::assertStringContainsString('<f:comment>', $text);
    }

    #[Test]
    public function theFileAnExtensionNoLongerNeedsIsCoveredWhereItsFilesAre(): void
    {
        // "Which files does an extension need" listed every file that is still
        // current and left out the one that costs something: ext_emconf.php,
        // absent from the list, reads as "not relevant" rather than as "declare
        // this in composer.json instead".
        $hint = ArchitectureHints::byId('extension-files');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('ext_emconf.php', $text);

        $current = implode("\n", array_column((array) ArchitectureHints::byId('extension-files', 14)['hints'], 'text'));
        self::assertStringContainsString('providesPackages', $current);
        self::assertStringContainsString('extra.typo3/cms.version', $current);
    }

    #[Test]
    public function theIconHintSaysWhichHalfOfTypo3ItIsAbout(): void
    {
        // Every API the hint names is a backend one, and a reader who is writing
        // a page template does not infer the boundary from that list — they read
        // it as how an icon is rendered. The lookup says so on every answer; the
        // hint describing the same registry has to say it too.
        $hint = ArchitectureHints::byId('icon-usage');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));

        self::assertStringContainsString('backend', mb_strtolower($text));
        self::assertStringContainsString('frontend template', $text);
        self::assertStringContainsString('SVG', $text);
    }

    #[Test]
    public function aBackendModuleQueryIsStillAnsweredAboutBackendModules(): void
    {
        // The frontend hint is reached by naming the frontend, not by naming a
        // template: "backend module template" is a PHP question.
        $result = ArchitectureHints::find([], 'backend module template', 6);

        self::assertContains('backend-modules', array_column($result['matchedHints'], 'id'));
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
                $hint['title'] . "\n" . implode("\n", array_column($hint['hints'], 'text'))
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
    public function aWordThatOnlyNamesTheSubjectMatchesConditionally(): void
    {
        // "field label" in a FormEngine task is not an XLF change, but the word
        // alone looks exactly like one.
        $intents = TaskIntents::detect(
            'Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields'
        );

        self::assertSame(['labels'], array_column($intents, 'id'));
        self::assertSame('weak', $intents[0]['confidence']);
        self::assertSame([], TaskIntents::confirmed($intents));
        self::assertSame([], TaskIntents::rules(TaskIntents::confirmed($intents)));
    }

    #[Test]
    public function anXlfSignalMatchesTheLabelIntentOutright(): void
    {
        $intents = TaskIntents::confirmed(TaskIntents::detect('Add one label to locallang_layout.xlf'));

        self::assertSame(['labels'], array_column($intents, 'id'));
        self::assertNotSame([], TaskIntents::rules($intents));
    }

    #[Test]
    public function theChecksOfAMatchedHintAreStatedAsChecks(): void
    {
        // The FormEngine hint names the functional suite; before it was merged
        // in, the brief listed the XLIFF checks of a weakly matched intent and
        // dropped the one suite the change could actually fail on.
        $checks = Tools::call('typo3_task_guide', [
            'task' => 'Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields',
            'area' => 'backend/FormEngine',
            'changeType' => 'bugfix',
        ])->data['checks'];

        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s functional', $checks);
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
    public function upgradingAnInstallationIsAnsweredAsAnOrderOfOperations(): void
    {
        // The question a site maintainer asks first — "what do I do, in which
        // order" — used to be answered with how to author a deprecation, which
        // is the same subject seen from the core's side and useless here.
        $result = Tools::call('typo3_task_guide', ['task' => 'upgrade this composer site project to TYPO3 v14']);

        self::assertContains('installation-upgrade', array_column($result->data['intents'], 'id'));

        $group = array_values(array_filter(
            $result->data['architectureHints'],
            static fn(array $hint): bool => $hint['id'] === 'installation-upgrade',
        ));
        self::assertCount(1, $group, 'the order of operations is part of the answer');

        // The steps are only worth anything in this order: the schema before
        // the wizards that declare it as their prerequisite, the caches last.
        $statements = implode("\n", array_column($group[0]['hints'], 'text'));
        $order = array_map(
            static fn(string $command): int => (int) strpos($statements, $command),
            ['extension:setup', 'upgrade:run', 'cache:flush'],
        );
        self::assertNotContains(0, $order, 'every step of the sequence is named');
        self::assertLessThan($order[1], $order[0], 'the schema is applied before the wizards run');
        self::assertLessThan($order[2], $order[1], 'the caches are flushed after the wizards');
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
