<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Upkeep\Decisions;
use Typo3CmsMcp\Upkeep\Requirements;

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
        $files = Decisions::files();
        $decisions = Decisions::all();

        self::assertNotSame([], $decisions);
        self::assertCount(count($files), $decisions, 'two decision files claim the same id');

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
            self::assertContains($decision['status'], Decisions::STATUSES, $id . ' has no usable status');
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
        $known = [...Decisions::FIELDS, ...Decisions::LATER_FIELDS];

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
     * `tested` and `corrected` are claims about a later run, and the line that
     * carries it has to be in the file. Both directions: a status without its
     * line says nothing, and a line without its status is invisible in every
     * listing a reader looks at first.
     */
    #[Test]
    public function aStatusAndTheLineBehindItAgree(): void
    {
        foreach (Decisions::all() as $id => $decision) {
            $later = Decisions::fieldFor($decision['status']);
            if ($later !== '') {
                self::assertContains(
                    $later,
                    $decision['fields'],
                    $id . ' is ' . $decision['status'] . ' and carries no ' . $later . ' line',
                );
            }

            foreach (['Tested on', 'Corrected on'] as $field) {
                if (in_array($field, $decision['fields'], true)) {
                    self::assertSame(
                        $field,
                        $later,
                        $id . ' carries a ' . $field . ' line and does not say so in its status',
                    );
                }
            }
        }
    }

    /**
     * The listing under each readme is generated from the files below it, so a
     * decision added without `bin/cli decisions:index` is missing from the one
     * place a reader looks first — and the root listing is the only place every
     * decision stands in one order.
     */
    #[Test]
    public function everyGroupListsWhatIsInIt(): void
    {
        foreach (['', ...array_values(Decisions::GROUPS)] as $group) {
            $readme = Decisions::directory() . '/' . ($group === '' ? '' : $group . '/') . 'readme.md';

            self::assertFileExists($readme);
            self::assertStringEndsWith(
                Decisions::listing($group),
                (string) file_get_contents($readme),
                ($group === '' ? 'readme.md' : $group . '/readme.md')
                    . ' is not the listing of its files — run bin/cli decisions:index',
            );
        }
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
