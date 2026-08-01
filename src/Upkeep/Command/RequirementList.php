<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Requirements;

/**
 * What must hold, and what state it is in.
 *
 * One requirement is one file below requirements/, and an id decides the
 * directory and the file name — so which group a requirement is in is a
 * comparison rather than a search.
 */
#[AsCommand(
    name: 'requirements:list',
    description: 'every requirement with the state it is in, or one group of them',
)]
final class RequirementList
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('one group of them rather than all')]
        string $group = '',
    ): int {
        if ($group !== '' && !in_array($group, Requirements::GROUPS, true)) {
            Cli::errors($output)->writeln('No such group: ' . $group . "\nGroups: " . implode(', ', Requirements::GROUPS));

            return 2;
        }

        foreach (Requirements::all() as $requirement) {
            if ($group !== '' && $requirement['group'] !== $group) {
                continue;
            }

            $output->writeln(sprintf(
                '%-10s %-13s %-14s %s',
                $requirement['id'],
                $requirement['group'],
                Requirements::state($requirement),
                $requirement['title'],
            ));
        }

        return 0;
    }
}
