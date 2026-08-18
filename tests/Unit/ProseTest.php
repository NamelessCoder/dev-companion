<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Prose;
use TYPO3\DevCompanion\Upkeep\Wrap;

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
    /**
     * `R-COD-002`. The half a caller pays for is reached at all.
     *
     * What is held is that the reading happens and reaches every tool, not that
     * nothing runs long: `Prose::documents()` reads the markdown corpus and no
     * file in `src/`.
     */
    #[Test]
    public function theProseAClientIsHandedIsMeasured(): void
    {
        $payload = Prose::payload();
        $where = array_column($payload, 'where');

        self::assertContains('instructions', $where);
        foreach (array_column(Registry::definitions(), 'name') as $tool) {
            self::assertContains($tool . ' description', $where, $tool . ' has no description in the payload');
        }

        // The nested walk, not one level of it: a field inside items inside
        // properties is read by the same client as the one at the top.
        self::assertContains('typo3_gerrit_lookup output changes.subject', $where);
        self::assertContains('typo3_gerrit_lookup output indistinguishable', $where);

        self::assertGreaterThan(0, Prose::payloadWeight());
        foreach (Prose::payloadOverTheMeasure() as $entry) {
            self::assertGreaterThan(Prose::MEASURE, $entry['words']);
            self::assertContains($entry['where'], $where);
        }
    }

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
     * `D-DOC-035`. The comments are reached, and what they cost is a share.
     *
     * The sentence measure reads markdown and no file in `src/`, so the third
     * of the PHP that is comment was counted by nobody. What is held is that
     * the reading happens over both halves of the corpus and that a retelling
     * is a comment naming an entry, not that any number stays where it is.
     */
    #[Test]
    public function whatTheCommentsCostIsMeasured(): void
    {
        $code = Prose::code();

        self::assertContains('src/Upkeep/Prose.php', $code);
        self::assertContains('tests/Unit/ProseTest.php', $code);
        self::assertSame([], array_filter($code, static fn(string $file): bool => !str_ends_with($file, '.php')));

        $weight = Prose::commentWeight();
        self::assertGreaterThan(0, $weight['comment']);
        self::assertGreaterThan($weight['comment'], $weight['lines']);
    }

    /**
     * A comment resting on a decision names its id instead of repeating what
     * it settled — AGENTS.md's rule, and the one the sentence measure cannot
     * see: a retelling is within the measure on every sentence of it.
     *
     * Reported rather than failed on, so what is held is that the report finds
     * the shape it claims to find.
     */
    #[Test]
    public function aCommentThatNamesAnEntryAndRetellsItAnywayIsReported(): void
    {
        $retold = Prose::retellings();

        self::assertNotEmpty($retold, 'the measure found nothing at all, which means it read nothing');
        foreach ($retold as $comment) {
            self::assertGreaterThan(Prose::RETOLD, $comment['lines']);
            self::assertNotEmpty($comment['names']);
            self::assertContains($comment['file'], Prose::code());
            self::assertGreaterThan(0, $comment['line']);
        }
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
        self::assertContains('documentation/readme.rst', $documents);
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
                self::words(self::rewrapped($document, $contents)),
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
            $once = self::rewrapped($document, (string) file_get_contents(Paths::root() . '/' . $document));

            self::assertSame($once, self::rewrapped($document, $once), $document . ' does not settle');
        }
    }

    /**
     * Which formatter a file gets, asked the way `prose:format` asks it.
     *
     * The corpus is two markups since `D-DOC-029`, and running the markdown
     * reader over reStructuredText passes both tests above while holding
     * nothing: it preserves the words and settles, and it does that by wrapping
     * a heading whose rule is then the wrong length.
     */
    private static function rewrapped(string $document, string $contents): string
    {
        return str_ends_with($document, '.rst') ? Wrap::rst($contents) : Wrap::document($contents);
    }

    /**
     * What carries its meaning in a line break or a column, in the other
     * markup: a heading and the rule under it, a directive and its indent, a
     * drawn table, a label.
     */
    #[Test]
    public function whatIsNotProseInReStructuredTextComesBackUnchanged(): void
    {
        $document = ".. _a-label:\n\nA heading that is long enough to be wrapped if anything wrapped a heading at all\n"
            . "===============================================================================\n\n"
            . ".. code-block:: php\n\n    \$a = 'one very long line of code that no formatter here is allowed to touch';\n\n"
            . ".. image:: ../images/answer-sources.svg\n    :alt: A sentence standing under the directive that owns it.\n\n"
            . "=========  ==============================================================\n"
            . "Source     What it means\n"
            . "=========  ==============================================================\n"
            . "knowledge  Bundled, and it answers with nothing at all running on the box.\n"
            . "=========  ==============================================================\n";

        self::assertSame($document, Wrap::rst($document));
    }

    /**
     * A literal and a role are spans a line break would break, and both are
     * written with the backticks the markdown reader treats as one span.
     */
    #[Test]
    public function aLiteralAndARoleAreNeverBrokenAcrossLines(): void
    {
        $wrapped = Wrap::rst(
            'A sentence long enough to be wrapped somewhere near its end, and then '
            . '``bin/cli hints:probe`` plus :doc:`the writing rules <../contributing/glossary>` at the end of it.',
        );

        self::assertStringContainsString('``bin/cli hints:probe``', $wrapped);
        self::assertStringContainsString(':doc:`the writing rules <../contributing/glossary>`', $wrapped);
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
            . '`bin/cli hints:probe` plus [the writing rules](documentation/contributing/glossary.rst) at the end of it.',
        );

        self::assertStringContainsString('`bin/cli hints:probe`', $wrapped);
        self::assertStringContainsString('[the writing rules](documentation/contributing/glossary.rst)', $wrapped);
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
     * A list is what markdown reads as one, and a figure at the head of a line
     * is not.
     *
     * The formatter decides where a paragraph ends, so reading a marker where
     * markdown reads none reformats prose that nobody wrote as a list — and no
     * word moves, so the corpus assertions above pass while it happens. The
     * rule markdown uses is what separates the two: a bullet and a `1.` may
     * interrupt a paragraph, any other figure only as the next item of the list
     * already running.
     */
    #[Test]
    #[DataProvider('linesThatOpenAnItemAndLinesThatOnlyLookLikeIt')]
    public function aListIsWhatMarkdownReadsAsOne(string $markdown, string $expected): void
    {
        self::assertSame($expected, Wrap::document($markdown));
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function linesThatOpenAnItemAndLinesThatOnlyLookLikeIt(): array
    {
        return [
            // `D-KNW-049` has one, and a run of the formatter hung the rest of
            // the paragraph off the port number.
            'a figure a wrap left at the head of a line is prose' => [
                "and `GetInternalPort` answers the constant 3306 or\n"
                . "5432. Nothing on that path reads `omit_containers`, and the file is\n"
                . 'written anyway.',
                'and `GetInternalPort` answers the constant 3306 or 5432. Nothing on that path'
                . "\nreads `omit_containers`, and the file is written anyway.",
            ],
            'an ordered list runs on past the item it starts with' => [
                "1. The first of them, long enough that it has to be wrapped somewhere near the end of it.\n"
                . '2. The second.',
                "1. The first of them, long enough that it has to be wrapped somewhere near the\n"
                . "   end of it.\n"
                . '2. The second.',
            ],
            'a bracket closes an ordered marker as a full stop does' => [
                "1) The first of them, long enough that it has to be wrapped somewhere near the end of it.\n"
                . '2) The second.',
                "1) The first of them, long enough that it has to be wrapped somewhere near the\n"
                . "   end of it.\n"
                . '2) The second.',
            ],
            'a bullet interrupts a paragraph wherever it stands' => [
                "The paragraph this one interrupts.\n"
                . '- The item, long enough that it has to be wrapped somewhere near the end of it all.',
                "The paragraph this one interrupts.\n"
                . "- The item, long enough that it has to be wrapped somewhere near the end of it\n"
                . '  all.',
            ],
            'a figure under an item of another list is that item still' => [
                "- The first of them, long enough that it has to be wrapped somewhere near\n"
                . '  5432. Nothing on that path reads it.',
                "- The first of them, long enough that it has to be wrapped somewhere near 5432.\n"
                . '  Nothing on that path reads it.',
            ],
            // What `1830ee9` did to `typo3-core-patch-development`: steps 4, 5
            // and 6 stood under a sub-bullet of step 3, and read against that
            // bullet alone each of them is a figure at the head of a line. The
            // list they belong to is the one at their own indent, which is open
            // the whole time and two levels up from the paragraph above them.
            'a step after a nested bullet is the outer list\'s next item' => [
                "3. The step, with a reading under it:\n"
                . "   - The reading, long enough that it has to be wrapped somewhere near the end of it.\n"
                . '4. The step after it.',
                "3. The step, with a reading under it:\n"
                . "   - The reading, long enough that it has to be wrapped somewhere near the end\n"
                . "     of it.\n"
                . '4. The step after it.',
            ],
            'a figure after a nested bullet of another list is prose still' => [
                "- The item, with a reading under it:\n"
                . "  - The reading, long enough that it has to be wrapped somewhere near\n"
                . '    5432. Nothing on that path reads it.',
                "- The item, with a reading under it:\n"
                . "  - The reading, long enough that it has to be wrapped somewhere near 5432.\n"
                . '    Nothing on that path reads it.',
            ],
            'a marker with nothing after it opens no item' => [
                "-\n- The item, long enough that it has to be wrapped somewhere near the end of it all.",
                "-\n"
                . "- The item, long enough that it has to be wrapped somewhere near the end of it\n"
                . '  all.',
            ],
        ];
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
