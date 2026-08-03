<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Checkouts;
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
 * The last two things it says are about the other direction, where the fault is
 * in the relation between a feedback and the todos rather than in one file: an
 * open feedback that no todo answers for, and a card still asking for a
 * judgement another todo has already been given. Both are drift rather than a
 * state — the first is repaired by `bin/cli todo:sync` and the second by the
 * deletion it names — and both are reported here because this is what a session
 * runs, while the cases that also hold them are in a suite the session that
 * recorded the feedback never runs.
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
            if ($todo['priority'] !== '' && !in_array($todo['priority'], Todo::PRIORITIES, true)) {
                $problems[] = $where . ' is ' . $todo['priority'] . ', and a priority is '
                    . implode(', ', Todo::PRIORITIES);
            }
            // A todo in a stage carries a priority and one that recurs does not.
            // The clock is what orders an appointment, and a word beside it
            // would be a second answer to the same question — while a stage
            // without one is a file that says nothing about where it stands,
            // which is exactly what could not be reported while absence meant
            // something.
            if (in_array($todo['kind'], ['queue', 'progress', 'waiting'], true)) {
                if ($todo['priority'] === '') {
                    $problems[] = $where . ' carries no `**Priority:**`, so nothing says where it stands';
                }
                // The stamp is the second half of the order, so a todo in a
                // stage that has none sorts wherever the file system puts it.
                if (preg_match(Todo::STAMP, basename($where)) !== 1) {
                    $problems[] = $where . ' is named by no date, so nothing says how long it has been waiting';
                }
            } elseif ($todo['priority'] !== '') {
                $problems[] = $where . ' recurs and carries a priority, where the cadence is what orders it';
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
                // A claim is right for as long as its branch is. The session
                // that ended on a question leaves one here on purpose, because
                // the branch still holds the half that is done — and the moment
                // that branch is merged and deleted the same file becomes a
                // lock on a todo with nothing behind it. Nothing noticed that
                // before: the field was checked for being written, never for
                // naming something that exists.
                if ($todo['branch'] !== '' && !self::branchExists($todo['branch'])) {
                    $problems[] = $where . ' is in hand on ' . $todo['branch']
                        . ', which is gone — `bin/cli todo:release ' . basename($where) . '`';
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

        // And the same relation from above. `todo:sync` never writes a second
        // card, so the pair only ever arrives the other way round: a judgement
        // folds a feedback onto another todo's `Serves:` line and the card it
        // already had keeps asking for the judgement that has just been made.
        // Nothing repairs this one — the fold deletes the card, and what is
        // named here is that deletion.
        foreach (Todo::folded() as $pair) {
            $problems[] = $pair['card'] . ' still asks for the judgement of ' . $pair['feedback']
                . ', which is already served by ' . implode(' and ', $pair['judged'])
                . ' — delete the card the judgement replaced';
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

    /**
     * Whether the branch a claim names is still there.
     *
     * Asked of git rather than of the worktree list, because a branch outlives
     * the worktree it was checked out in and it is the branch that carries the
     * work. `--quiet` keeps a missing one off the error stream, where it would
     * read as this command failing.
     */
    private static function branchExists(string $branch): bool
    {
        [$exitCode] = Checkouts::run([
            'git', '-C', Paths::root(), 'rev-parse', '--verify', '--quiet', 'refs/heads/' . $branch,
        ]);

        return $exitCode === 0;
    }
}
