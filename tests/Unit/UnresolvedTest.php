<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\DecisionStatus;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Todo;
use TYPO3\DevCompanion\Upkeep\Unresolved;

final class UnresolvedTest extends TestCase
{
    /**
     * The reading exists so that nothing unfinished can be invisible, which
     * only holds if it is the whole of what is unfinished. A reading that
     * shows some of it is worse than none: it reads as a complete list.
     */
    #[Test]
    public function everyRequirementNothingAnswersForIsInTheReading(): void
    {
        $expected = [];
        foreach (Requirements::all() as $requirement) {
            if (!Requirements::state($requirement)->isGuarded()) {
                // The value rather than the case: the reading is printed, and
                // it declares `state` as a string. Comparing the enum passed
                // for as long as every requirement was guarded and the two
                // empty arrays never said which side was right.
                $expected[$requirement['id']] = Requirements::state($requirement)->value;
            }
        }

        $reported = [];
        foreach (Unresolved::requirements() as $requirement) {
            $reported[$requirement['id']] = $requirement['state'];
        }

        self::assertSame($expected, $reported);
    }

    /**
     * A queued todo naming the id is the whole coupling between what must
     * be true and the order the work happens in. An entry nobody has queued is
     * the case the reading exists for, so getting that flag backwards would
     * hide exactly the entries it is meant to surface.
     *
     * It is what the *queue* names, not what `todo/` contains. The directory
     * also keeps the page listing what is deliberately not queued, and an id
     * named there has been decided about in the opposite direction.
     *
     * Read as two lists rather than entry by entry, because the reading is
     * empty on any day every requirement is held, and a loop over nothing is a
     * test that reports as passing while holding no such thing.
     */
    #[Test]
    public function anEntryIsQueuedWhenAnItemNamesIt(): void
    {
        $reading = Unresolved::requirements();

        $flagged = array_column(array_filter($reading, static fn(array $r): bool => $r['queued']), 'id');
        $named = array_values(array_intersect(array_column($reading, 'id'), Todo::serves()));

        self::assertSame($named, $flagged);
    }

    /**
     * The second answer, and the one the reading could not see until this was
     * written: a requirement no test can hold is a legitimate state, so an
     * entry whose **Held by** says so stays in the listing for good, and every
     * session that ran `judge what nothing has answered for` re-derived the
     * same judgement about the same entries.
     *
     * The date is carried through rather than folded into a flag, because the
     * entry can be rewritten under the stamp and nothing catches that: what the
     * reading can do is print the day the judgement was made — `D-DOC-038`.
     */
    #[Test]
    public function aJudgedEntryCarriesTheDayItWasDecidedOn(): void
    {
        $reading = Unresolved::requirements();
        $requirements = Requirements::all();

        $judged = array_column(array_filter($reading, static fn(array $r): bool => $r['judged'] !== ''), 'id');
        self::assertNotSame([], $judged, 'no unguarded requirement has been judged, which the reading would have to say instead');

        foreach ($reading as $entry) {
            self::assertSame(
                $requirements[$entry['id']]['judged'],
                $entry['judged'],
                $entry['id'] . ' is read out with a judgement its file does not carry',
            );
        }
    }

    /**
     * `open` is two states and the report separates them, so the flag that
     * separates them has to be the file's own. A **Since then** is what a
     * reading that settles the **Wrong if** neither way leaves behind, and half
     * the open entries carry one — counted as unread, the pile reads as
     * untouched and the oldest named is one somebody has already been back to.
     *
     * Both spellings counted while the corpus had two. It has one since
     * `D-DOC-039`, which is the entry this holds and where the numbers are.
     */
    #[Test]
    public function anOpenDecisionSomebodyHasBeenBackToIsToldApart(): void
    {
        $open = Unresolved::decisions();
        $decisions = Decisions::all();

        $revisited = array_filter($open, static fn(array $d): bool => $d['revisited']);
        self::assertNotSame([], $revisited, 'no open decision carries a Since then, which the report would have to say instead');
        self::assertNotSame($open, array_values($revisited), 'every open decision has been back-checked, which the report would have to say instead');

        foreach ($open as $decision) {
            $path = Decisions::directory() . '/' . $decisions[$decision['id']]['group']
                . '/' . $decisions[$decision['id']]['file'];
            self::assertSame(
                preg_match('/^(## |\*\*)Since then\b/m', (string) file_get_contents($path)) === 1,
                $decision['revisited'],
                $decision['id'] . ' is reported as ' . ($decision['revisited'] ? 'read' : 'unread') . ', and its file says otherwise',
            );
        }
    }

    /**
     * The oldest open decision is the one the repository has moved furthest
     * away from, so it is the candidate the report names. Decisions::all() is
     * newest first for the listings, and this is the one caller that wants the
     * other end — `D-DOC-003`.
     */
    #[Test]
    public function theOpenDecisionsAreReadOldestFirst(): void
    {
        $open = Unresolved::decisions();

        self::assertNotSame([], $open, 'no decision is open, which the report would have to say instead');

        $dates = array_column($open, 'date');
        $sorted = $dates;
        sort($sorted);
        self::assertSame($sorted, $dates);

        foreach ($open as $decision) {
            self::assertSame(
                DecisionStatus::Open->value,
                Decisions::all()[$decision['id']]['status'],
                $decision['id'] . ' has been back-checked and is still reported as waiting',
            );
        }
    }
}
