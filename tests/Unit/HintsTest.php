<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Knowledge\Domains;
use Typo3CmsMcp\Knowledge\Hints;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\TaskIntents;
use Typo3CmsMcp\Knowledge\TestSuiteHints;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Tool\TaskGuide;

final class HintsTest extends TestCase
{
    /**
     * Every statement of the named hints, as one text.
     *
     * A subject is several hints since `D-KNW-030`, so a test asking whether the
     * corpus states something names the hints it was split into. What a caller
     * gets is still one of them; that is what the reachability cases assert.
     */
    private static function statementsOf(string ...$ids): string
    {
        $texts = [];
        foreach ($ids as $id) {
            $hint = Hints::byId($id);
            self::assertNotNull($hint, $id . ' is not a hint');
            $texts[] = implode("\n", array_column($hint['hints'], 'text'));
        }

        return implode("\n", $texts);
    }

    #[Test]
    public function aPhpPathIsNeverAnsweredWithFrontendConventions(): void
    {
        $result = Hints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], '', 6);

        self::assertContains(Domains::PHP, $result['domains']);
        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertContains($hint['category'], ['PHP', 'General']);
        }
    }

    #[Test]
    public function aDistributedExtensionIsNotAnsweredWithTheProjectRepositoryLayout(): void
    {
        // The two hints describe different repositories, and the one that was
        // written first describes the one with an installation in it. A review
        // of a package quoted it and moved the browser suite out of the only
        // repository there is.
        $result = Hints::find(
            ['composer.json', 'Tests/', 'Build/'],
            'reusable extension published for several TYPO3 versions: lock file, supported range, browser tests',
            6,
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('extension-repository-layout', $ids);
        self::assertLessThan(
            array_search('project-repository-layout', $ids, true) === false
                ? PHP_INT_MAX
                : (int) array_search('project-repository-layout', $ids, true),
            (int) array_search('extension-repository-layout', $ids, true),
            'the repository that is only the extension answers before the one that holds an installation',
        );
    }

    /**
     * Setting the analysis up is the extension author's question, and the core
     * is no answer to it: its configuration sits in a mono repository, half of
     * what it declares is its own rule set, and the paths are relative to a
     * tree an extension does not have. So the hint has to be reachable from the
     * words somebody setting it up would use, and a core testing task must not
     * reach it — that caller has runTests.sh and a configuration already there.
     */
    #[Test]
    public function settingUpTheAnalysisReachesTheExtensionHintAndACoreTaskDoesNot(): void
    {
        $setup = Hints::find([], 'how do I set up phpstan for my extension', 6);

        self::assertContains('extension-static-analysis', array_column($setup['matchedHints'], 'id'));

        $core = Hints::find(
            ['typo3/sysext/core/Classes/'],
            'write a functional test for a core patch',
            6,
        );

        self::assertNotContains('extension-static-analysis', array_column($core['matchedHints'], 'id'));
    }

    #[Test]
    public function aSassPathReachesTheCssHints(): void
    {
        $result = Hints::find(['Build/Sources/Sass/component/_badge.scss'], '', 6);

        self::assertContains(Domains::CSS, $result['domains']);
        self::assertNotSame([], $result['matchedHints']);
    }

    #[Test]
    public function aSassPathIsNeverAnsweredWithTypeScriptConventions(): void
    {
        $result = Hints::find(['Build/Sources/Sass/component/_card.scss'], 'card component styling', 8);

        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotContains($hint['category'], [Hints::CATEGORY_TYPESCRIPT, 'JavaScript'], $hint['id']);
        }
    }

    #[Test]
    public function aTypeScriptPathIsNeverAnsweredWithCssConventions(): void
    {
        $result = Hints::find(
            ['Build/Sources/TypeScript/backend/form-editor/inspector-component.ts'],
            'field label override per record type',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function aFluidPathReachesTheFluidHintsAndNoOthers(): void
    {
        $result = Hints::find(
            [
                'typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html',
                'typo3/sysext/core/Classes/ViewHelpers/IconViewHelper.php',
            ],
            'Fluid template ViewHelper conventions escaping namespace',
            6
        );

        $categories = array_column($result['matchedHints'], 'category');
        self::assertContains('Fluid', $categories);
        self::assertNotContains(Hints::CATEGORY_TYPESCRIPT, $categories);
        self::assertNotContains(Hints::CATEGORY_CSS, $categories);
    }

    #[Test]
    public function aQueryWrittenInFluidTagSyntaxReachesTheFluidHints(): void
    {
        // Somebody reporting what a template did writes the tag and not the
        // word: the query below is the one a session arrived with, and it named
        // no path, no file extension and never "Fluid". The domain fell back to
        // PHP and the whole category was gone before anything was scored, so
        // the statement about the branch could not be found by the phrasing it
        // was written for — `D-KNW-024`.
        $reached = Hints::find(
            [],
            'f:if with f:else but no explicit f:then swallows the inline then-branch / f:link.typolink output',
            6,
        );

        self::assertContains('fluid-conditions-and-arrays', array_column($reached['matchedHints'], 'id'));

        // The prefix is a token rather than a word, so it cannot land inside
        // one. A question about PHP stays PHP.
        $php = Hints::find([], 'inject a service into my DataHandler hook class', 6);
        self::assertNotContains('Fluid', array_column($php['matchedHints'], 'category'));
    }

    #[Test]
    public function aFluidResourceUriTaskIsAnsweredWithWhoAppliesCacheBusting(): void
    {
        // The query is the one a bugfix session arrived with on 15.0.0-dev. It
        // got the ViewHelper class shape back — correct, and silent about the
        // API the area had been rebuilt on, which is what the task was about.
        $query = 'Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource';
        $result = Hints::find([], $query, 6);
        self::assertSame('fluid-resource-uris', $result['matchedHints'][0]['id']);

        $onFifteen = self::statementsOf('fluid-resource-uris');
        self::assertStringContainsString('f:image and f:uri.image are not on it', $onFifteen);
        self::assertStringContainsString('belongs to the publisher rather than to the ViewHelper', $onFifteen);
        self::assertStringContainsString('useCacheBusting', $onFifteen);

        // Read on both sides in .checkouts/: the SystemResource namespace, the
        // f:resource ViewHelper and File implementing PublicResourceInterface
        // are on 14.3 and on main alike, and on 13.4 there is none of it. The
        // report read the API as a 15 change because it came from 13.
        $guide = Registry::call('typo3_task_guide', ['task' => $query, 'targetVersion' => '13.4']);
        self::assertStringNotContainsString('System Resource API', $guide->text);
        self::assertStringContainsString('computes the URL itself through PathUtility', $guide->text);
    }

    #[Test]
    public function aTypoScriptPathReachesTheTypoScriptHintsAndNotTheCssOnes(): void
    {
        // .typoscript and .tsconfig used to fall into the generic frontend
        // bucket, which answered a site set with the CSS browser baseline.
        $result = Hints::find(
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
        self::assertNotContains(Hints::CATEGORY_CSS, $categories);
    }

    #[Test]
    public function aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions(): void
    {
        $result = Hints::find(
            ['Resources/Public/Scss/bootstrap.scss', 'Build/Sources/Sass/_variables.scss'],
            'Sass architecture, variables and build pipeline for a Bootstrap 5 based frontend theme',
            8
        );

        self::assertContains(Hints::CATEGORY_CSS, $result['withheldCategories']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function stylingABackendModuleStillReachesTheBackendCssHints(): void
    {
        $result = Hints::find(
            ['Resources/Public/Css/backend-icon-search.css'],
            'styling for the backend module of a site package',
            8
        );

        self::assertSame([], $result['withheldCategories']);
        self::assertContains(Hints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    #[Test]
    public function aPhpClassNameThatCarriesTheWordScssIsStillPhp(): void
    {
        $result = Hints::find(
            ['Classes/ViewHelpers/Format/ScssViewHelper.php', 'Configuration/Services.yaml'],
            '',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function aQueryAboutLanguageFilesReachesTheLanguageFilesHint(): void
    {
        $result = Hints::find([], 'Language files, XLF labels and how to reference them in TCA', 6);

        self::assertContains('language-files', array_column($result['matchedHints'], 'id'));
    }

    /**
     * The two sinks arrive as different words — one caller asks about output
     * escaping, the other about a query — and what they need is the same
     * reading. A hint reachable only through the phrasing it was written for
     * would leave the second caller with the conventions of its surface and no
     * method.
     */
    #[Test]
    #[DataProvider('securityQueries')]
    public function bothSidesOfAnInjectionQuestionReachTheSinkMethod(string $task): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('claim about its sink', $result->text);
        self::assertStringContainsString('does this hop emit the value, or hand it to something else', $result->text);
        self::assertStringContainsString('createNamedParameter()', $result->text);
        self::assertStringContainsString('returns nothing at all', $result->text);
    }

    /** @return array<string, array{0: string}> */
    public static function securityQueries(): array
    {
        return [
            'escaping' => ['review an extension for unescaped user input and raw output in templates'],
            'sql' => ['review an extension for sql injection in its repository query building'],
        ];
    }

    /**
     * The steady state alone left a caller holding a German source file with
     * two remedies that both read as consistent with it, and it picked the one
     * that changes nothing — D-KNW-011. So the correction is named too, and it
     * is an authoring procedure rather than a v14 mechanism: the unit shape is
     * the same on 12.4, 13.4 and 14.3, and so is the fallback that makes an
     * en.-prefixed file useless, so no answer this server gives may omit it.
     */
    #[Test]
    #[TestWith(['12'])]
    #[TestWith(['13'])]
    #[TestWith(['14'])]
    public function aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes(string $targetVersion): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'backend module registration controller and language files in a project sitepackage extension',
            'targetVersion' => $targetVersion,
        ]);

        self::assertStringContainsString('new labels in English in the source XLF', $result->text);
        self::assertStringContainsString('de.locallang.xlf', $result->text);
        self::assertStringContainsString('a defect to report', $result->text);

        self::assertStringContainsString('keeps its path and it keeps its unit ids', $result->text);
        self::assertStringContainsString('source-language="en" target-language="de"', $result->text);
        self::assertStringContainsString('as <source> and the wording it replaced as <target>', $result->text);
        self::assertStringContainsString('en.-prefixed file is never the correction', $result->text);
        self::assertStringContainsString('default is the fallback of every other locale', $result->text);
    }

    #[Test]
    public function labelReuseStaysAtTheUsageContext(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'language-files',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('translation resource it already uses', $result->text);
        self::assertStringContainsString('matching label elsewhere in the installation is not', $result->text);
        self::assertStringContainsString('actions.createRecord', $result->text);
        self::assertStringContainsString('context-free id such as new', $result->text);
    }

    #[Test]
    public function aGermanSiteTaskReachesItsLabelLanguageSetup(): void
    {
        $query = 'Set up a German-language site whose core and form labels still render in English';
        $result = Hints::find([], $query, 6);

        self::assertContains('site-label-language', array_column($result['matchedHints'], 'id'));

        $guide = Registry::call('typo3_task_guide', ['task' => $query]);
        self::assertContains('site-label-language', array_column($guide->data['hints'], 'id'));
        self::assertStringContainsString('typo3Language: de', $guide->text);
        self::assertStringContainsString('language:update de', $guide->text);
        self::assertStringContainsString('renderingOptions.submitButtonLabel', $guide->text);
    }

    #[Test]
    public function aSettingIsPlacedByTheReachOfItsValue(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'where a backend module stores a configurable storage pid in a sitepackage',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('Configuration Belongs to Its Reach', $result->text);
        self::assertStringContainsString('Configuration/Sets/<Set>/settings.definitions.yaml', $result->text);
        self::assertStringContainsString('Extension configuration is installation-wide', $result->text);
        self::assertStringContainsString("scheduler task's own parameters", $result->text);
    }

    #[Test]
    public function aBackendModuleNamesItsShortcutApiAndPostRedirect(): void
    {
        $query = 'doc header buttons and the redirect after a POST in a backend module';
        $onThirteen = Registry::call('typo3_hint_lookup', [
            'task' => $query,
            'targetVersion' => '13',
        ])->text;
        self::assertStringContainsString('makeShortcutButton()', $onThirteen);
        self::assertStringNotContainsString('setShortcutContext(', $onThirteen);
        self::assertStringContainsString('RedirectResponse with HTTP 303 status', $onThirteen);

        $onFourteen = Registry::call('typo3_hint_lookup', [
            'task' => $query,
            'targetVersion' => '14',
        ])->text;
        self::assertStringContainsString('setShortcutContext(', $onFourteen);
        self::assertStringNotContainsString('makeShortcutButton()', $onFourteen);
        self::assertStringContainsString('RedirectResponse with HTTP 303 status', $onFourteen);
    }

    #[Test]
    public function siteScopedConfigurationIsOfferedOnlyWhereSiteSettingsExist(): void
    {
        $onTwelve = implode("\n", array_column(
            Hints::byId('configuration-reach', 12)['hints'],
            'text',
        ));
        self::assertStringNotContainsString('settings.definitions.yaml', $onTwelve);
        self::assertStringContainsString('installation-wide', $onTwelve);

        $onThirteen = implode("\n", array_column(
            Hints::byId('configuration-reach', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('settings.definitions.yaml', $onThirteen);
    }

    #[Test]
    public function languagePackActivationUsesTheConfigurationOfTheTargetBranch(): void
    {
        $onThirteen = implode("\n", array_column(
            Hints::byId('site-label-language', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('EXTCONF', $onThirteen);
        self::assertStringNotContainsString('LANG/availableLocales', $onThirteen);

        $onFourteen = implode("\n", array_column(
            Hints::byId('site-label-language', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('LANG/availableLocales', $onFourteen);
        self::assertStringNotContainsString('EXTCONF', $onFourteen);
    }

    /**
     * The sweep the three constants in `D-ANS-002` were picked off, as far as it
     * was written down.
     *
     * It was eighteen queries with a known right answer, run against every
     * candidate value until one held all of them; only what the commit and the
     * decision quote survives, which is fourteen. Four are lost, and that they
     * are is the reason the rest is here rather than in a session's scrollback:
     * a constant measured against a set nobody kept can only be re-measured
     * against a set somebody reconstructs.
     *
     * Twelve of the fourteen are in this provider. The other two are recorded
     * misses and are asserted where their reason is written down —
     * «my button looks wrong» in whatACallerCanSeeReachesTheHintAboutIt, and
     * «wie lege ich ein neues Content-Element an» nowhere, because `R-AUD-006`
     * settled that this server is queried in English.
     *
     * A null answer is the corpus having none, and it carries the same weight
     * as a hit: answering everything is worth what answering nothing is, and
     * the negative controls are what the dilution weight exists for.
     *
     * @return array<string, array{0: string, 1: ?string}>
     */
    public static function theSweep(): array
    {
        return [
            // The two that named the case: the hint says "the failure is a
            // service-not-found at request time" and names
            // PageTitleProviderManager, and neither phrasing reached it.
            'a service the container did not build' => [
                'my extension service is not found at runtime',
                'di-service-not-found',
            ],
            'the symptom that service produces' => [
                'page title provider does not work',
                'di-service-not-found',
            ],
            'a word nobody indexed' => ['file upload storage configuration', 'fal-writing'],
            'a backend form' => ['validate a form field in the backend', 'tca-formengine'],
            'where something goes' => ['where do I put my backend layouts', 'sitepackage-backend-layouts'],
            'a stale answer' => ['caching does not invalidate', 'caching'],
            'a menu that is wrong' => ['menu does not show all pages', 'page-variables-and-processors'],
            'a label in the wrong language' => [
                'the frontend shows the wrong language label',
                'language-files',
            ],
            'what the caller can see' => ['dark mode colors in my backend module', 'css-light-dark-mode'],
            'the curated vocabulary' => ['event listener', 'events-extension-points'],
            'a question about something else' => ['how do I write a good sonnet', null],
            'a question about somewhere else' => ['what is the weather in Düsseldorf', null],
        ];
    }

    /**
     * The symptom a caller arrives with is not the vocabulary a hint was
     * indexed under, and for a long time only the vocabulary was searched: a
     * hint's own two hundred words were invisible to the matcher, so the
     * subject was reachable through the handful of keywords whoever wrote it
     * happened to think of. Most of these reached nothing at all before the
     * hint text was scored, and each is answered by a hint that says the thing
     * in so many words.
     */
    #[Test]
    #[DataProvider('theSweep')]
    public function theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay(
        string $query,
        ?string $expected,
    ): void {
        $result = Hints::find([], $query, 6);
        $ids = array_column($result['matchedHints'], 'id');

        if ($expected === null) {
            self::assertSame([], $ids, $query);
            self::assertNotSame([], $result['availableHints'], 'a miss says what there would have been to find');

            return;
        }

        self::assertContains($expected, $ids, $query);
    }

    /**
     * What the sweep cannot say on its own: the corpus may outgrow the length
     * its matcher was measured against, and no single query fails when it does.
     *
     * Re-measured on 2026-08-01 at a mean of 266 words, up from 212 when the
     * reference was picked: recall over the sweep is whole from 120 words to
     * 320, and above 320 «how do I write a good sonnet» is answered by
     * `installation-upgrade`, which by then is long enough to contain it. So
     * the reference stays where it is — the returned hints climb the whole way
     * up that range, and the low end is the precise one — and what is watched
     * is the corpus walking towards the far end of it.
     */
    #[Test]
    public function theCorpusHasNotOutgrownTheLengthItsMatcherWasMeasuredAgainst(): void
    {
        $lengths = array_values(Hints::bodyWords());
        $mean = (int) round(array_sum($lengths) / count($lengths));

        self::assertLessThanOrEqual(
            Hints::MAX_MEAN_BODY_WORDS,
            $mean,
            'the mean hint body is ' . $mean . ' words: re-run the sweep and pick UNDILUTED_WORDS again, '
            . 'rather than raising the ceiling',
        );
    }

    #[Test]
    public function everyHintIsReachedByItsOwnTitle(): void
    {
        // The weakest thing that can be asked of a lookup, and eight of the
        // nineteen Backend CSS hints failed it: "Color and Surface Tokens"
        // returned nothing, because none of those words is a CSS signal, the
        // domain fell back to PHP, and the hint's own category was then never a
        // candidate. Scoring had nothing to do with it — the hint never reached
        // the matcher. This holds the gate rather than the ranking, so it is
        // about candidacy: the hint has to be somewhere in the answer.
        foreach (Hints::load() as $hint) {
            $reached = array_column(Hints::find([], $hint['title'], 6)['matchedHints'], 'id');

            self::assertContains($hint['id'], $reached, $hint['title'] . ' does not reach ' . $hint['id']);
        }
    }

    #[Test]
    public function whatACallerCanSeeReachesTheHintAboutIt(): void
    {
        // A caller names the symptom in the words of what is in front of them —
        // a colour, a dark mode, a shadow — not in the vocabulary of the
        // subsystem that produces it. The dark-mode half is a sweep query and
        // is asserted there; this is the other half, which the sweep records as
        // a miss and which is not one to fix here. "my button looks wrong" is
        // the thirteenth of the eighteen, it carries no CSS signal at all, so
        // the domain falls back to PHP and no CSS hint is ever a candidate —
        // and a component by name is typo3_component_lookup's question anyway,
        // answered with the markup rather than with a convention.
        $seen = Hints::find([], 'dark mode colors in my backend module', 6);
        self::assertContains('css-light-dark-mode', array_column($seen['matchedHints'], 'id'));

        $named = Hints::find([], 'my button looks wrong', 6);
        self::assertNotContains(
            Hints::CATEGORY_CSS,
            array_column($named['matchedHints'], 'category'),
            'a component by its name is the component lookup\'s to answer',
        );
    }

    #[Test]
    public function theTestApiAProjectWritesItsTestsWithReachesTheProjectHint(): void
    {
        // The API is the same one the core tests are written with, so the two
        // testing hints both answer here — but a test in a package of this
        // repository runs in a harness the core hint knows nothing about, and
        // that hint is the one that has to come first.
        $result = Hints::find(
            ['packages/my_sitepackage/Tests/Functional/HeroCarouselTest.php'],
            'FunctionalTestCase executeFrontendSubRequest CSV fixture for my content element',
            6,
        );

        $ids = array_column($result['matchedHints'], 'id');
        self::assertContains('project-extension-tests', $ids);
        self::assertLessThan(
            array_search('core-tests', $ids, true) ?: PHP_INT_MAX,
            array_search('project-extension-tests', $ids, true),
        );
    }

    #[Test]
    public function aShortTermIsNotMatchedAsThePrefixOfALongerWord(): void
    {
        // Prefix matching is how a stem finds every form of its word. At three
        // characters there is no form left to find, and what it finds instead
        // is whatever starts with those letters — "fal" reaching seven hints
        // through "fallback" and "false". It is worse than ordinary noise
        // because a term is weighed by how few documents carry it: an accident
        // landing in exactly one document becomes the most discriminating term
        // in the query and decides the answer.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );

        self::assertSame(['fal-storages-drivers', 'fal-basics'], $reached('fal storage driver'));

        // The same rule on the curated vocabulary, which is the stronger path:
        // an appliesTo hit is admitted whatever the coverage. "fal" is one of
        // fal-basics's patterns and it used to be found by plain substring.
        self::assertSame([], array_filter(
            $reached('the label is falsch'),
            static fn(string $id): bool => str_starts_with($id, 'fal-'),
        ));

        // And the tolerance itself is intact from four characters up.
        self::assertContains('events-extension-points', $reached('hooks'));
    }

    #[Test]
    public function aCompoundIsFoundWhicheverWayTheCallerJoinsIt(): void
    {
        // Everything a query passes through is written in spaced compounds —
        // the domain keywords, the hint patterns, the markers — and one hyphen
        // used to miss all of them at once. The first query is the sentence a
        // reporting session wrote its own task down in, and it reached nothing:
        // the domain fell back to PHP, and the `content element` pattern was
        // searched for verbatim in a query that spells it with a hyphen.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );

        self::assertContains(
            'content-elements',
            $reached('show assigned related groups in a backend content-element preview template'),
        );

        // The domain gate is the half that fails first and silently, so it is
        // asserted where the hint's own category has to be earned: "dark mode"
        // is a CSS signal and "dark-mode" was none, which left every Backend
        // CSS hint out of the candidates rather than out of the ranking.
        self::assertContains('css-light-dark-mode', $reached('dark-mode'));
        self::assertContains('fluid-viewhelpers', $reached('view-helper'));

        // And the separator inside one word is left alone, which is what
        // D-ANS-006 is about: these are identifiers, not compounds a caller
        // joined up, and each is still one token.
        foreach (['tt_content', 'list_type'] as $identifier) {
            self::assertContains('content-elements', $reached($identifier), $identifier);
        }
        self::assertContains('content-element-preview', $reached('mod.web_layout'));
    }

    /**
     * The gate that closed on length rather than on relevance.
     *
     * `MIN_COVERAGE` is a share of the query, and the dilution weight damped it
     * by the length of the body the term was found in — so past `200 * e` words
     * a hint stopped being a candidate for any one-term query it was not
     * curated for. Not ranked lower: dropped, with nothing to say so.
     *
     * Each of these four is stated by exactly one hint in the corpus, appears
     * nowhere in that hint's title or `appliesTo`, and reached nothing at all,
     * while the hint's own body is what a caller naming it is after. Two hints
     * are the ones the symptom was reported on and two are the far end of the
     * corpus at 981 and 1147 words.
     */
    #[Test]
    #[TestWith(['showitem', 'content-elements'])]
    #[TestWith(['allowProperties', 'extbase-arguments'])]
    #[TestWith(['sys_registry', 'initial-content-import-once'])]
    #[TestWith(['PidInList', 'frontend-records'])]
    #[TestWith(['withQueryParameters', 'extension-test-frontend-request'])]
    public function aTermOnlyOneHintStatesReachesItHoweverLongThatHintIs(
        string $query,
        string $expected,
    ): void {
        self::assertContains(
            $expected,
            array_column(Hints::find([], $query, 6)['matchedHints'], 'id'),
            $query,
        );
    }

    /**
     * And the half the dilution weight is still for. A query the corpus has no
     * answer to is covered in part by a long enough text — «write» and «good»
     * are in half the hints there are — and what keeps that from being an
     * answer is the share the floor asks for. Nothing carries "sonnet", so the
     * cover is never whole and the exception above never applies.
     *
     * The negative controls of the sweep assert the outcome; this asserts the
     * reason, which is the part a change to the floor would take away without
     * moving them.
     */
    #[Test]
    public function aHintThatCarriesPartOfAQueryStillDoesNotAnswerIt(): void
    {
        // Both terms belong to this one, and it is long enough to be damped —
        // which is what makes it the case worth asserting. It was named as
        // "the longest hint there is" until the corpus was split by subject and
        // the longest became one that carries neither term.
        $diluted = 'extension-test-site';
        self::assertGreaterThan(
            Hints::UNDILUTED_WORDS,
            Hints::bodyWords()[$diluted],
            'a hint the matcher does not damp would prove nothing here',
        );

        $whole = array_column(
            Hints::find([], 'diffed truncation', 6)['matchedHints'],
            'id',
        );
        self::assertContains($diluted, $whole, 'both terms are its own');

        $part = array_column(
            Hints::find([], 'diffed sonnet', 6)['matchedHints'],
            'id',
        );
        self::assertNotContains($diluted, $part, 'half a query is a mention, whatever the other half was');
    }

    #[Test]
    public function theCuratedVocabularyStillDecidesWhereItWasWritten(): void
    {
        // Scoring the text is additive: where somebody anticipated a phrasing,
        // that hint is still what comes back first. Otherwise every hint that
        // mentions a subject in passing would compete with the one about it.
        $result = Hints::find([], 'event listener', 6);

        self::assertSame('events-extension-points', $result['matchedHints'][0]['id']);
    }

    #[Test]
    public function aHintCanBeAskedForByItsIdInsteadOfGuessedAt(): void
    {
        $result = Hints::find([], '', 6, 'language-files');

        self::assertSame(['language-files'], array_column($result['matchedHints'], 'id'));
        self::assertSame([], $result['domains'], 'nothing was inferred, so nothing is claimed');
        self::assertSame([], $result['availableHints']);
    }

    #[Test]
    public function anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo(): void
    {
        $result = Hints::find([], '', 6, 'language-file');

        self::assertSame([], $result['matchedHints']);
        self::assertContains('language-files', array_column($result['availableHints'], 'id'));
    }

    #[Test]
    public function aMissNamesWhatThereWouldHaveBeenToFind(): void
    {
        $hit = Hints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], 'DataHandler', 6);
        self::assertSame([], $hit['availableHints'], 'an index would only bury the hints it lists');

        $miss = Hints::find([], 'how do I write a good sonnet', 6);
        self::assertSame([], $miss['matchedHints']);
        self::assertContains('dependency-injection', array_column($miss['availableHints'], 'id'));
        foreach ($miss['availableHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
        }
    }

    /**
     * Whether a removal is breaking is a question the corpus answers, and it
     * answers that the annotation does not settle it.
     *
     * A patch review reported reading the class and its history by hand to
     * establish that `GifBuilder` is public API while the removed member is
     * `@internal`, and asked for a lookup that reports the marker. The core has
     * filed removals of `@internal` members both ways — `Breaking-110319`
     * because non-Composer bootstraps called them, `Important-108796` because
     * nothing did — so a tool answering the marker would answer beside the
     * question. What was missing is the rule.
     */
    #[Test]
    public function whetherARemovalIsBreakingIsAnsweredAndTheMarkerDoesNotSettleIt(): void
    {
        $reached = array_column(
            Hints::find([], 'is removing this internal method breaking', 6)['matchedHints'],
            'id',
        );
        self::assertContains('deprecated-apis', $reached);

        $written = json_encode(Hints::byId('deprecated-apis'), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('@internal', $written);
        self::assertStringContainsString('never the answer on its own', $written);
        self::assertStringContainsString('whether anything outside the core calls it', $written);

        // And the brief for the change type says it before it says how to write
        // the entry, because the entry is what a wrong answer produces.
        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Remove an internal method from a public core class',
            'changeType' => 'breaking',
        ])->text;
        self::assertStringContainsString('Settle first that the change is breaking at all', $brief);
    }

    #[Test]
    public function aPathAloneReachesTheHintForTheSubsystemItIsIn(): void
    {
        // Both were subsystems with no hint at all, and an extension
        // maintenance task got generic TCA and Fluid advice for them.
        $reached = static fn(string $path): array => array_column(
            Hints::find([$path], '', 6)['matchedHints'],
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
        $result = Hints::find([], 'Fluid templates frontend', 6);

        self::assertContains('frontend-page-rendering', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function anExtbasePluginHasAHintOfItsOwn(): void
    {
        // There was none at all: the task returned datahandler-persistence,
        // which is about DataHandler, and asking by id returned the index.
        $result = Hints::find(
            [],
            'Extbase plugin in a project extension: domain model, repository, controller, plugin registration, '
            . 'persistence mapping to a custom table, pagination and search',
            6
        );
        self::assertContains('extbase', array_column($result['matchedHints'], 'id'));

        // The failures are the half that cost the session, and each of them
        // answers with a wrong page rather than with an error.
        $text = implode("\n", array_column((array) Hints::byId('extbase-arguments')['hints'], 'text'));
        self::assertStringContainsString('cacheHash', $text);
        self::assertStringContainsString('allowProperties', $text);
    }

    /**
     * The extension a file sits in is not what the file is, and a bare extension
     * key in `appliesTo` cannot tell the two apart — `D-KNW-038`. The paths and
     * the task are the ones a core bugfix arrived with: `ImageService` resolves
     * files and builds URLs, and the plugin briefing was the largest block of
     * the answer it got.
     *
     * `hints:coverage` cannot see this. It counts what nothing reaches, and a
     * hint that answers everything is reached by all of it.
     */
    #[Test]
    public function aFileBelowAnExtensionIsAnsweredByItsRoleRatherThanByTheExtension(): void
    {
        $result = Hints::find(
            [
                'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
                'typo3/sysext/extbase/Classes/Service/ImageService.php',
            ],
            'Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource',
            6,
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('fluid-viewhelpers', $ids, 'what the task is about');
        self::assertSame(
            [],
            array_values(array_filter($ids, static fn(string $id): bool => str_starts_with($id, 'extbase'))),
            'nothing in the Extbase family bears on a file-resolution helper',
        );

        // And the same word asked as a question still reaches the family.
        self::assertContains(
            'extbase',
            array_column(Hints::find([], 'do I need extbase for a list of records', 6)['matchedHints'], 'id'),
        );
    }

    #[Test]
    public function registeringSomethingSoTheCoreFindsItIsCovered(): void
    {
        // "How do I register X so the core actually finds it" fell between the
        // component catalog and the subsystem conventions, and was answered by
        // reading the core sources by hand.
        $element = Hints::find([], 'register a new content element with its own CType', 6);
        self::assertContains('content-elements', array_column($element['matchedHints'], 'id'));

        $di = Hints::byId('di-service-not-found');
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
        $result = Hints::find(
            [],
            'Add a product list and product detail rendering plus a product teaser element to a sitepackage '
            . 'extension: custom database table, TCA, frontend content elements, routing for the detail view',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('frontend-records', $ids);
        self::assertContains('sitepackage-layout', $ids);
        self::assertContains('record-routing', $ids, 'the detail view has to be addressable');
        self::assertNotContains(Hints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    #[Test]
    public function siteLocalSettingsSourcesAreAnsweredWithTheirPrecedence(): void
    {
        $result = Hints::find(
            [
                'config/sites/main/config.yaml',
                'config/sites/main/settings.yaml',
                'Configuration/Sets/Printworks/settings.definitions.yaml',
            ],
            'Site settings: settings.yaml of a site versus the inline settings key in config.yaml, and settings shipped by a site set',
            6
        );
        self::assertContains('site-sets', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('site-sets', 'site-set-settings');
        self::assertStringContainsString('alternatives, not layers', $text);
        self::assertStringContainsString('does not merge', $text);
        self::assertStringContainsString('backend settings editor', $text);
    }

    #[Test]
    public function projectSystemConfigurationStatesItsOwnershipBoundary(): void
    {
        $result = Hints::find(
            ['config/system/additional.php', 'config/system/.gitignore'],
            'Who owns additional.php in a TYPO3 project that uses DDEV?',
            6,
        );
        self::assertSame('project-configuration-files', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('project-repository-layout', 'project-configuration-files');
        self::assertStringContainsString('settings.php is the configuration array written by TYPO3', $text);
        self::assertStringContainsString('additional.php is optional PHP loaded afterwards', $text);
        self::assertStringContainsString('Remove that marker to take the file over', $text);
        self::assertStringContainsString('config/system/.gitignore', $text);
        self::assertStringContainsString('verify additional.php is still tracked', $text);
        self::assertStringContainsString('local-development environment, not the production configuration source', $text);
        self::assertStringContainsString('IS_DDEV_PROJECT', $text);
        self::assertStringContainsString('never commit production secrets', $text);
    }

    #[Test]
    public function whichEnvironmentVariablesTheCoreReadsItselfIsAnswered(): void
    {
        // The other half of the sentence above. "Read deployment values and
        // secrets from environment variables" tells a project to wire something
        // up without saying what the core already does, and the corpus said
        // only that half: a session asking whether TYPO3_ENCRYPTION_KEY is read
        // automatically got nothing back and answered itself from memory. It
        // happened to be right. The three the core does read are the ones a
        // wrong answer is expensive about, because a project that assumes more
        // of them ships a deployment whose secrets are never applied.
        $result = Hints::find(
            [],
            'Does TYPO3 read TYPO3_ENCRYPTION_KEY or TYPO3_DB_HOST from the environment by itself?',
            6,
        );
        self::assertSame('environment-variables', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('environment-variables', 'environment-placeholders', 'environment-runtime-readers');
        self::assertStringContainsString('SystemEnvironmentBuilder is the only thing that reads them', $text);
        self::assertStringContainsString('TYPO3_CONTEXT', $text);
        self::assertStringContainsString('TYPO3_PATH_ROOT', $text);
        self::assertStringContainsString('TYPO3_PATH_APP', $text);
        self::assertStringContainsString('REDIRECT_ prefix', $text);
        self::assertStringContainsString('HTTP_TYPO3_CONTEXT', $text);
        self::assertStringContainsString('resolved by the core\'s YamlFileLoader', $text);
        self::assertStringContainsString('does not reach config/system/settings.php or additional.php', $text);
        self::assertStringContainsString('the project\'s own getenv() in config/system/additional.php', $text);
        self::assertStringContainsString('ships no .env reader', $text);
        // The variable the reporting session found documented but unread: it
        // exists, the core does take it, and only while `typo3 setup` runs.
        self::assertStringContainsString('write settings.php once', $text);
    }

    #[Test]
    public function settingTheEncryptionKeyFromAnExtensionIsBoundToWhereItBreaks(): void
    {
        // Read on both sides in .checkouts/: up to 14.3 Bootstrap::init()
        // calls checkEncryptionKey() after ExtLocalconfFactory->load(), on main
        // before it. So the same ext_localconf.php assignment is merely bad
        // practice on one branch and a boot failure on the next, and a hint
        // that stated either one flat would be wrong for half the callers.
        $onFourteen = implode("\n", array_column(
            Hints::byId('environment-runtime-readers', 14)['hints'],
            'text',
        ));
        self::assertStringNotContainsString('ext_localconf.php', $onFourteen);

        $onFifteen = implode("\n", array_column(
            Hints::byId('environment-variables', 15)['hints'],
            'text',
        ));
        self::assertStringContainsString('ext_localconf.php', $onFifteen);
        self::assertStringContainsString('TYPO3 Encryption is empty', $onFifteen);
    }

    #[Test]
    public function routedArgumentsAreAnsweredWithTheirCacheHashBoundary(): void
    {
        $result = Hints::find(
            ['Configuration/Sets/Printworks/route-enhancers.yaml'],
            'Route enhancer aspects and the cache hash: when does a mapped route argument still need cHash in the URL',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');
        self::assertSame('record-routing', $ids[0]);

        $text = self::statementsOf('frontend-records', 'record-routing', 'record-page-title');
        self::assertStringContainsString('PersistedAliasMapper and StaticValueMapper', $text);
        self::assertStringContainsString('needs no cHash', $text);
        self::assertStringContainsString('dynamicArguments', $text);
    }

    #[Test]
    public function persistedAliasesStateBothDirectionsAndTheirValidationBoundary(): void
    {
        $query = 'What does PersistedAliasMapper map, which value belongs in the query argument, and why is there no cHash?';
        $result = Hints::find(
            ['Configuration/Sets/Printworks/route-enhancers.yaml'],
            $query,
            6,
        );
        self::assertSame('record-routing', $result['matchedHints'][0]['id']);

        $guide = Registry::call('typo3_task_guide', [
            'task' => $query,
            'targetVersion' => '14',
        ]);
        $text = $guide->text;
        self::assertStringContainsString('record uid as the query argument', $text);
        self::assertStringContainsString('routeFieldName in the path', $text);
        self::assertStringContainsString('uniqueInSite', $text);
        self::assertStringContainsString('rejects the enhanced route before rendering', $text);
        self::assertStringContainsString('needs no cHash', $text);
    }

    #[Test]
    public function theFormFrameworkIsCoveredAsAWholeSubsystem(): void
    {
        $result = Hints::find(
            [],
            'EXT:form form definition YAML, form setup in sitepackage, prefill form fields',
            6
        );
        self::assertSame('form-framework', $result['matchedHints'][0]['id']);

        $hint = Hints::byId('form-framework');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('Configuration/Form/<SetName>/config.yaml', $text);
        self::assertStringContainsString('.form.yaml', $text);
        self::assertStringContainsString('_originalIdentifier', $text);
        self::assertStringContainsString('AfterCurrentPageIsResolvedEvent', $text);
        self::assertStringContainsString('submitted value still wins', $text);
    }

    #[Test]
    public function survivingHooksAreNamedByTheirSubsystemAndIntent(): void
    {
        $form = Hints::byId('form-framework');
        self::assertNotNull($form);
        $formText = implode("\n", array_column($form['hints'], 'text'));
        self::assertStringContainsString('ext/form/afterFormStateInitialized', $formText);
        self::assertStringContainsString('ext/form/buildFormDefinitionValidationConfiguration', $formText);

        $events = Hints::byId('events-extension-points');
        self::assertNotNull($events);
        $eventText = implode("\n", array_column($events['hints'], 'text'));
        self::assertStringContainsString('subsystem hint with the intent', $eventText);
        self::assertStringContainsString('form-framework', $eventText);
    }

    #[Test]
    public function coreOnlyDocumentationAndBuildHintsHaveProjectTwins(): void
    {
        $documentation = Hints::byId('extension-documentation');
        self::assertNotNull($documentation);
        $documentationText = implode("\n", array_column($documentation['hints'], 'text'));
        self::assertStringContainsString('guides.xml', $documentationText);
        self::assertStringContainsString('semantic version', $documentationText);
        self::assertStringContainsString('documentation-changelog', $documentationText);

        $assets = Hints::byId('extension-asset-build');
        self::assertNotNull($assets);
        $assetText = implode("\n", array_column($assets['hints'], 'text'));
        self::assertStringContainsString('does not attach', $assetText);
        self::assertStringContainsString('public-assets', $assetText);
        self::assertStringContainsString('extension-declarative-files', $assetText);

        $docsQuery = Hints::find(
            [],
            'guides.xml and Documentation/Index.rst for my extension documentation and release notes',
            6
        );
        self::assertSame('extension-documentation', $docsQuery['matchedHints'][0]['id']);
        $assetQuery = Hints::find(
            [],
            'build scss and typescript frontend assets in a sitepackage extension',
            6
        );
        self::assertContains('extension-asset-build', array_column($assetQuery['matchedHints'], 'id'));
    }

    #[Test]
    public function theSeedingAdviceCarriesTheStepsItAsksFor(): void
    {
        // "Seed with DataHandler, then export" named the way in and stopped
        // where the work starts. Getting a DataHandler at all is a hand-written
        // boot, and each of its steps is a trap.
        //
        // The two halves are two hints since D-KNW-030: how records come into
        // being is DataHandler's subject, and shipping the export is the
        // package's. What this holds is that neither half lost its steps.
        $seeding = Hints::byId('datahandler-seeding');
        self::assertNotNull($seeding);
        $steps = implode("\n", array_column($seeding['hints'], 'text'));

        self::assertStringContainsString('Bootstrap::init', $steps);
        self::assertStringContainsString('initializeBackendUser', $steps);

        self::assertStringContainsString('--table', self::statementsOf('impexp-artifact'));
    }

    /**
     * The datamap statement said how a scalar field is written and stopped, so
     * a session seeding an element with inline children had nothing at the
     * first relation it reached: it wrote the child's pointer column by hand
     * and read the parent's int column as one that rejects a comma list. That
     * column is a counter DataHandler maintains, and the same holds on every
     * covered major, so the statement carries no range.
     */
    #[Test]
    public function aRelationInADatamapSaysWhatTheParentColumnEndsUpHolding(): void
    {
        $statements = static fn(?int $major): string => implode("\n", array_column(
            Hints::byId('datahandler-relations', $major)['hints'] ?? [],
            'text',
        ));
        $text = $statements(null);

        self::assertStringContainsString('comma-separated list of the related uids', $text);
        self::assertStringContainsString('foreign_field', $text, 'what moves the relation onto the child');
        self::assertStringContainsString('foreign_table_field', $text);
        self::assertStringContainsString('foreign_match_fields', $text);
        self::assertStringContainsString(
            'holds the number of children rather than the list',
            $text,
            'the counter that reads as a column rejecting a comma list',
        );

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString(
                'holds the number of children rather than the list',
                $statements($major),
                'the mechanism is the same on every covered major',
            );
        }

        $reached = Hints::find(
            [],
            'IRRE inline child records written through DataHandler datamap parent field',
            6,
        );
        self::assertSame('datahandler-relations', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    #[Test]
    public function shippedContentIsAnsweredPastThePointWhereTheFileExists(): void
    {
        // The mechanism was covered and the lifecycle was not: the file was
        // regenerated three times and never imported, because the installation
        // it came from had already run it and nothing said where else it could
        // be. What is remapped and what ships as a stale integer was missing
        // for the same reason — it is only visible on the way back in.
        $text = self::statementsOf('initial-content-import-once', 'initial-content-references', 'impexp-artifact');

        // The key is the operative half of the registry entry; the namespace
        // alone re-triggers nothing.
        self::assertStringContainsString('Initialisation/dataImported', $text);
        self::assertStringContainsString('importData()', $text, 'where the artifact can be verified at all');
        self::assertStringContainsString('ReferenceIndex::getRelations()', $text, 'what decides whether a uid survives');
        self::assertStringContainsString('--save-files-outside-export-file', $text);

        // The site configuration is remapped by a different mechanism than the
        // records are, and only one field of it is. A reader who carries the
        // relation rule over to config.yaml gets the opposite of the answer,
        // so what the import leaves alone is the half worth holding.
        self::assertStringContainsString(
            'the root page id to the page that was actually imported, and nothing else',
            $text,
            'what the site configuration import does not remap'
        );
        self::assertStringContainsString('t3://page?uid=', $text, 'the reference that ships stale');
    }

    #[Test]
    public function aNavigationIsAnsweredWhereMenusAreActuallyConfigured(): void
    {
        // excludeDoktypes replaces the default list instead of extending it,
        // which puts every storage folder into the menu. The hint that says so
        // has to be reachable from the word the question is asked with.
        $result = Hints::find([], 'main navigation of the site, menu levels and which pages it shows', 6);

        self::assertContains('page-variables-and-processors', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function aMenuQuestionThatReadsAsFrontendWorkStillReachesTheMenuTrap(): void
    {
        // The statement was there and was re-reported as missing: it sat in the
        // PHP category, and a question phrased as sitepackage work has that
        // whole category withheld from it. Where a statement lives decides who
        // can see it.
        $result = Hints::find(
            [],
            'the main navigation of my sitepackage shows storage folders, menu dataProcessing excludeDoktypes',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');
        self::assertContains('page-variables-and-processors', $ids);

        $text = implode("\n", array_column((array) Hints::byId('page-variables-and-processors')['hints'], 'text'));
        self::assertStringContainsString('excludeDoktypes', $text);
    }

    #[Test]
    public function aSitepackageIsAnsweredWithTheLayoutTheCoreItselfShips(): void
    {
        // A layout was invented for a sitepackage and rejected afterwards,
        // because the core ships a theme extension that establishes one and
        // nothing here pointed at it.
        $result = Hints::find([], 'directory structure of a sitepackage extension', 6);
        self::assertContains('sitepackage-layout', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('sitepackage-layout', 'sitepackage-templates', 'sitepackage-backend-layouts', 'sitepackage-typoscript-reference');
        self::assertStringContainsString('theme_camino', $text);
        self::assertStringContainsString('Content/Default', $text, 'the layout name collision is the load-bearing half');
    }

    #[Test]
    public function whatOnlyBindsACorePatchSaysSoOutsideTheCore(): void
    {
        // The backend's design system is a condition of a core patch and of
        // nothing in a project — which does not make it useless there, so it is
        // marked rather than dropped. Inside the core the marker would be on
        // every line and say nothing.
        $project = Registry::call('typo3_hint_lookup', [
            'id' => 'css-class-naming',
            'paths' => ['packages/my_sitepackage/Classes/Controller/ProductController.php'],
        ]);
        self::assertSame(
            [Scope::Extension],
            array_values(array_unique(array_column($project->data['scopes'], 'scope'))),
        );
        self::assertSame(Scope::Core->value, $project->data['hints'][0]['scope']);
        self::assertStringContainsString('Binding for a patch to the TYPO3 core', $project->text);
        self::assertStringContainsString('conventions you may adopt', $project->text);

        $core = Registry::call('typo3_hint_lookup', [
            'id' => 'css-class-naming',
            'paths' => ['Build/Sources/Sass/component/_card.scss'],
        ]);
        self::assertStringNotContainsString('Binding for a patch', $core->text);
    }

    /**
     * The same rule the other way round, which the corpus could not say until
     * `D-KNW-007`: a hint whose whole subject is a repository outside the core
     * is context there rather than a condition, and inside its own scope the
     * notice would be on every answer and say nothing.
     */
    #[Test]
    public function whatOnlyBindsOutsideTheCoreSaysSoInsideIt(): void
    {
        $inTheCore = Registry::call('typo3_hint_lookup', [
            'id' => 'project-repository-layout',
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
        ]);

        self::assertSame(Scope::Project->value, $inTheCore->data['hints'][0]['scope']);
        self::assertStringContainsString('Binding for work outside the TYPO3 core', $inTheCore->text);

        $inAProject = Registry::call('typo3_hint_lookup', [
            'id' => 'project-repository-layout',
            'paths' => ['packages/my_sitepackage/composer.json'],
        ]);
        self::assertStringNotContainsString('Binding for work outside', $inAProject->text);

        // The pair the corpus draws this line with: two hints, one subject
        // each, and now each saying whose it is rather than only its title.
        $tests = array_column(Hints::load(), 'scope', 'id');
        self::assertSame(Scope::Core, $tests['core-tests']);
        self::assertSame(Scope::Extension, $tests['project-extension-tests']);
    }

    /**
     * Three hints read as one audience by their titles and are not, which the
     * checkouts settled rather than intuition: `theme_camino` is a sitepackage
     * in the core repository and ships `Initialisation/data.xml`, so laying one
     * out and seeding it are obligations the core has too, and a site set is
     * how any extension ships TypoScript — `fluid_styled_content` is one. A
     * declaration would tell a contributor working on those that their own
     * subject is somebody else's.
     *
     * What would change it is a release: the theme leaving the core, which its
     * own first statement says is announced.
     */
    #[Test]
    public function aHintTheCoreIsAlsoObligedByDeclaresNoAudience(): void
    {
        $scopes = array_column(Hints::load(), 'scope', 'id');

        foreach (['sitepackage-layout', 'sitepackage-initial-content', 'site-sets'] as $id) {
            self::assertNull($scopes[$id], $id . ' declares an audience the core is obliged by too');
        }
    }

    /**
     * The upstream XML's own header says to copy the bootstrap along with it,
     * which is boilerplate maintenance advice rather than a requirement: the
     * file holds a Testbase, ORIGINAL_ROOT and two directories, and nothing an
     * extension configures. A copy is a file nobody updates afterwards.
     */
    #[Test]
    public function theBootstrapIsReferencedRatherThanCopied(): void
    {
        $text = self::statementsOf('project-extension-tests');

        self::assertStringContainsString('Copy those two to Build/', $text, 'the XML is copied');
        self::assertStringContainsString('vendor/typo3/testing-framework', $text, 'the bootstrap is referenced');
        self::assertStringNotContainsString('Copy all four', $text);
    }

    /**
     * The chain a patch review read seven core classes for, because nothing
     * below `knowledge/` said how a file becomes a processed one (`D-KNW-028`).
     * What decides it is the order the registry asks in and the first `yes`,
     * which is why registering after the processor that already claims a case
     * changes nothing.
     */
    #[Test]
    public function whichProcessorClaimsAFileIsAnswered(): void
    {
        $reached = array_column(
            Hints::find([], 'which processor makes the thumbnail', 6)['matchedHints'],
            'id',
        );
        self::assertContains('fal-processing', $reached);

        $text = self::statementsOf('fal-processing');

        self::assertStringContainsString('stops at the first that says yes', $text, 'what decides it');
        self::assertStringContainsString('never after it', $text, 'the mistake the order makes possible');
        self::assertStringContainsString('fileNeedsProcessing()', $text, 'why a processor may not run at all');
        self::assertStringContainsString('1560876294', $text, 'what an unclaimed task throws');
        self::assertStringContainsString('typo3_configuration_lookup', $text, 'where the real list is');
    }

    /**
     * Two FAL traps that only show up as "nothing happened": the same call
     * carries a different default on the folder and on the storage, and a
     * reference's own fields are the ones an editor filled in.
     */
    #[Test]
    public function theFileAnswersNameWhatFailsSilently(): void
    {
        $writing = implode("\n", array_column(Hints::byId('fal-writing')['hints'], 'text'));

        self::assertStringContainsString('ExistingTargetFileNameException', $writing);
        self::assertStringContainsString('ResourceStorage::addFile() renames', $writing);

        $reading = implode("\n", array_column(Hints::byId('fal-reading')['hints'], 'text'));

        self::assertStringContainsString('getOriginalFile()', $reading);
        self::assertStringContainsString('alternative text', $reading, 'what the reference record carries');
    }

    /**
     * The gap the umbrella hid: `datahandler-persistence` carried `querybuilder`,
     * `restriction`, `enablecolumns`, `hidden record` and `deleted record` in its
     * vocabulary and not one statement about reading a record. The whole corpus
     * held one sentence naming any of the reading APIs, and it was about a menu.
     */
    #[Test]
    public function readingRecordsIsAnsweredAsWellAsWritingThem(): void
    {
        $reached = array_column(
            Hints::find([], 'why is a record missing from my query result', 6)['matchedHints'],
            'id',
        );
        self::assertContains('persistence-reading', $reached);

        $text = implode("\n", array_column(Hints::byId('persistence-reading')['hints'], 'text'));

        // What is applied without being asked for, and what is a step after the
        // query rather than a condition in it. Both read as a missing record.
        self::assertStringContainsString('DefaultRestrictionContainer', $text);
        self::assertStringContainsString('removeAll()', $text);
        self::assertStringContainsString('versionOL', $text);
        self::assertStringContainsString('getLanguageOverlay', $text);
    }

    /**
     * impexp is how a site or a page tree is established again — the export is
     * the artifact a distribution ships and re-imports, not the leftover of a
     * seeding run. The corpus said "seed with DataHandler, then export", which
     * reads as the other way round.
     */
    #[Test]
    public function theSeedingAnswerNamesImpexpAsTheWayATreeIsEstablishedAgain(): void
    {
        $text = implode("\n", array_column(Hints::byId('datahandler-seeding')['hints'], 'text'));

        self::assertStringContainsString('impexp:import', $text);
        self::assertStringContainsString('exists nowhere yet', $text, 'what a seeding script is for');
    }

    /**
     * The tag is the selector, so a domain nobody selects by is a hint nobody
     * reaches — and it fails silently, because an unknown tag reads exactly like
     * a narrow one. `Domains::hintDomains()` is the whole vocabulary there is.
     */
    #[Test]
    public function everyHintIsTaggedWithADomainSomeQuerySelects(): void
    {
        $selectable = Domains::hintDomains([
            Domains::PHP,
            Domains::TYPOSCRIPT,
            Domains::FLUID,
            Domains::TYPESCRIPT,
            Domains::CSS,
            Domains::XLIFF,
            Domains::DOCS,
        ]);

        foreach (Hints::load() as $hint) {
            self::assertNotEmpty($hint['domains'], $hint['id'] . ' names no domain');
            foreach ($hint['domains'] as $domain) {
                self::assertContains(
                    $domain,
                    $selectable,
                    $hint['id'] . ' is tagged ' . $domain . ', which no query selects',
                );
            }
        }
    }

    /**
     * The bucket, as a number rather than as a plan.
     *
     * `any` is still a tag a hint may carry — `D-KNW-029` kept it deliberately —
     * and nothing carries it since `D-KNW-033` named the domains each of the 38
     * General hints is really asked from. This fails on the first new one, which
     * is the point: an `any` hint is reachable from every task there is, so it
     * has to be argued for in a decision rather than reached for when a query
     * misses.
     */
    #[Test]
    public function nothingIsTaggedAnyWithoutSayingWhy(): void
    {
        $tagged = array_values(array_filter(
            Hints::load(),
            static fn(array $hint): bool => in_array(Domains::ANY, $hint['domains'], true),
        ));

        self::assertSame(
            [],
            array_column($tagged, 'id'),
            'a hint every query selects is a decision, not a tag chosen while writing it',
        );
    }

    /**
     * What the file a hint sits in is allowed to mean: nothing.
     *
     * It used to be the domain, which is why a hint belonging to two of them had
     * to be filed as `general` — the one domain every query selects. The tag
     * carries it now, so the file is free to be the subject, and this fails the
     * moment somebody re-derives the domain from the file name.
     */
    #[Test]
    public function theFileAHintSitsInDoesNotDecideWhatSelectsIt(): void
    {
        $filed = [];
        $files = Finder::create()->files()->in(Paths::knowledge() . '/hints')->depth(0)->name('*.json');
        foreach ($files as $file) {
            foreach (json_decode((string) file_get_contents($file->getPathname()), true) as $entry) {
                $filed[$entry['id']] = $file->getBasename('.json');
            }
        }

        $differ = array_values(array_filter(
            Hints::load(),
            static fn(array $hint): bool => $hint['domains'] !== [$filed[$hint['id']]],
        ));

        // The `general.json` entries are the proof today: their file says
        // `general` and their tag says `any`, so a matcher that read the file
        // name would select them by a domain that is not in the vocabulary at
        // all. When the corpus is filed by subject the same test passes for
        // every entry in it.
        self::assertNotSame([], $differ, 'every hint would answer the same if the file name were still the domain');
    }

    #[Test]
    public function oneCoreObligationInATransferableHintIsMarkedOnItsOwn(): void
    {
        // The ViewHelper conventions hold wherever TYPO3 is written; the
        // changelog file under typo3/sysext/ is the one sentence in them that
        // does not. Splitting the hint to say so would duplicate the six
        // statements around it, so the statement carries it — the same place
        // since/until sits.
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'fluid-viewhelpers',
            'paths' => ['packages/my_sitepackage/Classes/ViewHelpers/GreetingViewHelper.php'],
        ]);

        self::assertNull($result->data['hints'][0]['scope'], 'the hint as a whole transfers');
        $bound = array_values(array_filter(
            $result->data['hints'][0]['hints'],
            static fn(array $statement): bool => $statement['scope'] !== null,
        ));
        self::assertCount(1, $bound);
        self::assertStringContainsString('changelog entry', $bound[0]['text']);
        self::assertStringContainsString('binding for a core patch', $result->text);
    }

    #[Test]
    public function whereSomethingGoesInTheRepositoryIsAnsweredToo(): void
    {
        // The extension was answered and the repository around it was not, so a
        // project invented the location of its phpunit configurations, its
        // browser suite and its scripts. The load-bearing rule is which of the
        // two units a thing belongs to.
        $result = Hints::find([], 'how do I structure the repository around my sitepackage', 6);
        self::assertContains('project-repository-layout', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('project-repository-layout', 'project-configuration-files', 'project-build-and-scripts');
        self::assertStringContainsString('config/sites/', $text);
        self::assertStringContainsString('composer.json and package.json', $text, 'nothing else says how a project is run');
    }

    #[Test]
    public function whereBackendLayoutsGoIsAnsweredWithTheConditionItDependsOn(): void
    {
        // The extension-level directory was stated as the rule, read off a
        // distributable theme. In a sitepackage with one set and no
        // Configuration/page.tsconfig it is an indirection with no effect,
        // because the set is the only path into any backend.
        $text = self::statementsOf('sitepackage-layout', 'sitepackage-templates', 'sitepackage-backend-layouts', 'sitepackage-typoscript-reference');
        self::assertStringContainsString('Configuration/Sets/<Set>/BackendLayouts/', $text);
        self::assertStringContainsString('Configuration/page.tsconfig', $text, 'the condition is what makes it transfer');
    }

    #[Test]
    public function theTemplateTrapsThatFailWithoutAnErrorAreNamed(): void
    {
        // Both produce a wrong page rather than a failure: a variable assigned
        // outside a section of a template that declares a layout is never
        // executed, and an HTML comment is rendered into the response with its
        // expressions resolved. Neither is logged, so neither is searchable.
        $text = self::statementsOf('fluid-templates', 'fluid-backend-view', 'fluid-layouts-sections', 'fluid-conditions-and-arrays');

        self::assertStringContainsString('<f:section>', $text);
        self::assertStringContainsString('<f:comment>', $text);
    }

    #[Test]
    public function theTestKindThatNeedsABrowserIsCovered(): void
    {
        // Asking for browser tests returned the id index and a knowledge section
        // about site sets. The core works the conventions out in
        // Build/tests/playwright/, and nothing here pointed at them.
        $result = Hints::find(
            [],
            'acceptance and end-to-end browser tests for a TYPO3 site with Playwright',
            6
        );
        self::assertContains('browser-tests', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('browser-tests', 'browser-test-accessibility', 'browser-tests-outside-core');
        // The accessibility half is the one that finds defects no PHP test can,
        // and the rendering test is what gets mistaken for a frontend test.
        self::assertStringContainsString('@axe-core/playwright', $text);
        self::assertStringContainsString('executeFrontendSubRequest', $text);
    }

    /**
     * The corpus stated every fixture rule and never the premise under them, so
     * a session read `importCSVDataSet()` as a convention it could also satisfy
     * another way, fetched records nobody primed, and took the empty table for a
     * broken query. «empty database per test run» reached neither testing hint.
     *
     * The premise holds across every covered line and was read rather than
     * recalled — `.checkouts/testing-framework` at `8.3.3`, `9.6.1` and
     * `main`. `FunctionalTestCase::setUp()` builds the instance for the first
     * test of a class and `Testbase::createDatabaseStructure()` installs the
     * `CREATE TABLE` statements alone, no rows; every test after it goes through
     * `initializeTestDatabaseAndTruncateTables()`, which truncates every table
     * the schema manager lists — or, for sqlite, copies back the `.empty.sqlite`
     * file taken right after the schema was installed. `setUpBackendUser()` is
     * the consequence in one method: it throws rather than creating the user.
     *
     * Both boundaries are asserted because either one left out makes the
     * sentence too strong: `$initializeDatabase = false` skips creation and
     * truncation alike, and `withDatabaseSnapshot()` restores what the first
     * test of a class created for all the ones after it.
     */
    #[Test]
    public function theFixtureRuleIsStatedWithTheEmptyDatabaseUnderIt(): void
    {
        self::assertContains(
            'core-tests',
            array_column(Hints::find([], 'empty database per test run', 6)['matchedHints'], 'id'),
            'the premise is not reachable in the words the failing session asked it in',
        );

        $text = implode("\n", array_column((array) Hints::byId('core-tests')['hints'], 'text'));
        self::assertStringContainsString('empty database', $text);
        self::assertStringContainsString('importCSVDataSet', $text, 'the premise stands beside the rule it explains');
        self::assertStringContainsString('$initializeDatabase = false', $text);
        self::assertStringContainsString('withDatabaseSnapshot()', $text);
    }

    /**
     * The phrasing that reached the layout instead, measured while settling
     * `D-KNW-008`: two hints are named after a sitepackage and none after
     * setting tests up, so "site package" was the discriminating pair of terms
     * and "tests" separated nothing — `R-ANS-007` working as designed on a
     * corpus where the word naming the subject is the weaker signal.
     *
     * The vocabulary is what moved, not the corpus: three phrasings that name
     * the work rather than the harness. `add tests` was measured with them and
     * left out — it puts the project hint ahead of `core-tests` for "add tests
     * for the DataHandler change", which is a core question.
     */
    #[Test]
    public function settingTestsUpInAPackageReachesTheHintAboutThat(): void
    {
        $reaches = static fn(string $task): array => array_column(
            Hints::find(['packages/my_sitepackage/Classes/Foo.php'], $task, 5)['matchedHints'],
            'id',
        );

        self::assertSame('project-extension-tests', $reaches('Set up tests for our site package extension')[0] ?? '');

        // The neighbours it was measured against, which any change here has to
        // keep: each reaches the cell it is about.
        self::assertContains('project-extension-tests', $reaches('how do I test my extension'));
        self::assertContains('project-extension-tests', $reaches('add functional tests to the extension'));
        self::assertContains('extension-static-analysis', $reaches('set up phpstan for our extension'));
        self::assertContains('browser-tests', $reaches('browser tests for the site package'));

        // Without a path, where the domain rather than the ranking used to
        // decide: "site package" is a Fluid and TypoScript keyword and nothing
        // in the sentence was a PHP one, so php.json was filtered out before
        // anything was scored (D-KNW-009).
        self::assertSame(
            'project-extension-tests',
            array_column(
                Hints::find([], 'Set up tests for our site package extension', 5)['matchedHints'],
                'id',
            )[0] ?? '',
        );
        self::assertSame(
            'project-extension-tests',
            array_column(
                Hints::find([], "Review our site package's test coverage", 5)['matchedHints'],
                'id',
            )[0] ?? '',
            'SKILL-01 asks in the words the vocabulary was missing',
        );

        // And the core side, which is what the fourth phrasing would have cost.
        $core = array_column(
            Hints::find(
                ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
                'add tests for the DataHandler change',
                5,
            )['matchedHints'],
            'id',
        );
        self::assertContains('core-tests', $core);
        self::assertNotContains('project-extension-tests', $core, 'a core testing question reached the project hint');
    }

    /**
     * The sixth phrasing `D-KNW-009`'s first **Wrong if** asked about, and it
     * came out of this repository's own text: the conformance checklist wrote
     * its quality surface down as the bare `tests` that entry had rejected, so
     * an audit asking in the checklist's own wording reached no PHP hint at
     * all and no rule about the supported range either. What that cost is on
     * the record — a recommendation to use a `typo3/cms-compatibility` package
     * for cross-version testing that no covered line ships (`D-KNW-013`).
     *
     * Both ends are held here because either alone is inert, and the wording
     * is read out of the checklist rather than quoted: the vocabulary was
     * widened to meet that sentence, so a session that writes the bare word
     * back fails here rather than in an audit six weeks later.
     */
    #[Test]
    public function anAuditAskingAboutTestsReachesTheRuleAboutTheSupportedRange(): void
    {
        $checklist = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-conformance/references/checklist.md',
        );
        preg_match('/^- Quality: (.+?)\.$/ms', $checklist, $surface);
        self::assertNotSame([], $surface, 'the checklist still writes a quality surface down');

        $reaches = static fn(string $task): array => array_column(
            Hints::find([], $task, 6)['matchedHints'],
            'id',
        );

        self::assertContains(
            'extension-repository-layout',
            $reaches('audit the quality surface of an extension: ' . $surface[1]),
            'the audit asks in the checklist\'s own words',
        );
        self::assertContains(
            'extension-repository-layout',
            $reaches('does the test suite cover every supported TYPO3 version'),
            'and a caller asks for the same rule without naming a repository at all',
        );

        // Through the tool the skill actually calls, where the answer used to
        // be the layout of a repository with an installation in it — which is
        // not even the unit under audit.
        $audit = Registry::call('typo3_hint_lookup', [
            'task' => 'Quality: ' . $surface[1],
            'paths' => ['composer.json', 'Tests/', 'Build/'],
            'targetVersion' => '14',
        ]);
        $ids = array_column($audit->data['hints'], 'id');
        self::assertContains('extension-repository-layout', $ids);
        self::assertLessThan(
            array_search('project-repository-layout', $ids, true) === false
                ? PHP_INT_MAX
                : (int) array_search('project-repository-layout', $ids, true),
            (int) array_search('extension-repository-layout', $ids, true),
            'the repository that is only the extension answers before the one that holds an installation',
        );

        // The neighbour the widened vocabulary was measured against: a testing
        // question with no version in it still leads with the harness hint.
        self::assertSame('project-extension-tests', $reaches('how do I test my extension')[0] ?? '');
    }

    #[Test]
    public function aProjectExtensionIsToldHowToGetASuiteAtAll(): void
    {
        // core-tests describes how a test is written inside the mono repository,
        // where the harness already exists. In a project everything between
        // "composer require" and the first green run is the work, and none of it
        // was written down.
        $result = Hints::find(
            [],
            'Add automated tests for a project sitepackage extension: unit and functional tests for an Extbase '
            . 'model, repository and controller, plus frontend tests for the rendered pages',
            6
        );
        self::assertContains('project-extension-tests', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('project-extension-tests', 'extension-test-extensions', 'extension-test-site');
        // Each of these is a failure whose message does not name its cause.
        self::assertStringContainsString('typo3DatabaseUsername', $text);
        self::assertStringContainsString('$testExtensionsToLoad', $text);
        self::assertStringContainsString('SiteBasedTestTrait', $text);
        self::assertStringContainsString('setUpFrontendRootPage', $text);
    }

    #[Test]
    public function theFileAnExtensionNoLongerNeedsIsCoveredWhereItsFilesAre(): void
    {
        // "Which files does an extension need" listed every file that is still
        // current and left out the one that costs something: ext_emconf.php,
        // absent from the list, reads as "not relevant" rather than as "declare
        // this in composer.json instead".
        $hint = Hints::byId('extension-manifest');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('ext_emconf.php', $text);

        $current = implode("\n", array_column((array) Hints::byId('extension-manifest', 14)['hints'], 'text'));
        self::assertStringContainsString('providesPackages', $current);
        self::assertStringContainsString('extra.typo3/cms.version', $current);
    }

    /**
     * A review of `bootstrap_package` found two contentRenderingTemplates
     * entries naming a directory the move to site sets had removed, and had to
     * read SysTemplateTreeBuilder and TreeFromLineStreamBuilder out of its own
     * vendor tree to settle that they were inert. Nothing here answered it: the
     * hint the question lands on said nothing about the key, and the key is not
     * deprecated, so no scanner names it either.
     *
     * What a reader needs is both halves — what a matched entry does, so a live
     * one is not deleted, and what makes one unmatchable, so a dead one is not
     * treated as a defect.
     */
    #[Test]
    public function aRegistrationThatCanNoLongerBeMatchedIsToldApartFromALiveOne(): void
    {
        $result = Hints::find(
            ['ext_localconf.php'],
            'is this contentRenderingTemplates registration still consumed, it names a directory that is gone',
            6,
        );
        self::assertSame('content-rendering-templates', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('content-rendering-templates', 'site-set-migration');
        // What a matched entry does, and the two identifier shapes that match.
        self::assertStringContainsString("['defaultContentRendering']", $text);
        self::assertStringContainsString('configurePlugin()', $text);
        self::assertStringContainsString('fluidstyledcontent/Configuration/TypoScript/', $text);
        self::assertStringContainsString('ext_typoscript_setup.typoscript', $text);
        // Where it is read, so the claim can be re-checked against a checkout.
        // The two IncludeTree classes hold on every branch; the resolver they
        // replaced is a statement of its own rather than prose about a version.
        self::assertStringContainsString('SysTemplateTreeBuilder::addStaticMagicFromGlobals()', $text);
        self::assertStringContainsString('TreeFromLineStreamBuilder', $text);
        $onFourteen = implode("\n", array_column((array) Hints::byId('content-rendering-templates', 14)['hints'], 'text'));
        self::assertStringNotContainsString('TemplateService', $onFourteen);
        $onTwelve = implode("\n", array_column((array) Hints::byId('content-rendering-templates', 12)['hints'], 'text'));
        self::assertStringContainsString('TemplateService::prependStaticExtra()', $onTwelve);
        self::assertStringContainsString('TypoScriptParser', $onTwelve);
        // And why nothing flags a dead one.
        self::assertStringContainsString('addStaticFile()', $text);
        self::assertStringContainsString('cleanup rather than a defect', $text);
        self::assertStringContainsString('not deprecated', $text);

        // The migration these entries are left over from says so where it
        // happens, because that is the change that strands them.
        $sets = implode("\n", array_column((array) Hints::byId('site-set-migration', 14)['hints'], 'text'));
        self::assertStringContainsString('contentRenderingTemplates', $sets);
        self::assertStringContainsString('content-rendering-templates', $sets);

        // Sets are what a caller on the older branch has no migration into, so
        // the pointer is not offered there.
        $setsOnTwelve = implode("\n", array_column((array) Hints::byId('site-set-migration', 12)['hints'], 'text'));
        self::assertStringNotContainsString('contentRenderingTemplates', $setsOnTwelve);
    }

    #[Test]
    public function theIconHintSaysWhichHalfOfTypo3ItIsAbout(): void
    {
        // Every API the hint names is a backend one, and a reader who is writing
        // a page template does not infer the boundary from that list — they read
        // it as how an icon is rendered. The lookup says so on every answer; the
        // hint describing the same registry has to say it too.
        $hint = Hints::byId('icon-usage');
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
        $result = Hints::find([], 'backend module template', 6);

        self::assertContains('backend-modules', array_column($result['matchedHints'], 'id'));
    }

    #[Test]
    public function aBackendModuleInASitepackageDoesNotBecomeFrontendWork(): void
    {
        $task = 'Add a backend module to the project site package for reviewing imported product records, '
            . 'with a refresh action, status badges, icons and translated labels';
        $guide = Registry::call('typo3_task_guide', ['task' => $task]);
        $hintIds = array_column($guide->data['hints'], 'id');
        $tools = array_column($guide->data['nextTools'], 'tool');

        self::assertContains(Domains::PHP, $guide->data['domains']);
        self::assertNotContains(Domains::FLUID, $guide->data['domains']);
        self::assertNotContains(Domains::TYPOSCRIPT, $guide->data['domains']);
        self::assertSame('backend-modules', $hintIds[0]);
        self::assertNotContains('frontend-records', $hintIds);
        self::assertNotContains('sitepackage-layout', $hintIds);
        self::assertNotContains('sitepackage-initial-content', $hintIds);
        self::assertContains('typo3_component_lookup', $tools);
        self::assertContains('typo3_backend_module_lookup', $tools);
        self::assertContains('typo3_icon_lookup', $tools);
        self::assertContains('typo3_label_lookup', $tools);
    }

    /**
     * `D-KNW-001`'s **Wrong if**, run against the server on 2026-08-02. Two of
     * five backend-only task texts that name a content element came back with
     * the sitepackage layout — «Add a TCA field to the content element in the
     * backend» and «The backend preview of the content element is broken in the
     * page module».
     *
     * The exclusion that answers it existed already and was reached only by the
     * words "backend module". Nothing about it was about modules: the two large
     * hints displace what the task named whenever the task is backend-only,
     * because a sitepackage layout is written in the words of the backend it is
     * administered from and wins on its body rather than on its vocabulary.
     */
    #[Test]
    public function aBackendOnlyTaskNamingAContentElementIsNotAnsweredWithTheSitepackageLayout(): void
    {
        foreach ([
            'Add a TCA field to the content element in the backend',
            'The backend preview of the content element is broken in the page module',
            'Fix the icon shown for our accordion content element in the backend new content element wizard',
            // SITE-08's prompt.
            'The accordion content element we already have needs one more field in the backend form, and the '
                . 'wrong icon shows for it in the new content element wizard. Nothing about the rendering changes.',
        ] as $task) {
            $guide = Registry::call('typo3_task_guide', ['task' => $task]);
            $hintIds = array_column($guide->data['hints'], 'id');

            self::assertNotContains('sitepackage-layout', $hintIds, $task);
            self::assertContains('content-elements', $hintIds, $task);
        }
    }

    /**
     * The half of `SITE-08` the exclusion above does not reach: the brief still
     * called such a task Fluid and TypoScript work, because "content element"
     * is a keyword of both. It stays one — take it out and «Add a hero carousel
     * content element whose slides editors can create, order, translate and
     * hide» loses both domains too, and a rendering question stops reaching
     * `frontend-page-rendering`. What it stops doing is adding them where the
     * task names only the backend (`D-KNW-006`).
     */
    #[Test]
    public function aBackendOnlyTaskNamingAContentElementIsNotCalledFluidAndTypoScriptWork(): void
    {
        foreach ([
            'Add a TCA field to the content element in the backend',
            'The backend preview of the content element is broken in the page module',
            // SITE-08's prompt.
            'The accordion content element we already have needs one more field in the backend form, and the '
                . 'wrong icon shows for it in the new content element wizard. Nothing about the rendering changes.',
        ] as $task) {
            $domains = Registry::call('typo3_task_guide', ['task' => $task])->data['domains'];

            self::assertContains(Domains::PHP, $domains, $task);
            self::assertNotContains(Domains::FLUID, $domains, $task);
            self::assertNotContains(Domains::TYPOSCRIPT, $domains, $task);
        }

        foreach ([
            // SITE-05's prompt, which names both halves.
            'Editors need a "team members" content element: a list of people picked from a folder, rendered as '
                . 'cards. Build it in our site package — the element, its backend form, and its frontend output.',
            // SKILL-04's, which names neither and is the reason the keyword stays.
            'Add a hero carousel content element whose slides editors can create, order, translate and hide '
                . 'directly inside the element. Keep its implementation maintainable and test the behavior that '
                . 'matters.',
        ] as $task) {
            $domains = Registry::call('typo3_task_guide', ['task' => $task])->data['domains'];

            self::assertContains(Domains::FLUID, $domains, $task);
            self::assertContains(Domains::TYPOSCRIPT, $domains, $task);
        }
    }

    /**
     * The other side of it, and why the gate reads the frontend markers rather
     * than negating namesTheFrontend(): there the backend markers win, so a task
     * naming both halves would count as backend-only and lose the layout hint
     * that is half of its answer. This is `SITE-05`'s prompt.
     */
    #[Test]
    public function aContentElementBuiltInASitepackageKeepsItsLayout(): void
    {
        $result = Hints::find([], 'Editors need a "team members" content element: a list of people '
            . 'picked from a folder, rendered as cards. Build it in our site package — the element, its backend '
            . 'form, and its frontend output.', 6);

        self::assertContains('sitepackage-layout', array_column($result['matchedHints'], 'id'));
    }

    /**
     * The order the labels are declared in, which is what a reader meets first.
     * "General" is not among them any more: every hint names the domains it is
     * asked from since `D-KNW-033`, so nothing is filed under the label that
     * meant "belongs to more than one and had nowhere to say so".
     */
    #[Test]
    public function hintsAreGroupedByDomainWithPhpFirst(): void
    {
        $hints = Hints::load();
        $categories = array_column(Hints::groupByCategory($hints), 'category');

        self::assertSame('PHP', $categories[0]);
        self::assertNotContains('General', $categories);
        self::assertContains('Labels', $categories);
    }

    #[Test]
    public function everyHintCarriesItsSectionAndAtLeastOneHint(): void
    {
        foreach (Hints::load() as $hint) {
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

        foreach (Hints::load() as $hint) {
            // A PSR number names an interface, an XLIFF number names a file
            // format, an HTTP number names a response status, and a TYPO3
            // exception code names one throw site for good — assigned once and
            // never reused, so it says nothing about a branch. None of them
            // dates the statement against a TYPO3 branch, which is the only
            // thing this is looking for. Each is worth carrying because it is
            // the symptom a caller arrives with — so each is written with its
            // word in front, "HTTP 404" and "exception 1560876294" rather than
            // bare, which is what makes them exemptible here without also
            // exempting a count that happens to be three digits long.
            $text = (string) preg_replace(
                ['/\bPSR-\d+/i', '/\bXLIFF \d+\.\d+/i', '/\bHTTP \d{3}\b/i', '/\bexception \d{10}\b/i'],
                ['PSR', 'XLIFF', 'HTTP', 'exception'],
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

    /**
     * A suite is a runTests.sh target, and which ones that script offers changes
     * between majors. Handing over a command the caller's checkout does not have
     * is not a weaker answer than none — it sends them to debug their checkout
     * for something this server invented for another branch.
     *
     * Verified against this repository's own checkouts: no suite matching xlf or
     * xliff exists in Build/Scripts/runTests.sh on 12.4 or 13.4 under any name,
     * while 14.3 and main have checkIntegrityXliff and normalizeXliff.
     */
    #[Test]
    public function theSuiteListItselfIsFilteredByTheBranchItIsAskedFor(): void
    {
        $suites = static fn(?string $version): array => array_column(
            Registry::call('typo3_test_run_guide', ['query' => 'xliff labels', 'targetVersion' => $version])->data['suites'],
            'suite',
        );

        self::assertNotContains('checkIntegrityXliff', $suites('13.4'));
        self::assertContains('checkIntegrityXliff', $suites('14'));
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
        $suites = array_column(Registry::call('typo3_test_run_guide', [
            'query' => 'Only a Sass change in Build/Sources/Sass/component/_card.scss; recommend the narrow '
                . 'iteration check and the review-ready checks, without unrelated PHP or TypeScript suites',
        ])->data['suites'], 'suite');

        // Nothing said which version this is for, so the frontend build is
        // listed under both names it has across the covered range, each with
        // its own range beside it. What the case is about is the narrowing: a
        // Sass path, and no PHP or TypeScript suite in the answer.
        self::assertSame(['build', 'build-css', 'buildCss', 'lintScss'], $suites);
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

    /**
     * A deprecation is a change type, and the rules it owes arrive with it.
     *
     * The reviewing session behind `feedback/2026-08-01-122113` classified the
     * patch rather than describing it, and the one core patch shape with a
     * fixed rule set was the one the enum had no value for — so it verified
     * every rule by grepping the `setCorrelationId` precedent instead. The
     * rules asserted here are that precedent, read in `.checkouts/main`.
     */
    #[Test]
    public function aDeprecationIsAChangeTypeThatCarriesWhatOneOwes(): void
    {
        self::assertContains(
            'deprecation',
            TaskGuide::inputSchema()['properties']['changeType']['enum'],
        );

        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Review the patch that deprecates the AssetCollector media handling',
            'changeType' => 'deprecation',
        ]);

        self::assertContains('deprecation', array_column($brief->data['intents'], 'id'));
        foreach ([
            '@deprecated since TYPO3 v',
            'E_USER_DEPRECATED',
            'Deprecation-<issue>-<UpperCamelCaseDescription>.rst',
            '_deprecation-<issue>-<unix timestamp>:',
            'NotScanned',
            'typo3/sysext/install/Configuration/ExtensionScanner/Php/',
        ] as $owed) {
            self::assertStringContainsString($owed, $brief->text);
        }
        self::assertContains(
            'CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst',
            $brief->data['checks'],
        );

        // Once, not twice: the change type routes into the intent that already
        // carries the rules, rather than holding a second copy of them.
        self::assertSame(
            array_values(array_unique($brief->data['checklist'])),
            $brief->data['checklist'],
        );
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

        // What the intent is worth is the rules the caller ends up with, not
        // which corpus they came from. The XLIFF lifecycle used to be a prose
        // section this intent queried by name; it is a bound statement in
        // language-files now, and the guide reaches it by matching instead.
        $guide = Registry::call('typo3_task_guide', ['task' => 'Add one label to locallang_layout.xlf']);

        self::assertContains('language-files', array_column($guide->data['hints'], 'id'));
        self::assertStringContainsString('x-unused-since', $guide->text);
    }

    /**
     * The suites a domain always runs are stated even where nothing in the task
     * names a suite. This sentence is about TSconfig field labels: the intent
     * matcher finds the XLIFF checks in it, the suite matcher finds the label
     * integrity ones, and neither reaches the functional suite the change can
     * actually fail on. It came off the hints until `D-KNW-031`,
     * where twenty-eight of them repeated it as their own `checks` list.
     */
    #[Test]
    public function theBaseSuitesOfADomainAreStatedWhateverTheTaskNames(): void
    {
        $checks = Registry::call('typo3_task_guide', [
            'task' => 'Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields',
            'area' => 'backend/FormEngine',
            'changeType' => 'bugfix',
        ])->data['checks'];

        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s functional', $checks);
        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s unit', $checks);

        // And they are the domain's, not one list for everybody: a Sass change
        // gets the frontend build and no PHP suite.
        $sass = Registry::call('typo3_task_guide', [
            'task' => 'Restyle the card component in the backend',
            'paths' => ['Build/Sources/Sass/component/_card.scss'],
        ])->data['checks'];

        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s lintScss', $sass);
        self::assertNotContains('CI=true ./Build/Scripts/runTests.sh -s functional', $sass);
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
    public function aRemovalIsToldWhatTheScannerMatcherRequires(): void
    {
        // R-ANS-017. "Consider an extension scanner matcher" was the whole of
        // what a removal was told, and the reviewer of one asked the rules and
        // was answered without it at all.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Remove public method GifBuilder::getTemporaryImageWithText()',
        ]);

        $checklist = implode("\n", $result->data['checklist']);

        self::assertStringContainsString('Configuration/ExtensionScanner/Php/', $checklist);
        self::assertStringContainsString('FullyScanned', $checklist);
    }

    #[Test]
    public function upgradingAnInstallationIsAnsweredAsAnOrderOfOperations(): void
    {
        // The question a site maintainer asks first — "what do I do, in which
        // order" — used to be answered with how to author a deprecation, which
        // is the same subject seen from the core's side and useless here.
        $result = Registry::call('typo3_task_guide', ['task' => 'upgrade this composer site project to TYPO3 v14']);

        self::assertContains('installation-upgrade', array_column($result->data['intents'], 'id'));

        $group = array_values(array_filter(
            $result->data['hints'],
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
    public function aRepeatableContentElementIsRoutedThroughWhatItOwns(): void
    {
        // A session designed a hero carousel out of generic record references —
        // technically possible, and what an element ends up with when nobody
        // asked who creates, orders, translates and hides a slide. The decision
        // has to be in the answer before the registration is, and the task that
        // asks for it does not have to say "content element" to get there.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add a hero carousel content element whose slides editors can create, order, translate and hide inside the element',
            'area' => 'packages/printworks_sitepackage/',
        ]);

        self::assertContains('content-element', array_column($result->data['intents'], 'id'));

        $hint = array_values(array_filter(
            $result->data['hints'],
            static fn(array $entry): bool => $entry['id'] === 'content-elements',
        ));
        self::assertCount(1, $hint);
        $ownership = self::statementsOf('content-element-shape');
        self::assertStringContainsString('type=inline', $ownership);
        self::assertStringContainsString('reuse is a requirement somebody stated', $ownership);

        // The wording a first question actually arrives with reaches it too,
        // and stays a conditional match, because nothing in it says the work is
        // a content element.
        $vague = Registry::call('typo3_task_guide', ['task' => 'Add a Hero Carousel that rotates different elements']);
        self::assertNotSame(
            [],
            array_intersect(
                ['content-elements', 'content-element-shape'],
                array_column($vague->data['hints'], 'id'),
            ),
            'a carousel is the subject even where nothing in the wording says content element',
        );
    }

    /**
     * The corpus registered the preview template and stopped there, so a
     * session arrived at a template with one variable in it and no statement
     * about what that variable is. Both halves are read off the checkouts:
     * FluidBasedContentPreviewRenderer assigns the row's columns and record on
     * 13.4 and record alone on 14.3, and what a field off it resolves to is
     * decided by the TCA type of that field — which is why the types that come
     * back as records are named rather than a single rule for "a relation".
     */
    #[Test]
    public function aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo(): void
    {
        $onThirteen = implode("\n", array_column(
            Hints::byId('content-element-preview', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('{pi_flexform_transformed}', $onThirteen);
        self::assertStringNotContainsString('no longer variables of their own', $onThirteen);

        $onFourteen = implode("\n", array_column(
            Hints::byId('content-element-preview', 14)['hints'],
            'text',
        )) . "\n" . implode("\n", array_column(
            Hints::byId('preview-record-variable', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('handed one variable, record', $onFourteen);
        self::assertStringNotContainsString('{pi_flexform_transformed}', $onFourteen);

        // The mechanism the reporting session could not find, and the field it
        // renders an empty spot over: what the record type's schema does not
        // declare is not on the record, and the path resolves to null.
        self::assertStringContainsString('PSR-11 container', $onFourteen);
        self::assertStringContainsString('has() and get()', $onFourteen);
        self::assertStringContainsString('{record.systemProperties.disabled}', $onFourteen);

        // What a relation comes back as, and what reads like one and is not.
        self::assertStringContainsString('lazy collection f:for iterates', $onFourteen);
        self::assertStringContainsString(
            'type=select with a relation, group, inline, category and file',
            $onFourteen,
        );
        self::assertStringContainsString('renderType is selectSingle', $onFourteen);

        // The subject is reachable from the question it was missing for. The
        // reporting session's own spelling, «content-element», reaches it as
        // little as before — that is D-ANS-022 and not this statement's to fix.
        $reached = Hints::find(
            [],
            'Record API field access in a backend content element preview Fluid template: '
            . 'what a relational select field resolves to',
            6,
        );
        self::assertSame('content-elements', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * The reported miss is a template that repeats what is already on the page:
     * GridColumnItem::getPreview() renders the header before dispatching the
     * event FluidBasedContentPreviewRenderer listens on, and that listener sets
     * the content alone. The header parts are asserted by field, because a
     * session told only that "the header" exists repeats subheader or date
     * instead. Both majors draw the same four, so the statement carries no
     * version binding and has to reach a caller on either.
     */
    #[Test]
    public function aPreviewAnswerSaysWhatTheDefaultRendererAlreadyDraws(): void
    {
        foreach ([13, 14] as $major) {
            $texts = implode("\n", array_column(
                Hints::byId('content-element-preview', $major)['hints'],
                'text',
            ));

            self::assertStringContainsString('replaces the content half', $texts);
            self::assertStringContainsString('where header_layout hides the header', $texts);
            self::assertStringContainsString('the date field with its label', $texts);
            self::assertStringContainsString("record type's label field linked to the edit form", $texts);
            self::assertStringContainsString('and subheader', $texts);
            self::assertStringContainsString('space_before_class', $texts);
        }

        // The words the reporting session arrived with. The probe D-KNW-015
        // recorded the gap on reached nothing at all, and «backend preview» was
        // in none of the hint's own vocabulary until this statement landed.
        $reached = Hints::find(
            [],
            'backend preview element header already rendered by the default renderer',
            6,
        );
        self::assertSame('content-element-preview', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * Registration, what the template is handed and what the header already
     * draws are all stated, and a preview reading "Testimonials element"
     * satisfies every one of them. What the statement names is read off the ten
     * previews in theme_camino's ContentPreviews/ on 14.3: all ten draw the
     * record's own payload, none renders a label naming the element, and the
     * only header any of them draws is a child's, in TextmediaTeaserGrid. The
     * field kinds are asserted one at a time, because "show a summary" changes
     * no line of the template somebody writes. It holds on every covered major:
     * the evidence is a 14 package, and what a preview owes the editor is not a
     * property of a major — how a field is reached carries the binding instead.
     */
    #[Test]
    public function aPreviewAnswerNamesTheFieldsThePreviewDrawsFrom(): void
    {
        foreach (Versions::majors() as $major) {
            $texts = implode("\n", array_column(
                Hints::byId('content-element-preview', $major)['hints'],
                'text',
            ));

            self::assertStringContainsString("the element's own payload", $texts);
            self::assertStringContainsString('without opening either', $texts);

            self::assertStringContainsString('the text columns stripped of their markup', $texts);
            self::assertStringContainsString('every file relation as a thumbnail', $texts);
            self::assertStringContainsString('a link field as its label', $texts);
            self::assertStringContainsString('a select as the translated label', $texts);
            self::assertStringContainsString('each child of an inline relation', $texts);

            self::assertStringContainsString('label naming the element', $texts);
        }

        $reached = Hints::find(
            [],
            'what a backend preview of a content element should show the editor',
            6,
        );
        self::assertSame('content-element-preview', array_column($reached['matchedHints'], 'id')[0] ?? '');
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
