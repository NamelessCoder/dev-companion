<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Documentation;

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
        self::assertSame([], array_filter($requested, static fn(string $url): bool => !str_contains($url, '/13.4/')));
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
