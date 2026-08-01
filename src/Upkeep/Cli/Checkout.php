<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\TestingFramework;

/**
 * Keeps one TYPO3 core checkout per covered version below .checkouts/, so that
 * verifying what a statement holds for is done against this repository's own
 * sources rather than against whatever checkout happens to be on the machine.
 *
 * The versions come from knowledge/versions.json and from nowhere else. One
 * treeless clone carries the history; each covered branch is a worktree of it,
 * so four lines cost one object store.
 *
 * typo3/testing-framework is kept here too, because a statement about the
 * harness a project extension tests in is verified against a tag of that
 * package rather than against a core branch (D-KNW-2), and reading it anywhere
 * else makes the evidence unreproducible for exactly the same reason. Which
 * release line pairs with which major is the core's own require-dev pin, so
 * nothing about it is recorded: see Typo3CmsMcp\TestingFramework.
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

        printf("\n%s, one release line per pin\n", TestingFramework::PACKAGE);
        foreach (TestingFramework::pairing($checkouts) as $pair) {
            printf(
                "  %-6s %-9s %s\n",
                $pair['branch'],
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                is_dir($pair['path'] . '/Classes') ? self::revision($pair['path']) : 'missing — run bin/cli checkouts update',
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

        if (!self::mirror($mirror, self::REPOSITORY, false)) {
            return 1;
        }

        $failed = 0;
        foreach (Versions::covered() as $version) {
            $branch = $version['branch'];
            $path = $checkouts . '/' . $branch;
            printf("%s (TYPO3 v%d, %s)\n", $branch, $version['major'], $version['status']);

            if (self::worktree($mirror, $path, $branch) !== 0) {
                ++$failed;
                continue;
            }

            printf("    %s\n", self::revision($path));
        }

        $failed += self::updateTestingFramework($checkouts);

        if ($failed > 0) {
            printf("\n%d checkout(s) failed.\n", $failed);

            return 1;
        }

        printf("\nReady. Verify a statement against .checkouts/<branch>, on both sides of its boundary.\n");

        return 0;
    }

    /**
     * One worktree per testing-framework release line the covered majors pin,
     * checked out at that line's newest tag.
     *
     * Two majors pinning the same line share one worktree — the core pins 9.x on
     * both 13.4 and 14.3 — so what is created follows the pins rather than the
     * version list.
     */
    private static function updateTestingFramework(string $checkouts): int
    {
        printf("\n%s (the harness a project extension tests in)\n", TestingFramework::PACKAGE);
        $mirror = TestingFramework::mirror($checkouts);
        if (!self::mirror($mirror, TestingFramework::REPOSITORY, true)) {
            return 1;
        }

        $failed = 0;
        $created = [];
        foreach (TestingFramework::pairing($checkouts) as $pair) {
            printf(
                "  %-6s %-9s %s\n",
                $pair['branch'],
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                $pair['ref'] ?? 'names no single release line',
            );
            if ($pair['ref'] === null) {
                ++$failed;
                continue;
            }
            if (isset($created[$pair['ref']])) {
                continue;
            }

            $created[$pair['ref']] = true;
            if (self::worktree($mirror, $pair['path'], $pair['ref']) !== 0) {
                ++$failed;
            }
        }

        return $failed;
    }

    /** One treeless bare clone, created where it is missing and fetched to its refs. */
    private static function mirror(string $path, string $repository, bool $tags): bool
    {
        if (!is_dir($path)) {
            printf("Cloning %s (treeless; blobs are fetched as they are read)\n", $repository);
            // --filter=blob:none keeps the history and the trees and leaves the file
            // contents on the server until something asks for them. A full clone of the
            // core is several gigabytes; this is a fraction of it, and the worktrees
            // below still read like ordinary checkouts.
            [$exitCode] = self::run(['git', 'clone', '--bare', '--filter=blob:none', $repository, $path]);
            if ($exitCode !== 0) {
                fwrite(STDERR, "Clone failed.\n");

                return false;
            }
        }

        // A bare clone keeps the branches as its own refs and configures no refspec, so
        // a later fetch would update nothing. This is what makes the mirror updatable.
        self::run(['git', '-C', $path, 'config', 'remote.origin.fetch', '+refs/heads/*:refs/heads/*'], null, true);

        printf("Fetching %s\n", basename($path));
        $command = ['git', '-C', $path, 'fetch', '--quiet', '--force', '--prune', 'origin'];
        if ($tags) {
            // The release lines are read at their tags, so a line that gained one
            // since the last update is only there if the tags come along.
            $command[] = '--tags';
        }
        [$exitCode] = self::run($command);

        return $exitCode === 0;
    }

    /** One worktree of a mirror, detached on a ref. */
    private static function worktree(string $mirror, string $path, string $ref): int
    {
        if (!is_dir($path)) {
            [$exitCode] = self::run(['git', '-C', $mirror, 'worktree', 'add', '--quiet', '--detach', '--force', $path, $ref]);

            return $exitCode;
        }

        // Detached on the ref: these are read, never committed to, so there is
        // nothing to merge and nothing to lose.
        [$exitCode] = self::run(['git', '-C', $path, 'checkout', '--quiet', '--force', '--detach', $ref]);

        return $exitCode;
    }

    private static function directory(): string
    {
        return Paths::root() . '/.checkouts';
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
