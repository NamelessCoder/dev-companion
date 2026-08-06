<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\ToolSurface;

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
        $pages = ToolSurface::pages();

        $stale = [];
        foreach ($pages as $file => $contents) {
            if (!is_file($file) || (string) file_get_contents($file) !== $contents) {
                $stale[] = substr($file, strlen(Paths::root()) + 1);
            }
        }
        foreach (ToolSurface::written() as $written) {
            if (!isset($pages[$written->getPathname()])) {
                $stale[] = substr($written->getPathname(), strlen(Paths::root()) + 1);
            }
        }

        foreach ($stale as $file) {
            Cli::errors($output)->writeln($file . ' is not what the registry declares — run bin/cli tools:index');
        }
        $output->writeln(sprintf('%d tools, %d problems', count(Registry::definitions()), count($stale)));

        return $stale === [] ? 0 : 1;
    }
}
