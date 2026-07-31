<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Cli;
use Typo3CmsMcp\Todo as TodoFile;

/**
 * Holds todo.md to the shape `bin/cli next` reads it in, and lists what is in
 * it for whoever wants the overview `next` deliberately does not give.
 *
 * The file is prose and stays prose — the next concrete step is a paragraph
 * somebody wrote for somebody else to start from, and nothing here shortens it.
 * What is checked is the head of labelled lines each section opens with, and
 * that what a todo claims to serve exists. A todo naming a note that was closed
 * two commits ago is the failure worth catching: the note is the reason it is
 * in the queue, and when it goes the todo is either done or needs trimming to
 * the part that is left.
 */
final class Todo implements Subject
{
    public static function about(): string
    {
        return 'the order of the work, and where the last session stopped';
    }

    public static function commands(): array
    {
        return [
            'list' => ['', 'every todo by title: what recurs, then the queue in order', self::list(...)],
            'check' => ['', 'hold every section to the head that says what it is', self::check(...)],
        ];
    }

    /**
     * Titles only, because that is what an overview is. What a todo asks for is
     * a paragraph, and five paragraphs are what `bin/cli next` exists to spare
     * a session that only has to start one of them.
     */
    private static function list(): int
    {
        foreach (TodoFile::recurring() as $todo) {
            printf(
                "%-12s %s%s\n",
                $todo['every'],
                $todo['title'],
                TodoFile::due($todo['every'], $todo['checked']) ? '' : ' — not due, last ' . $todo['checked'],
            );
        }

        $items = TodoFile::items();
        foreach ($items as $position => $item) {
            printf("%-12s %s\n", $position === 0 ? 'next' : (string) ($position + 1), $item['title']);
        }
        if ($items === []) {
            print "The queue is empty.\n";
        }

        foreach (TodoFile::references() as $reference) {
            printf("%-12s %s\n", 'read only', $reference['title']);
        }

        return 0;
    }

    /**
     * Every section says what it is, and every todo says what it answers for.
     */
    public static function check(): int
    {
        $problems = [];
        $reading = [];

        foreach (TodoFile::sections() as $section) {
            $where = '"' . $section['title'] . '"';

            foreach ($section['strays'] as $stray) {
                $problems[] = $where . ' opens with ' . $stray . ', which is no field of a todo';
            }

            if ($section['kind'] === '') {
                $problems[] = $where . ' opens with ' . ($section['head'] === '' ? 'no head at all' : 'no `Serves:`')
                    . ', so nothing says whether it is work';
                continue;
            }

            if ($section['kind'] === 'reference') {
                continue;
            }

            if ($section['body'] === '') {
                $problems[] = $where . ' does not say what the next concrete step is';
            }
            foreach ($section['serves'] as $what) {
                $unreadable = TodoFile::unreadable($what);
                if ($unreadable !== null) {
                    $problems[] = $where . ' serves ' . $what . ', ' . $unreadable;
                }
            }
            foreach ($section['run'] as $command) {
                if (!Cli::knows($command)) {
                    $problems[] = $where . ' runs `' . $command . '`, which this command cannot do';
                }
                $reading[$command][] = $section['title'];
            }

            if ($section['every'] === '') {
                continue;
            }
            if ($section['every'] !== 'session' && preg_match('/^\d+ days?$/', $section['every']) !== 1) {
                $problems[] = $where . ' recurs every ' . $section['every'] . ', and a cadence is ' . TodoFile::CADENCE;
            } elseif ($section['every'] !== 'session' && strtotime($section['checked']) === false) {
                $problems[] = $where . ' recurs on a clock and was last checked '
                    . ($section['checked'] === '' ? 'never — `**Checked:**` is what dates it' : $section['checked']);
            }
        }

        // The readings `bin/cli next` performs are the reason it can tell a
        // session there is nothing left to read: none and it silently stops
        // doing half its job, two and it does it twice.
        foreach (TodoFile::READINGS as $command) {
            $named = $reading[$command] ?? [];
            if ($named === []) {
                $problems[] = 'no todo runs `' . $command . '`';
            } elseif (count($named) > 1) {
                $problems[] = '`' . $command . '` is run by ' . implode(' and ', $named);
            }
        }

        foreach ($problems as $problem) {
            fwrite(STDERR, $problem . "\n");
        }
        printf(
            "%d sections, %d recurring, %d queued, %d problems\n",
            count(TodoFile::sections()),
            count(TodoFile::recurring()),
            count(TodoFile::items()),
            count($problems),
        );

        return $problems === [] ? 0 : 1;
    }
}
