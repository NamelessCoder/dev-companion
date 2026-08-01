<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Todo as Todos;

/**
 * Holds todo/ to the shape `bin/cli next` reads it in, and lists what is in it
 * for whoever wants the overview `next` deliberately does not give.
 *
 * A todo is prose and stays prose — the next concrete step is a paragraph
 * somebody wrote for somebody else to start from, and nothing here shortens it.
 * What is checked is the head of labelled lines each file opens with, where the
 * file sits, and that what a todo claims to serve exists. A todo naming a
 * feedback that was closed two commits ago is the failure worth catching: the
 * feedback is the reason it is in the queue, and when it goes the todo is either
 * done or needs trimming to the part that is left. A todo in `waiting/` is held
 * to the one thing it exists to carry — the question it is blocked on, in the
 * words it was asked in — because no session is offered it to ask again.
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
            'list' => ['', 'every todo by title: what recurs, the queue in order, and what waits', self::list(...)],
            'check' => ['', 'hold every file to the head and the place that say what it is', self::check(...)],
        ];
    }

    /**
     * Titles only, because that is what an overview is. What a todo asks for is
     * a paragraph, and five paragraphs are what `bin/cli next` exists to spare
     * a session that only has to start one of them.
     */
    private static function list(): int
    {
        foreach (Todos::recurring() as $todo) {
            printf(
                "%-12s %s%s\n",
                $todo['every'],
                $todo['title'],
                Todos::due($todo['every'], $todo['checked']) ? '' : ' — not due, last ' . $todo['checked'],
            );
        }

        $items = Todos::items();
        foreach ($items as $position => $item) {
            printf("%-12s %s\n", $position === 0 ? 'next' : (string) ($position + 1), $item['title']);
        }
        if ($items === []) {
            print "The queue is empty.\n";
        }

        foreach (Todos::waiting() as $todo) {
            printf("%-12s %s — %s\n", 'waiting', $todo['title'], $todo['waitingOn']);
        }

        foreach (Todos::references() as $reference) {
            printf("%-12s %s\n", 'read only', $reference['title']);
        }

        return 0;
    }

    /**
     * Every file says what it is by where it sits, and every todo says what it
     * answers for.
     */
    public static function check(): int
    {
        $problems = [];
        $reading = [];
        $positions = [];

        foreach (Todos::all() as $todo) {
            $where = $todo['path'];

            if ($todo['title'] === '') {
                $problems[] = $where . ' opens with no heading, so nothing says what it is about';
            }
            foreach ($todo['strays'] as $stray) {
                $problems[] = $where . ' opens with ' . $stray . ', which is no field of a todo';
            }

            if ($todo['kind'] === 'reference') {
                if ($todo['serves'] !== []) {
                    $problems[] = $where . ' is kept for reading and serves ' . implode(', ', $todo['serves']);
                }
                continue;
            }

            if ($todo['serves'] === []) {
                $problems[] = $where . ' opens with no `Serves:`, so it is an idea rather than a todo';
            }
            if ($todo['body'] === '') {
                $problems[] = $where . ' does not say what the next concrete step is';
            }
            foreach ($todo['serves'] as $what) {
                $unreadable = Todos::unreadable($what);
                if ($unreadable !== null) {
                    $problems[] = $where . ' serves ' . $what . ', ' . $unreadable;
                }
            }
            foreach ($todo['run'] as $command) {
                if (!Cli::knows($command)) {
                    $problems[] = $where . ' runs `' . $command . '`, which this command cannot do';
                }
                $reading[$command][] = $todo['title'];
            }

            if ($todo['kind'] === 'waiting') {
                // The question is the whole of what a waiting todo adds: it is
                // offered to no session, so nothing else will ask it again.
                if ($todo['waitingOn'] === '') {
                    $problems[] = $where . ' waits and does not say on what — `**Waiting on:**` is the question';
                }
                continue;
            }
            if ($todo['waitingOn'] !== '') {
                $problems[] = $where . ' says what it waits on and is not in todo/waiting/';
            }

            if ($todo['kind'] === 'queue') {
                // The number is the place in the queue, and two files claiming
                // one leave the order to whatever the file system answers.
                if ($todo['position'] === '') {
                    $problems[] = $where . ' is queued and unnumbered, so nothing says where in the order it is';
                } elseif (isset($positions[$todo['position']])) {
                    $problems[] = $where . ' is number ' . $todo['position'] . ', and so is ' . $positions[$todo['position']];
                }
                $positions[$todo['position']] = $where;

                if ($todo['every'] !== '') {
                    $problems[] = $where . ' is queued and recurs every ' . $todo['every']
                        . ' — what comes round belongs in todo/recurring/';
                }
                continue;
            }

            if ($todo['every'] === '') {
                $problems[] = $where . ' comes round and does not say how often';
            } elseif ($todo['every'] !== 'session' && preg_match('/^\d+ days?$/', $todo['every']) !== 1) {
                $problems[] = $where . ' recurs every ' . $todo['every'] . ', and a cadence is ' . Todos::CADENCE;
            } elseif ($todo['every'] !== 'session' && strtotime($todo['checked']) === false) {
                $problems[] = $where . ' recurs on a clock and was last checked '
                    . ($todo['checked'] === '' ? 'never — `**Checked:**` is what dates it' : $todo['checked']);
            }
        }

        // The readings `bin/cli next` performs are the reason it can tell a
        // session there is nothing left to read: none and it silently stops
        // doing half its job, two and it does it twice.
        foreach (Todos::READINGS as $command) {
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
            "%d files, %d recurring, %d queued, %d waiting, %d problems\n",
            count(Todos::all()),
            count(Todos::recurring()),
            count(Todos::items()),
            count(Todos::waiting()),
            count($problems),
        );

        return $problems === [] ? 0 : 1;
    }
}
