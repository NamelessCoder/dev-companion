<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * What is written down and nothing has answered for yet.
 *
 * Both directories carry a state that means unfinished, and neither of them
 * was ever read for it. A requirement is `open` when nobody has built it and
 * `not guarded` when nothing holds it; a decision is `standing` when nobody
 * has been back to its "Wrong if". None of the three is an error, which is
 * exactly why none of them surfaced: todo.md is fed by feedback/ and the
 * forward reviews, so an entry could sit in either directory indefinitely
 * without anything saying so — and one of them sat there from the day the
 * directory was created.
 *
 * This is the reading, and it reports rather than fails. Whether an entry is
 * worth working off is a judgement, and the judgement stays with whoever runs
 * `bin/cli backlog list`. What it cannot stay is invisible.
 */
final class Unresolved
{
    /**
     * Every requirement nothing answers for, in id order.
     *
     * `queued` is the whole coupling to the pipeline: todo.md naming the id is
     * what turns an entry into work, and an entry no item names is one nobody
     * has decided about either way.
     *
     * @return array<int, array{id: string, state: string, title: string, queued: bool}>
     */
    public static function requirements(): array
    {
        $todo = (string) file_get_contents(Paths::root() . '/todo.md');

        $waiting = [];
        foreach (Requirements::all() as $requirement) {
            if (Requirements::state($requirement) === 'held') {
                continue;
            }

            $waiting[] = [
                'id' => $requirement['id'],
                'state' => Requirements::state($requirement),
                'title' => $requirement['title'],
                'queued' => str_contains($todo, $requirement['id']),
            ];
        }

        return $waiting;
    }

    /**
     * The decisions nobody has been back to, oldest first.
     *
     * A standing decision is not a defect the way an open requirement is —
     * most of them are simply still true, and some name a "Wrong if" only a
     * forward run or an outside event could answer. What makes the oldest
     * worth naming is that the repository around it has moved furthest since,
     * so it is where a decision has most likely been overtaken without anyone
     * noticing.
     *
     * @return array<int, array{id: string, date: string, title: string}>
     */
    public static function decisions(): array
    {
        $standing = [];
        foreach (Decisions::all() as $decision) {
            if ($decision['status'] !== 'standing') {
                continue;
            }

            $standing[] = [
                'id' => $decision['id'],
                'date' => $decision['date'],
                'title' => $decision['title'],
            ];
        }

        // Decisions::all() is newest first, and reversing it would leave the
        // ids of one day in the order that listing wants them read.
        usort($standing, static fn (array $a, array $b): int => [$a['date'], $a['id']] <=> [$b['date'], $b['id']]);

        return $standing;
    }
}
