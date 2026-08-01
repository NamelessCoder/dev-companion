<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Decisions;
use Typo3CmsMcp\Upkeep\Requirements;

/**
 * Everything the format of decisions/ promises a reader, checked against the
 * files.
 *
 * An id that agrees with its file name, its heading and its group, a date, a
 * status, a sentence to open with, fields from the fixed set in the order they
 * belong in, and something under **Wrong if**. `composer test` runs the same
 * check through DecisionsTest; this is the readable half.
 */
#[AsCommand(
    name: 'decisions:check',
    description: 'hold the files to the shape the readme describes',
)]
final class DecisionCheck
{
    public function __invoke(OutputInterface $output): int
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
                    . ' is not the listing of its files — run bin/cli decisions:index';
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

        $errors = Cli::errors($output);
        foreach ($problems as $problem) {
            $errors->writeln($problem);
        }
        $output->writeln(sprintf('%d decisions, %d problems', count($seen), count($problems)));

        return $problems === [] ? 0 : 1;
    }
}
