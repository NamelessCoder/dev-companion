<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Which typo3/testing-framework release each covered TYPO3 major is read
 * against, and where that release is kept.
 *
 * The package has a release cycle of its own and `.checkouts/` does not contain
 * it, so a statement about it is verified against a tag rather than against a
 * branch. Which tag that is is derived from the core's own `require-dev` pin
 * rather than recorded here — `D-KNW-106`.
 */
final class TestingFramework
{
    public const PACKAGE = 'typo3/testing-framework';

    public const REPOSITORY = 'https://github.com/TYPO3/testing-framework.git';

    /** The bare clone the lines are worktrees of, beside the core's own. */
    public static function mirror(string $checkouts): string
    {
        return $checkouts . '/testing-framework.git';
    }

    /** Where one release line is checked out. */
    public static function worktree(string $checkouts, string $line): string
    {
        return $checkouts . '/testing-framework/' . $line;
    }

    /**
     * The release each covered major pairs with, as the core checkouts say it.
     *
     * `constraint` is empty where the branch pins nothing, and `line` and `ref`
     * are null where the pin names no single release line — both are reported
     * rather than guessed at, because a pin that spans two lines means the core
     * major no longer says which harness a statement was read in.
     *
     * @return array<int, array{major: int, branch: string, constraint: string, line: ?string, ref: ?string, path: string}>
     */
    public static function pairing(string $checkouts): array
    {
        $pairs = [];
        foreach (Versions::covered() as $version) {
            $manifest = $checkouts . '/' . $version['branch'] . '/composer.json';
            $constraint = is_file($manifest)
                ? (json_decode((string) file_get_contents($manifest), true)['require-dev'][self::PACKAGE] ?? null)
                : null;
            $constraint = is_string($constraint) ? $constraint : '';
            $line = self::line($constraint);
            $pairs[] = [
                'major' => $version['major'],
                'branch' => $version['branch'],
                'constraint' => $constraint,
                'line' => $line,
                'ref' => $line === null ? null : self::ref($checkouts, $line),
                'path' => self::worktree($checkouts, (string) $line),
            ];
        }

        return $pairs;
    }

    /**
     * The release line a core branch pins itself to: a major as a string, or the
     * branch name where the pin is a development one.
     *
     * Asked over a window rather than parsed into a range, the way the Fluid
     * engine is asked in `bin/cli versions:check`: the question is only ever
     * "does this pin line 9", and a pin that answers for two lines is the case
     * this exists to catch.
     */
    public static function line(string $constraint): ?string
    {
        $constraint = trim($constraint);
        if (str_starts_with($constraint, 'dev-')) {
            return substr($constraint, 4);
        }

        $majors = array_values(array_filter(
            range(1, 30),
            static fn(int $major): bool => Versions::admits($constraint, $major),
        ));

        return count($majors) === 1 ? (string) $majors[0] : null;
    }

    /**
     * The ref a line is read at: its newest tag, or the branch itself where the
     * line is a development one.
     */
    public static function ref(string $checkouts, string $line): ?string
    {
        if (!ctype_digit($line)) {
            return $line;
        }

        $tags = (string) shell_exec(sprintf(
            'git -C %s tag --list %s --sort=-v:refname 2>/dev/null',
            escapeshellarg(self::mirror($checkouts)),
            escapeshellarg($line . '.*'),
        ));
        $newest = strtok($tags, "\n");

        return $newest === false ? null : $newest;
    }

    /** The commit a ref points at, in the mirror or in a worktree. */
    public static function revision(string $repository, string $ref): string
    {
        return trim((string) shell_exec(sprintf(
            'git -C %s rev-parse %s 2>/dev/null',
            escapeshellarg($repository),
            escapeshellarg($ref . '^{commit}'),
        )));
    }
}
