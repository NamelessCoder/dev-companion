<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * Putting a claim back, which is the half that keeps `progress/` from filling up.
 *
 * A session ends where it ends, and the ones that end without finishing are the
 * common case rather than the exception: the branch was merged and a question is
 * left over, the step turned out to be somebody else's, the worktree is gone.
 * Each of those leaves a todo nobody is working and nobody else is offered, and
 * a state that can only be entered is one that gets worked around within a week.
 *
 * Where it goes is read off the claim. One carrying a `**Waiting on:**` waits,
 * because that is what it is; the rest go to the end of the queue. `progress/`
 * is for as long as a branch is live, and a claim whose work has come home is in
 * one of the two states this repository already had.
 *
 * The branch is not touched. Where the work is not merged, it is still the only
 * place the half that is done exists.
 */
#[AsCommand(
    name: 'todo:release',
    description: 'put a claim nobody is working back into the queue, or into waiting where it carries a question',
)]
final class TodoRelease
{
    /**
     * @param array<int, string> $todo
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the claims nobody is working, by file name')]
        array $todo,
    ): int {
        $inHand = [];
        foreach (Todo::progress() as $claim) {
            $inHand[basename($claim['path'], '.md')] = $claim;
        }

        foreach ($todo as $one) {
            $name = basename($one, '.md');
            if (!isset($inHand[$name])) {
                Cli::errors($output)->writeln($one . ' is no claim in todo/progress/ — `bin/cli todo:list`.');

                return 1;
            }

            $claim = $inHand[$name];
            $output->writeln(Todo::release($claim));
            $output->writeln($claim['waitingOn'] === ''
                ? '    Nobody was working it, so it is queued behind everything else.'
                : '    It waits on ' . $claim['waitingOn']);
            $output->writeln('    ' . $claim['branch'] . ' is what it was worked on, and nothing here deletes it.');
        }

        return 0;
    }
}
