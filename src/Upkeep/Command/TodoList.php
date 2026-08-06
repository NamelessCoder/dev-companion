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
        $items = Todo::items();
        foreach ($items as $item) {
            $output->writeln(sprintf('%-12s %s', $item['priority'], $item['title']));
        }
        if ($items === []) {
            $output->writeln('The queue is empty.');
        }

        // A claim is the one line here that goes stale on its own: the session
        // holding it may have ended without coming back, and nothing notices.
        // So it is printed with the date it was taken on, and whoever reads an
        // old one decides whether the branch is still being worked.
        foreach (Todo::progress() as $todo) {
            $output->writeln(sprintf(
                '%-12s %s — %s since %s%s',
                'in hand',
                $todo['title'],
                $todo['branch'],
                $todo['claimed'],
                $todo['waitingOn'] === '' ? '' : ', waiting on ' . $todo['waitingOn'],
            ));
        }

        foreach (Todo::waiting() as $todo) {
            $output->writeln(sprintf('%-12s %s — %s', 'waiting', $todo['title'], $todo['waitingOn']));
        }

        foreach (Todo::references() as $reference) {
            $output->writeln(sprintf('%-12s %s', 'read only', $reference['title']));
        }

        return 0;
    }
}
