<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Manuals\Documentation;

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
    }
}
