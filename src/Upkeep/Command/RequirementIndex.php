<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Requirements;

/**
 * Writes the listing of each group back into its readme.
 *
 * What this replaces is a document nobody could index. Entries arrived at the
 * top of whichever section they belonged to, the ids ran in no order, and five
 * of them had been handed out twice before anybody read far enough to notice.
 */
#[AsCommand(
    name: 'requirements:index',
    description: 'rewrite the listing at the foot of each group readme from the files',
)]
final class RequirementIndex
{
    /**
     * Where the generated listing begins, so everything above it survives a
     * regeneration. Both shapes are matched: the table these listings were
     * until D-DOC-001, and the list they are now.
     */
    private const LISTING_STARTS = '/(?:\| Id\s|- \[`R-)[^\n]*(?:\n.*)?$/s';

    public function __invoke(OutputInterface $output): int
    {
        foreach (Requirements::GROUPS as $group) {
            $readme = Requirements::directory() . '/' . $group . '/readme.md';
            $contents = (string) file_get_contents($readme);
            $head = (string) preg_replace(self::LISTING_STARTS, '', $contents);
            file_put_contents($readme, $head . Requirements::listing($group));
            $output->writeln($group . '/readme.md');
        }

        return 0;
    }
}
