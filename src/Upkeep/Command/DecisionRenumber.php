<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Decisions;
use Typo3CmsMcp\Upkeep\Renumber;

/**
 * Giving a decision another number, and handing over the references it cannot.
 *
 * Ten sessions read one `main` and two of them write one id; the rebase fails on
 * *two decision files claim the same id* and the later entry moves. What used to
 * be dangerous is the move: a search and replace over the id is silently wrong
 * wherever the files naming it did not all mean one entry, and no check fails
 * afterwards because the entry it now points at exists.
 *
 * This settles the half a file can settle — the entry, its name, and every
 * reference whose link path says which entry is meant — and prints the rest,
 * which is the half that has gone wrong both times it went wrong. A person
 * reads those against `git diff main -- <file>`: a line this branch added means
 * this branch's entry. `D-DOC-015` is what that split was measured against.
 *
 * The generated listings are put back in order where they already carried the
 * entry, because the id is what a group sorts on. Where they did not — a branch
 * that added the entry and left the listing alone, which is what a worktree is
 * told to do — nothing here writes one either.
 */
#[AsCommand(
    name: 'decisions:renumber',
    description: 'give a decision another number, moving what a link path settles and naming what it does not',
)]
final class DecisionRenumber
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the decision to move, by id or by file')]
        string $decision,
        #[Argument('the number it takes, or nothing for the next one free in its group')]
        string $number = '',
    ): int {
        $errors = Cli::errors($output);
        $root = Paths::root();

        $from = $this->id($decision);
        if ($from === '') {
            $errors->writeln($decision . ' names neither an id nor a decision file');

            return 1;
        }

        try {
            $to = $number === '' ? Renumber::next($root, substr($from, 2, 3)) : $this->target($from, $number);
            $report = Renumber::decision($root, $from, $to);
        } catch (\InvalidArgumentException|\RuntimeException $exception) {
            $errors->writeln($exception->getMessage());

            return 1;
        }

        $output->writeln(sprintf('%s → %s', $report['from'], $report['to']));
        $output->writeln('    ' . $report['file']);

        $output->writeln('');
        $output->writeln(sprintf(
            '%d mentions moved: the entry itself, and every reference whose own line names its file.',
            count($report['moved']),
        ));
        foreach ($this->byFile($report['moved']) as $file => $lines) {
            $output->writeln(sprintf('    %s (%d)', $file, count($lines)));
        }

        $output->writeln('');
        if ($report['named'] === []) {
            $output->writeln('Nothing else names ' . $report['from'] . '.');
        } else {
            $output->writeln(sprintf(
                '%d name %s and no file says which entry is meant. Read each against',
                count($report['named']),
                $report['from'],
            ));
            $output->writeln('`git diff main -- <file>`: a line this branch added means this branch\'s entry.');
            foreach ($this->byFile($report['named']) as $file => $lines) {
                $output->writeln('');
                $output->writeln('    ' . $file);
                foreach ($lines as $line) {
                    $output->writeln(sprintf('    %5d  %s', $line['line'], $line['text']));
                }
            }
        }

        if ($this->listed($report['moved'])) {
            (new DecisionIndex())(new NullOutput());
            $output->writeln('');
            $output->writeln('The listings are regenerated: the number is what a group sorts on.');
        }

        return 0;
    }

    /** The id a caller named, whether they named it or the file carrying it. */
    private function id(string $decision): string
    {
        if (preg_match('/^D-[A-Z]{3}-\d{3}[a-z]?$/', $decision) === 1) {
            return $decision;
        }

        foreach (Decisions::files() as $path) {
            if (basename($path) === basename($decision)) {
                return Decisions::read($path)['id'];
            }
        }

        return '';
    }

    /**
     * The number a caller named, as a whole id. A bare one takes the group of
     * the entry being moved; a written-out id is left as it stands, so naming
     * another group is refused rather than silently corrected.
     */
    private function target(string $from, string $number): string
    {
        if (preg_match('/^D-[A-Z]{3}-\d{3}[a-z]?$/', $number) === 1) {
            return $number;
        }
        if (preg_match('/^(\d{1,3})([a-z]?)$/', $number, $matches) !== 1) {
            throw new \InvalidArgumentException($number . ' is no decision number');
        }

        return substr($from, 0, 6) . str_pad($matches[1], 3, '0', STR_PAD_LEFT) . $matches[2];
    }

    /**
     * @param list<array{file: string, line: int, text: string}> $references
     * @return array<string, list<array{file: string, line: int, text: string}>>
     */
    private function byFile(array $references): array
    {
        $byFile = [];
        foreach ($references as $reference) {
            $byFile[$reference['file']][] = $reference;
        }

        return $byFile;
    }

    /** @param list<array{file: string, line: int, text: string}> $moved */
    private function listed(array $moved): bool
    {
        foreach ($moved as $reference) {
            if (preg_match('#^decisions/(?:[^/]+/)?readme\.md$#', $reference['file']) === 1) {
                return true;
            }
        }

        return false;
    }
}
