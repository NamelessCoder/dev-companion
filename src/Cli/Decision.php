<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Decisions;
use Typo3CmsMcp\Requirements;

/**
 * Reads decisions/, where one decision is one file.
 *
 * What this replaces is one document of thirty entries that called itself
 * newest-first and was not: two had arrived at its foot, and the labels a
 * reader navigates by had drifted into thirteen spellings of four things.
 * An id now decides the directory and the file name, and the order is
 * generated from the dates rather than maintained by where a paragraph
 * was pasted.
 */
final class Decision implements Subject
{
    public static function about(): string
    {
        return 'what was decided, and on what evidence';
    }

    public static function commands(): array
    {
        return [
            'list' => ['[group]', 'every decision newest first, or one group of them', self::list(...)],
            'check' => ['', 'hold the files to the shape the readme describes', self::check(...)],
            'index' => ['', 'rewrite the listing at the foot of the readme and of each group readme', self::index(...)],
        ];
    }

    /**
     * What was decided, newest first.
     *
     * @param array<int, string> $arguments
     */
    private static function list(array $arguments): int
    {
        $group = $arguments[0] ?? '';
        if ($group !== '' && !in_array($group, Decisions::GROUPS, true)) {
            fwrite(STDERR, 'No such group: ' . $group . "\nGroups: " . implode(', ', Decisions::GROUPS) . "\n");

            return 2;
        }

        foreach (Decisions::group($group) as $decision) {
            printf(
                "%s  %-10s %-12s %-10s %s\n",
                $decision['date'],
                $decision['id'],
                $decision['group'],
                $decision['status'],
                $decision['title'],
            );
        }

        return 0;
    }

    /**
     * Everything the format promises a reader, checked against the files.
     *
     * An id that agrees with its file name, its heading and its group, a date, a
     * status, a sentence to open with, fields from the fixed set in the order
     * they belong in, and something under **Wrong if**. `composer test` runs the
     * same check through DecisionsTest; this is the readable half.
     */
    public static function check(): int
    {
        $problems = [];
        $seen = [];
        $known = [...Decisions::FIELDS, ...Decisions::LATER_FIELDS];

        foreach (Decisions::files() as $path) {
            $file = substr($path, strlen(Decisions::directory()) + 1);
            preg_match('/^([a-z]+)-(\d+[a-z]?)-/', basename($path, '.md'), $named);
            $expected = 'D-' . strtoupper($named[1] ?? '') . '-' . ($named[2] ?? '');

            $decision = Decisions::read($path);
            if ($decision['id'] === '') {
                $problems[] = $file . ' has no id';
                continue;
            }

            $id = $decision['id'];
            if ($id !== $expected) {
                $problems[] = $file . ' is named after ' . $expected . ' and says it is ' . $id;
            }
            if ($decision['heading'] !== $id) {
                $problems[] = $id . ' has the heading of ' . $decision['heading'];
            }
            if (isset($seen[$id])) {
                $problems[] = $id . ' is claimed by ' . $seen[$id] . ' and by ' . $file;
            }
            $seen[$id] = $file;

            $group = Decisions::GROUPS[substr($id, 2, 3)] ?? null;
            if ($group === null) {
                $problems[] = $id . ' has a prefix no group is named after';
            } elseif ($group !== $decision['group']) {
                $problems[] = $id . ' belongs in ' . $group . '/ and sits in ' . $decision['group'] . '/';
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $decision['date']) !== 1) {
                $problems[] = $id . ' was decided on ' . ($decision['date'] === '' ? '(no date)' : $decision['date']);
            }
            if (!in_array($decision['status'], Decisions::STATUSES, true)) {
                $problems[] = $id . ' has the status ' . ($decision['status'] === '' ? '(none)' : $decision['status']);
            }
            if ($decision['title'] === '' || $decision['statement'] === '') {
                $problems[] = $id . ' does not open with what was decided';
            }

            $rank = -1;
            foreach ($decision['fields'] as $field) {
                if (!in_array($field, $known, true)) {
                    $problems[] = $id . ' carries a field nothing reads: ' . $field;
                    continue;
                }
                if (Decisions::rank($field) < $rank) {
                    $problems[] = $id . ' has ' . $field . ' below a field that belongs under it';
                }
                $rank = max($rank, Decisions::rank($field));
            }
            if (!in_array('Wrong if', $decision['fields'], true)) {
                $problems[] = $id . ' does not say what would show it to be wrong';
            }

            $later = Decisions::fieldFor($decision['status']);
            if ($later !== '' && !in_array($later, $decision['fields'], true)) {
                $problems[] = $id . ' is ' . $decision['status'] . ' and carries no ' . $later . ' line';
            }
            foreach (Decisions::LATER_FIELDS as $field) {
                if ($field !== 'Since then' && in_array($field, $decision['fields'], true) && $later !== $field) {
                    $problems[] = $id . ' carries a ' . $field . ' line and does not say so in its status';
                }
            }
        }

        foreach (['', ...array_values(Decisions::GROUPS)] as $group) {
            $readme = Decisions::directory() . '/' . ($group === '' ? '' : $group . '/') . 'readme.md';
            if (!is_file($readme)) {
                $problems[] = $group . '/ has no readme';
                continue;
            }
            if (!str_ends_with((string) file_get_contents($readme), Decisions::listing($group))) {
                $problems[] = ($group === '' ? 'readme.md' : $group . '/readme.md')
                    . ' is not the listing of its files — run bin/cli decisions index';
            }
        }

        $requirements = Requirements::all();
        foreach (Decisions::files() as $path) {
            preg_match_all('/`(R-[A-Z]{3}-\d+[a-z]?)`/', (string) file_get_contents($path), $matches);
            foreach ($matches[1] as $requirement) {
                if (!isset($requirements[$requirement])) {
                    $problems[] = basename($path) . ' names ' . $requirement . ', which no requirement has';
                }
            }
        }

        foreach ($problems as $problem) {
            fwrite(STDERR, $problem . "\n");
        }
        printf("%d decisions, %d problems\n", count($seen), count($problems));

        return $problems === [] ? 0 : 1;
    }

    /** Writes the listing of every group, and of all of them, back into the readmes. */
    private static function index(): int
    {
        foreach (['', ...array_values(Decisions::GROUPS)] as $group) {
            $readme = Decisions::directory() . '/' . ($group === '' ? '' : $group . '/') . 'readme.md';
            $contents = (string) file_get_contents($readme);
            $head = (string) preg_replace('/\| Decided \| Id \|.*$/s', '', $contents);
            file_put_contents($readme, $head . Decisions::listing($group));
            echo substr($readme, strlen(dirname(__DIR__, 2)) + 1), "\n";
        }

        return 0;
    }
}
