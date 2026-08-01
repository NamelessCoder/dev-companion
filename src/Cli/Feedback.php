<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Cli;

use Typo3CmsMcp\Cli;
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
 * Handed over a few at a time rather than all of it. The directory holds what
 * every session everywhere reported, and it grows on its own; the reading a
 * session owes it does not grow with it, or it stops being a reading and
 * becomes a backlog nobody starts.
 *
 * It is a subject rather than something `bin/cli next` knows how to do because
 * a recurring todo is a heading, a cadence and a command, and a command only
 * that one caller can run makes it a special case again.
 */
final class Feedback implements Subject
{
    /**
     * How many notes one session is handed. The number is small for two
     * reasons, and neither is the size of the directory: five queries can be
     * re-run in a session that also has work of its own, and five judgements
     * can be read by whoever disagrees with one before the commit is made. A
     * listing of fifty-six is neither — it is a session that reads for ten
     * minutes, closes what is easiest, and leaves nobody able to say whether
     * the choice was right.
     */
    private const CHUNK = 5;

    /** Every note there is: the size of the directory is what this is about. */
    private const ALL = PHP_INT_MAX;

    public static function about(): string
    {
        return 'the notes sessions elsewhere left behind';
    }

    public static function commands(): array
    {
        return [
            'next' => ['', 'the ' . self::CHUNK . ' oldest notes no todo has judged', self::next(...)],
            'list' => ['', 'every open note, newest first', self::list(...)],
            'archive' => ['<note>...', 'move the notes a change worked off into feedback/archive/', self::archive(...)],
        ];
    }

    /**
     * One session's worth of notes, oldest first, and what waits behind them.
     *
     * Nonzero says there is something to do, which is how `bin/cli next` knows
     * the todo that starts here is still the next thing. Not a failure, and not
     * the count of open notes either: what is owed a note is the judgement, and
     * a note some todo already names has had it. Three notes being worked off
     * in order are not three reasons to stop and read them again — a note
     * nobody has looked at is.
     *
     * Oldest first because the queue is a queue. Newest first is what a reader
     * wants and what `list` gives; a note that has waited a fortnight while
     * fresher ones kept arriving in front of it is what oldest-first is for.
     *
     * Each is printed with its category, the model that left it and its own
     * first line, because the judgement is what is being reviewed, and a
     * filename alone cannot be disagreed with.
     */
    private static function next(): int
    {
        $unjudged = array_values(array_filter(self::open(), static fn(array $note): bool => !$note['judged']));
        if ($unjudged === []) {
            print "Every open note has had its judgement.\n";

            return 0;
        }

        $chunk = array_slice($unjudged, 0, self::CHUNK);
        printf("%d unjudged. These %d, oldest first:\n", count($unjudged), count($chunk));
        foreach ($chunk as $note) {
            printf("%s\n    %s · %s · %s\n", $note['file'], $note['category'], $note['model'], self::first($note['title']));
        }
        if (count($unjudged) > count($chunk)) {
            printf("%d wait behind them — `bin/cli feedback list`.\n", count($unjudged) - count($chunk));
        }

        return 1;
    }

    /**
     * The overview `next` deliberately does not give, for whoever wants it.
     */
    private static function list(): int
    {
        $notes = self::open();
        if ($notes === []) {
            print "No open notes.\n";

            return 0;
        }

        printf("%d open, newest first.\n", count($notes));
        foreach (array_reverse($notes) as $note) {
            printf("%s%s\n", $note['file'], $note['judged'] ? '' : ' — no todo names it');
        }

        return 0;
    }

    /**
     * Every open note, oldest first, with whether a todo already names it —
     * which is the whole difference between a note that is waiting and one that
     * somebody has taken on.
     *
     * @return array<int, array{file: string, category: string, model: string, title: string, judged: bool, ...}>
     */
    private static function open(): array
    {
        $queued = Todo::serves();

        return array_map(
            static fn(array $note): array => $note + ['judged' => in_array($note['file'], $queued, true)],
            array_reverse(Notes::notes('open', null, self::ALL)),
        );
    }

    /** A note's own first line, cut where a terminal would wrap it anyway. */
    private static function first(string $title): string
    {
        return mb_strlen($title) > 72 ? mb_substr($title, 0, 71) . '…' : $title;
    }

    /**
     * Closing a note, which is moving it.
     *
     * In the same commit as the improvement it asked for, so that commit is
     * both what answers the note and what says so. Several notes closed by one
     * change are named in one call, because they are one commit.
     *
     * @param array<int, string> $arguments
     */
    private static function archive(array $arguments): int
    {
        if ($arguments === []) {
            return Cli::usage(self::class, 'archive');
        }

        foreach ($arguments as $note) {
            try {
                printf("%s\n", Notes::archive($note));
            } catch (\InvalidArgumentException|\RuntimeException $exception) {
                fwrite(STDERR, $exception->getMessage() . "\n");

                return 1;
            }
        }

        return 0;
    }
}
