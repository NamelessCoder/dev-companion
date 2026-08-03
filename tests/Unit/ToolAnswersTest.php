<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\ToolAnswers;
use Typo3CmsMcp\Upkeep\ToolCalls;
use Typo3CmsMcp\Upkeep\ToolSurface;

/**
 * The recording of what the tools answered, as far as it can be held here.
 *
 * Not that it is current: it is a run against an installation, no test run has
 * one, and pages a command only some machines can produce may not be able to
 * turn the suite red. What is held is the shape they are written in — that
 * every answer is JSON a reader can paste anywhere, and that no absolute path
 * survives into a page every reader of this package gets.
 */
final class ToolAnswersTest extends TestCase
{
    #[Test]
    public function everyRecordedAnswerIsJson(): void
    {
        $found = 0;
        foreach (self::recorded() as $name => $section) {
            foreach (self::blocks($section) as [$language, $block]) {
                if ($language !== 'json') {
                    continue;
                }
                ++$found;
                self::assertNotNull(
                    json_decode($block, true),
                    'a recorded block is not JSON: ' . $name . ' — ' . substr($block, 0, 120),
                );
            }
        }

        self::assertGreaterThan(0, $found, 'the recording carries no data at all');
    }

    /**
     * Half of these answers are markdown themselves, fenced blocks included,
     * and an answer's own closing fence ends the block it was written into. The
     * rest of the section then renders as prose and the JSON above reads as the
     * end of it — which looks like a recording and is not one.
     *
     * What says it happened is what is left around the blocks: a leaked fence
     * swallows the headings that follow it, so the call keeps its `### ` line
     * and loses the `Data:` under it. Counting blocks does not say it — the
     * leaked pair reopens and the total comes out right — which is how the one
     * page this replaced carried four of them unnoticed.
     */
    #[Test]
    public function noAnswerEndsTheBlockItWasWrittenInto(): void
    {
        foreach (self::recorded() as $name => $section) {
            $outside = self::outsideBlocks($section);
            $calls = preg_match_all('/^### /m', $outside);
            $answers = preg_match_all('/^Data:$/m', $outside);

            self::assertGreaterThanOrEqual($calls, $answers, $name . ': answers per call');
            self::assertSame($calls + $answers * 2, count(self::blocks($section)), $name . ': blocks');
            self::assertSame(
                $calls,
                preg_match_all('/^Called with:$/m', $outside),
                $name . ': ' . $calls . ' calls, and this many "Called with:" left outside a block',
            );
            self::assertSame(
                $answers,
                preg_match_all('/^Text:$/m', $outside),
                $name . ': ' . $answers . ' answers, and this many "Text:" left outside a block',
            );
        }
    }

    /**
     * A page carrying two answers per call has to say which is which, or it is
     * two recordings a reader cannot tell apart — and the one they are told
     * apart by is the whole reason for the second one being there.
     */
    #[Test]
    public function everyAnswerOnAPageOfTwoRecordingsSaysWhichItCameFrom(): void
    {
        foreach (self::recorded() as $name => $section) {
            $outside = self::outsideBlocks($section);
            $answers = preg_match_all('/^Data:$/m', $outside);
            if ($answers === preg_match_all('/^### /m', $outside)) {
                continue;
            }

            self::assertSame(
                $answers,
                preg_match_all('/^#### From /m', $outside),
                $name . ': answers, and this many saying which recording they are of',
            );
        }
    }

    /**
     * The pages ship inside this package, so a path from the machine that
     * recorded them would be in every checkout of it. The substitutions are
     * `ToolAnswers`' own and the surface says they happened.
     */
    #[Test]
    public function theRecordingCarriesNobodysDirectoryLayout(): void
    {
        $home = (string) getenv('HOME');

        foreach (ToolSurface::written() as $page) {
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
     * every tool in the table has answered, not that the pages match call for
     * call.
     */
    #[Test]
    public function everyToolTheTableDrivesHasARecordedAnswer(): void
    {
        $missing = [];
        foreach (array_unique(array_column(ToolCalls::all(), 0)) as $name) {
            if (ToolAnswers::recordedIn(ToolSurface::file($name)) === '') {
                $missing[] = $name;
            }
        }

        self::assertSame([], $missing, 'driven by the table and recorded nowhere — run bin/cli tools:record');
    }

    /**
     * Every answer on a page is one the tool could have given.
     *
     * A recording was evidence and was therefore held to nothing: it said what
     * came back on a day, and a field that has since left the schema goes on
     * being shown to every reader. What the pages are for is the endpoint and
     * the states it answers in, and a state illustrated by an answer the schema
     * no longer allows illustrates nothing.
     *
     * This is the one thing that can be checked without an installation: the
     * answer is on the page and the schema is in the class, so the two can be
     * held to each other wherever the suite runs.
     */
    #[Test]
    public function everyAnswerOnAPageIsOneItsSchemaAllows(): void
    {
        $schemas = [];
        foreach (Registry::definitions() as $definition) {
            $schemas[$definition['name']] = $definition['outputSchema'] ?? [];
        }

        $validator = new SchemaValidator();
        $broken = [];
        foreach (self::recorded() as $name => $section) {
            foreach (self::answers($section) as $state => $answer) {
                $errors = $validator->validateAgainstJsonSchema(json_decode($answer), $schemas[$name]);
                if ($errors !== []) {
                    $broken[] = $name . ' — ' . $state . ': ' . implode(' ', array_column($errors, 'message'));
                }
            }
        }

        // All of them, because one at a time says a page is stale and the list
        // says how far the recording as a whole has drifted from the classes.
        self::assertSame([], $broken, 'answers no schema allows');
    }

    /**
     * The data half of every answer in a section, by the state it is under.
     *
     * A state answered from two working directories carries two, and both are
     * the same tool's: the key keeps them apart by the heading each sits under.
     *
     * @return array<string, string>
     */
    private static function answers(string $section): array
    {
        $blocks = self::blocks($section);
        $answers = [];
        $state = '';
        $from = '';
        $index = 0;

        foreach (explode("\n", self::outsideBlocks($section)) as $line) {
            if (str_starts_with($line, '### ')) {
                $state = substr($line, 4);
                $from = '';
            }
            if (str_starts_with($line, '#### From ')) {
                $from = ', ' . substr($line, 10);
            }
            if ($line === 'Called with:') {
                ++$index;
            }
            if ($line === 'Data:') {
                $answers[$state . $from] = $blocks[$index + 1][1] ?? '';
                $index += 2;
            }
        }

        return $answers;
    }

    /**
     * Every tool the table leaves out says why it is out.
     *
     * This used to name the two in the assertion itself, which held the list
     * and nothing else: a third tool dropping out failed here, and the cheapest
     * way to make it pass again was to add its name. The list is
     * `ToolCalls::undriven()` now, so making this green means writing the
     * reason — and the reason is what the tool's own page then states where a
     * reader meets the absence.
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

    /**
     * The recorded half of every page that has one, by the tool it is of.
     *
     * @return array<string, string>
     */
    private static function recorded(): array
    {
        $sections = [];
        foreach (array_column(Registry::definitions(), 'name') as $name) {
            $section = ToolAnswers::recordedIn(ToolSurface::file($name));
            if ($section !== '') {
                $sections[$name] = $section;
            }
        }

        return $sections;
    }

    /**
     * The fenced blocks of a section, by the rule a renderer reads them with: a
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

    /** The section with its blocks taken out, so what is left is its own text. */
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
}
