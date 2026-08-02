<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\ToolAnswers;
use Typo3CmsMcp\Upkeep\ToolCalls;

/**
 * The recording of what the tools answered, as far as it can be held here.
 *
 * Not that it is current: it is a run against an installation, no test run has
 * one, and pages a command only some machines can produce may not be able to
 * turn the suite red. What is held is the shape they are written in — that
 * every answer is JSON a reader can paste anywhere, that no absolute path
 * survives into a page every reader of this package gets, and that the index
 * reaches all of them.
 */
final class ToolAnswersTest extends TestCase
{
    #[Test]
    public function everyRecordedAnswerIsJson(): void
    {
        $found = 0;
        foreach (ToolAnswers::written() as $page) {
            foreach (self::blocks($page->getContents()) as [$language, $block]) {
                if ($language !== 'json') {
                    continue;
                }
                ++$found;
                self::assertNotNull(
                    json_decode($block, true),
                    'a recorded block is not JSON: ' . $page->getFilename() . ' — ' . substr($block, 0, 120),
                );
            }
        }

        self::assertGreaterThan(0, $found, 'the recording carries no data at all');
    }

    /**
     * Half of these answers are markdown themselves, fenced blocks included,
     * and an answer's own closing fence ends the block it was written into. The
     * rest of the page then renders as prose and the JSON above reads as the
     * end of it — which looks like a recording and is not one.
     *
     * What says it happened is what is left of the page around the blocks: a
     * leaked fence swallows the headings that follow it, so the call keeps its
     * `## ` line and loses the `Data:` under it. Counting blocks does not say
     * it — the leaked pair reopens and the total comes out right — which is how
     * the one page this replaced carried four of them unnoticed.
     */
    #[Test]
    public function noAnswerEndsTheBlockItWasWrittenInto(): void
    {
        foreach (ToolAnswers::written() as $page) {
            if ($page->getPathname() === ToolAnswers::index()) {
                continue;
            }

            $contents = $page->getContents();
            $outside = self::outsideBlocks($contents);
            $calls = preg_match_all('/^## /m', $outside);

            self::assertSame($calls * 3, count(self::blocks($contents)), $page->getFilename() . ': blocks');
            foreach (['Called with:', 'Text:', 'Data:'] as $heading) {
                self::assertSame(
                    $calls,
                    preg_match_all('/^' . preg_quote($heading, '/') . '$/m', $outside),
                    $page->getFilename() . ': ' . $calls . ' calls, and this many "' . $heading . '" left outside a block',
                );
            }
        }
    }

    /**
     * The fenced blocks of a page, by the rule a renderer reads them with: a
     * block runs to the first fence at least as long as the one that opened it
     * and carrying no language of its own.
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function blocks(string $page): array
    {
        $blocks = [];
        $open = null;
        $language = '';
        $content = [];

        foreach (explode("\n", $page) as $line) {
            $fence = preg_match('/^ {0,3}(`{3,})(.*)$/', $line, $matched) === 1;
            if ($open === null) {
                if ($fence) {
                    $open = strlen($matched[1]);
                    $language = trim($matched[2]);
                    $content = [];
                }
                continue;
            }
            if ($fence && strlen($matched[1]) >= $open && trim($matched[2]) === '') {
                $blocks[] = [$language, implode("\n", $content)];
                $open = null;
                continue;
            }
            $content[] = $line;
        }

        return $blocks;
    }

    /** The page with its blocks taken out, so what is left is the page's own text. */
    private static function outsideBlocks(string $page): string
    {
        $outside = [];
        $open = null;

        foreach (explode("\n", $page) as $line) {
            $fence = preg_match('/^ {0,3}(`{3,})(.*)$/', $line, $matched) === 1;
            if ($open === null) {
                $fence ? $open = strlen($matched[1]) : $outside[] = $line;
                continue;
            }
            if ($fence && strlen($matched[1]) >= $open && trim($matched[2]) === '') {
                $open = null;
            }
        }

        return implode("\n", $outside);
    }

    /**
     * The pages ship inside this package, so a path from the machine that
     * recorded them would be in every checkout of it. The substitutions are
     * `ToolAnswers`' own and each head says they happened.
     */
    #[Test]
    public function theRecordingCarriesNobodysDirectoryLayout(): void
    {
        $home = (string) getenv('HOME');

        foreach (ToolAnswers::written() as $page) {
            self::assertStringNotContainsString(Paths::root(), $page->getContents(), $page->getFilename());
            if ($home !== '') {
                self::assertStringNotContainsString($home, $page->getContents(), $page->getFilename());
            }
        }
    }

    /**
     * The recording is of the table the contract test drives, so a call added
     * to one is a call the other shows. It may be older than the table — that
     * is the whole point of it not being checked — so what is asserted is that
     * every tool in the table has a page, not that the pages match call for
     * call.
     */
    #[Test]
    public function everyToolTheTableDrivesHasARecordedAnswer(): void
    {
        $missing = [];
        foreach (array_unique(array_column(ToolCalls::all(), 0)) as $name) {
            if (!is_file(ToolAnswers::file($name))) {
                $missing[] = $name;
            }
        }

        self::assertSame([], $missing, 'driven by the table and recorded nowhere — run bin/cli tools:record');
    }

    /**
     * A page nothing links to is one nobody arrives at, which is the failure
     * one file per tool trades for: the index is the only thing that still sees
     * all of them.
     */
    #[Test]
    public function theIndexReachesEveryPage(): void
    {
        $index = (string) file_get_contents(ToolAnswers::index());

        foreach (ToolAnswers::written() as $page) {
            if ($page->getPathname() === ToolAnswers::index()) {
                continue;
            }
            self::assertStringContainsString('(' . $page->getFilename() . ')', $index, $page->getFilename());
        }
    }

    /**
     * Every tool the table leaves out says why it is out.
     *
     * This used to name the two in the assertion itself, which held the list
     * and nothing else: a third tool dropping out failed here, and the cheapest
     * way to make it pass again was to add its name. The list is
     * `ToolCalls::undriven()` now, so making this green means writing the
     * reason — and the reason is what `tools.md` and the recording's own map
     * then state where a reader meets the absence.
     */
    #[Test]
    public function everyToolTheTableLeavesOutSaysWhy(): void
    {
        $driven = array_unique(array_column(ToolCalls::all(), 0));
        $offered = array_column(Registry::definitions(), 'name');

        self::assertSame(
            array_values(array_diff($offered, $driven)),
            array_keys(ToolCalls::undriven()),
            'a tool joined or left the table without its reason being written down',
        );
        foreach (ToolCalls::undriven() as $name => $why) {
            self::assertNotSame('', trim($why), $name . ' is left out of the table and says nothing about why');
        }
    }
}
