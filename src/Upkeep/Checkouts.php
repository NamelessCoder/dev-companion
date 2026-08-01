<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Paths;

/**
 * Where the core checkouts live, and the git this repository reads them with.
 *
 * Two commands stand on this — `checkouts:update` creates them, `checkouts:status`
 * reports what is there — and a third, `catalog:check`, verifies against them.
 * What they share is the directory and the one way a command in here runs git:
 * captured rather than streamed, so a command decides what of it reaches its
 * caller.
 */
final class Checkouts
{
    public const REPOSITORY = 'https://github.com/TYPO3/typo3.git';

    public static function directory(): string
    {
        return Paths::root() . '/.checkouts';
    }

    /** The head of a checkout, as one line, or nothing where there is none. */
    public static function revision(string $path): string
    {
        [$exitCode, $output] = self::git(['git', '-C', $path, 'log', '-1', '--format=%h %ci %s']);

        return $exitCode === 0 ? trim($output) : '';
    }

    /**
     * One git command, with both its streams as one string.
     *
     * @param array<int, string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function git(array $command, ?string $cwd = null): array
    {
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $cwd);
        if (!is_resource($process)) {
            return [1, ''];
        }

        $output = (string) stream_get_contents($pipes[1]) . (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $output];
    }
}
