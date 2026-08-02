<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\OpenFeedback;

/**
 * The pile as a pile, for whoever wants to read it rather than work it.
 *
 * What is to be done about any of it is on the board — one card per open
 * feedback, handed over one at a time like every other todo. This is the other
 * question: what has arrived, newest first, and which of it somebody has taken
 * on.
 */
#[AsCommand(
    name: 'feedback:list',
    description: 'every open feedback, newest first',
)]
final class FeedbackList
{
    public function __invoke(OutputInterface $output): int
    {
        $open = OpenFeedback::all();
        if ($open === []) {
            $output->writeln('No open feedback.');

            return 0;
        }

        $output->writeln(sprintf('%d open, newest first.', count($open)));
        foreach (array_reverse($open) as $feedback) {
            $output->writeln(sprintf('%s%s', $feedback['file'], $feedback['judged'] ? '' : ' — no todo names it'));
        }

        return 0;
    }
}
