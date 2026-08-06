<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * Writes the index the published site is searched over, beside the pages it
 * was rendered into.
 *
 * It runs after the renderer rather than before it, because the renderer copies
 * an image a page names and nothing else: a file written into the source copy
 * for the site to fetch would never arrive. `Site` says what is indexed and
 * what is deliberately not.
 */
#[AsCommand(
    name: 'documentation:search',
    description: 'write the index the published site is searched over, into the rendered site',
)]
final class DocumentationSearch
{
    /** What the layout template fetches, relative to the root of the site. */
    public const FILE = 'search.json';

    public function __invoke(
        OutputInterface $output,
        #[Argument('the rendered site to write the index into')]
        string $site = '.site/html',
    ): int {
        $index = Site::search();
        $file = $site . '/' . self::FILE;
        file_put_contents($file, json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $output->writeln(sprintf('%s — %d pages, %d KB', $file, count($index), intdiv((int) filesize($file), 1024)));

        return 0;
    }
}
