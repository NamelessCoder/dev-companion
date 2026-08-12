<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Prose;
use TYPO3\DevCompanion\Upkeep\Wrap;

/**
 * `knowledge:format` for the half of this repository that is prose.
 *
 * What it is for is the paragraph a rename left ragged. A word swept out of a
 * hundred files leaves a hundred short lines behind it, and rewrapping them was
 * a throwaway script every time — one that nobody reviewed, that knew nothing
 * about a code fence, and that was written again from memory on the next
 * rename.
 *
 * It rewrites rather than reports, like `knowledge:format`: what it changed is
 * in the working tree, where `git diff` is the report. `prose:check` is the
 * other half and stays a report, because a long sentence can be the right one
 * and no formatter can tell.
 *
 * A path narrows it to the files a change touched, which is what a commit
 * wants. Named nothing, it rewraps the whole corpus, and that is a diff to look
 * at before it is a diff to make.
 */
#[AsCommand(
    name: 'prose:format',
    description: 'rewrap the prose this repository writes at column ' . Wrap::COLUMN . ', and say which files that changed',
)]
final class ProseFormat
{
    /**
     * @param list<string> $paths
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the files or directories to rewrap; the whole prose corpus when none is named')]
        array $paths = [],
    ): int {
        $files = self::targets($paths);
        if ($files === []) {
            Cli::errors($output)->writeln(sprintf(
                'No prose file this repository writes about itself matches %s.',
                implode(', ', $paths),
            ));

            return 1;
        }

        $rewritten = [];
        foreach ($files as $file) {
            $contents = (string) file_get_contents(Paths::root() . '/' . $file);
            // Which markup it is, asked of the file rather than of the
            // directory: `documentation/` is reStructuredText and every
            // other working directory is markdown — `D-DOC-029`.
            $wrapped = str_ends_with($file, '.rst') ? Wrap::rst($contents) : Wrap::document($contents);
            if ($wrapped !== $contents) {
                file_put_contents(Paths::root() . '/' . $file, $wrapped);
                $rewritten[] = $file;
            }
        }

        $output->writeln(sprintf('%d of %d files rewrapped.', count($rewritten), count($files)));
        foreach ($rewritten as $file) {
            $output->writeln('  ' . $file);
        }

        return 0;
    }

    /**
     * The files a run works on: the corpus, or the part of it that was named.
     *
     * Matched against `Prose::documents()` rather than resolved into a file to
     * rewrite, so this touches the prose this repository writes about itself
     * and nothing else. `feedback/` is outside it on purpose — a feedback is a
     * session's report, and reformatting somebody else's report is an edit to
     * evidence.
     *
     * @param list<string> $paths
     *
     * @return list<string>
     */
    private static function targets(array $paths): array
    {
        $files = array_values(array_filter(Prose::documents(), self::isWrittenByHand(...)));
        if ($paths === []) {
            return $files;
        }

        $named = [];
        foreach ($paths as $path) {
            $resolved = realpath($path) ?: realpath(Paths::root() . '/' . $path);
            if ($resolved === false) {
                continue;
            }

            $relative = substr($resolved, strlen(Paths::root()) + 1);
            foreach ($files as $file) {
                if ($file === $relative || str_starts_with($file, $relative . '/')) {
                    $named[$file] = $file;
                }
            }
        }

        return array_values($named);
    }

    /**
     * Whether a file has a writer already.
     *
     * Not because it is generated — the column is no longer the difference,
     * since `ToolSurface` wraps through `Wrap` like everything else here. It is
     * that a generator decides what a line is: `tools:index` keeps the
     * annotations of a tool on one line however wide, because they are one
     * fact. A formatter cannot know that, so it wraps them, and the next
     * `tools:index` puts them back — the file then changes in every commit and
     * says nothing by changing.
     *
     * A recording is out for a second reason. Every block below a page's
     * `## Answered` heading is what a client received, and rewrapping it makes
     * the page claim an answer arrived in lines it did not.
     */
    private static function isWrittenByHand(string $file): bool
    {
        if (str_starts_with($file, 'documentation/server/tools/')) {
            return false;
        }

        return basename($file) !== 'readme.md'
            || (!str_starts_with($file, 'decisions/') && !str_starts_with($file, 'requirements/'));
    }
}
