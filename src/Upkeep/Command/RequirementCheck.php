<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\RequirementState;

/**
 * Everything the format of requirements/ promises a reader, checked against the
 * files.
 *
 * An id that agrees with its file name, its heading and its group, a statement
 * to open with, a status, and tests that exist behind what claims to be held.
 * `composer test` runs the same check through RequirementsTest, the listing
 * apart: that one is generated from every file in a group and can only be true
 * on a checkout that has all of them, so it is held here alone (D-FBK-011).
 * This is also the readable half.
 */
#[AsCommand(
    name: 'requirements:check',
    description: 'hold the files to the shape the readme describes',
)]
final class RequirementCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $problems = [];
        $decisions = Decisions::all();
        $tests = self::testMethods();
        $seen = [];

        foreach (Requirements::files() as $path) {
            $file = substr($path, strlen(Requirements::directory()) + 1);
            preg_match('/^([a-z]+)-(\d+[a-z]?)-/', basename($path, '.md'), $named);
            $expected = 'R-' . strtoupper($named[1] ?? '') . '-' . ($named[2] ?? '');

            $requirement = Requirements::read($path);
            if ($requirement['id'] === '') {
                $problems[] = $file . ' has no id';
                continue;
            }

            $id = $requirement['id'];
            if ($id !== $expected) {
                $problems[] = $file . ' is named after ' . $expected . ' and says it is ' . $id;
            }
            if (preg_match('/^\d{3}[a-z]?$/', $named[2] ?? '') !== 1) {
                $problems[] = $file . ' is numbered ' . ($named[2] ?? '(nothing)') . ' and a number is three digits, which is what lists the files in order';
            }
            if ($requirement['heading'] !== $id) {
                $problems[] = $id . ' has the heading of ' . $requirement['heading'];
            }
            if (isset($seen[$id])) {
                $problems[] = $id . ' is claimed by ' . $seen[$id] . ' and by ' . $file;
            }
            $seen[$id] = $file;

            $group = Requirements::GROUPS[substr($id, 2, 3)] ?? null;
            if ($group === null) {
                $problems[] = $id . ' has a prefix no group is named after';
            } elseif ($group !== $requirement['group']) {
                $problems[] = $id . ' belongs in ' . $group . '/ and sits in ' . $requirement['group'] . '/';
            }

            if (!in_array($requirement['status'], RequirementState::writtenValues(), true)) {
                $problems[] = $id . ' has the status ' . ($requirement['status'] === '' ? '(none)' : $requirement['status']);
            }
            foreach ($requirement['restsOn'] as $decision) {
                // Whether the decision still holds is a reading rather than a
                // failure, and bin/cli unresolved:list is where that is read out.
                // What fails here is a pointer at nothing.
                if (!isset($decisions[$decision])) {
                    $problems[] = $id . ' rests on ' . $decision . ', which no decision has';
                }
            }
            if ($requirement['title'] === '' || $requirement['statement'] === '') {
                $problems[] = $id . ' does not open with the sentence that has to hold';
            }

            if (RequirementState::tryFrom($requirement['status']) === RequirementState::Open) {
                continue;
            }
            if ($requirement['heldBy'] === '') {
                $problems[] = $id . ' does not say what holds it, or that nothing does';
            }
            if ($requirement['tests'] === [] && !str_contains($requirement['heldBy'], 'not guarded')) {
                $problems[] = $id . ' names neither a test nor that it is not guarded';
            }
            foreach ($requirement['tests'] as $test) {
                $exists = str_contains($test, '::')
                    ? in_array($test, $tests, true)
                    : in_array($test, array_map(static fn(string $m): string => explode('::', $m)[0], $tests), true);
                if (!$exists) {
                    $problems[] = $id . ' names ' . $test . ', which no test declares';
                }
            }
        }

        foreach (Requirements::GROUPS as $group) {
            $readme = Requirements::directory() . '/' . $group . '/readme.md';
            if (!is_file($readme)) {
                $problems[] = $group . '/ has no readme';
                continue;
            }
            if (!str_ends_with((string) file_get_contents($readme), Requirements::listing($group))) {
                $problems[] = $group . '/readme.md is not the listing of its files — run bin/cli requirements:index';
            }
        }

        foreach (self::scenarioReferences() as $id => $where) {
            if (!isset($seen[$id])) {
                $problems[] = $where . ' names ' . $id . ', which no requirement has';
            }
        }

        $errors = Cli::errors($output);
        foreach ($problems as $problem) {
            $errors->writeln($problem);
        }
        $output->writeln(sprintf('%d requirements, %d problems', count($seen), count($problems)));

        return $problems === [] ? 0 : 1;
    }

    /**
     * Every test method in this suite as `Class::method`, read from the files
     * rather than from reflection — the same list ScenariosTest holds its cases to.
     *
     * @return array<int, string>
     */
    private static function testMethods(): array
    {
        $methods = [];
        foreach (['Unit', 'Contract', 'Smoke'] as $suite) {
            foreach (Finder::create()->files()->in(Paths::root() . '/tests/' . $suite)->depth(0)->name('*Test.php')->sortByName() as $file) {
                preg_match_all('/public function (\w+)\(/', (string) file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $method) {
                    $methods[] = $file->getBasename('.php') . '::' . $method;
                }
            }
        }

        return $methods;
    }

    /**
     * The requirement ids the scenario suite names, with the file that names them.
     * A scenario that holds itself to a withdrawn requirement is a claim about
     * something nobody can read any more.
     *
     * @return array<string, string>
     */
    private static function scenarioReferences(): array
    {
        $references = [];
        $root = Paths::root();
        foreach (Finder::create()->files()->in($root . '/scenarios')->name('*.md')->sortByName() as $file) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($file->getPathname()), $matches);
            foreach ($matches[1] as $id) {
                $references[$id] ??= substr($file->getPathname(), strlen($root) + 1);
            }
        }

        return $references;
    }
}
