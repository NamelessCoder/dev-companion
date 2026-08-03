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
 *
 * What a tool answered is not rewritten. It is carried over from the page as it
 * stands, because `tools:record` is the only thing that can produce it.
 */
#[AsCommand(
    name: 'tools:index',
    description: 'rewrite the tool reference under documentation/tools/ from the registry',
)]
final class ToolIndex
{
    public function __invoke(OutputInterface $output): int
    {
        $pages = ToolSurface::pages();
        if (!is_dir(ToolSurface::directory())) {
            mkdir(ToolSurface::directory(), 0777, true);
        }
        foreach ($pages as $file => $contents) {
            file_put_contents($file, $contents);
        }

        foreach (ToolSurface::written() as $written) {
            if (!isset($pages[$written->getPathname()])) {
                unlink($written->getPathname());
                $output->writeln(sprintf('removed %s, which the registry no longer offers', $written->getFilename()));
            }
        }

        $output->writeln(sprintf(
            '%s — %d pages',
            substr(ToolSurface::index(), strlen(Paths::root()) + 1),
            count($pages) - count(ToolSurface::standingPages()),
        ));

        return 0;
    }
}
