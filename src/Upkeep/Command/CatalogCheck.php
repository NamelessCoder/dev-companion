<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Upkeep\Catalogs;
use Typo3CmsMcp\Upkeep\Checkouts;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\TestingFramework;

/**
 * Verifies the bundled catalogs below knowledge/catalog/ against the TYPO3 core
 * checkouts and reports drift. It never writes: the component catalog stays
 * the curated search index and fallback, while an active installation supplies
 * the primary component contract at lookup time.
 *
 * It also reads the two things outside `knowledge/catalog/` that a release can
 * invalidate silently and that the checkouts answer: which Fluid engine each
 * branch pins itself to, and whether the testing-framework release each branch
 * pins still says what the hints about it state.
 */
#[AsCommand(
    name: 'catalog:check',
    description: 'the versions each entry holds on, the shipped system extensions, the worked examples, the Fluid engine each branch pins, and the testing-framework release it pins, against .checkouts/',
)]
final class CatalogCheck
{
    /**
     * What the hints about typo3/testing-framework rest on, per file of the
     * package, so that a release changing one of them fails here rather than
     * ageing quietly into a wrong answer (D-KNW-2).
     *
     * Existence carries the statement that the four boilerplate files are there
     * to be copied; each needle carries one sentence of `project-extension-tests`
     * in `knowledge/architecture-hints/php.json`, named beside it. What no needle
     * covers is not guarded — this is the load-bearing half, not the hint.
     *
     * @var array<string, array<int, string>>
     */
    private const TESTING_FRAMEWORK_EVIDENCE = [
        // "the files say in their own header that extensions should copy them"
        'Resources/Core/Build/UnitTests.xml' => ['copy it to an own place'],
        'Resources/Core/Build/FunctionalTests.xml' => ['copy it to an own place'],
        'Resources/Core/Build/UnitTestsBootstrap.php' => [],
        'Resources/Core/Build/FunctionalTestsBootstrap.php' => [],
        'Classes/Core/Testbase.php' => [
            // "a functional run needs database credentials in the environment",
            // and the message that does not name the variables it is missing
            'typo3DatabaseDriver',
            'typo3DatabaseHost',
            'typo3DatabaseName',
            'typo3DatabaseUsername',
            'typo3DatabasePassword',
            'Database credentials for tests are neither set through environment',
            // "$testExtensionsToLoad takes paths relative to the document root"
            'ORIGINAL_ROOT . $extensionPath',
            'Test extension path ',
        ],
        // "thrown by the testing framework's own package collection"
        'Classes/Core/PackageCollection.php' => ['depends on package '],
        // "it writes a sys_template row with the clear flag set"
        'Classes/Core/Functional/FunctionalTestCase.php' => ["'clear' => 3"],
    ];

    /**
     * Whether every binding still says what this repository's own sources say.
     *
     * Every covered version is read from .checkouts/ (`bin/cli checkouts:update`),
     * so the binding is re-derived from those rather than from whatever checkout
     * happens to be on the machine.
     */
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();

        return max(
            self::verifyBindings($output, $checkouts, Catalogs::read('components')),
            self::verifySystemExtensions($output, $checkouts, Catalogs::read('system-extensions')),
            self::verifyReferences($output, $checkouts, Catalogs::read('references')),
            self::verifyFluidEngine($output, $checkouts),
            self::verifyTestingFramework($output, $checkouts),
        );
    }

    /**
     * Whether the testing-framework release each branch pins still says what the
     * hints about it say.
     *
     * D-KNW-2 verified those statements against tags, because the package has a
     * release cycle of its own and no checkout here contains it — and named its
     * own gap in doing so: a release that changes one of them inside a line would
     * be noticed by nobody, since this command re-read the core and nothing else.
     *
     * Nothing about the pairing is recorded: the core pins the line per branch in
     * its own require-dev, the newest tag on that line is what the worktree holds,
     * and both are re-derived here. So a release that changes nothing relevant
     * passes silently, and one that moves a load-bearing sentence fails — which is
     * the difference between a guard and a reminder to go and look.
     */
    private static function verifyTestingFramework(OutputInterface $output, string $checkouts): int
    {
        $output->writeln('typo3/testing-framework');
        $mirror = TestingFramework::mirror($checkouts);
        if (!is_dir($mirror)) {
            Cli::errors($output)->writeln(sprintf('No %s clone below %s — run bin/cli checkouts:update.', TestingFramework::PACKAGE, $checkouts));

            return 2;
        }

        $problems = 0;
        $read = [];
        foreach (TestingFramework::pairing($checkouts) as $pair) {
            $output->writeln(sprintf(
                '  %-5s %-9s %s',
                $pair['branch'],
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                $pair['ref'] ?? 'names no single release line',
            ));
            if ($pair['ref'] === null) {
                ++$problems;
                continue;
            }
            if (isset($read[$pair['ref']])) {
                continue;
            }

            $read[$pair['ref']] = true;
            $problems += self::readTestingFramework($output, $mirror, $pair);
        }
        $output->writeln(sprintf('  %d release line(s) against %s', count($read), implode(', ', array_column(Versions::covered(), 'branch'))));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every statement about the harness still reads as D-KNW-2 read it.');

            return 0;
        }

        $output->writeln(sprintf('%d statement(s) about the harness no longer read as D-KNW-2 read them.', $problems));

        return 1;
    }

    /**
     * One release line, read where it is checked out.
     *
     * A worktree behind the line's newest tag is reported rather than read: the
     * release that moved it is precisely what this is looking for, and reading
     * the older one would answer for a version nobody installs any more.
     *
     * @param array{major: int, branch: string, constraint: string, line: ?string, ref: ?string, path: string} $pair
     */
    private static function readTestingFramework(OutputInterface $output, string $mirror, array $pair): int
    {
        $ref = (string) $pair['ref'];
        $checkedOut = TestingFramework::revision($pair['path'], 'HEAD');
        if ($checkedOut === '') {
            $output->writeln(sprintf('    %s is not checked out — run bin/cli checkouts:update', $ref));

            return 1;
        }
        if ($checkedOut !== TestingFramework::revision($mirror, $ref)) {
            $output->writeln(sprintf('    %s is checked out at %s — run bin/cli checkouts:update', $ref, substr($checkedOut, 0, 12)));

            return 1;
        }

        $problems = 0;
        foreach (self::TESTING_FRAMEWORK_EVIDENCE as $file => $needles) {
            $source = is_file($pair['path'] . '/' . $file) ? (string) file_get_contents($pair['path'] . '/' . $file) : null;
            if ($source === null) {
                $output->writeln(sprintf('    %s: %s is gone', $ref, $file));
                ++$problems;
                continue;
            }
            foreach ($needles as $needle) {
                if (!str_contains($source, $needle)) {
                    $output->writeln(sprintf('    %s: %s no longer says %s', $ref, $file, $needle));
                    ++$problems;
                }
            }
        }

        return $problems;
    }

    /**
     * Which Fluid engine each covered branch pins itself to.
     *
     * D-VER-3 gave Fluid no version axis of its own: the core pins the engine in
     * its own composer.json, so `since` / `until` on the TYPO3 major already says
     * which engine a Fluid statement was verified against. That holds only while
     * a branch admits exactly one engine major — the day one loosens its
     * constraint to span two, a `since:` on a Fluid statement stops naming an
     * engine, and nothing about the statement changes to say so.
     *
     * The reading is printed whether or not it fails, because a Fluid statement
     * is written against the engine a branch pins and this is where that number
     * is looked up.
     */
    private static function verifyFluidEngine(OutputInterface $output, string $checkouts): int
    {
        $output->writeln('Fluid engine');
        $problems = 0;
        foreach (Versions::covered() as $version) {
            $manifest = $checkouts . '/' . $version['branch'] . '/composer.json';
            if (!is_file($manifest)) {
                Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }

            $constraint = json_decode((string) file_get_contents($manifest), true)['require']['typo3fluid/fluid'] ?? null;
            if (!is_string($constraint)) {
                $output->writeln(sprintf('  %-5s requires no typo3fluid/fluid', $version['branch']));
                ++$problems;
                continue;
            }

            // Asked over a window rather than parsed into a range, which is what
            // makes an open constraint visible: `^5.3.1` answers for one major
            // and `>=4` for every one in the window, and the second is the case
            // this exists to catch. The window is wide enough that a Fluid major
            // reaching its edge would be news in itself.
            $majors = array_values(array_filter(
                range(1, 20),
                static fn(int $major): bool => Versions::admits($constraint, $major),
            ));

            $output->writeln(sprintf('  %-5s %-10s %s', $version['branch'], $constraint, $majors === [] ? 'unreadable' : 'Fluid v' . implode(', v', $majors)));
            if (count($majors) !== 1) {
                ++$problems;
            }
        }
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every branch pins one Fluid engine major, so the TYPO3 major still carries it.');

            return 0;
        }

        $output->writeln(sprintf('%d branch(es) no longer pin one Fluid engine major — D-VER-3 says the engine needs a field of its own.', $problems));

        return 1;
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
    private static function verifyBindings(OutputInterface $output, string $checkouts, array $components): int
    {
        $covered = Versions::covered();
        $sources = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/Build/Sources';
            if (!is_dir($directory)) {
                Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

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
        $output->writeln('Component bindings');
        $problems = 0;
        foreach ($components as $component) {
            $holds = [];
            foreach ($covered as $version) {
                $holds[$version['major']] = self::describesTheSame($component, $sources, $version, $newest, $checkouts);
            }

            $found = self::derivedSince($holds);
            $recorded = isset($component['since']) ? (int) $component['since'] : null;
            if ($found !== $recorded) {
                $output->writeln(sprintf(
                    '  %s: records %s, holds %s',
                    $component['name'],
                    $recorded === null ? 'no binding' : 'since v' . $recorded,
                    $found === null ? 'on every covered version' : 'from v' . $found,
                ));
                ++$problems;
            }
        }
        $output->writeln(sprintf('  %d components against %s', count($components), implode(', ', array_column($covered, 'branch'))));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every binding still says what the checkouts say.');

            return 0;
        }

        $output->writeln(sprintf('%d binding(s) out of date.', $problems));

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
        $entries = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS));
        foreach ($entries as $entry) {
            if ($entry instanceof \SplFileInfo && $entry->getExtension() === $extension) {
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
    private static function verifySystemExtensions(OutputInterface $output, string $checkouts, array $recorded): int
    {
        $output->writeln('System extensions');
        $covered = Versions::covered();
        $shipped = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/typo3/sysext';
            if (!is_dir($directory)) {
                Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

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
                $output->writeln(sprintf('  %s: shipped on v%s, not in the catalog', $key, implode(', v', $majors)));
                ++$problems;
                continue;
            }
            $problems += self::reportRange($output, $key, $entry, $majors, array_column($covered, 'major'));

            $package = end($packages);
            if (($entry['package'] ?? '') !== $package) {
                $output->writeln(sprintf('  %s: records package %s, ships as %s', $key, (string) ($entry['package'] ?? ''), $package));
                ++$problems;
            }
        }
        foreach ($byKey as $key => $entry) {
            if (!isset($shipped[$key])) {
                $output->writeln(sprintf('  %s: in the catalog, shipped by no covered version', $key));
                ++$problems;
            }
        }
        $output->writeln(sprintf('  %d system extensions against %s', count($shipped), implode(', ', array_column($covered, 'branch'))));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every system extension is recorded as the checkouts ship it.');

            return 0;
        }

        $output->writeln(sprintf('%d system extension(s) out of date.', $problems));

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
    private static function verifyReferences(OutputInterface $output, string $checkouts, array $references): int
    {
        $output->writeln('Core references');
        $covered = Versions::covered();
        $problems = 0;
        foreach ($references as $entry) {
            $path = (string) $entry['path'];
            $majors = [];
            foreach ($covered as $version) {
                $branch = $checkouts . '/' . $version['branch'];
                if (!is_dir($branch)) {
                    Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                    return 2;
                }
                if (file_exists($branch . '/' . $path)) {
                    $majors[] = $version['major'];
                }
            }

            if ($majors === []) {
                $output->writeln(sprintf('  %s: on no covered version', $path));
                ++$problems;
                continue;
            }
            $problems += self::reportRange($output, $path, $entry, $majors, array_column($covered, 'major'));
        }
        $output->writeln(sprintf('  %d references against %s', count($references), implode(', ', array_column($covered, 'branch'))));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every worked example is where it is recorded.');

            return 0;
        }

        $output->writeln(sprintf('%d reference(s) out of date.', $problems));

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
    private static function reportRange(OutputInterface $output, string $key, array $entry, array $majors, array $covered): int
    {
        if ($majors !== range((int) $majors[0], (int) end($majors))) {
            $output->writeln(sprintf('  %s: shipped on v%s, which no range can express', $key, implode(', v', $majors)));

            return 1;
        }

        $since = $majors[0] === $covered[0] ? null : $majors[0];
        $until = end($majors) === end($covered) ? null : end($majors);
        $recordedSince = isset($entry['since']) ? (int) $entry['since'] : null;
        $recordedUntil = isset($entry['until']) ? (int) $entry['until'] : null;
        if ($since === $recordedSince && $until === $recordedUntil) {
            return 0;
        }

        $output->writeln(sprintf(
            '  %s: records %s, ships %s',
            $key,
            self::rangeLabel($recordedSince, $recordedUntil),
            self::rangeLabel($since, $until),
        ));

        return 1;
    }

    private static function rangeLabel(?int $since, ?int $until): string
    {
        $label = Versions::label($since, $until);

        return $label === '' ? 'on every covered version' : $label;
    }
}
