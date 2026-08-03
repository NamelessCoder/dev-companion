<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Process\CommandRunner;
use Typo3CmsMcp\Process\SystemRunner;

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
        [$exitCode, $output] = self::run(['git', '-C', $path, 'log', '-1', '--format=%h %ci %s']);

        return $exitCode === 0 ? trim($output) : '';
    }

    /**
     * What leaves this process, and the seam a unit test takes instead.
     *
     * `R-COD-003`: a unit test stubs what is outside it. `Todo::standing()`
     * and `Todo::linked()` ask git through here, and the test that held them
     * used to answer by making a real worktree and a real branch in whatever
     * checkout the suite was running in — a process, and one that wrote.
     */
    private static ?CommandRunner $runner = null;

    /** What a test hands in, so nothing it drives has to exist on the machine. */
    public static function useRunner(?CommandRunner $runner): void
    {
        self::$runner = $runner;
    }

    /**
     * One command, with both its streams as one string.
     *
     * Almost every caller runs git, which is what it was named for. The one
     * that does not is the claim setting up a worktree: `composer install`
     * belongs to the same step and starting it any other way would be a second
     * way to start a process, kept apart by nothing but the name.
     *
     * Unlike the other two callers of `SystemRunner` this one leaves stdin
     * inherited — which is what git wants, and why it was written separately
     * in the first place. It is a parameter rather than two implementations.
     *
     * @param array<int, string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function run(array $command, ?string $cwd = null): array
    {
        $result = (self::$runner ?? new SystemRunner())->run($command, $cwd, null, true);

        return [$result['exitCode'], $result['output'] . $result['error']];
    }
}
