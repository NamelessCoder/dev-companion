<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Links;

/**
 * Whether the paths this repository writes between its own files still resolve.
 *
 * This one fails rather than reports. A long sentence can be the right sentence
 * and `prose:check` says so; a link to a file that is not there is wrong in
 * every reading, and the reader who follows it is otherwise the check.
 */
#[AsCommand(
    name: 'links:check',
    description: 'every path this repository writes between its own files',
)]
final class LinkCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $dead = Links::dead();
        if ($dead === []) {
            $output->writeln('Every link resolves.');

            return 0;
        }

        foreach ($dead as $link) {
            $output->writeln(sprintf('%s:%d links to %s, which is not there', $link['file'], $link['line'], $link['link']));
        }

        $output->writeln('');
        $output->writeln(sprintf('%d dead links.', count($dead)));

        return 1;
    }
}
