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
        $branches = [];
        foreach ($taken as $todo) {
            $branches[] = $branch = Todo::branch($todo);
            $output->writeln($todo['title']);
            $output->writeln(sprintf('    %s · %s', Todo::claim($todo), $branch));
        }
        $output->writeln('');

        foreach (self::overlapping($taken) as $what => $titles) {
            $output->writeln(sprintf(
                '%s is answered for twice, by %s — one entry two sessions will edit.',
                $what,
                implode(' and ', $titles),
            ));
            $output->writeln('');
        }

        self::setup($output, $branches);

        return 0;
    }

    /**
     * The rest of the arrangement, as the commands that make it and the message
     * that starts it.
     *
     * Printing it is not decoration. What this repository owns is the move out
     * of the queue, and everything after it — the commit, the worktrees, the
     * sessions — used to live on a page somebody had to remember to open,
     * where the order was the load-bearing part and the least visible one. A
     * claim that is not on `main` when the worktree is cut from it produces a
     * worktree standing on nothing, hours later and for reasons no session can
     * see from inside.
     *
     * The message is printed whole and with no blank in it, because that is the
     * one thing a caller cannot get wrong by hand. The run this exists because
     * of sent the template as it stood, `<absolute path to the worktree>` and
     * all, and the session on the other end could not tell that from what it
     * was supposed to say.
     *
     * @param array<int, string> $branches
     */
    private static function setup(OutputInterface $output, array $branches): void
    {
        $output->writeln(sprintf(
            "Commit these to `main` before anything else. A worktree cut from a `main` that does\n"
            . "not carry its claim stands on nothing, and `bin/cli todo:next --worktree` refuses\n"
            . "there rather than reading the queue — which is how that is found in the first\n"
            . 'minute instead of the third hour. Then one worktree each:',
        ));
        $output->writeln('');
        foreach ($branches as $branch) {
            $name = basename($branch);
            $output->writeln(sprintf('    git worktree add .worktrees/%s -b %s', $name, $branch));
            $output->writeln(sprintf('    (cd .worktrees/%s && composer install && ln -s ../../.checkouts .checkouts)', $name));
        }
        $output->writeln('');
        $output->writeln(sprintf(
            "`composer install` in the worktree, never a symlinked `vendor/`: the autoload map\n"
            . "resolves `src/` from where the autoloader sits, so a shared one points every path\n"
            . "in this repository back at this checkout and the session then keeps its todos here.\n"
            . "\n"
            . "Start one session per worktree, with its own directory as the working directory.\n"
            . "This is the whole message, the same for every one of them, and nothing in it is\n"
            . 'filled in — which todo is whose is read out of the worktree, not out of the text:',
        ));
        $output->writeln('');
        $output->writeln(self::indent(Todo::BRIEFING));
        $output->writeln('');
        $output->writeln(sprintf(
            'How the branches come home, and what a question that arrives mid-work leaves behind: %s.',
            Todo::PARALLEL,
        ));
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block));
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
