<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Typo3CmsMcp\Cli;
use Typo3CmsMcp\Versions;

/**
 * Verifies the bundled catalogs below knowledge/catalog/ against TYPO3 core
 * checkouts and reports drift. It never writes: the component catalog stays
 * the curated search index and fallback, while an active installation supplies
 * the primary component contract at lookup time.
 *
 * Two things can be wrong with an entry, and each has its own command: the
 * paths it names are gone from a checkout, or its `since`/`until` no longer say
 * which versions it holds on.
 */
final class Catalog implements Subject
{
    public static function about(): string
    {
        return 'what a core update invalidated in knowledge/catalog/';
    }

    public static function commands(): array
    {
        return [
            'check' => ['', 'the versions each entry holds on, the shipped system extensions, and the worked examples, against .checkouts/', self::check(...)],
            'paths' => ['<checkout>', 'the paths one entry names, against one core checkout of your own', self::paths(...)],
        ];
    }

    /**
     * Whether every binding still says what this repository's own sources say.
     *
     * Every covered version is read from .checkouts/ (`bin/cli checkouts`), so
     * the binding is re-derived from those rather than from whatever checkout
     * happens to be on the machine.
     */
    public static function check(): int
    {
        $root = dirname(__DIR__, 2);

        return max(
            self::verifyBindings($root . '/.checkouts', self::read('components')),
            self::verifySystemExtensions($root . '/.checkouts', self::read('system-extensions')),
            self::verifyReferences($root . '/.checkouts', self::read('references')),
        );
    }

    /**
     * Whether every path an entry names still exists in one checkout.
     *
     * @param array<int, string> $arguments
     */
    private static function paths(array $arguments): int
    {
        $coreRoot = rtrim($arguments[0] ?? '', '/');
        if ($coreRoot === '') {
            return Cli::usage(self::class, 'paths');
        }
        if (!is_dir($coreRoot . '/typo3/sysext/core')) {
            fwrite(STDERR, sprintf("Not a TYPO3 core checkout: %s\n", $coreRoot));

            return Cli::usage(self::class, 'paths');
        }

        $components = self::read('components');
        echo "Components\n";
        $problems = 0;
        foreach ($components as $component) {
            $paths = $component['sassPaths'] ?? [];
            if (isset($component['sassPath'])) {
                $paths[] = $component['sassPath'];
            }
            if (isset($component['demoPath'])) {
                $paths[] = $component['demoPath'];
            }
            foreach (array_unique($paths) as $path) {
                if (is_string($path) && $path !== '' && !file_exists($coreRoot . '/' . $path)) {
                    echo '  path gone: ' . $component['name'] . ' → ' . $path . "\n";
                    ++$problems;
                }
            }
        }
        printf("  %d components\n\n", count($components));

        if ($problems === 0) {
            $revision = trim((string) shell_exec(sprintf('git -C %s rev-parse HEAD 2>/dev/null', escapeshellarg($coreRoot))));
            printf("No drift against %s%s\n", $coreRoot, $revision === '' ? '' : ' @ ' . substr($revision, 0, 12));

            return 0;
        }

        printf("%d problem(s) found.\n", $problems);

        return 1;
    }

    /**
     * One bundled catalog, as it is on disk.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function read(string $name): array
    {
        $path = dirname(__DIR__, 2) . '/knowledge/catalog/' . $name . '.json';

        return json_decode((string) file_get_contents($path), true);
    }

    /**
     * Re-derives which majors each entry holds on, and reports where that differs
     * from what it records.
     *
     * An entry holds on a version when everything it describes is there: its Sass
     * sources, and every class and custom property it names that the newest covered
     * version has. Missing a custom property is not a detail — a caller pasting one
     * that does not exist gets CSS that silently does nothing.
     *
     * @param array<int, array<string, mixed>> $components
     */
    private static function verifyBindings(string $checkouts, array $components): int
    {
        $covered = Versions::covered();
        $sources = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/Build/Sources';
            if (!is_dir($directory)) {
                fwrite(STDERR, sprintf("No checkout for TYPO3 v%d below %s — run bin/cli checkouts update.\n", $version['major'], $checkouts));

                return 2;
            }
            // Two corpora, because the core writes the two kinds of name in
            // different places: a class or a custom property is in the Sass, and a
            // custom element's tag name is in the TypeScript that defines it.
            $sources[$version['major']] = [
                'scss' => self::readSources($directory . '/Sass', 'scss'),
                'ts' => self::readSources($directory . '/TypeScript', 'ts'),
            ];
        }

        $newest = end($covered)['major'];
        echo "Component bindings\n";
        $problems = 0;
        foreach ($components as $component) {
            $holds = [];
            foreach ($covered as $version) {
                $holds[$version['major']] = self::describesTheSame($component, $sources, $version, $newest, $checkouts);
            }

            $found = self::derivedSince($holds);
            $recorded = isset($component['since']) ? (int) $component['since'] : null;
            if ($found !== $recorded) {
                printf(
                    "  %s: records %s, holds %s\n",
                    $component['name'],
                    $recorded === null ? 'no binding' : 'since v' . $recorded,
                    $found === null ? 'on every covered version' : 'from v' . $found,
                );
                ++$problems;
            }
        }
        printf("  %d components against %s\n\n", count($components), implode(', ', array_column($covered, 'branch')));

        if ($problems === 0) {
            echo "Every binding still says what the checkouts say.\n";

            return 0;
        }

        printf("%d binding(s) out of date.\n", $problems);

        return 1;
    }

    /**
     * The first covered major from which an entry holds without a gap up to the
     * newest, or null when it holds everywhere. A gap in the middle is reported as
     * no binding at all — a range cannot express it, and the entry needs splitting
     * rather than a number.
     *
     * @param array<int, bool> $holds
     */
    private static function derivedSince(array $holds): ?int
    {
        $since = null;
        foreach (array_reverse($holds, true) as $major => $holdsHere) {
            if (!$holdsHere) {
                break;
            }
            $since = $major;
        }

        return $since === array_key_first($holds) ? null : $since;
    }

    /**
     * @param array<string, mixed> $component
     * @param array<int, array{scss: string, ts: string}> $sources
     * @param array{major: int, branch: string, status: string} $version
     */
    private static function describesTheSame(array $component, array $sources, array $version, int $newest, string $checkouts): bool
    {
        $major = $version['major'];
        foreach ($component['sassPaths'] ?? [] as $path) {
            if (!file_exists($checkouts . '/' . $version['branch'] . '/' . $path)) {
                return false;
            }
        }

        // An entry with no Sass source is a custom element, and what it is called
        // is in the TypeScript that defines it rather than in any stylesheet.
        if (($component['sassPaths'] ?? []) === []
            && !self::carries($sources[$major]['ts'], (string) $component['rootClass'])
        ) {
            return false;
        }

        $named = array_merge(
            $component['customProperties'] ?? [],
            $component['variants'] ?? [],
            $component['modifiers'] ?? [],
            $component['subComponents'] ?? [],
        );
        foreach ($named as $token) {
            // Only what the newest covered version actually writes in its Sass. A
            // Bootstrap class the core never spells out — btn-secondary comes from
            // a state map loop — is absent on every version and says nothing about
            // which ones this entry holds on.
            if (self::carries($sources[$newest]['scss'], $token) && !self::carries($sources[$major]['scss'], $token)) {
                return false;
            }
        }

        return true;
    }

    private static function carries(string $haystack, string $token): bool
    {
        return preg_match('/(^|[^a-z0-9-])' . preg_quote($token, '/') . '([^a-z0-9-]|$)/', $haystack) === 1;
    }

    /** Every file of one extension below a directory, as one corpus to search. */
    private static function readSources(string $directory, string $extension): string
    {
        $sources = '';
        $entries = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
        foreach ($entries as $entry) {
            if ($entry instanceof SplFileInfo && $entry->getExtension() === $extension) {
                $sources .= (string) file_get_contents($entry->getPathname());
            }
        }

        return $sources;
    }

    /**
     * Re-reads which system extensions each covered version ships, and reports
     * every difference from what the catalog records.
     *
     * Nothing is judged here: the extension keys below typo3/sysext are the answer,
     * and the Composer package name is what that directory's own composer.json says.
     * A core release that adds or drops a system extension therefore invalidates the
     * catalog loudly rather than leaving it to be noticed by a caller.
     *
     * @param array<int, array<string, mixed>> $recorded
     */
    private static function verifySystemExtensions(string $checkouts, array $recorded): int
    {
        echo "System extensions\n";
        $covered = Versions::covered();
        $shipped = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/typo3/sysext';
            if (!is_dir($directory)) {
                fwrite(STDERR, sprintf("No checkout for TYPO3 v%d below %s — run bin/cli checkouts update.\n", $version['major'], $checkouts));

                return 2;
            }
            foreach (scandir($directory) ?: [] as $key) {
                $manifest = $directory . '/' . $key . '/composer.json';
                if ($key === '.' || $key === '..' || !is_file($manifest)) {
                    continue;
                }
                $shipped[$key][$version['major']] = (string) (json_decode((string) file_get_contents($manifest), true)['name'] ?? '');
            }
        }

        $problems = 0;
        $byKey = array_column($recorded, null, 'key');
        foreach ($shipped as $key => $packages) {
            $majors = array_keys($packages);
            $entry = $byKey[$key] ?? null;
            if ($entry === null) {
                printf("  %s: shipped on v%s, not in the catalog\n", $key, implode(', v', $majors));
                ++$problems;
                continue;
            }
            $problems += self::reportRange($key, $entry, $majors, array_column($covered, 'major'));

            $package = end($packages);
            if (($entry['package'] ?? '') !== $package) {
                printf("  %s: records package %s, ships as %s\n", $key, (string) ($entry['package'] ?? ''), $package);
                ++$problems;
            }
        }
        foreach ($byKey as $key => $entry) {
            if (!isset($shipped[$key])) {
                printf("  %s: in the catalog, shipped by no covered version\n", $key);
                ++$problems;
            }
        }
        printf("  %d system extensions against %s\n\n", count($shipped), implode(', ', array_column($covered, 'branch')));

        if ($problems === 0) {
            echo "Every system extension is recorded as the checkouts ship it.\n";

            return 0;
        }

        printf("%d system extension(s) out of date.\n", $problems);

        return 1;
    }

    /**
     * Re-reads which covered versions have each worked example, and reports every
     * entry whose recorded range no longer says so.
     *
     * An index of paths is the kind of thing a core release invalidates silently: a
     * directory that moved leaves an answer pointing at nothing, and the caller
     * reads the miss as "I looked in the wrong place". Existence is the whole test
     * here — what is inside is prose that a human wrote and a human has to reread.
     *
     * @param array<int, array<string, mixed>> $references
     */
    private static function verifyReferences(string $checkouts, array $references): int
    {
        echo "Core references\n";
        $covered = Versions::covered();
        $problems = 0;
        foreach ($references as $entry) {
            $path = (string) $entry['path'];
            $majors = [];
            foreach ($covered as $version) {
                $branch = $checkouts . '/' . $version['branch'];
                if (!is_dir($branch)) {
                    fwrite(STDERR, sprintf("No checkout for TYPO3 v%d below %s — run bin/cli checkouts update.\n", $version['major'], $checkouts));

                    return 2;
                }
                if (file_exists($branch . '/' . $path)) {
                    $majors[] = $version['major'];
                }
            }

            if ($majors === []) {
                printf("  %s: on no covered version\n", $path);
                ++$problems;
                continue;
            }
            $problems += self::reportRange($path, $entry, $majors, array_column($covered, 'major'));
        }
        printf("  %d references against %s\n\n", count($references), implode(', ', array_column($covered, 'branch')));

        if ($problems === 0) {
            echo "Every worked example is where it is recorded.\n";

            return 0;
        }

        printf("%d reference(s) out of date.\n", $problems);

        return 1;
    }

    /**
     * Whether the recorded since/until still says which versions ship an extension.
     * A range with a hole in it is reported as such: it cannot be expressed, and an
     * extension that came back is a different statement from one that never left.
     *
     * @param array<string, mixed> $entry
     * @param array<int, int> $majors
     * @param array<int, int> $covered
     */
    private static function reportRange(string $key, array $entry, array $majors, array $covered): int
    {
        if ($majors !== range((int) $majors[0], (int) end($majors))) {
            printf("  %s: shipped on v%s, which no range can express\n", $key, implode(', v', $majors));

            return 1;
        }

        $since = $majors[0] === $covered[0] ? null : $majors[0];
        $until = end($majors) === end($covered) ? null : end($majors);
        $recordedSince = isset($entry['since']) ? (int) $entry['since'] : null;
        $recordedUntil = isset($entry['until']) ? (int) $entry['until'] : null;
        if ($since === $recordedSince && $until === $recordedUntil) {
            return 0;
        }

        printf(
            "  %s: records %s, ships %s\n",
            $key,
            self::rangeLabel($recordedSince, $recordedUntil),
            self::rangeLabel($since, $until),
        );

        return 1;
    }

    private static function rangeLabel(?int $since, ?int $until): string
    {
        $label = Versions::label($since, $until);

        return $label === '' ? 'on every covered version' : $label;
    }
}
