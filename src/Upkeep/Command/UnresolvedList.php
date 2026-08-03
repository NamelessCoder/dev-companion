<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
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
 * from the day the directory was created. This command is that reading, and it
 * is its own command rather than a line in `bin/cli repository:check` because a session
 * that wants to know what is waiting should not have to run three checks to
 * find out.
 */
#[AsCommand(
    name: 'unresolved:list',
    description: 'the requirements nothing answers for, and the decisions nobody has been back to',
)]
final class UnresolvedList
{
    /**
     * What is waiting, and whether the pipeline knows about it.
     *
     * A requirement is named with the state it is in and with the one thing
     * that turns it into work: a todo in the queue. The decisions are counted
     * rather than listed — most of them are standing because they are still
     * true, and a report that prints all of them every time is one nobody
     * reads twice. The oldest is named because it is the one the repository
     * has moved furthest away from.
     *
     * Nonzero says there is something to do, which is how `bin/cli todo:next` knows
     * the todo that starts here is still the next thing. Not a failure: a
     * repository nobody owes anything to is the good outcome and exits 0.
     */
    public function __invoke(OutputInterface $output): int
    {
        $requirements = Unresolved::requirements();
        foreach ($requirements as $requirement) {
            $output->writeln(sprintf(
                '%-10s %-12s %s%s',
                $requirement['id'],
                $requirement['state'],
                $requirement['title'],
                $requirement['queued'] ? '' : ' — no todo names it',
            ));
        }
        if ($requirements === []) {
            $output->writeln('Every requirement is met and guarded.');
        }

        // What is owed here is the judgement, not the work: a requirement some
        // todo names has had it, and the open decisions have had it as soon
        // as a todo takes on sorting them. Everything else would make a list
        // that is legitimately long the only thing a session is ever offered.
        $unjudged = array_filter($requirements, static fn(array $r): bool => !$r['queued']);
        $sorting = in_array('decisions/', Todo::serves(), true);

        // Before the count of what nobody has been back to, because this one is
        // not one of them: the reasoning under a held requirement is gone and
        // its test is still green, so nothing else in this report would say so.
        foreach (Unresolved::requirementsOnRevokedDecisions() as $resting) {
            $output->writeln(sprintf(
                '%s rests on %s, which is revoked%s.',
                $resting['id'],
                $resting['decision'],
                $resting['revokedBy'] === '' ? '' : ' — see ' . $resting['revokedBy'],
            ));
        }

        $standing = Unresolved::decisions();
        $total = count(Decisions::all());
        if ($standing === []) {
            $output->writeln(sprintf('All %d decisions have been back-checked.', $total));

            return $unjudged === [] ? 0 : 1;
        }

        $output->writeln(sprintf(
            "%d of %d decisions are open: nobody has been back to their \"Wrong if\".\n"
            . 'The oldest is %s (%s). bin/cli decisions:list has them all.',
            count($standing),
            $total,
            $standing[0]['id'],
            $standing[0]['date'],
        ));

        return $unjudged === [] && $sorting ? 0 : 1;
    }
}
