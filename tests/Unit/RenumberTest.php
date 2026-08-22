<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Upkeep\Links;
use TYPO3\DevCompanion\Upkeep\Renumber;

/**
 * A decision really moved to another number, in a corpus of this case's own.
 *
 * The command's whole value is that it leaves nothing behind, so the cases that
 * matter are the accounting ones: every line naming the old id is either
 * rewritten or reported, and a renumber that dropped one would fail silently,
 * because the entry the stale reference now points at exists.
 *
 * The corpus is written rather than the repository's own, for `R-COD-003`: this
 * case renames files and rewrites them, and doing that to `decisions/` would
 * leave the checkout wrong wherever a run stops in the middle. It carries one of
 * each reference this repository writes.
 */
final class RenumberTest extends TestCase
{
    private const MOVED = 'D-GUI-901';
    private const TO = 'D-GUI-903';
    private const OLD = 'gui-901-a-fixture-entry-the-renumber-case-moves.md';
    private const NEW = 'gui-903-a-fixture-entry-the-renumber-case-moves.md';

    private string $root = '';

    #[After]
    public function removeTheCorpus(): void
    {
        if ($this->root !== '') {
            Directory::remove($this->root);
            $this->root = '';
        }
    }

    /**
     * The entry is its id: the file name, the front matter and the heading, all
     * three of which `decisions:check` compares against each other.
     */
    #[Test]
    public function theEntryTakesTheNumberInItsNameItsFrontMatterAndItsHeading(): void
    {
        $root = $this->corpus();
        $report = Renumber::decision($root, $this->entry($root), self::TO);

        self::assertSame('decisions/guides/' . self::NEW, $report['file']);
        self::assertFileDoesNotExist($root . '/decisions/guides/' . self::OLD);
        self::assertStringContainsString(
            "id: D-GUI-903\n",
            (string) file_get_contents($root . '/decisions/guides/' . self::NEW),
        );
        self::assertStringContainsString(
            '# D-GUI-903 — A fixture entry the renumber case moves',
            (string) file_get_contents($root . '/decisions/guides/' . self::NEW),
        );
    }

    /**
     * A link path says which entry is meant, whichever of the two forms it is
     * written in. The reference definition a generated listing ends with carries
     * the path, so the usage above it is settled by the same file.
     */
    #[Test]
    public function aReferenceWhoseOwnLineNamesTheFileMovesWithIt(): void
    {
        $root = $this->corpus();
        Renumber::decision($root, $this->entry($root), self::TO);

        self::assertStringContainsString(
            '[`D-GUI-903`](../../decisions/guides/' . self::NEW . ')',
            (string) file_get_contents($root . '/scenarios/contracts/ext-03-a-fixture-case.md'),
        );
        self::assertStringContainsString(
            "- [`D-GUI-903`][D-GUI-903] — A fixture entry the renumber case moves · 2026-07-29\n",
            (string) file_get_contents($root . '/decisions/guides/readme.md'),
        );
        self::assertStringContainsString(
            '[D-GUI-903]: ' . self::NEW . "\n",
            (string) file_get_contents($root . '/decisions/guides/readme.md'),
        );
        self::assertStringContainsString(
            '[D-GUI-903]: guides/' . self::NEW . "\n",
            (string) file_get_contents($root . '/decisions/readme.md'),
        );
    }

    /**
     * The one both mis-pointings on record were. A `restsOn:` is checked for
     * existence and never for correctness, and a sentence naming an id is
     * checked for neither — so moving either is a guess, and the entry it would
     * land on is real whichever way the guess went — `D-DOC-015`.
     */
    #[Test]
    public function aReferenceNoLineSettlesIsNamedRatherThanMoved(): void
    {
        $root = $this->corpus();
        $report = Renumber::decision($root, $this->entry($root), self::TO);

        $named = array_map(
            static fn(array $reference): string => $reference['file'] . ':' . $reference['line'],
            $report['named'],
        );

        self::assertSame([
            'decisions/guides/gui-902-a-fixture-entry-that-stays-where-it-is.md:11',
            'requirements/task-skills/skl-901-a-fixture-requirement-that-rests-on-one.md:4',
            'requirements/task-skills/skl-901-a-fixture-requirement-that-rests-on-one.md:13',
            'tests/Unit/ScopeTest.php:5',
        ], $named);

        self::assertStringContainsString(
            'restsOn: [D-GUI-901, D-SKL-901]',
            (string) file_get_contents($root . '/requirements/task-skills/skl-901-a-fixture-requirement-that-rests-on-one.md'),
        );
    }

    /**
     * The whole of what the command is worth. A line naming the old id is
     * rewritten or reported and never neither, and once the call is over the
     * lines still naming it are the reported ones exactly — so a person handed
     * that list has been handed all of it — `D-DOC-015`.
     */
    #[Test]
    public function everyMentionIsEitherMovedOrNamed(): void
    {
        $root = $this->corpus();
        $before = $this->mentions($root);

        $report = Renumber::decision($root, $this->entry($root), self::TO);

        $moved = array_map(static fn(array $r): string => $r['file'] . ':' . $r['line'], $report['moved']);
        $named = array_map(static fn(array $r): string => $r['file'] . ':' . $r['line'], $report['named']);

        self::assertNotSame([], $before);
        self::assertSame([], array_intersect($moved, $named), 'a line was both moved and named');
        self::assertEqualsCanonicalizing(
            // The entry's own two lines are reported where they are afterwards,
            // which is the file under its new name.
            str_replace(self::OLD, self::NEW, $before),
            [...$moved, ...$named],
            'a line naming the id was neither moved nor named',
        );
        self::assertEqualsCanonicalizing($named, $this->mentions($root), 'a mention was left where nothing names it');
    }

    /**
     * The other half of leaving nothing behind: a path that no longer resolves.
     * `links:check` would catch this one, which is why the command may not
     * produce it — a renumber that needs a check to finish it is a renumber
     * somebody has to remember to finish — `D-DOC-015`.
     */
    #[Test]
    public function noPathIsLeftPointingAtTheOldFile(): void
    {
        $root = $this->corpus();
        Renumber::decision($root, $this->entry($root), self::TO);

        $dead = [];
        foreach (Finder::create()->files()->in($root)->name('*.md') as $file) {
            self::assertStringNotContainsString(self::OLD, (string) file_get_contents($file->getPathname()));
            $dead = [...$dead, ...Links::deadIn($file->getPathname())];
        }

        self::assertSame([], $dead);
    }

    /**
     * `D-GUI-901b` is the entry that was split off `D-GUI-901` and never a
     * spelling of it — D-DOC-005. It is the case a search and replace over the
     * id gets wrong without anything being ambiguous about it — `D-DOC-015`.
     */
    #[Test]
    public function aLetterSuffixIsAnotherEntryAndStaysWhereItIs(): void
    {
        $root = $this->corpus();
        $split = $root . '/decisions/guides/gui-901b-a-fixture-entry-split-off-the-one-that-moves.md';
        $before = (string) file_get_contents($split);

        Renumber::decision($root, $this->entry($root), self::TO);

        self::assertFileExists($split);
        self::assertSame($before, (string) file_get_contents($split));
    }

    /**
     * One past the highest rather than the first gap, because an id is never
     * reused and nothing here reads a gap.
     */
    #[Test]
    public function theNextNumberIsOnePastTheHighestTheGroupHas(): void
    {
        $root = $this->corpus();

        self::assertSame('D-GUI-903', Renumber::next($root, 'GUI'));
        self::assertSame('D-SKL-902', Renumber::next($root, 'SKL'));
    }

    /**
     * A renumbering that cannot be right is refused rather than half done. The
     * group is the one that matters: the prefix names the directory, so moving
     * it is a re-filing and what the entry is about moves with it.
     */
    #[Test]
    public function aMoveThatCannotBeRightIsRefused(): void
    {
        $root = $this->corpus();

        $refusals = [
            'gui-999-a-file-no-group-holds.md' => ['file' => $root . '/decisions/guides/gui-999-a-file-no-group-holds.md', 'to' => self::TO, 'because' => 'is no decision in'],
            'D-GUI-902' => ['file' => $this->entry($root), 'to' => 'D-GUI-902', 'because' => 'is already'],
        ];
        foreach ($refusals as $case => $refusal) {
            try {
                Renumber::decision($root, $refusal['file'], $refusal['to']);
                self::fail($case . ' was not refused');
            } catch (\InvalidArgumentException $exception) {
                self::assertStringContainsString($refusal['because'], $exception->getMessage());
            }
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('are not in one group');
        Renumber::decision($root, $this->entry($root), 'D-SKL-950');
    }

    /**
     * The state the command exists for, and the one it got wrong twice on
     * 2026-08-18: two files carry one id after a rebase, and the caller names
     * the branch's, because the entry already on `main` keeps its number. What
     * moved instead was whichever file sorted first, which was `main`'s both
     * times, and nothing failed afterwards — both ids existed and both files
     * were real.
     */
    #[Test]
    public function theFileNamedIsTheOneThatMovesWhenTwoCarryTheId(): void
    {
        $root = $this->corpus();
        // Sorting before the entry the case moves, which is how the wrong one
        // was picked: `a-second` against `a-fixture` puts this one second, so
        // the file that sorted first was the one nobody named.
        $collision = $root . '/decisions/guides/gui-901-a-collision-cut-from-another-branch.md';
        file_put_contents($collision, "---\nid: D-GUI-901\ndate: 2026-08-18\nstatus: open\n---\n\n# D-GUI-901 — A collision cut from another branch\n\n**This entry carries the number the other one does too.**\n");

        $report = Renumber::decision($root, $this->entry($root), self::TO);

        self::assertSame('decisions/guides/' . self::NEW, $report['file']);
        self::assertFileExists($collision);
        self::assertStringContainsString("id: D-GUI-901\n", (string) file_get_contents($collision));
    }

    /**
     * The entry is recognised among the documents by being the same path, and
     * the caller writes that path however they reached the file. Named in any
     * other spelling than the one the corpus is read in, it was rewritten
     * everywhere except in its own front matter and heading — which is the one
     * place `decisions:check` reads, so the run looked finished and the entry
     * carried a number no longer its own.
     */
    #[Test]
    public function theEntryMovesWhateverSpellingOfItsPathTheCallerUsed(): void
    {
        $root = $this->corpus();

        $report = Renumber::decision($root, $root . '/decisions/guides/../guides/' . self::OLD, self::TO);

        self::assertSame('decisions/guides/' . self::NEW, $report['file']);
        self::assertStringContainsString(
            "id: D-GUI-903\n",
            (string) file_get_contents($root . '/decisions/guides/' . self::NEW),
        );
    }

    /** Every file naming one id, which is two only after a rebase merged two branches. */
    #[Test]
    public function anIdTwoFilesCarryNamesBothOfThem(): void
    {
        $root = $this->corpus();
        $collision = $root . '/decisions/guides/gui-901-a-collision-cut-from-another-branch.md';
        file_put_contents($collision, "---\nid: D-GUI-901\n---\n\n# D-GUI-901 — A collision cut from another branch\n");

        self::assertSame([$collision, $this->entry($root)], Renumber::files($root, self::MOVED));
        self::assertSame([$this->entry($root)], Renumber::files($root, $this->entry($root)));
        self::assertSame([], Renumber::files($root, 'D-GUI-999'));
    }

    /** The entry every case moves, by the path a caller would name it with. */
    private function entry(string $root): string
    {
        return $root . '/decisions/guides/' . self::OLD;
    }

    /**
     * Every line naming the moved id, as `file:line`, over the whole corpus.
     *
     * @return list<string>
     */
    private function mentions(string $root): array
    {
        $mentions = [];
        foreach (Finder::create()->files()->in($root)->sortByName() as $file) {
            $lines = explode("\n", (string) file_get_contents($file->getPathname()));
            foreach ($lines as $number => $line) {
                if (preg_match('/\b' . self::MOVED . '(?![0-9a-z])/', $line) === 1) {
                    $mentions[] = substr($file->getPathname(), strlen($root) + 1) . ':' . ($number + 1);
                }
            }
        }

        return $mentions;
    }

    /**
     * One of every reference this repository writes to a decision, in a
     * directory this case owns.
     */
    private function corpus(): string
    {
        $this->root = (string) tempnam(sys_get_temp_dir(), 'renumber');
        unlink($this->root);

        foreach ($this->files() as $path => $contents) {
            $file = $this->root . '/' . $path;
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0o777, true);
            }
            file_put_contents($file, $contents);
        }

        return $this->root;
    }

    /** @return array<string, string> */
    private function files(): array
    {
        return [
            'decisions/guides/' . self::OLD => <<<'MARKDOWN'
                ---
                id: D-GUI-901
                date: 2026-07-29
                status: open
                ---

                # D-GUI-901 — A fixture entry the renumber case moves

                **This entry exists so that something can be moved off its number.**

                ## Wrong if

                - Nothing ever has to move.

                MARKDOWN,
            'decisions/guides/gui-901b-a-fixture-entry-split-off-the-one-that-moves.md' => <<<'MARKDOWN'
                ---
                id: D-GUI-901b
                date: 2026-07-30
                status: open
                ---

                # D-GUI-901b — A fixture entry split off the one that moves

                **`D-GUI-901b` is its own decision.**

                ## Wrong if

                - Somebody reads it as a spelling of the number it was split off.

                MARKDOWN,
            'decisions/guides/gui-902-a-fixture-entry-that-stays-where-it-is.md' => <<<'MARKDOWN'
                ---
                id: D-GUI-902
                date: 2026-07-29
                status: open
                ---

                # D-GUI-902 — A fixture entry that stays where it is

                **This entry exists so that the group has an entry that does not move.**

                `D-GUI-901` has been waiting for exactly this run.

                ## Wrong if

                - Nothing at all.

                MARKDOWN,
            'decisions/guides/readme.md' => <<<'MARKDOWN'
                # Guides

                What a returned draft is worth.

                - [`D-GUI-902`][D-GUI-902] — A fixture entry that stays where it is · 2026-07-29
                - [`D-GUI-901b`][D-GUI-901b] — A fixture entry split off the one that moves · 2026-07-30
                - [`D-GUI-901`][D-GUI-901] — A fixture entry the renumber case moves · 2026-07-29

                [D-GUI-902]: gui-902-a-fixture-entry-that-stays-where-it-is.md
                [D-GUI-901b]: gui-901b-a-fixture-entry-split-off-the-one-that-moves.md
                [D-GUI-901]: gui-901-a-fixture-entry-the-renumber-case-moves.md

                MARKDOWN,
            'decisions/readme.md' => <<<'MARKDOWN'
                # What a change assumed

                ### guides

                - [`D-GUI-902`][D-GUI-902] — A fixture entry that stays where it is · 2026-07-29
                - [`D-GUI-901b`][D-GUI-901b] — A fixture entry split off the one that moves · 2026-07-30
                - [`D-GUI-901`][D-GUI-901] — A fixture entry the renumber case moves · 2026-07-29

                [D-GUI-902]: guides/gui-902-a-fixture-entry-that-stays-where-it-is.md
                [D-GUI-901b]: guides/gui-901b-a-fixture-entry-split-off-the-one-that-moves.md
                [D-GUI-901]: guides/gui-901-a-fixture-entry-the-renumber-case-moves.md

                MARKDOWN,
            'decisions/task-skills/skl-901-a-fixture-entry-in-another-group.md' => <<<'MARKDOWN'
                ---
                id: D-SKL-901
                date: 2026-08-03
                status: open
                ---

                # D-SKL-901 — A fixture entry in another group

                **This entry exists so that a `restsOn:` names two decisions.**

                ## Wrong if

                - Nothing rests on it.

                MARKDOWN,
            'requirements/task-skills/skl-901-a-fixture-requirement-that-rests-on-one.md' => <<<'MARKDOWN'
                ---
                id: R-SKL-901
                status: held
                restsOn: [D-GUI-901, D-SKL-901]
                ---

                # R-SKL-901 — A fixture requirement that rests on one

                **This requirement exists so that a `restsOn:` names the entry being moved.**

                ## From

                The sentence naming it is prose. `D-GUI-901` is bare here, which is
                the form both mis-pointings on record were.

                MARKDOWN,
            'scenarios/contracts/ext-03-a-fixture-case.md' => <<<'MARKDOWN'
                # A fixture case that links the entry

                What the link path settles rather than leaves to a person is
                [`D-GUI-901`](../../decisions/guides/gui-901-a-fixture-entry-the-renumber-case-moves.md).

                MARKDOWN,
            'tests/Unit/ScopeTest.php' => <<<'PHP'
                <?php

                declare(strict_types=1);

                // A comment naming an entry, which is bare wherever it is written (`D-GUI-901`).
                PHP,
        ];
    }
}
