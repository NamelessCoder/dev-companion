<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * The questions nothing else will ask again.
 *
 * A waiting todo is offered to no session, which is the point and also the
 * risk: `todo/waiting/` is one unread directory away from being where todos go
 * to be forgotten. This is the way back. It prints whole rather than by title —
 * the question is the thing to be answered, and a session that has to open the
 * file to see it is one that will not — and it exits nonzero while anything
 * waits, which is what makes the todo that runs it due.
 */
#[AsCommand(
    name: 'todo:waiting',
    description: 'what is blocked, and the question each one is blocked on',
)]
final class TodoWaiting
{
    public function __invoke(OutputInterface $output): int
    {
        $waiting = Todo::waiting();
        foreach ($waiting as $todo) {
            $output->writeln(sprintf("%s\n  waiting on %s\n  %s\n", $todo['title'], $todo['waitingOn'], $todo['path']));
        }
        if ($waiting === []) {
            $output->writeln('Nothing is waiting on an answer.');
        }

        return $waiting === [] ? 0 : 1;
    }
}
