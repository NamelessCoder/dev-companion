<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * The overview `bin/cli todo:next` deliberately does not give, for whoever wants it.
 *
 * Titles only, because that is what an overview is. What a todo asks for is a
 * paragraph, and five paragraphs are what `next` exists to spare a session that
 * only has to start one of them.
 */
#[AsCommand(
    name: 'todo:list',
    description: 'every todo by title: what recurs, the queue in order, what is in hand, and what waits',
)]
final class TodoList
{
    public function __invoke(OutputInterface $output): int
    {
        foreach (Todo::recurring() as $todo) {
            $output->writeln(sprintf(
                '%-12s %s%s',
                $todo['every'],
                $todo['title'],
                Todo::due($todo['every'], $todo['checked']) ? '' : ' — not due, last ' . $todo['checked'],
            ));
        }

        // The queue comes out in the order it is worked, so the column says
        // what put each one where it is rather than repeating the order as a
        // count. A blank there is a todo carrying no priority, which
        // `bin/cli todo:check` reports — the gap is the point.
        // What somebody has in hand is in the queue like everything else, and is
        // marked rather than listed apart: it is the worktree that says so, and a
        // todo whose worktree came down is workable again with nothing rewritten.
        $held = [];
        foreach (Todo::held() as $branch => $todo) {
            $held[$todo['path']] = $branch;
        }
        $items = Todo::items();
        foreach ($items as $item) {
            $branch = $held[$item['path']] ?? '';
            $output->writeln(sprintf(
                '%-12s %-16s %s',
                $branch === '' ? $item['priority'] : 'in hand',
                Todo::identifier($item),
                $item['title'],
            ));
        }
        if ($items === []) {
            $output->writeln('The queue is empty.');
        }

        foreach (Todo::waiting() as $todo) {
            $output->writeln(sprintf(
                '%-12s %-16s %s — %s',
                'waiting',
                Todo::identifier($todo),
                $todo['title'],
                $todo['waitingOn'],
            ));
        }

        foreach (Todo::references() as $reference) {
            $output->writeln(sprintf('%-12s %s', 'read only', $reference['title']));
        }

        return 0;
    }
}
