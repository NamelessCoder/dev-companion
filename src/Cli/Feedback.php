<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Feedback as Notes;
use Typo3CmsMcp\Todo;

/**
 * The notes that arrived from outside this repository and are still open.
 *
 * Listed rather than read out: what a note says is not what it is worth today,
 * and the todo that names this command says what is owed — run each note's own
 * query against the server as it is now. A note is evidence about a version of
 * this server that may no longer exist.
 *
 * It is a subject rather than something `bin/cli next` knows how to do because
 * a recurring todo is a heading, a cadence and a command, and a command only
 * that one caller can run makes it a special case again.
 */
final class Feedback implements Subject
{
    public static function about(): string
    {
        return 'the notes sessions elsewhere left behind';
    }

    public static function commands(): array
    {
        return [
            'list' => ['', 'every open note, newest first', self::list(...)],
        ];
    }

    /**
     * Nonzero says there is something to do, which is how `bin/cli next` knows
     * the todo that starts here is still the next thing. Not a failure, and not
     * the count of open notes either: what is owed a note is the judgement, and
     * a note some todo already names has had it. Three notes being worked off
     * in order are not three reasons to stop and read them again — a note
     * nobody has looked at is.
     */
    private static function list(): int
    {
        $notes = Notes::notes('open', null, 100);
        if ($notes === []) {
            print "No open notes.\n";

            return 0;
        }

        $queued = Todo::serves();
        $unjudged = 0;
        printf("%d open.\n", count($notes));
        foreach ($notes as $note) {
            $named = in_array($note['file'], $queued, true);
            $unjudged += $named ? 0 : 1;
            printf("%s%s\n", $note['file'], $named ? '' : ' — no todo names it');
        }

        return $unjudged === 0 ? 0 : 1;
    }
}
