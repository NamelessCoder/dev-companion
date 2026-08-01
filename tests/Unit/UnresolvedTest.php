<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Decisions;
use Typo3CmsMcp\Requirements;
use Typo3CmsMcp\Todo;
use Typo3CmsMcp\Unresolved;

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
            if (Requirements::state($requirement) !== 'held') {
                $expected[$requirement['id']] = Requirements::state($requirement);
            }
        }

        $reported = [];
        foreach (Unresolved::requirements() as $requirement) {
            $reported[$requirement['id']] = $requirement['state'];
        }

        self::assertSame($expected, $reported);
    }

    /**
     * An item in todo.md naming the id is the whole coupling between what must
     * be true and the order the work happens in. An entry nobody has queued is
     * the case the reading exists for, so getting that flag backwards would
     * hide exactly the entries it is meant to surface.
     *
     * It is what an *item* names, not what the file contains. todo.md also
     * lists what is deliberately not queued, and an id named there has been
     * decided about in the opposite direction.
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
     * The oldest standing decision is the one the repository has moved furthest
     * away from, so it is the candidate the report names. Decisions::all() is
     * newest first for the listings, and this is the one caller that wants the
     * other end.
     */
    #[Test]
    public function theStandingDecisionsAreReadOldestFirst(): void
    {
        $standing = Unresolved::decisions();

        self::assertNotSame([], $standing, 'no decision is standing, which the report would have to say instead');

        $dates = array_column($standing, 'date');
        $sorted = $dates;
        sort($sorted);
        self::assertSame($sorted, $dates);

        foreach ($standing as $decision) {
            self::assertSame(
                'standing',
                Decisions::all()[$decision['id']]['status'],
                $decision['id'] . ' has been back-checked and is still reported as waiting',
            );
        }
    }
}
