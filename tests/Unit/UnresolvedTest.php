<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Decisions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Requirements;
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
     * todo.md naming the id is the whole coupling between what must be true and
     * the order the work happens in. An entry nobody has queued is the case the
     * reading exists for, so getting that flag backwards would hide exactly the
     * entries it is meant to surface.
     */
    #[Test]
    public function anEntryIsQueuedWhenAnItemNamesIt(): void
    {
        $todo = (string) file_get_contents(Paths::root() . '/todo.md');

        foreach (Unresolved::requirements() as $requirement) {
            self::assertSame(
                str_contains($todo, $requirement['id']),
                $requirement['queued'],
                $requirement['id'] . ' is reported as ' . ($requirement['queued'] ? 'queued' : 'unqueued'),
            );
        }
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
