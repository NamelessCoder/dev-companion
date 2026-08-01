<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\Upkeep\Decisions;
use Typo3CmsMcp\Upkeep\Todo;
use Typo3CmsMcp\Upkeep\Unresolved;

/**
 * Reads what requirements/ and decisions/ say is unfinished.
 *
 * The two checks hold those files to their shape, and a file can be perfectly
 * shaped while saying that nobody has built the requirement or been back to
 * the decision. That is a legitimate state, so no check may fail on it — which
 * is why nothing read it at all, and why an entry sat in requirements/ unbuilt
 * from the day the directory was created. This subject is that reading, and it
 * is a subject rather than a line in `bin/cli check` because a session that
 * wants to know what is waiting should not have to run three checks to find out.
 */
final class Backlog implements Subject
{
    public static function about(): string
    {
        return 'what is written down and nothing has answered for yet';
    }

    public static function commands(): array
    {
        return [
            'list' => ['', 'the requirements nothing answers for, and the decisions nobody has been back to', self::list(...)],
        ];
    }

    /**
     * What is waiting, and whether the pipeline knows about it.
     *
     * A requirement is named with the state it is in and with the one thing
     * that turns it into work: an item in todo.md. The decisions are counted
     * rather than listed — most of them are standing because they are still
     * true, and a report that prints all of them every time is one nobody
     * reads twice. The oldest is named because it is the one the repository
     * has moved furthest away from.
     *
     * Nonzero says there is something to do, which is how `bin/cli next` knows
     * the todo that starts here is still the next thing. Not a failure: a
     * backlog nobody owes anything to is the good outcome and exits 0.
     */
    private static function list(): int
    {
        $requirements = Unresolved::requirements();
        foreach ($requirements as $requirement) {
            printf(
                "%-10s %-12s %s%s\n",
                $requirement['id'],
                $requirement['state'],
                $requirement['title'],
                $requirement['queued'] ? '' : ' — no todo.md item names it',
            );
        }
        if ($requirements === []) {
            print "Every requirement is met and guarded.\n";
        }

        // What is owed here is the judgement, not the work: a requirement some
        // todo names has had it, and the standing decisions have had it as soon
        // as a todo takes on sorting them. Everything else would make a backlog
        // that is legitimately long the only thing a session is ever offered.
        $unjudged = array_filter($requirements, static fn(array $r): bool => !$r['queued']);
        $sorting = in_array('decisions/', Todo::serves(), true);

        $standing = Unresolved::decisions();
        $total = count(Decisions::all());
        if ($standing === []) {
            printf("All %d decisions have been back-checked.\n", $total);

            return $unjudged === [] ? 0 : 1;
        }

        printf(
            "%d of %d decisions are standing: nobody has been back to their \"Wrong if\".\n"
            . "The oldest is %s (%s). bin/cli decisions list has them all.\n",
            count($standing),
            $total,
            $standing[0]['id'],
            $standing[0]['date'],
        );

        return $unjudged === [] && $sorting ? 0 : 1;
    }
}
