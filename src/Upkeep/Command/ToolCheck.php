<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\ToolSurface;

/**
 * Whether the tool reference still says what the registry declares.
 *
 * A generated page nothing reads back is a hand-written one that was generated
 * once: a tool added, a description rewritten or a schema field gained leaves
 * it standing, and it goes on being read. `composer test` runs the same
 * comparison through ToolSurfaceTest; this is the readable half.
 */
#[AsCommand(
    name: 'tools:check',
    description: 'hold the tool reference to what the registry declares',
)]
final class ToolCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $file = substr(ToolSurface::file(), strlen(Paths::root()) + 1);
        $stale = (string) file_get_contents(ToolSurface::file()) !== ToolSurface::page();

        if ($stale) {
            Cli::errors($output)->writeln($file . ' is not what the registry declares — run bin/cli tools:index');
        }
        $output->writeln(sprintf('%d tools, %d problems', count(Registry::definitions()), $stale ? 1 : 0));

        return $stale ? 1 : 0;
    }
}
