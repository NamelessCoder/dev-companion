<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Catalog\DemoMarkup;
use TYPO3\DevCompanion\Knowledge\TestSuiteHints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Tool\TranslationDomainLookup;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\TestingFramework;

/**
 * Verifies the bundled catalogs below knowledge/catalog/ against the TYPO3 core
 * checkouts and reports drift. It never writes: the component catalog stays
 * the curated search index and fallback, while an active installation supplies
 * the primary component contract at lookup time.
 *
 * It also reads the four things outside `knowledge/catalog/` that a release can
 * invalidate silently and that the checkouts answer: which Fluid engine each
 * branch pins itself to, whether the testing-framework release each branch pins
 * still says what the hints about it state, which suites `runTests.sh` actually
 * offers on each branch, and the major translation domains arrived in, which is
 * the one version fact the code carries.
 */
#[AsCommand(
    name: 'catalog:check',
    description: 'the versions each entry holds on, the markup it was read off, the shipped system extensions, the worked examples, the Fluid engine each branch pins, the testing-framework release it pins, the suites runTests.sh offers, and the major translation domains arrived in, against .checkouts/',
)]
final class CatalogCheck
{
    /** How many uncurated suites the report names before it stops naming them. */
    private const NAMED_UNCURATED = 12;

    /**
     * What the hints about typo3/testing-framework rest on, per file of the
     * package, so that a release changing one of them fails here rather than
     * ageing quietly into a wrong answer (D-KNW-002).
     *
     * Existence carries the statement that the four boilerplate files are there
     * to be copied; each needle carries one sentence of `project-extension-tests`
     * in `knowledge/hints/testing.json`, named beside it. What no needle
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
            self::verifyMarkup($output, $checkouts, Catalogs::read('components')),
            self::verifySystemExtensions($output, $checkouts, Catalogs::read('system-extensions')),
            self::verifyReferences($output, $checkouts, Catalogs::read('references')),
            self::verifyFluidEngine($output, $checkouts),
            self::verifyTestingFramework($output, $checkouts),
            self::verifyTestSuites($output, $checkouts, TestSuiteHints::load()),
            self::verifyTranslationDomains($output, $checkouts),
        );
    }

    /**
     * Whether the major the translation domain answer is withheld below is still
     * the major the checkouts say the API arrived in.
     *
     * This is D-DIS-004's first "Wrong if" and the reason the number is one number
     * in one place: the domain API backported into a 13.x patch makes the
     * constant in `Tools` wrong. What makes it wrong is a release rather than an
     * edit here, so no test in this repository can see it — `knowledge/` holds
     * the covered versions but not this fact, and `VersionsTest` can only tie
     * the constant to that list. The checkouts hold the fact, so it is read
     * where the other release-driven facts are.
     *
     * The class carrying the rules has been both TranslationDomainMapper and
     * TranslationDomainResolver, so the branch is asked for either rather than
     * for the name one branch happens to use. The reading is printed whether or
     * not it fails, because this is where that number is looked up.
     */
    private static function verifyTranslationDomains(OutputInterface $output, string $checkouts): int
    {
        $output->writeln('Translation domains');
        $resolves = [];
        foreach (Versions::covered() as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/typo3/sysext/core/Classes/Localization';
            if (!is_dir($directory)) {
                Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }

            $classes = [];
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('TranslationDomain*.php')->sortByName() as $file) {
                $classes[] = $file->getFilename();
            }
            $resolves[$version['major']] = $classes !== [];
            $output->writeln(sprintf(
                '  %-5s %s',
                $version['branch'],
                $classes === [] ? 'no TranslationDomain* class' : implode(', ', $classes),
            ));
        }

        $found = self::derivedSince($resolves);
        $output->writeln(sprintf(
            '  withheld below v%d, resolved %s',
            TranslationDomainLookup::SINCE,
            $found === null ? 'on every covered version' : 'from v' . $found,
        ));
        $output->writeln('');

        if ($found === TranslationDomainLookup::SINCE) {
            $output->writeln('Translation domains still arrive where TranslationDomainLookup withholds them.');

            return 0;
        }

        $output->writeln(sprintf(
            'TranslationDomainLookup::SINCE says v%d and the checkouts say %s — D-DIS-004 named this the release that makes it wrong.',
            TranslationDomainLookup::SINCE,
            $found === null ? 'every covered version resolves them' : 'v' . $found,
        ));

        return 1;
    }

    /**
     * Whether the testing-framework release each branch pins still says what the
     * hints about it say.
     *
     * D-KNW-002 verified those statements against tags, because the package has a
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
            $output->writeln('Every statement about the harness still reads as D-KNW-002 read it.');

            return 0;
        }

        $output->writeln(sprintf('%d statement(s) about the harness no longer read as D-KNW-002 read them.', $problems));

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
     * D-VER-003 gave Fluid no version axis of its own: the core pins the engine in
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

        $output->writeln(sprintf('%d branch(es) no longer pin one Fluid engine major — D-VER-003 says the engine needs a field of its own.', $problems));

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
        foreach (Finder::create()->files()->in($directory)->name('*.' . $extension)->sortByName() as $file) {
            $sources .= (string) file_get_contents($file->getPathname());
        }

        return $sources;
    }

    /**
     * Re-reads the markup each entry was read off, and reports every checkout
     * that no longer carries what the entry recorded.
     *
     * The binding above is derived from names, so a demo rewritten around the
     * same classes reads as unchanged and the snapshot ages quietly into a wrong
     * answer — D-CAT-001 named that as what would show it wrong. A digest per
     * entry per checkout is what notices it: the entry records what each covered
     * version's demo said when somebody last read it, and a rewrite fails here.
     *
     * Nothing is written. A failure is a demo somebody has to reread, and the
     * new digest is only true once the entry says what that demo now shows.
     *
     * @param array<int, array<string, mixed>> $components
     */
    private static function verifyMarkup(OutputInterface $output, string $checkouts, array $components): int
    {
        $output->writeln('Component markup');
        $covered = Versions::covered();
        $problems = 0;
        $withoutDemo = 0;
        $unnamed = 0;
        $suppressed = 0;
        foreach ($components as $component) {
            $demo = (string) ($component['demoPath'] ?? '');
            if ($demo === '') {
                ++$withoutDemo;
                continue;
            }

            if (($component['demoDerives'] ?? true) === false) {
                ++$suppressed;
            }

            $read = [];
            $names = false;
            foreach ($covered as $version) {
                $file = $checkouts . '/' . $version['branch'] . '/' . $demo;
                if (!is_dir($checkouts . '/' . $version['branch'])) {
                    Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                    return 2;
                }
                if (!is_file($file)) {
                    continue;
                }
                $selector = (string) ($component['demoSelector'] ?? '');
                $template = (string) file_get_contents($file);
                // An entry that derives nothing is held to the whole file, for
                // the reason a demo with no example at all is: nothing is
                // handed over, so what a rewrite could change is whether the
                // judgment behind the entry still stands.
                $markup = ($component['demoDerives'] ?? true) === false
                    ? $template
                    : self::demoMarkup($template, (string) $component['rootClass'], $selector === '' ? null : $selector);
                $read[$version['major']] = substr(hash('sha256', $markup), 0, 12);
                $names = $names || DemoMarkup::carries($markup, (string) $component['rootClass']);
            }
            if ($read !== [] && !$names) {
                ++$unnamed;
            }

            $recorded = [];
            foreach ((array) ($component['markupDigests'] ?? []) as $major => $digest) {
                $recorded[(int) $major] = (string) $digest;
            }
            foreach (array_keys($read + $recorded) as $major) {
                $was = $recorded[$major] ?? null;
                $now = $read[$major] ?? null;
                if ($was === $now) {
                    continue;
                }
                $output->writeln(sprintf(
                    '  %s: v%d records %s, and %s',
                    $component['name'],
                    $major,
                    $was ?? 'no markup',
                    $now === null ? $demo . ' is not there' : 'the demo reads ' . $now,
                ));
                ++$problems;
            }
        }
        $output->writeln(sprintf(
            '  %d demos against %s',
            count($components) - $withoutDemo,
            implode(', ', array_column($covered, 'branch')),
        ));
        $output->writeln(sprintf(
            '  %d of them name the component nowhere and %d entries name no demo, so their markup is held by nothing',
            $unnamed,
            $withoutDemo,
        ));
        $output->writeln(sprintf(
            '  %d show it nowhere copyable and say so, so the whole file is what moves under them',
            $suppressed,
        ));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every demo still reads as its entry recorded it.');

            return 0;
        }

        $output->writeln(sprintf('%d demo(s) no longer read as the entry records — reread them and record what they show.', $problems));

        return 1;
    }

    /**
     * What one checkout's demo says about one component.
     *
     * The examples carrying the component are the markup an installation would
     * hand a caller, so they are what the digest covers. Where a demo wraps none
     * in `sg:example`, the file itself is the demo: `Panels.fluid.html` and
     * `RecordSearchBox.fluid.html` are pages about one component rather than
     * galleries, so an edit anywhere in them is an edit to what the entry
     * describes.
     *
     * An entry's `demoSelector` narrows that the same way it narrows the
     * answer, because the two have to be the same reading: a digest over
     * examples nobody is handed would go on passing while the one example that
     * is handed over was rewritten underneath it.
     */
    private static function demoMarkup(string $template, string $rootClass, ?string $selector): string
    {
        $examples = DemoMarkup::examples($template, $rootClass, $selector);

        return $examples === [] ? $template : implode("\n", $examples);
    }

    /**
     * The range each suite hint declares, against the script that has to have it.
     *
     * `since`/`until` on an entry in `knowledge/test-suite-hints.json` is a
     * claim about `Build/Scripts/runTests.sh`, and the whole of `R-KNW-024`
     * rests on it: the checks a brief states, the suites a guide lists, and
     * what the prose documents may name. Nothing re-read the script, so `build`
     * sat unranged and was handed to 12.4 callers as a command its own tool
     * description promised it would not be.
     *
     * The suite is taken from the command rather than from the `suite` field,
     * because the command is what has to run: `build-css` is an npm script and
     * its entry runs `-s npm -- run build-css`, so what the script needs to
     * have is `npm`.
     *
     * @param array<int, array{suite: string, command: string, since: ?int, until: ?int}> $suites
     */
    private static function verifyTestSuites(OutputInterface $output, string $checkouts, array $suites): int
    {
        $output->writeln('Test suites');
        $covered = Versions::covered();
        $offered = [];
        foreach ($covered as $version) {
            $script = $checkouts . '/' . $version['branch'] . '/Build/Scripts/runTests.sh';
            if (!is_file($script)) {
                Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }
            $offered[$version['major']] = self::suitesIn((string) file_get_contents($script));
        }

        $majors = array_column($covered, 'major');
        $problems = 0;
        $named = [];
        foreach ($suites as $entry) {
            $suite = self::suiteOf($entry['command'], $entry['suite']);
            $named[$suite] = true;
            $on = array_values(array_filter(
                $majors,
                static fn(int $major): bool => in_array($suite, $offered[$major], true),
            ));
            if ($on === []) {
                Cli::errors($output)->writeln(sprintf(
                    '  %s runs -s %s, which no covered branch offers',
                    $entry['suite'],
                    $suite,
                ));
                ++$problems;
                continue;
            }

            $read = [
                min($on) === min($majors) ? null : min($on),
                max($on) === max($majors) ? null : max($on),
            ];
            if ([$entry['since'], $entry['until']] !== $read) {
                Cli::errors($output)->writeln(sprintf(
                    '  %s declares %s and the script offers it on %s',
                    $entry['suite'],
                    Versions::label($entry['since'], $entry['until']) ?: 'every covered version',
                    implode(', ', $on),
                ));
                ++$problems;
            }
        }

        // Not a problem, and the reason it is printed: the hints are a curated
        // subset with a description and a domain each, so a suite nobody wrote
        // one for is a gap somebody may want to close rather than an error.
        $uncurated = array_values(array_diff(
            array_unique(array_merge(...array_values($offered))),
            array_keys($named),
        ));
        sort($uncurated);
        $output->writeln(sprintf(
            '  %d suites against %s, %d of them curated',
            count(array_unique(array_merge(...array_values($offered)))),
            implode(', ', array_column($covered, 'branch')),
            count($named),
        ));
        $output->writeln(sprintf(
            '  no hint names: %s',
            $uncurated === []
                ? 'nothing'
                : implode(', ', array_slice($uncurated, 0, self::NAMED_UNCURATED))
                    . (count($uncurated) > self::NAMED_UNCURATED ? sprintf(' and %d more', count($uncurated) - self::NAMED_UNCURATED) : ''),
        ));
        $output->writeln('');

        if ($problems === 0) {
            $output->writeln('Every suite hint holds on the versions runTests.sh offers it.');

            return 0;
        }

        $output->writeln(sprintf('%d suite hint(s) no longer read as the script does.', $problems));

        return 1;
    }

    /**
     * The suites one `runTests.sh` offers, read out of the `-s` block of its own
     * usage text — the one place the list is written down, and in the same shape
     * from 12.4 to main. Only that block: the databases below it are written the
     * same way and are not suites.
     *
     * @return array<int, string>
     */
    private static function suitesIn(string $script): array
    {
        if (preg_match('/Specifies the test suite to run\n(.*?)\n {4}-\S/s', $script, $block) !== 1) {
            return [];
        }
        preg_match_all('/^\s+- ([A-Za-z][A-Za-z0-9_-]*)(?: \(default\))?:/m', $block[1], $names);

        return array_values(array_unique($names[1]));
    }

    /** What a hint's command asks the script for, falling back to what the entry calls itself. */
    private static function suiteOf(string $command, string $suite): string
    {
        return preg_match('/-s ([A-Za-z][A-Za-z0-9_-]*)/', $command, $named) === 1 ? $named[1] : $suite;
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
            foreach (Finder::create()->directories()->in($directory)->depth(0)->sortByName() as $extension) {
                $manifest = $extension->getPathname() . '/composer.json';
                if (!is_file($manifest)) {
                    continue;
                }
                $shipped[$extension->getFilename()][$version['major']] = (string) (json_decode((string) file_get_contents($manifest), true)['name'] ?? '');
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
     * reads the miss as "I looked in the wrong place". Existence of the directory
     * was the whole test until it caught nothing on the entry D-CAT-002 named itself:
     * `Build/tests/playwright/e2e` is there on 13.4 while the layout the entry
     * promises — one directory per module, the accessibility scan among them —
     * arrived in 14.3. So an entry whose sentence promises a shape names the files
     * that carry it in `files`, and a version has the example when it has all of
     * them. What no file covers is still prose a human wrote and a human has to
     * reread.
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
            $inside = [];
            foreach ($covered as $version) {
                $branch = $checkouts . '/' . $version['branch'];
                if (!is_dir($branch)) {
                    Cli::errors($output)->writeln(sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                    return 2;
                }
                $missing = self::missingFrom($branch, $entry);
                if ($missing === null) {
                    $majors[] = $version['major'];
                    continue;
                }
                if ($missing !== $path) {
                    // The failure the range alone cannot name: the directory is
                    // where it was, and what the entry says is inside it is not.
                    $inside[] = sprintf('    v%d has %s and not %s', $version['major'], $path, $missing);
                }
            }

            if ($majors === []) {
                $output->writeln(sprintf('  %s: on no covered version', $path));
                self::writeAll($output, $inside);
                ++$problems;
                continue;
            }
            $drift = self::reportRange($output, $path, $entry, $majors, array_column($covered, 'major'));
            if ($drift > 0) {
                self::writeAll($output, $inside);
            }
            $problems += $drift;
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
     * The first thing a worked example names that this checkout does not have, or
     * null where it has all of them.
     *
     * The path comes first, so an entry that names no files reads exactly as it did
     * before. `files` is what the entry adds when its sentence promises a shape: the
     * two or three files that carry it, named absolutely from the checkout root
     * because half of what a suite is made of sits beside the directory rather than
     * inside it.
     *
     * @param array<string, mixed> $entry
     */
    private static function missingFrom(string $branch, array $entry): ?string
    {
        $named = array_merge([(string) $entry['path']], array_map(strval(...), (array) ($entry['files'] ?? [])));
        foreach ($named as $path) {
            if (!file_exists($branch . '/' . $path)) {
                return $path;
            }
        }

        return null;
    }

    /** @param array<int, string> $lines */
    private static function writeAll(OutputInterface $output, array $lines): void
    {
        foreach ($lines as $line) {
            $output->writeln($line);
        }
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
