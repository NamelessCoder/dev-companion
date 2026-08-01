<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * Taking the front of the queue on, one branch per session.
 *
 * `bin/cli todo:next` hands the same first todo to everybody who asks, because
 * the queue is an order rather than an assignment. That is what has to change
 * before two sessions can work at once, and this is the change: the todos come
 * out of the queue in one move, so the second session is offered the item behind
 * them rather than the one somebody is already writing.
 *
 * What it does not do is start anything. The worktree, the branch and the merge
 * are git's, and this command is the upkeep of `todo/` — the same line
 * `bin/cli todo:next` draws when it names a command it does not own instead of
 * running it. It derives the branch name, because two sessions naming their own
 * would produce two names for one piece of work, and prints the rest.
 *
 * The overlap it reports is the only one it can see. Two todos that serve the
 * same entry are two sessions likely to edit one file, and nothing here knows
 * which files a step will touch — so it is a warning to read before the
 * worktrees exist, not a refusal.
 */
#[AsCommand(
    name: 'todo:claim',
    description: 'take the next todos out of the queue, one branch each, for sessions working at once',
)]
final class TodoClaim
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('how many sessions are going to work at once')]
        int $count = 1,
    ): int {
        $items = Todo::items();
        if ($items === []) {
            Cli::errors($output)->writeln('The queue is empty, so there is nothing to take on.');

            return 1;
        }
        if ($count < 1) {
            Cli::errors($output)->writeln('A claim is for at least one session.');

            return 1;
        }
        if ($count > count($items)) {
            $output->writeln(sprintf(
                'The queue holds %d, which is what %d sessions get.',
                count($items),
                count($items),
            ));
        }

        $taken = array_slice($items, 0, $count);
        foreach ($taken as $todo) {
            $branch = Todo::branch($todo);
            $output->writeln($todo['title']);
            $output->writeln(sprintf('    %s · %s', Todo::claim($todo), $branch));
            $output->writeln(sprintf('    git worktree add .worktrees/%s -b %s', basename($branch), $branch));
            $output->writeln('');
        }

        foreach (self::overlapping($taken) as $what => $titles) {
            $output->writeln(sprintf(
                '%s is answered for twice, by %s — one entry two sessions will edit.',
                $what,
                implode(' and ', $titles),
            ));
        }

        $output->writeln(sprintf(
            "Each claim is worked in its own worktree and put back or finished there; how the\n"
            . 'branches come home, and what a question that arrives mid-work leaves behind: %s.',
            Todo::PARALLEL,
        ));

        return 0;
    }

    /**
     * What two of the claimed todos both answer for, counting entries only.
     *
     * A directory is what most of the queue serves, so counting one would put a
     * line under every claim ever taken — and a warning that is always there is
     * one nobody reads by the third time. It would also be saying nothing:
     * `decisions/` names the place a step reports to rather than the file it
     * edits, and the two todos under it usually touch different entries.
     *
     * @param array<int, array{title: string, serves: array<int, string>, ...}> $taken
     *
     * @return array<string, array<int, string>>
     */
    private static function overlapping(array $taken): array
    {
        $serving = [];
        foreach ($taken as $todo) {
            foreach ($todo['serves'] as $what) {
                if (!str_ends_with($what, '/')) {
                    $serving[$what][] = $todo['title'];
                }
            }
        }

        return array_filter($serving, static fn(array $titles): bool => count($titles) > 1);
    }
}
