<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\DecisionStatus;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Sources;

/**
 * The shape of decisions/, as far as one branch can be right about it.
 *
 * What every entry is on its own — its id, its group, its date, its status, its
 * fields — is here, because a session working one todo can satisfy all of it.
 * What only the whole checkout can be right about is not: the listing at the
 * foot of a group readme is generated from every file in that group, and a
 * branch that adds one may not touch it (D-FBK-011). `bin/cli decisions:check`
 * holds that half, and the merge is what runs it.
 */
final class DecisionsTest extends TestCase
{
    /**
     * An id is the name a commit, a feedback and a later decision refer to this one
     * by. It decides the group directory and the file name, so two entries
     * cannot quietly share one — which is what the single document this
     * replaces had no way of noticing.
     */
    #[Test]
    public function everyDecisionIsFoundUnderTheIdItGoesBy(): void
    {
        $decisions = Decisions::all();
        $duplicates = Decisions::duplicates();

        self::assertNotSame([], $decisions);
        // The ids rather than the whole map: what a reader of this failure gets
        // is the message, and PHPUnit's diff under it would repeat every path
        // the message already names, in a tail `todo:home` cuts at 30 lines.
        self::assertSame([], array_keys($duplicates), Decisions::collision($duplicates));

        foreach ($decisions as $id => $decision) {
            self::assertSame($id, $decision['heading'], $id . ' has another id in its heading');
            self::assertStringStartsWith(
                strtolower(substr($id, 2)) . '-',
                $decision['file'],
                $id . ' is not the name of its file',
            );
            self::assertSame(
                Decisions::GROUPS[substr($id, 2, 3)] ?? null,
                $decision['group'],
                $id . ' sits in a group its prefix does not name',
            );
        }
    }

    /**
     * The collision is the one failure working in parallel predicts, and the
     * message is all its reader gets — a size mismatch between two counts was
     * what it used to be. Held here rather than by reading it, because the
     * checkout it fails on is the one checkout where nothing collides.
     */
    #[Test]
    public function aDuplicateIdNamesBothFilesAndTheCommandThatMovesOne(): void
    {
        $collision = Decisions::collision([
            'D-FBK-046' => ['decisions/feedback/fbk-046-one.md', 'decisions/feedback/fbk-046-two.md'],
        ]);

        self::assertStringContainsString('decisions/feedback/fbk-046-one.md', $collision);
        self::assertStringContainsString('decisions/feedback/fbk-046-two.md', $collision);
        // The id is what the message cannot end on: the command refuses one two
        // files claim, because it says which number is meant and not which of
        // them moves.
        self::assertStringContainsString('bin/cli decisions:renumber <the file this branch added>', $collision);
        self::assertStringNotContainsString('decisions:renumber D-FBK-046', $collision);
        self::assertSame('', Decisions::collision([]), 'a checkout without a collision says nothing');
    }

    /**
     * The number is the only part of an id a listing sorts on, and three digits
     * is what makes sorting it as text the same as sorting it as a number.
     * `Decisions::all()` compares ids as text, so unpadded it put `D-FBK-10`
     * between `D-FBK-1` and `D-FBK-2` in the generated readme as well —
     * `D-DOC-005`.
     */
    #[Test]
    public function everyNumberIsThreeDigitsWideSoAGroupListsInOrder(): void
    {
        $groups = [];

        foreach (Decisions::all() as $id => $decision) {
            self::assertMatchesRegularExpression(
                '/^D-[A-Z]{3}-\d{3}[a-z]?$/',
                $id,
                $id . ' is numbered in something other than three digits',
            );
            $groups[$decision['group']][] = $decision['file'];
        }

        self::assertNotSame([], $groups);

        foreach ($groups as $group => $files) {
            $asText = $files;
            sort($asText, SORT_STRING);
            $asNumbers = $files;
            usort($asNumbers, static fn(string $a, string $b): int => strnatcmp($a, $b));

            self::assertSame($asNumbers, $asText, $group . '/ lists in another order than it is numbered');
        }
    }

    /**
     * The bold first sentence is the decision, and a reader who stops there
     * knows what was settled. The date is what makes the entry findable a year
     * later, when the wording of the title is not what anybody remembers.
     */
    #[Test]
    public function everyDecisionOpensWithWhatWasDecided(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            self::assertNotSame('', $decision['title'], $id . ' has no title');
            self::assertNotSame('', $decision['statement'], $id . ' decides nothing');
            self::assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}$/',
                $decision['date'],
                $id . ' does not say when it was decided',
            );
            self::assertNotNull(DecisionStatus::tryFrom($decision['status']), $id . ' has no usable status');
        }
    }

    /**
     * The fields are what a reader navigates an entry by, and they had drifted
     * into thirteen spellings of four things before this. The order carries
     * meaning too: the evidence comes before what was decided on it, and
     * everything below **Wrong if** arrived later than the entry did.
     */
    #[Test]
    public function everyDecisionIsWrittenInTheFieldsTheFormatHas(): void
    {
        $known = [...Decisions::FIELDS, ...Decisions::laterFields()];

        foreach (Decisions::all() as $id => $decision) {
            $rank = -1;
            foreach ($decision['fields'] as $field) {
                self::assertContains($field, $known, $id . ' carries a field nothing reads: ' . $field);
                self::assertGreaterThanOrEqual(
                    $rank,
                    Decisions::rank($field),
                    $id . ' has ' . $field . ' below a field that belongs under it',
                );
                $rank = max($rank, Decisions::rank($field));
            }
        }
    }

    /**
     * A decision that cannot say what would falsify it is an opinion with a
     * date on it. This is the one field every entry owes the next reader.
     */
    /**
     * An entry whose history has outgrown it is read out and never failed on.
     *
     * `Confirmed on` is what a reading that held leaves behind, so an entry
     * stating a rule the repository applies often collects one per
     * application, which `bin/cli decisions:check` counts. None of that is a
     * defect,
     * which is exactly why nothing had ever counted it: the cost is a reader
     * who pays more for the history than for the decision, and only a reading
     * says which entries those are — `D-DOC-041`.
     */
    #[Test]
    public function anEntryOutgrownByItsHistoryIsReadOutRatherThanFailedOn(): void
    {
        $outgrown = Decisions::outgrown();

        self::assertNotSame([], $outgrown, 'no entry carries more later reading than decision, which the report would have to say instead');

        $lengths = array_column($outgrown, 'later');
        $sorted = $lengths;
        rsort($sorted);
        self::assertSame($sorted, $lengths, 'the longest history is not first');

        foreach ($outgrown as $entry) {
            self::assertGreaterThan(
                $entry['entry'],
                $entry['later'],
                $entry['id'] . ' is reported as outgrown and its decision is the longer half',
            );
            self::assertGreaterThan(0, $entry['dated'], $entry['id'] . ' has later reading and no dated section');
        }
    }

    /**
     * What an entry pointing at this code owes is read out, never failed on.
     *
     * A test named under **Covered by** is the one coupling that holds: it
     * fails when the behaviour moves, and `everyTestADecisionNamesExists`
     * fails when the test goes with the code. The three entries found stale on
     * 2026-08-22 named no such test, and the two whose code had moved under
     * them named one and were right — so the reading is the absence of a test
     * and not the age of the entry.
     *
     * Most entries here decide something about process and no test could keep
     * them, which is why nothing may fail on this: a demand for **Covered by**
     * would be answered with a name chosen to satisfy it — `D-DOC-043`.
     */
    #[Test]
    public function anEntryNamingThisCodeWithNoTestIsReadOutRatherThanFailedOn(): void
    {
        $uncovered = Decisions::uncovered();

        self::assertNotSame([], $uncovered, 'every entry naming this code names a test, which the report would have to say instead');

        $named = array_column($uncovered, 'names');
        $sorted = $named;
        rsort($sorted);
        self::assertSame($sorted, $named, 'the entry naming the most of our classes is not first');

        $classes = Sources::classes();
        foreach ($uncovered as $entry) {
            $decision = Decisions::all()[$entry['id']];
            $body = (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            self::assertNotContains('Covered by', $decision['fields'], $entry['id'] . ' names a test and is reported as naming none');
            preg_match_all('/`(\w+)::\w+/', $body, $matches);
            self::assertNotSame(
                [],
                array_filter(array_unique($matches[1]), static fn(string $class): bool => isset($classes[$class])),
                $entry['id'] . ' is reported as pointing at this code and points at none',
            );
        }
    }

    /**
     * The other degree of the coupling, and the one that runs the other way.
     *
     * An entry names the test that would catch its **Wrong if**, and the test
     * is where somebody stands when the code moves — so a session that changes
     * the behaviour, fixes the test and never learns which entry rested on it
     * is how `D-ANS-045` came to describe the opposite of what its method does.
     * `Covered by` never asked for the return naming, so this was read out
     * while 405 names were written under the older rule; it fails from the day
     * the last of them was named — `D-DOC-043`.
     */
    #[Test]
    public function everyTestADecisionNamesNamesTheEntryBack(): void
    {
        $loose = [];
        foreach (Decisions::unnamedByItsTests() as $entry) {
            $loose[] = $entry['id'] . ' is named by ' . $entry['silent'] . ' of its ' . $entry['tests']
                . ' tests that say nothing about it';
        }

        self::assertSame([], $loose, 'a test named under Covered by says which entry rests on it');
    }

    /**
     * A dated label is a section, and the spelling it had before `D-DOC-003` is
     * what nothing could read. 51 bold labels in 37 entries survived that move
     * because no check saw them: the field order could not place one, so it sat
     * wherever it was written, and four of them were bullets that
     * `Unresolved::decisions()` did not count as a reading at all.
     */
    #[Test]
    public function noDatedLabelIsWrittenAsABoldParagraph(): void
    {
        $written = [];
        foreach (Decisions::files() as $path) {
            if (preg_match(Decisions::labelAsAParagraph(), (string) file_get_contents($path), $matches) === 1) {
                $written[] = basename($path) . ': ' . trim($matches[0]);
            }
        }

        self::assertSame([], $written, 'a dated label opens a line in bold, and a dated label is a section');
    }

    #[Test]
    public function everyDecisionSaysWhatWouldShowItToBeWrong(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            self::assertContains(
                'Wrong if',
                $decision['fields'],
                $id . ' does not say what would show it to be wrong',
            );
        }
    }

    /**
     * `confirmed` and `revoked` are claims about a later reading, and the line
     * that carries it has to be in the file. The status names the **last** of
     * them rather than the only one: an entry may be confirmed by one run and
     * revoked by the next, and what a reader relies on is the latest —
     * `D-DOC-003`.
     */
    #[Test]
    public function aStatusNamesTheLastDatedLineInTheFile(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            $dated = Decisions::datedLines($decision['fields']);
            $latest = $dated === [] ? '' : $dated[count($dated) - 1];

            self::assertSame(
                Decisions::fieldFor($decision['status']),
                $latest,
                $id . ' is ' . $decision['status'] . ' and its last dated line is '
                    . ($latest === '' ? 'none' : $latest),
            );
        }
    }

    /**
     * A test named in a decision is a claim that something would catch the
     * **Wrong if** happening, and a renamed test turns it into a claim nobody
     * answers for — which reads exactly like one that still holds. This covers
     * the `Covered by` field and every test named in passing, because the prose
     * makes the same claim and goes stale the same way — `D-DOC-003`.
     */
    #[Test]
    public function everyTestADecisionNamesExists(): void
    {
        $methods = $this->testMethods();

        foreach (Decisions::all() as $id => $decision) {
            preg_match_all(
                '/\b([A-Z]\w*Test::\w+)/',
                (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']),
                $matches,
            );
            foreach (array_unique($matches[1]) as $named) {
                self::assertContains($named, $methods, $id . ' names ' . $named . ', which no test declares');
            }
        }
    }

    /**
     * A decision nobody has been back to names no command the console lost.
     *
     * `Cli::knows()` answered this for a todo's `**Run:**` line and for nothing
     * else, so a deleted command stayed written down as the way to do the thing.
     * `D-FBK-012` still had `bin/cli feedback:next` at the head of it 16 days
     * after `D-FBK-016` deleted the command and the sighting that ran it, and
     * nothing failed, because nothing asks.
     *
     * The head only — the statement and the paragraphs above the first section.
     * Below it an entry is an account of what was decided and what was rejected,
     * and the entry that removes a command names it there of necessity:
     * `D-FBK-045` says `bin/cli todo:sync` is deleted, which is the sentence
     * doing its job.
     *
     * And only where no dated section stands. One of those is somebody having
     * been back and written what changed, which is the mechanism this
     * repository already has for an entry that aged; a head left standing under
     * one is a question about how a record is kept rather than a name nothing
     * holds — `D-DOC-037`.
     */
    #[Test]
    public function anUnvisitedDecisionNamesNoCommandTheConsoleLost(): void
    {
        $lost = [];
        foreach (Decisions::all() as $id => $decision) {
            if ($decision['status'] === DecisionStatus::Revoked->value
                || array_intersect(Decisions::laterFields(), $decision['fields']) !== []
            ) {
                continue;
            }

            $contents = (string) file_get_contents(Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            $head = (string) preg_split('/^## /m', (string) preg_replace('/^---\R.*?\R---\R/s', '', $contents), 2)[0];
            preg_match_all('#bin/cli [a-z]+:[a-z]+#', $head, $matches);
            foreach (array_unique($matches[0]) as $named) {
                if (!Cli::knows($named)) {
                    $lost[] = $id . ' opens by naming `' . $named . '`, which the console does not have';
                }
            }
        }

        self::assertSame([], $lost);
    }

    /**
     * @return array<int, string>
     */
    private function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (Finder::create()->files()->in(Paths::root() . '/tests/' . $suite)->depth(0)->name('*Test.php')->sortByName() as $file) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = $file->getBasename('.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }

    /**
     * A decision that names a requirement is reasoning from it. One that names
     * a requirement nobody can read any more is reasoning from nothing.
     */
    #[Test]
    public function everyRequirementADecisionNamesExists(): void
    {
        $requirements = Requirements::all();

        foreach (Decisions::files() as $path) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($path), $matches);
            foreach ($matches[1] as $id) {
                self::assertArrayHasKey(
                    $id,
                    $requirements,
                    basename($path) . ' names ' . $id . ', which no requirement has',
                );
            }
        }
    }
}
