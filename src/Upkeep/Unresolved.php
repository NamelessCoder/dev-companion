<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * What is written down and nothing has answered for yet.
 *
 * Both directories carry a state that means unfinished, and neither of them
 * was ever read for it. A requirement is `open` when nobody has built it and
 * `not guarded` when nothing holds it; a decision is `open` when nobody
 * has been back to its "Wrong if". None of the three is an error, which is
 * exactly why none of them surfaced: the queue is fed by feedback/ and the
 * forward reviews, so an entry could sit in either directory indefinitely
 * without anything saying so — and one of them sat there from the day the
 * directory was created.
 *
 * This is the reading, and it reports rather than fails. Whether an entry is
 * worth working off is a judgement, and the judgement stays with whoever runs
 * `bin/cli unresolved:list`. What it cannot stay is invisible.
 */
final class Unresolved
{
    /**
     * Every requirement nothing answers for, in id order.
     *
     * `queued` is the whole coupling to the pipeline: a queued todo naming
     * the id is what turns an entry into work, and an entry no item names is one
     * nobody has decided about either way.
     *
     * Read from what the queue says it serves rather than from `todo/` as a
     * whole. A search over the directory answers yes for an id named in the
     * page that lists what is deliberately *not* queued — which is a decision
     * that was taken, and the opposite of the one this flag reports.
     *
     * @return array<int, array{id: string, state: string, title: string, queued: bool}>
     */
    public static function requirements(): array
    {
        $queued = Todo::serves();

        $waiting = [];
        foreach (Requirements::all() as $requirement) {
            if (Requirements::state($requirement)->isGuarded()) {
                continue;
            }

            $waiting[] = [
                'id' => $requirement['id'],
                'state' => Requirements::state($requirement)->value,
                'title' => $requirement['title'],
                'queued' => in_array($requirement['id'], $queued, true),
            ];
        }

        return $waiting;
    }

    /**
     * Requirements standing on a decision that has since been revoked.
     *
     * The quiet one. A decision is revoked because a later reading disproved
     * it, and the requirement written on top of it keeps its `held` status and
     * its passing test the whole time — the test holds the requirement, and
     * nothing holds the reasoning under it. Neither directory can see the other,
     * so this is the one crossing that has to be read out.
     *
     * @return array<int, array{id: string, title: string, decision: string, revokedBy: string}>
     */
    public static function requirementsOnRevokedDecisions(): array
    {
        $decisions = Decisions::all();

        $resting = [];
        foreach (Requirements::all() as $requirement) {
            foreach ($requirement['restsOn'] as $id) {
                if (DecisionStatus::tryFrom($decisions[$id]['status'] ?? '') !== DecisionStatus::Revoked) {
                    continue;
                }
                $resting[] = [
                    'id' => $requirement['id'],
                    'title' => $requirement['title'],
                    'decision' => $id,
                    'revokedBy' => $decisions[$id]['revokedBy'],
                ];
            }
        }

        usort(
            $resting,
            static fn(array $a, array $b): int => [$a['id'], $a['decision']] <=> [$b['id'], $b['decision']],
        );

        return $resting;
    }

    /**
     * The decisions nobody has been back to, oldest first.
     *
     * An open decision is not a defect the way an open requirement is —
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
        $open = [];
        foreach (Decisions::all() as $decision) {
            if (DecisionStatus::tryFrom($decision['status']) !== DecisionStatus::Open) {
                continue;
            }

            $open[] = [
                'id' => $decision['id'],
                'date' => $decision['date'],
                'title' => $decision['title'],
            ];
        }

        // Decisions::all() is newest first, and reversing it would leave the
        // ids of one day in the order that listing wants them read.
        usort($open, static fn(array $a, array $b): int => [$a['date'], $a['id']] <=> [$b['date'], $b['id']]);

        return $open;
    }
}
