<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\OpenFeedback;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * Holds todo/ to the shape `bin/cli todo:next` reads it in.
 *
 * A todo is prose and stays prose — the next concrete step is a paragraph
 * somebody wrote for somebody else to start from, and nothing here shortens it.
 * What is checked is the head of labelled lines each file opens with, where the
 * file sits, and that what a todo claims to serve exists. A todo naming a
 * feedback that was closed two commits ago is the failure worth catching: the
 * feedback is the reason it is in the queue, and when it goes the todo is either
 * done or needs trimming to the part that is left. A todo in `waiting/` is held
 * to the one thing it exists to carry — the question it is blocked on, in the
 * words it was asked in — because no session is offered it to ask again, and
 * one in `progress/` to the branch and the date, because a claim nobody can
 * date is one nobody dares take back.
 *
 * The last thing it says is about the other direction: an open feedback that no
 * todo answers for. That is drift rather than a state — `bin/cli todo:sync`
 * repairs it — and it is reported here because this is what a session runs,
 * while the case that also holds it is in a suite the session that recorded the
 * feedback never runs.
 */
#[AsCommand(
    name: 'todo:check',
    description: 'hold every file to the head and the place that say what it is',
)]
final class TodoCheck
{
    /**
     * Every file says what it is by where it sits, and every todo says what it
     * answers for.
     */
    public function __invoke(OutputInterface $output): int
    {
        $problems = [];
        $reading = [];

        foreach (Todo::all() as $todo) {
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
            // Absence is a reading and a wrong word is a typo, so only the
            // second is a problem: a todo carrying no priority is one nobody
            // has judged, which is a state the order already has a place for.
            if ($todo['priority'] !== '' && !in_array($todo['priority'], Todo::PRIORITIES, true)) {
                $problems[] = $where . ' is ' . $todo['priority'] . ', and a priority is '
                    . implode(', ', Todo::PRIORITIES);
            }
            // The stamp is the second half of the order, so a todo in a stage
            // that has none sorts wherever the file system puts it — which is
            // the one failure here nothing else would report.
            if (in_array($todo['kind'], ['queue', 'progress', 'waiting'], true)
                && preg_match(Todo::STAMP, basename($where)) !== 1) {
                $problems[] = $where . ' is named by no date, so nothing says how long it has been waiting';
            }
            foreach ($todo['serves'] as $what) {
                $unreadable = Todo::unreadable($what);
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

            if ($todo['kind'] === 'progress') {
                // A claim locks everybody else out of one todo, so it owes the
                // two things that tell it from one nobody came back to: where
                // the work is, and when it was taken on.
                if ($todo['branch'] === '') {
                    $problems[] = $where . ' is in hand and does not say where — `**Branch:**` is where the work is';
                }
                if (strtotime($todo['claimed']) === false) {
                    $problems[] = $where . ' is in hand since '
                        . ($todo['claimed'] === '' ? 'never — `**Claimed:**` is what dates a claim' : $todo['claimed']);
                }
                continue;
            }
            if ($todo['branch'] !== '' || $todo['claimed'] !== '') {
                $problems[] = $where . ' carries a claim and is not in todo/progress/';
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
                if ($todo['every'] !== '') {
                    $problems[] = $where . ' is queued and recurs every ' . $todo['every']
                        . ' — what comes round belongs in todo/recurring/';
                }
                continue;
            }

            if ($todo['every'] === '') {
                $problems[] = $where . ' comes round and does not say how often';
            } elseif ($todo['every'] !== 'session' && preg_match('/^\d+ days?$/', $todo['every']) !== 1) {
                $problems[] = $where . ' recurs every ' . $todo['every'] . ', and a cadence is ' . Todo::CADENCE;
            } elseif ($todo['every'] !== 'session' && strtotime($todo['checked']) === false) {
                $problems[] = $where . ' recurs on a clock and was last checked '
                    . ($todo['checked'] === '' ? 'never — `**Checked:**` is what dates it' : $todo['checked']);
            }
        }

        // The readings `bin/cli todo:next` performs are the reason it can tell a
        // session there is nothing left to read: none and it silently stops
        // doing half its job, two and it does it twice.
        foreach (Todo::READINGS as $command) {
            $named = $reading[$command] ?? [];
            if ($named === []) {
                $problems[] = 'no todo runs `' . $command . '`';
            } elseif (count($named) > 1) {
                $problems[] = '`' . $command . '` is run by ' . implode(' and ', $named);
            }
        }

        // An open feedback nothing answers for is one no session will be handed,
        // and it is drift rather than a state: `bin/cli todo:sync` repairs it,
        // so this says how many and names the command the way
        // `bin/cli requirements:check` names the one that rebuilds a listing.
        // Left to the suite alone it would be found by whoever runs phpunit,
        // which is not the session that recorded the feedback.
        $unserved = count(array_filter(OpenFeedback::all(), static fn(array $feedback): bool => !$feedback['judged']));
        if ($unserved > 0) {
            $problems[] = sprintf('%d open feedback answered for by no todo — `bin/cli todo:sync`', $unserved);
        }

        $errors = Cli::errors($output);
        foreach ($problems as $problem) {
            $errors->writeln($problem);
        }
        $output->writeln(sprintf(
            '%d files, %d recurring, %d queued, %d in hand, %d waiting, %d problems',
            count(Todo::all()),
            count(Todo::recurring()),
            count(Todo::items()),
            count(Todo::progress()),
            count(Todo::waiting()),
            count($problems),
        ));

        return $problems === [] ? 0 : 1;
    }
}
