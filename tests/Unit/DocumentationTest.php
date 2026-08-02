<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Manual\Documentation;
use Typo3CmsMcp\Tool\DocumentationLookup;
use Typo3CmsMcp\Tool\Registry;

final class DocumentationTest extends TestCase
{
    #[Test]
    public function itSearchesTheRequestedVersionAndKeepsProvenanceOnEveryResult(): void
    {
        $requested = [];
        $documentation = new Documentation(static function (string $url) use (&$requested): string {
            $requested[] = $url;
            if (str_ends_with($url, '/en-us/')) {
                return <<<'HTML'
                    <html><body>
                    <a href="ApiOverview/Seo/PageTitleApi.html">Page title API</a>
                    <a href="ApiOverview/Events/Index.html">Events and hooks</a>
                    </body></html>
                    HTML;
            }
            if (str_ends_with($url, 'PageTitleApi.html')) {
                return '<html><article role="main"><p>Page title providers implement the provider interface.</p></article></html>';
            }

            return '<html><article role="main"><p>PSR-14 events extend TYPO3 without replacing the implementation.</p></article></html>';
        });

        $answer = $documentation->lookup(['page title event', 'page title provider'], '13.4', 2);

        self::assertSame('search', $answer['mode']);
        self::assertSame('answered', $answer['status']);
        self::assertNotEmpty($answer['results']);
        self::assertSame('Page title API', $answer['results'][0]['title']);
        self::assertSame('13.4', $answer['results'][0]['documentVersion']);
        self::assertSame('typo3/reference-coreapi', $answer['results'][0]['document']);
        self::assertStringStartsWith(
            'https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/',
            $answer['results'][0]['url'],
        );
        self::assertNotSame('', $answer['results'][0]['excerpt']);
        self::assertSame('', $answer['results'][0]['content']);
        self::assertSame([], array_filter($requested, static fn(string $url): bool => !str_contains($url, '/13.4/')));
    }

    #[Test]
    public function itReadsACanonicalSearchResultAsStructuredText(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/DocHeaderComponent.html';
        $documentation = new Documentation(static fn(string $requested): ?string => $requested === $url
            ? <<<'HTML'
                <html><body><nav>Not page content</nav><article role="main">
                <h1>DocHeaderComponent</h1>
                <p>Use the document header for module buttons.</p>
                <h2>Shortcut context</h2>
                <pre><code>$docHeader->setShortcutContext('records', 'Records');</code></pre>
                <ul><li>The route and arguments describe the current module.</li></ul>
                </article></body></html>
                HTML
            : null);

        $answer = $documentation->page($url, '14.3');

        self::assertSame('page', $answer['mode']);
        self::assertSame('answered', $answer['status']);
        self::assertSame($url, $answer['results'][0]['url']);
        self::assertSame('DocHeaderComponent', $answer['results'][0]['title']);
        self::assertStringContainsString('# DocHeaderComponent', $answer['results'][0]['content']);
        self::assertStringContainsString('setShortcutContext', $answer['results'][0]['content']);
        self::assertStringNotContainsString('Not page content', $answer['results'][0]['content']);
    }

    #[Test]
    public function itRefusesAPageOutsideTheSelectedManualVersion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Documentation(static fn(string $url): ?string => null))->page(
            'https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/ApiOverview/Backend/Index.html',
            '14.3',
        );
    }

    #[Test]
    public function aTcaQuestionIsAnsweredFromTheTcaReferenceRatherThanFromWhatElseCarriesTheWord(): void
    {
        // TYPO3 Explained documents everything around TCA and not TCA itself,
        // so this used to come back as the events that carry "inline" and
        // "localization" in their class names.
        $answer = (new Documentation($this->manuals()))->lookup(
            ['TCA inline foreign_field foreign_sortby localization children'],
            '14.3',
            3,
        );

        self::assertSame('IRRE / inline', $answer['results'][0]['title']);
        self::assertSame('typo3/reference-tca', $answer['results'][0]['document']);
    }

    /**
     * A page titled after its subject and a page whose title is a long event
     * class name carry the word equally well, and the class name carries five
     * other words besides. While no title in the corpus was long enough to be
     * diluted the two were worth the same, and the tie went to whichever manual
     * was indexed first (`D-ANS-029`).
     */
    #[Test]
    public function aPageTitledAfterItsSubjectOutranksALongerTitleThatAlsoCarriesTheWord(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['inline'], '14.3', 2);

        self::assertSame('IRRE / inline', $answer['results'][0]['title']);
        self::assertSame('ModifyInlineElementControlsEvent', $answer['results'][1]['title']);
    }

    /**
     * The manuals of the core are published under `/m/` and this one is not,
     * so a search that built every base the same way reached three books, none
     * of which documents a ViewHelper, and the question was answered from
     * whichever of them carried the word (`D-ANS-023`). A base that is wrong is
     * silent — the index does not answer and the book is simply absent — so
     * what is held is that its pages are reached and reached at their own base.
     */
    #[Test]
    public function aViewHelperQuestionReachesTheManualPublishedOutsideTheCoreCollection(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(
            ['f:if f:then f:else condition ViewHelper'],
            '14.3',
            4,
        );

        $reference = array_values(array_filter(
            $answer['results'],
            static fn(array $result): bool => $result['document'] === 'typo3/view-helper-reference',
        ));
        self::assertNotSame([], $reference);
        self::assertSame('Fluid ViewHelper Reference', $reference[0]['documentTitle']);
        self::assertStringStartsWith(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/',
            $reference[0]['url'],
        );
    }

    /**
     * And the page of the ViewHelper that gave its name to the question.
     *
     * That book titles a page after the tag, so `Global/If.html` is called
     * "if" and the only word of `f:if` that can reach it is two characters
     * long. Every one of those was dropped before it was searched for, which
     * is what left the query answered by `Global/Else.html` — the right family
     * and the wrong page (`D-ANS-023`).
     */
    #[Test]
    public function aViewHelperNamedAfterAKeywordIsReachedByItsOwnName(): void
    {
        $documentation = new Documentation($this->manuals());

        self::assertSame(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html',
            $documentation->lookup(['f:if'], '14.3', 1)['results'][0]['url'],
        );
    }

    /** And the URL it hands back is one it takes back, on the same version. */
    #[Test]
    public function aPageOfThatManualIsReadBackAtItsOwnBase(): void
    {
        $url = 'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html';
        $documentation = new Documentation(static fn(string $requested): ?string => $requested === $url
            ? '<html><article role="main"><h1>If ViewHelper &lt;f:if&gt;</h1>'
                . '<p>This ViewHelper implements an if/else condition.</p></article></html>'
            : null);

        $answer = $documentation->page($url, '14.3');

        self::assertSame('answered', $answer['status']);
        self::assertSame('typo3/view-helper-reference', $answer['results'][0]['document']);
        self::assertSame('Fluid ViewHelper Reference', $answer['results'][0]['documentTitle']);
    }

    #[Test]
    public function anApiIdentifierReachesThePageThatIsNotNamedAfterIt(): void
    {
        // Nothing in a table of contents is called AssetCollector or
        // FunctionalTestCase; the pages that answer them are called "Assets"
        // and "Functional tests".
        $documentation = new Documentation($this->manuals());

        self::assertContains(
            'Assets',
            array_column($documentation->lookup(['Fluid AssetCollector css javascript ViewHelper'], '14.3', 3)['results'], 'title'),
        );
        self::assertContains(
            'Functional tests',
            array_column($documentation->lookup(['FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14'], '14.3', 3)['results'], 'title'),
        );
    }

    /**
     * The three queries of `D-ANS-021` came back `answered` with six results
     * each, and nothing in them showed that the word naming the subject had
     * reached none of the pages returned.
     */
    #[Test]
    public function aResultNamesTheWordsOfTheQueryItWasMatchedOn(): void
    {
        $documentation = new Documentation(static function (string $url): ?string {
            if (!str_contains($url, 'typo3/reference-coreapi')) {
                return null;
            }

            return str_ends_with($url, '/en-us/')
                ? '<html><body>'
                    . '<a href="ExtensionArchitecture/HowTo/Localization/Fluid.html">Multi-language Fluid templates</a>'
                    . '<a href="ApiOverview/Database/DatabaseRecords/RecordObjects.html">Record objects</a>'
                    . '</body></html>'
                : '<html><article role="main"><p>What this page says.</p></article></html>';
        });

        $answer = $documentation->lookup(['Record API Fluid template access record.header'], '14.3', 2);

        $matched = [];
        foreach ($answer['results'] as $result) {
            $matched[$result['title']] = array_column($result['matched'], 'field', 'term');
        }
        // The page the session was after carries the subject and nothing else;
        // the one that outranks it carries everything except the subject.
        self::assertSame(['record' => 'title', 'api' => 'path'], $matched['Record objects']);
        self::assertSame(['fluid' => 'title', 'templa' => 'title'], $matched['Multi-language Fluid templates']);
    }

    /** A page was not searched for, so it was matched on nothing. */
    #[Test]
    public function aPageReadBackCarriesNoMatch(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Assets/Index.html';
        $documentation = new Documentation(
            static fn(string $requested): string => '<html><article role="main"><p>What this page says.</p></article></html>',
        );

        self::assertSame([], $documentation->page($url, '14.3')['results'][0]['matched']);
    }

    #[Test]
    public function anAnsweredIndexWithNoMatchIsNotAnUnavailableService(): void
    {
        $documentation = new Documentation(static fn(string $url): ?string => str_ends_with($url, '/en-us/')
            ? '<html><body><a href="Introduction.html">Introduction</a></body></html>'
            : null);

        $answer = $documentation->lookup(['quantum pineapple'], '13.4');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['results']);
        self::assertNull($answer['unavailable']);
    }

    /**
     * The tables of contents as they are published, cut down to the pages this
     * is about: the ones that answer, and the ones that used to be answered
     * instead because they carry one of the words.
     */
    private function manuals(): \Closure
    {
        $manuals = [
            'typo3/reference-coreapi' => [
                'ApiOverview/Events/Events/Backend/ModifyInlineElementControlsEvent.html' => 'ModifyInlineElementControlsEvent',
                'ApiOverview/Events/Events/Backend/AfterPageColumnsSelectedForLocalizationEvent.html' => 'AfterPageColumnsSelectedForLocalizationEvent',
                'ApiOverview/Events/Events/Frontend/AfterStdWrapFunctionsExecutedEvent.html' => 'AfterStdWrapFunctionsExecutedEvent',
                'ApiOverview/Assets/Index.html' => 'Assets',
                'ApiOverview/Fluid/DevelopCustomViewhelper.html' => 'Developing a custom ViewHelper',
                'ApiOverview/ContentElements/AddingYourOwnContentElements.html' => 'Create a custom content element type (CType)',
                'Testing/FunctionalTesting/Index.html' => 'Functional tests',
            ],
            'typo3/reference-typoscript' => [
                'ContentObjects/Case/Index.html' => 'CASE',
            ],
            'typo3/reference-tca' => [
                'ColumnsConfig/Type/Inline/Index.html' => 'IRRE / inline',
                'ColumnsConfig/CommonProperties/FieldInformation/TcaDescription.html' => 'tcaDescription',
            ],
            // Titled as that book titles them: the tag name, lower case and
            // bare, rather than the "… ViewHelper <f:…>" heading of the page.
            'typo3/view-helper-reference' => [
                'Global/If.html' => 'if',
                'Global/Then.html' => 'then',
                'Global/Else.html' => 'else',
                'Global/Translate.html' => 'translate',
            ],
        ];

        return static function (string $url) use ($manuals): ?string {
            foreach ($manuals as $manual => $pages) {
                if (!str_contains($url, $manual)) {
                    continue;
                }
                if (!str_ends_with($url, '/en-us/')) {
                    return '<html><article role="main"><p>What this page says.</p></article></html>';
                }

                $links = '';
                foreach ($pages as $path => $title) {
                    $links .= sprintf('<a href="%s">%s</a>', $path, $title);
                }

                return '<html><body>' . $links . '</body></html>';
            }

            return null;
        };
    }

    #[Test]
    public function anUnreachableIndexIsDifferentFromNoMatch(): void
    {
        $documentation = new Documentation(static fn(string $url): ?string => null);

        $answer = $documentation->lookup(['page title'], '13.4');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame([], $answer['results']);
        self::assertNotNull($answer['unavailable']);
        self::assertNotSame('', $answer['unavailable']['reason']);
        // Which of the two unavailable cases it is, because the remedies are
        // opposite: this one is answered by asking again (D-ANS-007).
        self::assertSame('source-not-answering', $answer['unavailable']['cause']);
    }

    /**
     * What the shared call table rests on. Two of its entries ask
     * docs.typo3.org for real so the recording has a filled answer to show, and
     * `ToolContractTest` drives the same entries — so a host that is down has
     * to come back as an answer rather than as a red build (`D-DOC-008`).
     *
     * The data half is what is held here, on both modes. The text half is the
     * one branch every unavailable answer shares, and the entry that asks for
     * TYPO3 999 already drives it without reaching anything.
     */
    #[Test]
    public function aSourceThatDidNotAnswerIsStillAnAnswerToTheSchema(): void
    {
        $documentation = new Documentation(static fn(string $url): ?string => null);
        $schema = DocumentationLookup::outputSchema();

        $answers = [
            'search' => $documentation->lookup(['assets'], '14.3'),
            'page' => $documentation->page(
                'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Assets/Index.html',
                '14.3',
            ),
        ];

        foreach ($answers as $mode => $answer) {
            self::assertNotNull($answer['unavailable'], $mode . ' gave no reason');
            self::assertSame('source-not-answering', $answer['unavailable']['cause'], $mode);

            $errors = (new SchemaValidator())->validateAgainstJsonSchema(
                json_decode((string) json_encode($answer, JSON_THROW_ON_ERROR), true),
                $schema,
            );
            self::assertSame([], $errors, $mode . ' broke the output schema: ' . json_encode($errors));
        }
    }

    /**
     * The other one, and the reason the field exists: a release outside the
     * covered versions is permanent, and nothing is fetched to find that out.
     */
    #[Test]
    public function aVersionOutsideTheCoveredOnesIsNotAskedFor(): void
    {
        $answer = Registry::call('typo3_documentation_lookup', [
            'queries' => ['assets'],
            'targetVersion' => '9.5',
        ])->data;

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('version-not-covered', $answer['unavailable']['cause']);
        self::assertStringContainsString('outside the covered versions', $answer['unavailable']['reason']);
    }
}
