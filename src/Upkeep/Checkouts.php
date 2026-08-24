<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;

/**
 * Where the core checkouts live, and the git this repository reads them with.
 *
 * Two commands stand on this — `checkouts:update` creates them, `checkouts:status`
 * reports what is there — and two more read them: `components:check` and its three siblings verify
 * against one, `tools:record` answers from one. What they share is the
 * directory and the one way a command in here runs git: captured rather than
 * streamed, so a command decides what of it reaches its caller.
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
     * What a checkout carries that `checkouts:update` did not put there, an
     * ignored directory collapsed to the one entry git reports it as.
     *
     * `--ignored` is the whole of it: everything `composer install` writes into
     * a core checkout is ignored by the core's own `.gitignore`, so a plain
     * status calls such a tree clean while the console it installed answers.
     * A git that cannot answer reports nothing rather than a difference — the
     * question is what the tree carries, and "not a checkout at all" is not an
     * answer to it.
     *
     * @return array<int, string>
     */
    public static function beyondIndex(string $path): array
    {
        [$exitCode, $said] = self::run(['git', '-C', $path, 'status', '--porcelain', '--ignored']);
        if ($exitCode !== 0) {
            return [];
        }

        $carried = [];
        foreach (explode("\n", $said) as $line) {
            // Two status letters and a space, and the path after them.
            if (strlen($line) > 3) {
                $carried[] = trim(substr($line, 3));
            }
        }

        return $carried;
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
     * @param list<string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function run(array $command, ?string $cwd = null): array
    {
        $result = (self::$runner ?? new SystemRunner())->run($command, $cwd, null, true);

        return [$result['exitCode'], $result['output'] . $result['error']];
    }
}
