<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * The overview `bin/cli todo:next` deliberately does not give, for whoever wants it.
 *
 * Titles only, because that is what an overview is. What a todo asks for is a
 * paragraph, and five paragraphs are what `next` exists to spare a session that
 * only has to start one of them.
 */
#[AsCommand(
    name: 'todo:list',
    description: 'every todo by title: what recurs, the queue in order, and what waits',
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

        $items = Todo::items();
        foreach ($items as $position => $item) {
            $output->writeln(sprintf('%-12s %s', $position === 0 ? 'next' : (string) ($position + 1), $item['title']));
        }
        if ($items === []) {
            $output->writeln('The queue is empty.');
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
