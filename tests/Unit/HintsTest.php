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
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Tool\HintLookup;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Tool\TaskGuide;
use Typo3CmsMcp\Upkeep\Scenarios;

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
     * `R-KNW-064`. Two of the three findings are traps rather than absences:
     * `app-dir` is accepted where it is written and ignored where it is used,
     * and the `cms-cli` failure blames a version line that looks wrong and is
     * not. A hint that named the keys and not their two failures would leave a
     * session exactly where the report found it.
     */
    #[Test]
    public function installingTypo3BeneathTheExtensionNamesTheKeyThatMovesNothing(): void
    {
        // The feedback's own query, which used to reach the manifest and the
        // project scripts, and the narrowed one, which reached nothing at all.
        $reported = Hints::find(
            [],
            'Making a TYPO3 extension\'s own composer.json install a full TYPO3 into .build/ so the extension '
            . 'can be run locally: which config/extra keys apply, and which packages may be required.',
            6,
        );
        self::assertContains('extension-repository-installation', array_column($reported['matchedHints'], 'id'));

        $narrowed = Hints::find(
            [],
            'TYPO3 extension composer root package app-dir web-dir typo3/cms-cli local installation',
            6,
        );
        self::assertSame('extension-repository-installation', $narrowed['matchedHints'][0]['id']);

        $text = self::statementsOf('extension-repository-installation');

        // The keys that move the installation, and the one that is accepted and
        // then ignored — with the message, because that is what a session sees.
        self::assertStringContainsString('config.vendor-dir and config.bin-dir', $text);
        self::assertStringContainsString('Changing app-dir is not supported any more', $text);
        self::assertStringContainsString('whether or not web-dir is set beside it', $text);
        self::assertStringContainsString('belong in .gitignore', $text);
        self::assertStringContainsString('must be a subdirectory of Composer root directory', $text);

        // The constraint belongs to the core on every covered major, so the
        // root package requiring the package itself is what cannot resolve.
        self::assertStringContainsString('not the root package\'s to require', $text);
        self::assertStringContainsString('pins a major of its own', $text);

        // And the placement, which the empty directory beside it reads against.
        self::assertStringContainsString('package path is the Composer root itself', $text);
        self::assertStringContainsString('empty where it exists at all', $text);
        self::assertStringContainsString('public-assets', $text);
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

    /**
     * `D-KNW-055`. The corpus held the analyser half of the static-quality layer
     * and spelled the whole layer with the analyser's words, so every word a
     * fixer task carries reached something else: the reported query returned
     * `extension-repository-layout` on text alone, and "php-cs-fixer" reached
     * nothing out of 81 candidates.
     *
     * Read back out of an installed `typo3/coding-standards` v0.9.0, because the
     * package is in no checkout and in no environment here. Two of the three
     * things the report asked for do not hold: the excludes are directory names
     * matched at any depth rather than literal paths, and `.build` is hidden, so
     * the shipped `->in(__DIR__)` is not the trap it was reported as. What is
     * one is a build directory that is neither hidden nor one of the four names.
     */
    #[Test]
    public function theFixerHalfOfTheStaticQualityLayerIsStatedAndReachable(): void
    {
        // The words a fixer task arrives with, none of which is the analyser's.
        foreach ([
            'coding standards php-cs-fixer setup for an extension',
            'php-cs-fixer',
            'code style fixer extension',
            'editorconfig',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('extension-coding-standards', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('extension-coding-standards');

        // What the setup command takes, which the report had from `--help`.
        self::assertStringContainsString('type argument is optional', $text);
        self::assertStringContainsString('extra.typo3/cms.extension-key', $text);
        self::assertStringContainsString('--rule-set defaults to both sets', $text);

        // What the shipped configuration already excludes, and the case the
        // template does have to be corrected for. That `.build/` is not that
        // case is why the report's correction was dropped, and it is stated in
        // `D-KNW-055` rather than here.
        self::assertStringContainsString('directory name matched at any depth and case-sensitively', $text);
        self::assertStringContainsString('neither hidden nor one of the excluded names', $text);

        // The verdict is the dry run's, because the fixing run has none.
        self::assertStringContainsString('exits 0 whether it changed anything or not', $text);
        self::assertStringContainsString('exits 8 where a file would change', $text);

        // And the neighbour, which is the only route the reporting session had.
        self::assertStringContainsString('extension-static-analysis', $text);
    }

    /**
     * The other half of the same gap: the guide answered the task with
     * `intents: []` and the core patch checklist, so nothing named the skill
     * that owns the work.
     */
    #[Test]
    public function aCodeStyleFixerTaskIsRoutedToTheSkillThatOwnsIt(): void
    {
        $intents = TaskIntents::detect(
            'add a code style fixer (php-cs-fixer) to a standalone TYPO3 extension repository',
        );

        self::assertContains('coding-standards', array_column($intents, 'id'));
        self::assertSame(
            ['typo3-extension-testing'],
            TaskIntents::skills($intents, false),
        );

        // A word that names the subject without naming the work stays weak, so
        // the whole workflow is not loaded on it.
        $weak = TaskIntents::detect('reformat the generated output');
        self::assertSame(
            ['coding-standards' => 'weak'],
            array_column($weak, 'confidence', 'id'),
        );
        self::assertSame([], TaskIntents::skills($weak, false));
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

        // The rule the same session was handed in the tracker — "you must not
        // use f:image for anything but FAL resources" — and which the corpus
        // used to repeat by flattening the two image ViewHelpers into one
        // documented rule. Only f:uri.image carries it, on every checkout, and
        // f:image's own example is an EXT: path the core's suite covers.
        // `D-KNW-043`.
        self::assertStringContainsString('discouraged, not forbidden', $onFifteen);
        self::assertStringContainsString('f:uri.image\'s class documentation', $onFifteen);
        self::assertStringContainsString('SvgImageViewHelperTest', $onFifteen);
        self::assertStringContainsString('fallback storage', $onFifteen);
        self::assertStringNotContainsString('their own class documentation sends', $onFifteen);

        // Asked in the words of the rule rather than in the words of the API,
        // it used to reach nothing at all.
        $asQuoted = Hints::find([], 'must not use f:image for anything but FAL resources', 6);
        self::assertSame('fluid-resource-uris', $asQuoted['matchedHints'][0]['id']);

        // Read on both sides in .checkouts/: the SystemResource namespace, the
        // f:resource ViewHelper and File implementing PublicResourceInterface
        // are on 14.3 and on main alike, and on 13.4 there is none of it. The
        // report read the API as a 15 change because it came from 13.
        $guide = Registry::call('typo3_task_guide', ['task' => $query, 'targetVersion' => '13.4']);
        self::assertStringNotContainsString('System Resource API', $guide->text);
        self::assertStringContainsString('computes the URL itself through PathUtility', $guide->text);
    }

    /**
     * `R-KNW-063`. The audit this comes from had an extension's own
     * `Resources/Private/Layouts/Login.html` beside the core's
     * `Login.fluid.html` and could not say from the corpus which of the two
     * renders. It settled that by reading the resolver out of an installed
     * vendor tree — three shell round trips for the first half of the verdict.
     *
     * Read back the same way, because `typo3fluid/fluid` is in no checkout:
     * `TemplatePaths::resolveFileInPaths()` in 5.3.1 under `.environments/`
     * builds its candidates path by path over `array_reverse($paths)`, so the
     * whole file-name chain runs inside one root path before the next is
     * tried. Which root path is the later one is not in the changelog entry
     * that announced the chain, and the two mechanisms could have disagreed:
     * the core's own `TemplatePaths` sorts each list through
     * `ArrayUtility::sortArrayWithIntegerKeys()` first, on 12.4, 13.4 and 14.3
     * alike, and that sort is skipped for the whole list as soon as one key in
     * it is a string.
     */
    #[Test]
    public function aTemplateAnswerStatesThatTheFileNameFallbackRunsOncePerRootPath(): void
    {
        // The path the audit had in hand, and then the question in the words it
        // would be asked in — which used to fall to the PHP domain before
        // anything was scored, so no Fluid hint was ever a candidate.
        $audited = Hints::find(
            ['Resources/Private/Layouts/Login.html'],
            'fork of the core backend login layout',
            6,
        );
        self::assertSame('fluid-templates', $audited['matchedHints'][0]['id']);

        foreach ([
            'which template root path wins override order',
            'templateRootPaths order override core template',
            'does my extension Login.html still overload the core Login.fluid.html',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('fluid-templates', $ids, $task);
        }

        $text = self::statementsOf('fluid-templates');

        // The order, which is what decides between two files that both exist.
        self::assertStringContainsString('decided per root path', $text);
        self::assertStringContainsString('walks the root paths backwards', $text);
        self::assertStringContainsString('Foo.fluid.html, Foo.html, a bare Foo', $text);
        self::assertStringContainsString('first character uppercased', $text);

        // And which root path is the later one, which is the half no changelog
        // entry states and the reading had to settle.
        self::assertStringContainsString('sortArrayWithIntegerKeys', $text);
        self::assertStringContainsString('takes max+1 and does win', $text);
        self::assertStringContainsString('as soon as one key in it is a string', $text);

        // The three consequences of the same mechanism an audit needs, and
        // which the corpus had none of.
        self::assertStringContainsString('may not be used by an extension that still supports an older TYPO3 major', $text);
        self::assertStringContainsString('no longer has to begin with a capital', $text);
        self::assertStringContainsString('carries its own file extension leaves the chain', $text);
    }

    /**
     * The chain is the v14 half of that answer. Fluid 2.15.0 and 4.6.1, read in
     * `.environments/`, try the format and then the bare name and nothing else,
     * and the action name is capitalised before the lookup rather than after
     * it. A caller on 13 told about `.fluid.html` would go looking for a file
     * the resolver there never asks for.
     */
    #[Test]
    public function theFluidFileExtensionIsWithheldFromTheBranchesThatDoNotResolveIt(): void
    {
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('fluid-templates', $major)['hints'], 'text'),
        );

        self::assertStringContainsString('Foo.fluid.html, Foo.html, a bare Foo', $on(14));

        foreach ([12, 13] as $major) {
            self::assertStringNotContainsString('Foo.fluid.html, Foo.html, a bare Foo', $on($major));
            self::assertStringContainsString('Foo.html and then a bare Foo', $on($major));
            self::assertStringContainsString('no uppercase fallback', $on($major));

            // The root paths themselves are walked the same way on every major,
            // so that half carries no version boundary at all.
            self::assertStringContainsString('decided per root path', $on($major));
            self::assertStringContainsString('sortArrayWithIntegerKeys', $on($major));
        }
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

    /**
     * The authoring procedure above says what a correct translation file
     * declares, and an audit reads a rule the other way round: the file in front
     * of it declares nothing, and the loader takes <source> without saying so —
     * `D-KNW-050`. So the consequence is stated as the defect, and it is asked
     * for from both directions it arrives in, an audit of the file and an
     * upgrade that changed what the file does.
     */
    #[Test]
    #[TestWith(['full conformance audit of an extension, Resources/Private/Language/de.locallang.xlf'])]
    #[TestWith(['upgrade an extension to a new TYPO3 major, German labels render in English'])]
    public function aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt(string $task): void
    {
        $result = Registry::call('typo3_hint_lookup', ['task' => $task, 'targetVersion' => '14']);

        self::assertStringContainsString('declares no target-language is read as a default-language template', $result->text);
        self::assertStringContainsString('discards the <target> wording', $result->text);
        self::assertStringContainsString('Nothing is raised, logged or deprecated', $result->text);
        self::assertStringContainsString('No schema check reports this', $result->text);
        self::assertStringContainsString('leaves target-language optional', $result->text);
    }

    /**
     * The same file was read by the language that was asked for rather than by
     * an attribute of the file, so it kept its translations. A caller told
     * otherwise would go looking for a defect the branch does not have.
     */
    #[Test]
    #[TestWith(['12'])]
    #[TestWith(['13'])]
    public function whatAMissingTargetLanguageCostsIsWithheldFromTheBranchesItCostsNothingOn(string $targetVersion): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'language-files',
            'targetVersion' => $targetVersion,
        ]);

        self::assertStringNotContainsString('default-language template', $result->text);
        self::assertStringNotContainsString('No schema check reports this', $result->text);
        self::assertStringContainsString('new labels in English in the source XLF', $result->text);
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

    /**
     * The two statements a module needs after it is registered and before it
     * has a doc header: what its controller answers with, and what makes the
     * state it keeps per user survive the request.
     *
     * Both are read off `.checkouts/12.4` through `.checkouts/main`, where
     * `AboutController` builds its response the one way and every controller
     * setting a `ModuleData` property pushes it back itself — `set()` writes to
     * the object. Neither is bound: the shape is the same on all four.
     */
    #[Test]
    public function aBackendModuleNamesHowItAnswersAndHowItsStateIsPersisted(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'build a backend module controller that renders a listing and persists the filter the user set',
            'targetVersion' => '14',
        ])->text;

        self::assertStringContainsString('moduleTemplateFactory->create($request)', $result);
        self::assertStringContainsString('renderResponse(', $result);
        self::assertStringContainsString('pushModuleData(', $result);

        foreach ([12, 13, 14] as $major) {
            $statements = implode("\n", array_column(Hints::byId('backend-modules', $major)['hints'], 'text'));
            self::assertStringContainsString('pushModuleData(', $statements, 'not stated for ' . $major);
        }
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

        // An id is often the first id a caller learned, and the subject it
        // belongs to is larger than it. The neighbours are the hints in its own
        // domains, and it is not among them: the answer carries it in full.
        $available = array_column($result['availableHints'], 'id');
        self::assertNotSame([], $available, 'a hit names what stands beside it');
        self::assertNotContains('language-files', $available);
        $asked = Hints::byId('language-files');
        self::assertNotNull($asked);
        foreach ($available as $id) {
            $neighbour = Hints::byId($id);
            self::assertNotNull($neighbour, $id . ' is not a hint');
            self::assertNotSame(
                [],
                array_intersect($neighbour['domains'], $asked['domains']),
                $id . ' is not in a domain the hint that was asked for is in',
            );
        }
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
        $matched = array_column($hit['matchedHints'], 'id');
        $available = array_column($hit['availableHints'], 'id');
        self::assertNotSame([], $available, 'the index is what an answer is corrected with, hit or miss');
        self::assertSame([], array_intersect($matched, $available), 'what the answer carries in full is not offered again');

        $miss = Hints::find([], 'how do I write a good sonnet', 6);
        self::assertSame([], $miss['matchedHints']);
        self::assertContains('dependency-injection', array_column($miss['availableHints'], 'id'));
        foreach ($miss['availableHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
        }
    }

    /**
     * The near-miss is the answer the index is worth most on, and it was the
     * one answer without it.
     *
     * `feedback/2026-08-04-055626`: a query for a code style fixer matched
     * `extension-manifest`, `extension-repository-layout` and
     * `extension-boot-files` — three hints about something else — and
     * `availableHints` came back empty. So the field that would have named
     * `extension-static-analysis` was present and empty exactly where the
     * caller needed it, and the session reached that id only because a skill
     * file happens to name it in prose (`D-KNW-055`).
     *
     * The whole domain index rather than the categories the matched hints are
     * in, and the measurement is why: of the 81 hints the `php` domain holds,
     * 76 are in the category `PHP`, so narrowing to the categories that matched
     * this query drops five of them and none of the length. What it costs is
     * the length the empty answer in the same domains already carries — 81
     * entries at about 5.7 kB of text, against 127 for an id that does not
     * exist — so a near miss is now at most as long as the miss beside it, and
     * no answer of this tool grew a ceiling it did not have.
     */
    #[Test]
    public function anAnswerThatMatchedSomethingElseStillNamesTheIdsItDidNotReturn(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'coding standards php-cs-fixer setup for an extension',
            'paths' => ['composer.json', 'Classes/', 'ext_localconf.php'],
            'targetVersion' => '14.3',
        ]);

        $matched = array_column($result->data['hints'], 'id');
        $available = array_column($result->data['availableHints'], 'id');

        self::assertNotSame([], $matched, 'this query is a near miss, and a miss would prove nothing here');
        self::assertContains(
            'extension-static-analysis',
            $available,
            'the neighbouring subject the report had no route to',
        );
        self::assertSame([], array_intersect($matched, $available));
        // Named in the copy as well as in the payload: the id is what a second
        // call is made with, and a client that reads only the text has it too.
        self::assertStringContainsString('extension-static-analysis', $result->text);
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

    /**
     * What a deprecation carrying the docblock alone raises, which is nothing.
     *
     * A conformance audit went to the installed core for the severity of
     * `InfoboxViewHelper::STATE_ERROR` — `feedback/2026-08-03-164805`. The
     * corpus stated the marking as a pair and had no word for the half of it
     * the constant carries. Read in `.checkouts/14.3`: that file has no
     * `trigger_error` at all, and none can be added, because a class constant
     * is read without anything in the declaring class running. The other half
     * is what keeps the reading from being applied to a method — the trigger
     * for one can sit in the caller, or in the magic accessor of a
     * compatibility trait, so a file without one settles nothing there.
     */
    #[Test]
    public function whatADeprecationCarryingTheDocblockAloneRaisesIsStated(): void
    {
        $reached = array_column(
            Hints::find([], 'does a deprecated class constant raise a deprecation', 6)['matchedHints'],
            'id',
        );
        self::assertContains('deprecated-apis', $reached);

        $written = json_encode(Hints::byId('deprecated-apis'), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('no trigger_error can be attached to it anywhere', $written);
        self::assertStringContainsString('fatal error in the major that removes it', $written);
        self::assertStringContainsString('ClassConstantMatcher', $written);

        // The guard that keeps that reading off a method, and the measurement
        // it rests on: the attribute the core marks nothing with.
        self::assertStringContainsString('does not have to sit in the declaring body', $written);
        self::assertStringContainsString('#[\\\\Deprecated] attribute', $written);

        // The branch is not something the server is blind to where an
        // installation answers, which is where the changelog is read from.
        self::assertStringNotContainsString('does not know your branch', $written);
        self::assertStringContainsString('typo3_project_scope', $written);
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

    /**
     * `R-KNW-059`. The session this comes from ran `rm` on a cache directory
     * after a template edit, which is the one cache in the list that was
     * already correct: a compiled template is keyed on the file's modification
     * time, so the edit rewrote it. What kept answering with the old page was
     * the page cache, in another group and in the database.
     */
    #[Test]
    public function aChangeIsToldWhichCacheGroupHoldsItsOldOutput(): void
    {
        // The feedback's own query, which reached nothing.
        $result = Hints::find([], 'clearing the fluid_template (and code) caches after template changes', 6);
        self::assertContains('page-cache-flushing', array_column($result['matchedHints'], 'id'));

        // It arrives from three kinds of change, and they are three domains.
        foreach ([
            'which cache do I flush after a TypoScript change',
            'clear the caches after a TCA change',
            'my Fluid template change does not show on the page',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('page-cache-flushing', $ids, $task);
        }

        $text = self::statementsOf('page-cache-flushing');

        // The half that says no command is owed, and the half that says which
        // one is. Either alone leaves the caller where the feedback found them.
        self::assertStringContainsString('modification time', $text);
        self::assertStringContainsString('var/cache/code/fluid_template', $text);
        self::assertStringContainsString('--group=pages', $text);
        self::assertStringContainsString('--group=system', $text);
        self::assertStringContainsString('Typo3DatabaseBackend', $text);

        // `cache:flushtags` is the core's own command on 14 and on neither of
        // the majors below it.
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('page-cache-flushing', $major)['hints'], 'text'),
        );
        self::assertStringContainsString('cache:flushtags', $on(14));
        self::assertStringNotContainsString('cache:flushtags', $on(13));
        self::assertStringNotContainsString('cache:flushtags', $on(12));
    }

    /**
     * The nearest thing the corpus had was `caching`, whose statements are
     * about the `cacheConfigurations` entry and which frontend a payload wants.
     * Clearing a cache and declaring one are asked in the same words and are
     * not the same question (`D-KNW-027`).
     */
    #[Test]
    public function clearingACacheAndDeclaringOneAreDifferentQuestions(): void
    {
        $clearing = array_column(Hints::find([], 'how do I clear the TYPO3 caches', 6)['matchedHints'], 'id');
        self::assertSame('page-cache-flushing', $clearing[0]);

        foreach ([
            'declare a new cache for my extension',
            'inject a cache into a service instead of asking CacheManager',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('caching', $ids[0], $task);
        }
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

    /**
     * The two sentences a core session credited with deciding its work, and the
     * one it had to read neighbouring files for. It asked that the first two
     * survive any trim of the block, and an assertion is the only form this
     * repository has for a keep-request — `D-FBK-018`.
     */
    #[Test]
    public function aViewHelperPatchIsToldWhichTestItOwesAndWhichChangelogType(): void
    {
        $viewHelpers = self::statementsOf('fluid-viewhelpers');

        self::assertStringContainsString('Tests/Functional/ViewHelpers/', $viewHelpers);
        self::assertStringContainsString(
            'a ViewHelper needs a rendering context',
            $viewHelpers,
            'the reason, which is what makes the test layer obviously right',
        );
        self::assertStringContainsString('Documentation/Changelog/', $viewHelpers);
        self::assertStringContainsString(
            'documentation-changelog',
            $viewHelpers,
            'where the type of the entry is decided',
        );

        // The obligation is stated in the Fluid block and discharged in the
        // docs one, which a task about a ViewHelper bug never asks for: the
        // changelog intent matches the task text, and that text is about the
        // bug. The pointer is what crosses it, so the sentence it leads to has
        // to decide the type rather than only name the four.
        $changelog = self::statementsOf('documentation-changelog');
        self::assertStringContainsString('may require manual action', $changelog);
        self::assertStringContainsString('marked for a planned removal', $changelog);
        self::assertStringContainsString(
            'the directory of the minor version',
            $changelog,
            'where the file goes',
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

    /**
     * The corpus named the database settings alone, which made the two ways out
     * of the generated file look interchangeable: a session took the file over,
     * wrote that half back, and the installation answered every request with
     * exception 1396795884 for the trusted hosts pattern nobody had named —
     * `R-KNW-060`.
     *
     * The database-less half is the same omission read the other way. DDEV's
     * generator writes the DB block unconditionally, so an installation whose
     * connection comes from somewhere else has to take the file over.
     */
    #[Test]
    public function theDdevSettingsAnswerNamesEverySectionItGeneratesAndTheDatabaseItAssumes(): void
    {
        $result = Hints::find(
            [],
            'DDEV writes driver mysqli and host db into additional.php although the project runs on SQLite '
            . 'with omit_containers: [db]',
            6,
        );
        self::assertSame('project-configuration-files', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('project-configuration-files');
        self::assertStringContainsString('DB for its own database container', $text);
        self::assertStringContainsString('GFX for the ImageMagick in that container', $text);
        self::assertStringContainsString('MAIL for its mail catcher', $text);
        self::assertStringContainsString('SYS with trustedHostsPattern, devIPmask and displayErrors', $text);
        self::assertStringContainsString('supplying the three non-database sections', $text);
        self::assertStringContainsString('drops the trusted hosts pattern', $text);

        // What the generator cannot configure, and the one route left where it
        // cannot: the DB block merges over what settings.php carries.
        self::assertStringContainsString('no variant that reads the driver', $text);
        self::assertStringContainsString('omit_containers: [db] cannot leave the file generated', $text);
        self::assertStringContainsString('drop the DB section, keep GFX, MAIL and SYS', $text);
    }

    /**
     * `R-KNW-065`. The reported brief carried four PHP hints and none of them
     * was about booting anything, because the corpus said nothing about it: the
     * change type `operations` moved the checklist and could not move this, and
     * the four cards `D-SKL-012` put first landed the install rather than the
     * boot. What the task asks for is the second run of each step — a database
     * from elsewhere, a user that is already taken, a base that names another
     * host — and every one of those fails quietly.
     */
    #[Test]
    public function bootingADeclaredInstallationIsAnsweredBeforeThePhpFallback(): void
    {
        $reported = Hints::find(
            [],
            'Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, '
            . 'start the local environment, import the demo database and fileadmin, build frontend assets, '
            . 'create a backend user, verify the site responds',
            6,
        );
        self::assertSame('installation-boot', $reported['matchedHints'][0]['id']);

        $text = self::statementsOf('installation-boot');

        // Not the setup command, which is what a boot query used to be
        // answered with once it reached the corpus at all.
        self::assertStringContainsString('refuses an existing config/system/settings.php', $text);
        self::assertStringContainsString('typo3_project_scope reports the DDEV hooks', $text);

        // What closes the gap between an imported database and the code in
        // front of it, and what it deliberately leaves standing.
        self::assertStringContainsString('add, change, create_table and change_table', $text);
        self::assertStringContainsString('Nothing is dropped and nothing is renamed', $text);
        self::assertStringContainsString('database:updateschema is not the core\'s command', $text);
        self::assertStringContainsString('hash, pages and rootline caches', $text);

        // The two answers of the user step that only a script sees.
        self::assertStringContainsString('asked even under --no-interaction', $text);
        self::assertStringContainsString('1670797516', $text);

        // Why the site that was booted answers nothing on the host it is
        // reachable at.
        self::assertStringContainsString('host, scheme and port on the route as requirements', $text);
        self::assertStringContainsString('1396795884', $text);

        // And where the files were expected, which the database and not the
        // repository says.
        self::assertStringContainsString('sys_file_storage carries basePath and pathType', $text);
        self::assertStringContainsString('cleanup:localprocessedfiles', $text);
    }

    #[Test]
    public function whereAOneOffScriptMayNotGoNamesTheDocumentRootAsWellAsVar(): void
    {
        // The corpus placed such a script — Build/, and not var/ because var/
        // is ignored — and named no place that is served. A session debugging
        // the Record class wrote check_record.php into the root of a DDEV
        // project and ran it there, and this query reached nothing at all.
        $result = Hints::find(
            [],
            'writing and executing a PHP script in the live webroot to introspect core classes',
            6,
        );
        self::assertSame('project-build-and-scripts', $result['matchedHints'][0]['id']);

        $placement = Hints::find([], 'where do I put a one-off script', 6);
        self::assertContains('project-build-and-scripts', array_column($placement['matchedHints'], 'id'));

        // Named by what configures it rather than by a path, because the path
        // is a setting: typo3/cms-composer-installers defaults web-dir to
        // public and the root composer.json is what moves it.
        $text = self::statementsOf('project-build-and-scripts');
        self::assertStringContainsString('extra.typo3/cms.web-dir', $text);
        self::assertStringContainsString('public/ where that key is absent', $text);
        self::assertStringContainsString('/var/www/html', $text);
        // Both reasons: the served one is what only the document root has, and
        // the outliving one is what also covers the project root above it,
        // which is where the reported file actually went.
        self::assertStringContainsString('reachable over HTTP', $text);
        self::assertStringContainsString('after the run that wrote it ends', $text);
        self::assertStringContainsString('it goes into Build/, or it is not written at all', $text);
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
    public function thePublicAssetAnswerSeparatesTheSupportedRouteFromTheInternalStaticBesideIt(): void
    {
        // The query an audit of a v14 extension arrived with. What came back
        // named the factory and the publisher, and named
        // getPublicResourceWebPath() as "what computes such a URL here" with no
        // word of its deprecation, because that statement was banded up to 14.
        // The method the audited code actually called was in neither sentence,
        // so the session read PathUtility itself and stopped at the signature —
        // one line above the @internal. `D-KNW-051`.
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'Backend JavaScript ES modules, import maps and public assets shipped by an '
                . 'extension for the TYPO3 backend',
            'paths' => [
                'Configuration/JavaScriptModules.php',
                'Resources/Public/JavaScript/',
                'Resources/Public/Css/',
            ],
            'targetVersion' => '14.3',
        ]);

        self::assertStringContainsString('getSystemResourceUri', $result->text);
        self::assertStringContainsString('before this major reaches LTS', $result->text);
        self::assertStringContainsString('E_USER_DEPRECATED', $result->text);
        self::assertStringNotContainsString(
            'getPublicResourceWebPath() is what computes such a URL here',
            $result->text,
        );

        // The three classes the route is injected from, against the migration
        // example that imports them from a namespace none of them is in.
        self::assertStringContainsString('TYPO3\CMS\Core\SystemResource', $result->text);

        // Asked in the words of the API rather than in the words of the
        // subject — R-KNW-002.
        $asAsked = Hints::find([], 'PathUtility::getSystemResourceUri for an EXT: image path', 6);
        self::assertSame('public-assets', $asAsked['matchedHints'][0]['id']);

        // On 13 the deprecation had not happened and neither had the API.
        $onThirteen = Registry::call('typo3_hint_lookup', [
            'task' => 'public assets shipped by an extension',
            'paths' => ['Resources/Public/Css/'],
            'targetVersion' => '13.4',
        ]);
        self::assertStringContainsString(
            'getPublicResourceWebPath() is what computes such a URL here',
            $onThirteen->text,
        );
        self::assertStringNotContainsString('getSystemResourceUri', $onThirteen->text);
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

    /**
     * Picking the pid is the first question and the corpus answered only the
     * second one, so a session seeding a table of its own guessed at both the
     * page and the storage folder's role — `R-KNW-058`. What the doktype allows
     * is the same on every covered major; only where the list is declared moved,
     * which is the one statement carrying a range.
     */
    #[Test]
    public function thePlacementAnswerSaysWhichPageMayHoldTheRecord(): void
    {
        $statements = static fn(?int $major): string => implode("\n", array_column(
            Hints::byId('datahandler-placement', $major)['hints'] ?? [],
            'text',
        ));
        $text = $statements(null);

        self::assertStringContainsString('doktype 254', $text, 'the folder that allows every table');
        self::assertStringContainsString('ignorePageTypeRestriction', $text, 'how a table joins the four');
        self::assertStringContainsString('ctrl.rootLevel', $text, 'what decides a pid of 0');
        self::assertStringContainsString(
            'writes a log entry and carries on',
            $text,
            'the refusal that raises nothing',
        );
        self::assertStringContainsString('admin does not get past', $text);

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString('doktype 254', $statements($major));
            self::assertStringContainsString(
                'Which tables a page type allows is declared in',
                $statements($major),
                'every major says where the list is declared on it',
            );
        }

        self::assertStringContainsString('allowedRecordTypes', $statements(14));
        self::assertStringContainsString('PageDoktypeRegistry itself', $statements(13));

        $reached = Hints::find([], 'which page may hold a record of my own table, storage folder or standard page', 6);
        self::assertSame('datahandler-placement', array_column($reached['matchedHints'], 'id')[0] ?? '');
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
    #[DataProvider('theHintsTheCoreIsObligedByToo')]
    public function aHintTheCoreIsAlsoObligedByDeclaresNoAudience(string $id): void
    {
        $scopes = array_column(Hints::load(), 'scope', 'id');

        self::assertNull($scopes[$id], $id . ' declares an audience the core is obliged by too');
    }

    /** @return array<string, array{0: string}> */
    public static function theHintsTheCoreIsObligedByToo(): array
    {
        return [
            'the sitepackage layout' => ['sitepackage-layout'],
            'the initial content a sitepackage ships' => ['sitepackage-initial-content'],
            'site sets' => ['site-sets'],
        ];
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
     * The corpus said every test class gets a database of its own and never what
     * becomes of it, so a session watched the set grow, could not tell a
     * leftover from the database the site runs on, and started accounting for
     * records by hand (`D-KNW-022`). «live database versus test database»
     * reached no hint at all.
     *
     * Read rather than recalled, on `.checkouts/testing-framework` at `8`, `9`
     * and `main`, which agree line for line: the single `dropDatabase()` sits in
     * `Testbase::setUpTestDatabase()` and runs at the *start* of a class's first
     * test — drop where it exists, then create — `FunctionalTestCase::tearDown()`
     * removes a site configuration directory and one cache file, and no ref
     * declares `tearDownAfterClass()`. `getInstanceIdentifier()` is
     * `substr(sha1(static::class), 0, 7)`, so the suffix is per class and the
     * set is bounded by the classes rather than by the runs; and
     * `DatabaseSnapshot` keeps its rows in memory or in a `.snapshot.sqlite`
     * written by the first test of the same run, so a leftover is never read
     * back.
     *
     * Both cases that carry no such name are asserted, because either one left
     * out makes the suffix too strong as the mark: `$initializeDatabase = false`
     * returns before `setUpTestDatabase()` is reached, and under `pdo_sqlite`
     * the per-class database is a file below `functional-sqlite-dbs/` with no
     * `_ft` name anywhere.
     */
    #[Test]
    public function thePerClassDatabaseAnswerSaysWhatSurvivesTheRun(): void
    {
        $reaches = static fn(string $task): array => array_column(
            Hints::find([], $task, 6)['matchedHints'],
            'id',
        );

        self::assertContains('project-extension-tests', $reaches('live database versus test database'));
        self::assertContains('project-extension-tests', $reaches('what happens to the test databases after the run'));
        self::assertContains('project-extension-tests', $reaches('clean up functional test databases'));

        $text = self::statementsOf('project-extension-tests');

        // What survives, and what reclaims it.
        self::assertStringContainsString('Nothing drops one after the run ends', $text);
        self::assertStringContainsString('Testbase::setUpTestDatabase()', $text, 'where the only drop is');
        self::assertStringContainsString('tearDownAfterClass()', $text, 'what does not exist');
        self::assertStringContainsString('only when that same class runs again', $text);

        // Why the set is bounded, which is what makes dropping them all safe.
        self::assertStringContainsString('substr(sha1(<test class>), 0, 7)', $text);
        self::assertStringContainsString('costs the next run nothing', $text);

        // What tells a leftover from the live database, and the two cases that
        // carry no such name.
        self::assertStringContainsString('_ft<7 hex>', $text);
        self::assertStringContainsString('nothing a functional test writes reaches the configured database', $text);
        self::assertStringContainsString('$initializeDatabase = false', $text);
        self::assertStringContainsString('functional-sqlite-dbs/test_<7 hex>.sqlite', $text);
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
     * The half `D-KNW-028` left unsaid, which a session then filled in from the
     * one call path it had read: that image processing needs a FAL object
     * (`D-KNW-042`). Both entry points it named as the way past FAL resolve
     * their string through `ResourceFactory` first, so the correction is part
     * of the statement rather than a footnote to it.
     */
    #[Test]
    public function whereFalStopsInTheImagePipelineIsAnswered(): void
    {
        $reached = array_column(
            Hints::find([], 'does image processing require a FAL object', 6)['matchedHints'],
            'id',
        );
        self::assertContains('fal-processing', $reached);

        $text = self::statementsOf('fal-processing');

        self::assertStringContainsString('getForLocalProcessing(false)', $text, 'where FAL stops');
        self::assertStringContainsString('takes a path string', $text, 'what runs below it');
        self::assertStringContainsString('GraphicalFunctions', $text, 'the layer under the task');
        self::assertStringContainsString('ImageInfo', $text, 'dimensions without a FAL record');
        self::assertStringContainsString('not a way past FAL', $text, 'what getImgResource does with a path');
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
     * The corpus said only the root page id is remapped on import, which holds
     * for a configuration shipped in `Initialisation/Site/` and not for one
     * carried inside the export file: `Import::processSiteConfigurations()`
     * overwrites `base` as well, so a seeded distribution answers 404 at the
     * project root — `D-KNW-048`.
     */
    #[Test]
    public function theImportAnswerSaysWhatItRewritesInASiteConfiguration(): void
    {
        $reached = array_column(
            Hints::find([], 'imported site base url 404 root', 6)['matchedHints'],
            'id',
        );
        self::assertContains('initial-content-references', $reached);

        $text = implode("\n", array_column(Hints::byId('initial-content-references')['hints'], 'text'));

        self::assertStringContainsString('overwrites base with /<identifier>/', $text);
        self::assertStringContainsString('config/sites/<identifier>/config.yaml', $text, 'where it is corrected');
        // The two conditions under which the method does not run, both of which
        // read like the cause of a base nobody shipped.
        self::assertStringContainsString('not an admin', $text);
        self::assertStringContainsString('ImportSiteConfigurationsOnPackageInitialization', $text);

        // The two statements that say only rootPageId is remapped now name the
        // route they hold for.
        self::assertStringContainsString('The Initialisation/Site/ route remaps the root page id', $text);

        $routing = implode("\n", array_column(Hints::byId('record-routing')['hints'], 'text'));

        self::assertStringContainsString('shipped in Initialisation/Site/ only rootPageId is remapped', $routing);
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
     * The layer was covered and unreachable. `browser-tests` could be found only
     * by words that already name the answer — playwright, e2e, spec.ts — and the
     * caller who needs it most is the one who has not yet decided that a browser
     * is involved, so a question about whether an element renders correctly
     * stopped at `content-elements` and was answered with how to register it
     * (`D-KNW-017`).
     *
     * The crossing is a statement in the two hints those questions do reach,
     * rather than terms on `browser-tests` itself, and that was measured rather
     * than preferred: «content element» detects as Fluid and TypoScript, the
     * hint is PHP and TypeScript, so for half of these queries the domain gate
     * drops it before a single term is scored. The one term that did carry the
     * other half — `backend preview` — put a testing hint into "the backend
     * preview of my content element is empty" as well, which is what that entry
     * named as the way to get this wrong.
     */
    #[Test]
    public function aRenderedVerificationQuestionReachesTheLayerThatVerifiesIt(): void
    {
        foreach ([
            'verifying the rendered testimonials frontend and the backend page module preview',
            'verify that the content element renders correctly on the live site',
            'check the backend page module preview of a content element',
            'how do I verify rendered output of a content element',
        ] as $query) {
            $ids = array_column(Hints::find([], $query, 6)['matchedHints'], 'id');
            self::assertNotSame([], $ids, $query . ' reaches nothing at all');
            self::assertStringContainsString(
                'browser-tests',
                self::statementsOf(...$ids),
                sprintf('«%s» reaches %s, and none of them names the layer that verifies it', $query, implode(', ', $ids)),
            );
        }

        // The cost the crossing was chosen against: a question about the preview
        // itself is not a question about tests, and must not pay for one.
        self::assertNotContains(
            'browser-tests',
            array_column(Hints::find([], 'the backend preview of my content element is empty', 6)['matchedHints'], 'id'),
        );
    }

    /**
     * The same gap from the other side, and the one `bin/cli hints:coverage`
     * reports on: `browser-tests` was reached by no scenario prompt at all.
     *
     * The two prompts written for this layer are the two that ask for the
     * outcome — a smoke test before every deployment, browser coverage after a
     * regression got through the PHP suite — and neither names Playwright.
     * They are read from the contracts rather than restated here, so a prompt
     * that is rewritten into the vocabulary of the answer fails this instead of
     * passing it quietly.
     */
    #[Test]
    public function theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome(): void
    {
        $contracts = Scenarios::contracts();
        foreach (['SITE-06', 'SKILL-06'] as $id) {
            self::assertArrayHasKey($id, $contracts);
            $ids = array_column(Hints::find([], $contracts[$id]['prompt'], 6)['matchedHints'], 'id');
            self::assertContains('browser-tests', $ids, $id . ' reaches ' . (implode(', ', $ids) ?: 'nothing'));
        }
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
     * `R-KNW-055`. The question is "I am changing rendered output, what asserts
     * it", and before this statement existed the corpus answered a different
     * one: `hints:probe` with the reporting session's own query returned
     * system-extension-boundaries, project-build-and-scripts,
     * routing-request-handling and caching, none of them about a test
     * expectation.
     *
     * The two halves are asserted separately because either one alone leaves
     * the search wrong, and both were measured on `.checkouts/main` at
     * `c71b2bdb2f` over `typo3/sysext/*\/Tests/`. Grepping the tree for
     * `fileadmin/|typo3temp/assets|Resources/Public/` reaches 188 files and 24
     * of the 26 the session had to touch; the same pattern restricted to
     * `*Test.php` reaches 91 and 21, losing all three
     * `backend/Tests/Functional/Template/Fixtures/*CopyToClipboard.php` files,
     * which are the ones holding the expectations. The two it never reaches
     * hold none: `ShortcutButtonTest` requires its expected markup from those
     * fixtures, and `FluidEmailTest` asserts no URI at that commit at all.
     *
     * Searching for the value rather than around it is the other half:
     * `\?[0-9]{9,10}`, the rendered cache buster itself, reaches 1 of the 26.
     * The ratio is a property of the corpus rather than of the asset area —
     * `<a href=|<img src=|<script src=` sits at 44 of 71 in `*Test.php`, `&amp;`
     * at 49 of 79 — and it holds on 13.4 (85 of 179) and 14.3 (94 of 192) as it
     * does on main, so the statement carries no version range.
     */
    #[Test]
    public function aRenderedOutputChangeIsToldWhereTheExpectationsHide(): void
    {
        self::assertContains(
            'core-tests',
            array_column(Hints::find([], 'I am changing rendered output, what asserts it', 6)['matchedHints'], 'id'),
            'the question is not reachable in the words the reporting session asked it in',
        );

        $text = implode("\n", array_column((array) Hints::byId('core-tests')['hints'], 'text'));

        // Where they hide: not in the file the caller would think to search.
        self::assertStringContainsString('typo3/sysext/*/Tests/', $text);
        self::assertStringContainsString('*Test.php', $text);
        self::assertStringContainsString('.message', $text);
        self::assertStringContainsString('contentMatchRegExp', $text);

        // And what to search for, which is never the value that changed.
        self::assertStringContainsString('around the value rather than the value itself', $text);
        self::assertStringContainsString('quoted-printable', $text);
        self::assertStringContainsString('{$fileMtimeActions}', $text);
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
            // never reused, so it says nothing about a branch. A doktype value
            // is the same: it is the number in the pages row and in the page
            // tree, and 254 has been the folder for longer than any covered
            // branch. None of them dates the statement against a TYPO3 branch,
            // which is the only thing this is looking for. Each is worth
            // carrying because it is the symptom a caller arrives with — so each
            // is written with its word in front, "HTTP 404" and "doktype 254"
            // rather than bare, which is what makes them exemptible here without
            // also exempting a count that happens to be three digits long.
            $text = (string) preg_replace(
                ['/\bPSR-\d+/i', '/\bXLIFF \d+\.\d+/i', '/\bHTTP \d{3}\b/i', '/\bexception \d{10}\b/i', '/\bdoktype \d{1,3}\b/i'],
                ['PSR', 'XLIFF', 'HTTP', 'exception', 'doktype'],
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

    /**
     * The three things that make a suite command runnable unattended, on the
     * patch a session reported them from.
     *
     * `feedback/2026-08-01-121852` reviewed the AssetCollector deprecation and
     * reported the answer to these paths as the one that "ran clean first try",
     * naming CI=true, the `--` passthrough and `-n` for cgl. Narrowing was
     * already held — by theSuiteListItselfIsFilteredByTheBranchItIsAskedFor and
     * aPathNamedInTheQueryNarrowsTheSuitesAsAnExplicitPathWould — and the
     * invocation itself by nothing: `targeted` could have been dropped from
     * every entry in knowledge/test-suite-hints.json and no test would have
     * noticed, while `catalog:check` verifies the `-s <suite>` of a command and
     * not its options.
     *
     * All three are read in `.checkouts/main/Build/Scripts/runTests.sh`: line 6
     * branches on `CI`, `shift $((OPTIND - 1))` hands what follows `--` to the
     * tool, and `-n` sets CGLCHECK_DRY_RUN, which the cgl suite turns into
     * `--dry-run --diff`.
     */
    #[Test]
    public function theTargetedInvocationSurvivesWithTheThreeThingsThatMakeItRunnable(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'query' => 'functional unit cgl phpstan',
            'paths' => [
                'typo3/sysext/core/Classes/Page/AssetCollector.php',
                'typo3/sysext/core/Tests/Unit/Page/AssetCollectorTest.php',
                'typo3/sysext/frontend/Tests/Functional/ContentObject/ImageContentObjectTest.php',
            ],
            'targetVersion' => 'main',
        ]);

        $targeted = [];
        foreach ($answer->data['suites'] as $suite) {
            self::assertStringStartsWith('CI=true ', $suite['command'], $suite['suite'] . ' is handed over without CI=true');
            if ($suite['targeted'] !== null) {
                self::assertStringStartsWith('CI=true ', $suite['targeted']);
                $targeted[$suite['suite']] = $suite['targeted'];
            }
        }

        self::assertSame('CI=true ./Build/Scripts/runTests.sh -s cgl -n', $targeted['cgl'] ?? null);
        self::assertStringContainsString(' -- ', $targeted['unit'] ?? '');
        self::assertStringContainsString(' -- ', $targeted['functional'] ?? '');
        self::assertStringContainsString('Targeted run while iterating:', $answer->text);

        // And the note that says why the passthrough is there at all, which is
        // what tells a caller the form generalises past the examples.
        self::assertStringContainsString(
            'Everything after `--` is handed to the underlying tool unchanged',
            implode("\n", TestSuiteHints::invocation()['notes'])
        );
    }

    /**
     * `R-KNW-055`, the other half. `TestSuiteHints::invocation()` is emitted
     * with every `typo3_test_run_guide` answer, so the iterate-narrowly note
     * reaches the one caller it is wrong for whether or not they asked about
     * tests — and it is right for every other change, which is why what lands
     * beside it is the exception rather than a rewrite.
     *
     * Both notes are asserted together because the exception is only readable
     * next to the rule: on its own it says run the whole suite, which is the
     * advice `feedback/2026-08-02-145003` was already given and the one that
     * cost it fifteen runs. What makes it cheap is the search before the run,
     * so the note points at the statement that says where to aim it.
     */
    #[Test]
    public function theIterateNarrowlyNoteCarriesTheOneChangeItIsWrongFor(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString(
            'run a single test file or a single test method instead of a whole suite',
            $notes,
            'the rule the exception is an exception to',
        );
        self::assertStringContainsString('alters rendered output', $notes);
        self::assertStringContainsString('core-tests', $notes, 'the exception says where the expectations hide');
        self::assertStringContainsString('rather than widening the path set', $notes);
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

    /**
     * A task that changes nothing is not answered with the steps a patch owes.
     *
     * R-GUI-006, and the call is the one the requirement was re-run with: a
     * conformance review of a site package, classified as nothing because the
     * enum had no value for it. The three items asserted away are what came
     * back for it — a focused patch, test coverage, and a commit message for a
     * session that commits nothing.
     */
    #[Test]
    public function aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist(): void
    {
        self::assertContains('audit', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $described = Registry::call('typo3_task_guide', [
            'task' => 'review the TYPO3 project and site package',
        ]);
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'Is this sitepackage written the way TYPO3 14 expects',
            'changeType' => 'audit',
        ]);

        foreach ([$described, $stated] as $brief) {
            self::assertContains('audit', array_column($brief->data['intents'], 'id'));

            $checklist = implode("\n", $brief->data['checklist']);
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            self::assertNotContains(
                'typo3_commit_message_guide',
                array_column($brief->data['nextTools'], 'tool'),
                'the follow-up calls name the step the checklist dropped',
            );

            // What it does owe instead: a finding is a piece of read code, and
            // a check nobody ran proves nothing about what it covers.
            self::assertStringContainsString('rule or documentation it contradicts', $checklist);
            self::assertStringContainsString('gap in the check layer', $checklist);
        }

        // The caller's own classification keeps the skeleton: the same task
        // text, stated as a deprecation, is authoring work seen from the
        // reviewer's side and keeps every step that patch owes. What the words
        // recognized is appended rather than dropped, because the other caller
        // — a reviewer naming the type of the patch under review — cannot be
        // told apart from this one (`D-GUI-009`).
        $authoring = Registry::call('typo3_task_guide', [
            'task' => 'Review the patch that deprecates the AssetCollector media handling',
            'changeType' => 'deprecation',
        ]);

        self::assertContains('Keep the patch focused on the stated task.', $authoring->data['checklist']);
        self::assertContains('audit', array_column($authoring->data['intents'], 'id'));
        $both = implode("\n", $authoring->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $both);
        self::assertStringContainsString('typo3_commit_message_guide', $both);
    }

    /**
     * A brief names the task skill that owns the work.
     *
     * D-SKL-013. `skills/base.md` has told every task since 2026-07-31 that
     * this call returns "the workflow this task belongs to", and the
     * `instructions` every client receives at initialize say it "hands the
     * parts that have their own workflow to the skill that owns them" — while
     * `TaskGuide` named no skill at all. `feedback/2026-08-01-003356` is what
     * that cost: a session in `site-new` built a content element with a custom
     * backend preview, loaded no skill, and guessed at facts
     * `typo3-content-element-development` exists to route.
     *
     * It is named alone, which is the half `D-ANS-050` closed: "testimonials"
     * matched the `test` of the tests intent, so the same call named
     * `typo3-extension-testing` ahead of the skill that owns the task, and an
     * assertion that the right name is among them holds just as well when a
     * whole workflow the task has nothing to do with is loaded first.
     */
    #[Test]
    public function aBriefNamesTheSkillThatOwnsTheWork(): void
    {
        // That session's own task, re-run.
        $element = Registry::call('typo3_task_guide', [
            'task' => 'build a testimonials content element with a custom backend preview',
            'changeType' => 'feature',
            'targetVersion' => '14',
        ]);

        self::assertSame(['typo3-content-element-development'], $element->data['skills']);
        self::assertStringContainsString('typo3-content-element-development', $element->text);
        self::assertStringNotContainsString('typo3-extension-testing', $element->text);
        self::assertNotContains('tests', array_column($element->data['intents'], 'id'));
        // Above the payload, because a caller in the wrong workflow is in it
        // for the whole answer.
        self::assertLessThan(
            (int) strpos($element->text, 'Hints:'),
            (int) strpos($element->text, 'Owned by:'),
        );

        // The other half of the same question: a session that arrived through a
        // skill is told which one owns the task, and the two sides of an audit
        // are two skills whose own descriptions hand each other away.
        $package = Registry::call('typo3_task_guide', [
            'task' => 'TYPO3 extension conformance audit of the site package',
            'paths' => ['packages/printworks_sitepackage'],
        ]);
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'review the patch on this core branch',
            'paths' => ['typo3/sysext/frontend/Classes/ContentObject/ContentObjectRenderer.php'],
        ]);

        self::assertSame(['typo3-extension-conformance'], $package->data['skills']);
        self::assertSame(['typo3-core-patch-review'], $patch->data['skills']);

        // A weak match routes nothing. The word named the subject without
        // naming the work, and a whole workflow loaded on one of those is the
        // wrong answer rather than a partly wrong one.
        $weak = Registry::call('typo3_task_guide', [
            'task' => 'restyle the slider on the homepage',
            'paths' => ['packages/printworks_sitepackage'],
        ]);

        self::assertContains('content-element', array_column($weak->data['intents'], 'id'));
        self::assertSame([], $weak->data['skills']);
        self::assertStringNotContainsString('Owned by:', $weak->text);
    }

    /**
     * A backend-module task reaches the skill that owns it (R-SKL-001).
     *
     * The words of `SITE-07` matched `backend-ui` and nothing else, and that
     * intent names no skill: the brief answered with the markup a module writes
     * while the workflow that decides where the module sits, who may open it
     * and how it is proven stayed unloaded. Inside the core the same task is a
     * patch and owes what a patch owes, which is the split `tests` and `audit`
     * already make.
     */
    #[Test]
    public function aBackendModuleTaskReachesTheSkillThatOwnsIt(): void
    {
        $package = Registry::call('typo3_task_guide', [
            'task' => 'add a backend module to our site package that lists imported records',
            'paths' => ['packages/printworks_sitepackage'],
        ]);
        $core = Registry::call('typo3_task_guide', [
            'task' => 'fix the doc header button in the list module',
            'paths' => ['typo3/sysext/backend/Classes/Controller/RecordListController.php'],
        ]);

        self::assertSame(['typo3-backend-module-development'], $package->data['skills']);
        self::assertSame(['typo3-core-patch-development'], $core->data['skills']);
    }

    /**
     * Work that operates an installation is answered as a boot, not as a patch
     * and not as a review.
     *
     * D-GUI-008, and the first call is `feedback/2026-08-03-154508`'s own:
     * booting a Composer project from a fresh clone had no change type of its
     * own, so `unknown` handed it the patch skeleton, and the one intent it
     * reached was `installation-setup` — matched on `install dependencies`,
     * which is Composer's install and not TYPO3's.
     */
    #[Test]
    public function workThatOperatesAnInstallationIsAnsweredWithABootBrief(): void
    {
        self::assertContains('operations', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $confidences = static fn(ToolResult $brief): array => array_column(
            $brief->data['intents'],
            'confidence',
            'id',
        );
        $described = Registry::call('typo3_task_guide', [
            'task' => 'Boot up a TYPO3 project locally for the first time from a fresh clone: install '
                . 'dependencies, start the local environment, import the demo database and fileadmin, build '
                . 'frontend assets, create a backend user, verify the site responds',
        ]);
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'Bring the demo installation this repository declares up on this machine',
            'changeType' => 'operations',
        ]);

        foreach ([$described, $stated] as $brief) {
            self::assertSame('strong', $confidences($brief)['installation-operations'] ?? null);

            $checklist = implode("\n", $brief->data['checklist']);
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            self::assertNotContains(
                'typo3_commit_message_guide',
                array_column($brief->data['nextTools'], 'tool'),
                'the follow-up calls name the step the checklist dropped',
            );

            // Nor the review's, which is the second half of the fork: a boot
            // reports what it produced, and a surface it did not reach is not a
            // finding it is withholding.
            self::assertStringNotContainsString('Report what the review did not reach', $checklist);
            self::assertStringContainsString('the URL the installation answers on', $checklist);

            // What it owes instead: the environment this repository declares,
            // and the import that seeds it.
            self::assertStringContainsString('.ddev/config.yaml', $checklist);
            self::assertStringContainsString('typo3 extension:setup', $checklist);
        }

        // The value is half of it; the needle is the other half. The reported
        // call no longer reaches the intent whose five items are all properties
        // of the setup command.
        self::assertArrayNotHasKey('installation-setup', $confidences($described));

        // And a task that really does create an installation still does —
        // `feedback/2026-08-03-162826`, which D-GUI-008 names as the measure.
        $installing = Registry::call('typo3_task_guide', [
            'task' => 'install TYPO3 14.3.5 unattended from a shell script so that "ddev start" sets the '
                . 'instance up on its own',
        ]);

        self::assertSame('strong', $confidences($installing)['installation-setup'] ?? null);
        self::assertSame('weak', $confidences($installing)['installation-operations'] ?? null);
        self::assertContains('Keep the patch focused on the stated task.', $installing->data['checklist']);

        // A stated change type that does change something keeps the skeleton,
        // so the patch steps stay for work that boots something and fixes it —
        // D-GUI-008's own **Wrong if**. What the words recognized is appended
        // below them rather than dropped (`D-GUI-009`).
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'fix the deploy hook so the import runs when booting the installation',
            'changeType' => 'bugfix',
        ]);

        self::assertSame('strong', $confidences($patch)['installation-operations'] ?? null);
        self::assertContains('Keep the patch focused on the stated task.', $patch->data['checklist']);
        self::assertStringNotContainsString(
            'the URL the installation answers on',
            implode("\n", $patch->data['checklist']),
        );
    }

    /**
     * A review brief names what the change removes, whatever the task says.
     *
     * R-GUI-010, and the call is `feedback/2026-08-01-115711`'s own: a core
     * patch review that under-stated the removal of an `@internal`
     * `GifBuilder` method until the user pushed back. `D-GUI-004` read why
     * nothing could have named it — the `breaking` intent matches on words a
     * review's task text does not have, because what a diff takes away is what
     * the review is about to find out — so the surface is stated rather than
     * matched.
     */
    #[Test]
    public function aReviewBriefNamesWhatTheChangeRemoves(): void
    {
        $review = Registry::call('typo3_task_guide', [
            'task' => 'review the core patch replacing GD-based error thumbnails with a static SVG placeholder',
        ]);

        self::assertContains('audit', array_column($review->data['intents'], 'id'));
        self::assertNotContains('breaking', array_column($review->data['intents'], 'id'));

        $checklist = implode("\n", $review->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $checklist);
        foreach ([
            'typo3/sysext/install/Configuration/ExtensionScanner/Php/',
            'Breaking or Deprecation changelog file',
            '[!!!]',
            'CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst',
        ] as $owed) {
            self::assertStringContainsString($owed, $checklist);
        }

        // The rule the surface states is the core's own, read in
        // `.checkouts/main` for `D-ANS-035`, and not the feedback's: the marker
        // is not waived by `@internal`, because `@internal` does not decide
        // whether the removal is breaking at all.
        self::assertStringContainsString('whether anything outside the core calls it', $checklist);

        // Outside the core the enumeration is still owed and the core's own
        // obligations are not: a sitepackage has no changelog and no scanner.
        $extension = Registry::call('typo3_task_guide', [
            'task' => 'Is this sitepackage written the way TYPO3 14 expects',
            'changeType' => 'audit',
        ]);

        $outside = implode("\n", $extension->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $outside);
        self::assertStringNotContainsString('ExtensionScanner', $outside);
        self::assertStringNotContainsString('runTests.sh', $outside);
    }

    /**
     * The call the feedback reported, answered from the side that still had the
     * failure.
     *
     * It states the type of the patch under review rather than of its own work,
     * so the audit intent was filtered out and the surface `R-GUI-010` states
     * never reached it (`D-GUI-009`).
     */
    #[Test]
    public function aReviewThatStatesTheTypeOfThePatchUnderReviewNamesWhatItRemoves(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'review the core patch replacing GD-based error thumbnails with a static SVG placeholder',
            'changeType' => 'cleanup',
        ]);

        $checklist = implode("\n", $result->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $checklist);
        self::assertStringContainsString('typo3/sysext/install/Configuration/ExtensionScanner/Php/', $checklist);

        // The stated type keeps its skeleton and its own item, so the caller
        // who was authoring after all loses nothing to the appended half.
        self::assertContains('Keep the patch focused on the stated task.', $result->data['checklist']);
        self::assertStringContainsString('Keep the cleanup mechanical', $checklist);

        // Reading a patch is not putting one up. The Gerrit steps reached this
        // brief through "review" as a needle of the submission intent, which is
        // the workflow for the push and the patch set.
        self::assertNotContains('submission', array_column($result->data['intents'], 'id'));
        self::assertStringNotContainsString('refs/for/', $checklist);
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
            'paths' => ['typo3/sysext/backend/Classes/Form'],
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

    /**
     * R-GUI-008. The session that reported this had every fact it needed and
     * still read a report about f:image as an API question — is the value
     * passed of the type the argument accepts — where the product asks what the
     * editor and the visitor get once the image is replaced
     * (`feedback/2026-08-02-145043`).
     */
    #[Test]
    public function everyBriefOpensOnThePremiseADefectIsJudgedBy(): void
    {
        foreach ([
            [
                'task' => 'Fix f:image failing on a src that carries a cache busting query string',
                'paths' => ['typo3/sysext/fluid'],
                'changeType' => 'bugfix',
            ],
            // Outside the core as well: an editor and a visitor are the same
            // two people there, and the premise names no core-only artifact
            // that the filtering would take out with the changelog.
            [
                'task' => 'Add a testimonials content element',
                'paths' => ['packages/my_sitepackage/Classes/Controller/TestimonialController.php'],
                'changeType' => 'feature',
            ],
            // The arm that changes nothing carries it too, and it is the case
            // R-GUI-008 was written from: the session that read a report as an
            // API question was assessing one, not patching it.
            [
                'task' => 'Is this sitepackage written the way TYPO3 14 expects',
                'changeType' => 'audit',
            ],
        ] as $call) {
            $brief = Registry::call('typo3_task_guide', $call);

            self::assertSame(TaskGuide::PRODUCT_PREMISE, $brief->data['checklist'][0], $call['task']);
            self::assertStringContainsString(TaskGuide::PRODUCT_PREMISE, $brief->text, $call['task']);
        }
    }

    /**
     * R-GUI-009. The fifth recorded `REVIEW-03` run quoted `fluid-viewhelpers`
     * and `core-tests` as `typo3_hint_lookup` and never called it: the brief had
     * carried them, correctly and without saying whose they were, and the
     * report's reader was sent to a tool that had not answered
     * (`scenarios/runs/REVIEW-03.json`, `D-SKL-009`).
     */
    #[Test]
    public function theHintsABriefCarriesNameTheLookupTheyCameFrom(): void
    {
        // The paths of that run, which is the call the defect was found in.
        $paths = [
            'typo3/sysext/core/Classes/Resource/ResourceFactory.php',
            'typo3/sysext/extbase/Classes/Service/ImageService.php',
            'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
            'typo3/sysext/backend/Classes/ViewHelpers/ThumbnailViewHelper.php',
            'typo3/sysext/core/Tests/Functional/Resource/ResourceFactoryTest.php',
        ];
        $task = 'Review a core patch that moves file source resolution from Extbase ImageService into '
            . 'ResourceFactory and adds a new public method';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15.0',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '15.0',
            'paths' => $paths,
            'limit' => 10,
        ]);

        self::assertNotSame([], $brief->data['hints']);
        self::assertStringContainsString(
            sprintf(TaskGuide::HINTS_SOURCE, TaskGuide::HINTS_PER_GROUP),
            $brief->text,
        );

        // Quoted whole rather than summarised, which is what makes the citation
        // right: a reader following it to typo3_hint_lookup finds the same
        // statements, not a longer version of them.
        $owned = [];
        foreach ($lookup->data['hints'] as $hint) {
            $owned[$hint['id']] = $hint;
        }
        foreach ($brief->data['hints'] as $hint) {
            self::assertArrayHasKey($hint['id'], $owned, $hint['id'] . ' is not one of the lookup\'s');
            self::assertSame($owned[$hint['id']], $hint, $hint['id'] . ' is not what the lookup answers');
        }

        // And a selection of them, which is the other half of what the sentence
        // states: the brief stops where the lookup goes on.
        self::assertLessThanOrEqual(TaskGuide::HINTS_PER_GROUP, count($brief->data['hints']));
        self::assertGreaterThan(count($brief->data['hints']), count($lookup->data['hints']));
    }

    /**
     * R-GUI-012. The same run read the four hints the brief carried, made no
     * separate call, and established `#[Autowire(lazy: true)]` for the patch's
     * new service dependency by grepping three call sites out of the checkout —
     * `dependency-injection` is the seventh hint the lookup holds for those
     * paths, and the brief had stated a count rather than the subjects
     * (`feedback/2026-08-03-144410`).
     */
    #[Test]
    public function aBriefNamesTheHintsItLeftBehind(): void
    {
        $paths = [
            'typo3/sysext/core/Classes/Resource/ResourceFactory.php',
            'typo3/sysext/extbase/Classes/Service/ImageService.php',
            'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
            'typo3/sysext/backend/Classes/ViewHelpers/ThumbnailViewHelper.php',
            'typo3/sysext/core/Tests/Functional/Resource/ResourceFactoryTest.php',
        ];
        $task = 'Review a core patch that moves file source resolution from Extbase ImageService into '
            . 'ResourceFactory and adds a new public method';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15.0',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '15.0',
            'paths' => $paths,
            'limit' => HintLookup::MAX_HINTS,
        ]);

        $carried = array_column($brief->data['hints'], 'id');
        $left = array_column($brief->data['omittedHints'], 'id');

        // The measurement D-GUI-007 was decided from: four carried, three left,
        // and the one the report went to the checkout for is among the three.
        self::assertSame(['fal-reading', 'fal-processing', 'dependency-injection'], $left);
        // What the pointer stands for is what the lookup would answer, so the
        // two halves are that answer and nothing else.
        self::assertSame(array_column($lookup->data['hints'], 'id'), array_merge($carried, $left));

        // Named rather than quoted: the ids are in the copy a reader is looking
        // at, and each record is what the lookup takes back as an id.
        self::assertStringContainsString(sprintf(TaskGuide::HINTS_OMITTED, implode(', ', $left)), $brief->text);
        foreach ($brief->data['omittedHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
            self::assertArrayNotHasKey('hints', $entry);
        }
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
    public function anUnattendedInstallIsAnsweredWithWhatTheCommandRefuses(): void
    {
        // The script was written from the command's own --help, which names
        // neither the connection type the option takes nor the driver it
        // persists, and says nothing about a second run. Four failed runs.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'install TYPO3 unattended from a shell script so that "ddev start" sets the instance up on its own',
        ]);

        self::assertContains('installation-setup', array_column($result->data['intents'], 'id'));
        self::assertContains('installation-setup', array_column($result->data['hints'], 'id'));

        $statements = self::statementsOf('installation-setup');
        self::assertStringContainsString('pdo_sqlite', $statements, 'the driver name the option does not take');
        self::assertStringContainsString(
            'config/system/settings.php',
            $statements,
            'what an idempotent installer guards on, since the command guards on the schema',
        );

        // The site half is the one that moved. Before the distribution option
        // existed, --create-site wrote the site configuration whatever else was
        // installed; since it does, a required package that ships an
        // initialisation file silences both options.
        $onThirteen = implode("\n", array_column(Hints::byId('installation-setup', 13)['hints'], 'text'));
        self::assertStringContainsString('--create-site <url>', $onThirteen);
        self::assertStringNotContainsString('--distribution', $onThirteen);

        $onFourteen = implode("\n", array_column(Hints::byId('installation-setup', 14)['hints'], 'text'));
        self::assertStringContainsString('--distribution', $onFourteen);
        self::assertStringContainsString(
            'no way to be told its own URL',
            $onFourteen,
            'the consequence the two inert options have',
        );
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
            'paths' => ['packages/printworks_sitepackage/'],
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
     * The reported miss is a testimonials element built on TCA and a
     * DatabaseQueryProcessor, in an extension whose other plugins are Extbase,
     * with Extbase never considered. The fork is written on the extbase and the
     * frontend-records hint, and both open on a word this task has no reason to
     * use — which is asserted here, because it is what the checklist delivers
     * instead of. The wording is read off `.checkouts/14.3`:
     * `registerPlugin()` hands `addPlugin()` a `SelectItem` for the CType
     * column, the same column `addRecordType()` writes, and
     * `configurePlugin()` generates `tt_content.<signature> =< lib.contentElement`
     * with `20 = EXTBASEPLUGIN`. So the fork is not element or plugin.
     */
    #[Test]
    public function aContentElementTaskIsOfferedTheExtbaseForkWithoutNamingIt(): void
    {
        $task = 'new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering';

        $reached = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
        self::assertNotContains('extbase', $reached, 'the fork is filed under a branch this task did not name');
        self::assertNotContains('frontend-records', $reached);

        $result = Registry::call('typo3_task_guide', ['task' => $task]);
        self::assertContains('content-element', array_column($result->data['intents'], 'id'));

        $checklist = implode("\n", $result->data['checklist']);
        self::assertStringContainsString('needs Extbase', $checklist);
        self::assertStringContainsString('same CType selector', $checklist);
        self::assertStringContainsString('no model, no repository and no controller', $checklist);
        // The other half: what the extension already does is reported and is
        // evidence about the architecture, not only about where a template is.
        self::assertStringContainsString('kind of element or plugin', $checklist);

        // A fork is only worth anything while the choice is still free, so it
        // arrives before the registration and the template do.
        self::assertLessThan(
            (int) strpos($checklist, 'Register this element in a file of its own'),
            (int) strpos($checklist, 'needs Extbase'),
        );

        // Where the rest of the fork is, carried by the checklist rather than
        // by the tool list: nextTools keeps one entry per tool, and this is not
        // the only intent a content-element task matches that names the lookup.
        self::assertStringContainsString('id=extbase', $checklist);
        self::assertStringContainsString('id=frontend-records', $checklist);

        $when = array_column($result->data['nextTools'], 'when', 'tool');
        self::assertStringContainsString('architecture this extension already has', $when['typo3_extension_scope'] ?? '');
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
    #[DataProvider('theMajorsThePreviewAnswerIsBoundFor')]
    public function aPreviewAnswerSaysWhatTheDefaultRendererAlreadyDraws(int $major): void
    {
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
     * Both majors draw the same four, so the statement carries no version
     * binding and has to reach a caller on either.
     *
     * @return array<string, array{0: int}>
     */
    public static function theMajorsThePreviewAnswerIsBoundFor(): array
    {
        return ['on 13' => [13], 'on 14' => [14]];
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
