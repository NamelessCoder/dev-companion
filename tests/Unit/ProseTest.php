<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Prose;
use Typo3CmsMcp\Upkeep\Wrap;

/**
 * That the prose rule is measured, and that the one place it is held holds.
 *
 * The measure itself reports rather than fails — a long sentence in a body can
 * be the right sentence. The opening of a requirement or a decision is the
 * exception, because a reader who stops after it is supposed to know what was
 * settled, and 47 of them had run past the point where anybody could.
 */
final class ProseTest extends TestCase
{
    #[Test]
    public function everyRequirementAndDecisionOpensWithASentenceAReaderCanStopAfter(): void
    {
        $over = Prose::leadsOverTheMeasure();

        self::assertSame([], $over, implode("\n", array_map(
            static fn(array $lead): string => sprintf('%s opens with %d words: %s', $lead['id'], $lead['words'], $lead['text']),
            $over,
        )));
    }

    /**
     * `feedback/` is what this deliberately leaves out. A feedback is written
     * by a session somewhere else, and measuring it against this repository's
     * rule would report on the wrong author.
     */
    #[Test]
    public function theCorpusIsTheProseThisRepositoryWritesAboutItself(): void
    {
        $documents = Prose::documents();

        self::assertContains('AGENTS.md', $documents);
        self::assertContains('documentation/readme.md', $documents);
        self::assertNotEmpty(array_filter($documents, static fn(string $file): bool => str_starts_with($file, 'requirements/')));
        self::assertSame([], array_filter($documents, static fn(string $file): bool => str_starts_with($file, 'feedback/')));
    }

    /**
     * What is measured is what a reader reads. A table row and a code block are
     * neither sentences nor prose, and counting them would make the number say
     * that the files with the most examples are the worst written.
     */
    #[Test]
    public function nothingButProseIsCountedAsASentence(): void
    {
        $counted = [];
        foreach (Prose::documents() as $document) {
            foreach (Prose::measure($document)['over'] as $sentence) {
                $counted[] = $sentence['text'];
            }
        }

        self::assertNotEmpty($counted, 'the measure found nothing at all, which means it read nothing');
        foreach ($counted as $sentence) {
            self::assertStringStartsNotWith('|', $sentence);
            self::assertStringStartsNotWith('#', $sentence);
            self::assertStringStartsNotWith('>', $sentence);
            self::assertStringNotContainsString('```', $sentence);
        }
    }

    /**
     * The one thing a formatter may not do, held over the whole corpus rather
     * than over an example.
     *
     * A rewrap that drops or reorders a word does not look like a bug when the
     * diff is a hundred files of moved line breaks — it looks like an edit
     * somebody made on purpose, and the next reader has no way to tell.
     */
    #[Test]
    public function rewrappingChangesNothingButTheLineBreaks(): void
    {
        foreach (Prose::documents() as $document) {
            $contents = (string) file_get_contents(Paths::root() . '/' . $document);

            self::assertSame(
                self::words($contents),
                self::words(Wrap::document($contents)),
                $document . ' comes back out of the formatter saying something else',
            );
        }
    }

    /**
     * Run twice, it changes nothing the second time. Otherwise every commit
     * that touches a file carries the formatter arguing with itself.
     */
    #[Test]
    public function rewrappingASecondTimeChangesNothing(): void
    {
        foreach (Prose::documents() as $document) {
            $once = Wrap::document((string) file_get_contents(Paths::root() . '/' . $document));

            self::assertSame($once, Wrap::document($once), $document . ' does not settle');
        }
    }

    /**
     * What a line break would break if it landed inside it.
     *
     * A code span reads as two spans with a stray backtick, and a link stops
     * being a link. Both were what the throwaway scripts got wrong.
     */
    #[Test]
    public function aCodeSpanAndALinkAreNeverBrokenAcrossLines(): void
    {
        $wrapped = Wrap::document(
            'A sentence long enough to be wrapped somewhere near its end, and then '
            . '`bin/cli hints:probe` plus [the writing rules](documentation/glossary.md) at the end of it.',
        );

        self::assertStringContainsString('`bin/cli hints:probe`', $wrapped);
        self::assertStringContainsString('[the writing rules](documentation/glossary.md)', $wrapped);
    }

    /**
     * Everything a line break means something in comes back untouched: the
     * front matter of a requirement, a fenced block, a table, an indented
     * command.
     */
    #[Test]
    public function whatIsNotProseComesBackUnchanged(): void
    {
        $document = "---\nid: D-KNW-035\nstatus: open\n---\n\n# A heading that is long enough to be wrapped if anything wrapped a heading at all\n\n```php\n\$a = 'one very long line of code that no formatter here is allowed to touch at all';\n```\n\n    bin/cli todo:next\n\n| a | b |\n| - | - |\n\n> quoted material that is left where it stands even when it runs past the column\n";

        self::assertSame($document, Wrap::document($document));
    }

    /**
     * The head of a todo is fields rather than a paragraph, and stays so.
     *
     * `**Serves:**` and `**Priority:**` stand on their own lines; joined into
     * one, `Todo` reads neither. `**Waiting on:**` is one field over several
     * lines, and what says so is the hang under it.
     */
    #[Test]
    public function aFieldIsOneLineAndAHangingIndentIsKept(): void
    {
        $head = "**Serves:** feedback/2026-08-02-144326-working-inside-a-git-worktree-created-under.md\n"
            . "**Priority:** normal\n"
            . "**Waiting on:** which name the tool takes, because the directory and the class\n"
            . "    follow whichever wins and renaming one of the three alone is two names for\n"
            . '    one thing.';

        $lines = explode("\n", Wrap::document($head));

        self::assertSame('**Serves:** feedback/2026-08-02-144326-working-inside-a-git-worktree-created-under.md', $lines[0]);
        self::assertSame('**Priority:** normal', $lines[1]);
        self::assertStringStartsWith('**Waiting on:**', $lines[2]);
        foreach (array_slice($lines, 3) as $line) {
            self::assertStringStartsWith('    ', $line, 'the hang is gone: ' . $line);
        }
    }

    /**
     * The formatter writes no line past the column that was not already there.
     *
     * Stated that way round rather than as a ceiling, because the lines it
     * leaves alone are allowed to be wide — a table, a fenced command, a link
     * definition — and a test that knew which those are would be the formatter
     * written twice.
     */
    #[Test]
    public function noLinePastTheColumnIsTheFormattersDoing(): void
    {
        foreach (Prose::documents() as $document) {
            $contents = (string) file_get_contents(Paths::root() . '/' . $document);
            $before = self::overTheColumn($contents);

            foreach (self::overTheColumn(Wrap::document($contents)) as $line) {
                // A line with nowhere to break is a line of its own, however
                // wide: cutting a URL or a code span in half is worse.
                if (!self::isBreakable($line)) {
                    continue;
                }
                self::assertContains($line, $before, $document . ' gains a line past the column: ' . $line);
            }
        }
    }

    /**
     * Whether a line has a space the formatter was allowed to break at — one
     * outside a code span and outside a link, which are the two spans a break
     * would destroy.
     */
    private static function isBreakable(string $line): bool
    {
        $line = (string) preg_replace('/`[^`]*`|\[[^\]]*\]\([^)]*\)/', 'x', trim($line));
        $line = (string) preg_replace('/^(?:[-*+]|\d+\.)\s+/', '', $line);

        return str_contains($line, ' ');
    }

    /**
     * The lines of a document that run past the column.
     *
     * @return list<string>
     */
    private static function overTheColumn(string $markdown): array
    {
        return array_values(array_filter(
            explode("\n", $markdown),
            static fn(string $line): bool => mb_strlen($line) > Wrap::COLUMN,
        ));
    }

    /** The words of a document, with the wrapping taken out. */
    private static function words(string $markdown): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $markdown));
    }
}
