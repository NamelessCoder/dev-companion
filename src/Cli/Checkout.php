<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Versions;

/**
 * Keeps one TYPO3 core checkout per covered version below .checkouts/, so that
 * verifying what a statement holds for is done against this repository's own
 * sources rather than against whatever checkout happens to be on the machine.
 *
 * The versions come from knowledge/versions.json and from nowhere else. One
 * treeless clone carries the history; each covered branch is a worktree of it,
 * so four lines cost one object store.
 */
final class Checkout implements Subject
{
    private const REPOSITORY = 'https://github.com/TYPO3/typo3.git';

    public static function about(): string
    {
        return 'one TYPO3 core checkout per covered version, below .checkouts/';
    }

    public static function commands(): array
    {
        return [
            'update' => ['', 'create what is missing, update what is there', self::update(...)],
            'status' => ['', 'what exists, at which revision', self::status(...)],
        ];
    }

    /** What is there, and how old it is. */
    private static function status(): int
    {
        $checkouts = self::directory();
        printf("Core checkouts below %s\n", $checkouts);
        foreach (Versions::covered() as $version) {
            $path = $checkouts . '/' . $version['branch'];
            printf(
                "  %-6s %s\n",
                $version['branch'],
                is_dir($path . '/typo3/sysext/core') ? self::revision($path) : 'missing — run bin/cli checkouts update',
            );
        }

        return 0;
    }

    /** One worktree per covered branch, fetched to its tip. */
    private static function update(): int
    {
        $checkouts = self::directory();
        $mirror = $checkouts . '/typo3.git';

        if (!is_dir($checkouts) && !mkdir($checkouts, 0o777, true) && !is_dir($checkouts)) {
            fwrite(STDERR, sprintf("Cannot create %s\n", $checkouts));

            return 2;
        }

        if (!is_dir($mirror)) {
            printf("Cloning %s (treeless; blobs are fetched as they are read)\n", self::REPOSITORY);
            // --filter=blob:none keeps the history and the trees and leaves the file
            // contents on the server until something asks for them. A full clone of the
            // core is several gigabytes; this is a fraction of it, and the worktrees
            // below still read like ordinary checkouts.
            [$exitCode] = self::run(['git', 'clone', '--bare', '--filter=blob:none', self::REPOSITORY, $mirror]);
            if ($exitCode !== 0) {
                fwrite(STDERR, "Clone failed.\n");

                return 1;
            }
        }

        // A bare clone keeps the branches as its own refs and configures no refspec, so
        // a later fetch would update nothing. This is what makes the mirror updatable.
        self::run(['git', '-C', $mirror, 'config', 'remote.origin.fetch', '+refs/heads/*:refs/heads/*'], null, true);

        echo "Fetching\n";
        self::run(['git', '-C', $mirror, 'fetch', '--quiet', '--force', '--prune', 'origin']);

        $failed = 0;
        foreach (Versions::covered() as $version) {
            $branch = $version['branch'];
            $path = $checkouts . '/' . $branch;
            printf("%s (TYPO3 v%d, %s)\n", $branch, $version['major'], $version['status']);

            if (!is_dir($path)) {
                [$exitCode] = self::run(['git', '-C', $mirror, 'worktree', 'add', '--quiet', '--detach', '--force', $path, $branch]);
            } else {
                // Detached on the branch tip: these are read, never committed to, so
                // there is nothing to merge and nothing to lose.
                [$exitCode] = self::run(['git', '-C', $path, 'checkout', '--quiet', '--force', '--detach', $branch]);
            }
            if ($exitCode !== 0) {
                ++$failed;
                continue;
            }

            printf("    %s\n", self::revision($path));
        }

        if ($failed > 0) {
            printf("\n%d checkout(s) failed.\n", $failed);

            return 1;
        }

        printf("\nReady. Verify a statement against .checkouts/<branch>, on both sides of its boundary.\n");

        return 0;
    }

    private static function directory(): string
    {
        return dirname(__DIR__, 2) . '/.checkouts';
    }

    private static function revision(string $path): string
    {
        [$exitCode, $output] = self::run(['git', '-C', $path, 'log', '-1', '--format=%h %ci %s'], null, true);

        return $exitCode === 0 ? trim($output) : '';
    }

    /**
     * Runs a command and returns [exitCode, output]; output is streamed when verbose.
     *
     * @param array<int, string> $command
     * @return array{0: int, 1: string}
     */
    private static function run(array $command, ?string $cwd = null, bool $quiet = false): array
    {
        $process = proc_open(
            $command,
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $cwd,
        );
        if (!is_resource($process)) {
            return [1, ''];
        }
        $output = (string) stream_get_contents($pipes[1]) . (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        if (!$quiet && trim($output) !== '') {
            echo '    ' . str_replace("\n", "\n    ", rtrim($output)) . "\n";
        }

        return [$exitCode, $output];
    }
}
