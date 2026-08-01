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
 * common case rather than the exception: the branch was abandoned, the step
 * turned out to be somebody else's, the worktree is gone. Each of those leaves a
 * todo nobody is working and nobody else is offered, and a state that can only
 * be entered is one that gets worked around within a week.
 *
 * It goes to the end of the queue, which is where this repository already puts a
 * todo somebody could not finish: honest about the priority, and its own timer,
 * coming round again as the queue drains.
 *
 * A todo left in `progress/` with a `**Waiting on:**` is a different answer and
 * not this one. That one has work behind it on a branch and a question in front
 * of it; releasing it would throw away the half that is done.
 */
#[AsCommand(
    name: 'todo:release',
    description: 'put a claim nobody is working back onto the end of the queue',
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
            if ($claim['waitingOn'] !== '') {
                $output->writeln('    It was waiting on ' . $claim['waitingOn']);
            }
            $output->writeln('    The work is on ' . $claim['branch'] . ', which nothing here deletes.');
        }

        return 0;
    }
}
