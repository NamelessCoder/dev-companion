<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\ToolSurface;

/**
 * Writes the tool reference back from the registry.
 *
 * What this replaces is the surface being written twice: the classes declare
 * what a caller can see of a tool, and the only place it was readable without
 * calling the server was a list of names in the readme. The fields a tool
 * answers with were written down nowhere at all.
 */
#[AsCommand(
    name: 'tools:index',
    description: 'rewrite the tool reference under documentation/clients/ from the registry',
)]
final class ToolIndex
{
    public function __invoke(OutputInterface $output): int
    {
        file_put_contents(ToolSurface::file(), ToolSurface::page());
        $output->writeln(substr(ToolSurface::file(), strlen(Paths::root()) + 1));

        return 0;
    }
}
