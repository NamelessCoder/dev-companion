<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Feedback;

/**
 * The card a feedback brings with it: one todo in the queue, asking for the
 * judgement nobody has made yet.
 *
 * It is written when the feedback is recorded, so the pair is never apart. What
 * wrote it before was a command run from a pre-commit hook, which meant the
 * board was right on the checkout that committed and nowhere else — a session
 * that recorded a feedback and went on working had its report on disk and
 * nothing on the board until somebody else committed here. Both the command and
 * the hook are gone: the card is written where the feedback is, and there is
 * nothing left to run afterwards (`D-FBK-045`).
 *
 * It lives here rather than in `Upkeep/` because `Upkeep/` already reads this
 * channel, and the card written from there would make that a cycle. What the
 * queue is stays `Upkeep\Todo`'s; this is one file a feedback writes beside
 * itself, in the checkout it was stored in.
 *
 * The card points and does not copy. `**Serves:**` names the file, the heading
 * is the feedback's own so that a listing says which one it is, and the step is
 * the same on every card because it is the same step: judge it. What the
 * feedback reports is read in the feedback, where it cannot drift away from a
 * second copy of itself.
 */
final class Card
{
    /**
     * What a card is worth before anybody has said so.
     *
     * The lowest of the three rather than no priority at all: every todo in a
     * stage carries one, so that a forgotten priority can be reported, and the
     * word has to say the same thing absence used to — below everything
     * somebody has decided about. Raising it is a judgement, which is the work
     * the card asks for.
     */
    public const UNJUDGED = 'low';

    /**
     * The step every card carries, because judging one feedback is the same
     * work whichever it is. What differs is the heading and what it serves.
     *
     * Read from outside as well as written from here, which is what makes a
     * card that still asks for a judgement findable: `Todo::folded()` compares
     * a body against this constant rather than against a phrase somebody would
     * have to keep in step with it — `D-FBK-040`.
     */
    public const STEP = <<<'TEXT'
        Judge this feedback rather than fix what it reports: re-run the query that
        produced it against the server as it is now, then close it, trim it to the half
        that is still open, or write the todo that takes it on. Write the judgement into
        `decisions/` — the entry it was made against, or a new one where nothing says it
        yet — because the commit that closes a feedback is the one place nobody can
        search afterwards. `documentation/records/judging.rst` is the ladder and the one
        question it opens with, and what this feedback actually says is in the file it
        serves rather than here.
        TEXT;

    /**
     * Where the card for one feedback sits, relative to the checkout both are
     * in. The feedback is named the way it names itself — `feedback/<name>.md`.
     *
     * The feedback's own name is already a stamp and a slug, which is what a
     * card is named by — so the two are visibly one pair, and the card's age is
     * when the report arrived rather than when somebody got round to writing it
     * down.
     */
    public static function path(string $feedback): string
    {
        return 'todo/open/' . basename($feedback);
    }

    /**
     * Writes the card for one feedback, and returns the path it now has,
     * relative to the checkout the feedback was stored in.
     *
     * That checkout rather than `Paths::root()`, for the reason `Channel::git()`
     * states: the two are the same directory in an installation and differ where
     * a test writes into a store of its own, and a card written beside a
     * feedback belongs where the feedback is — `R-COD-003`.
     */
    public static function write(string $feedback, string $title): string
    {
        $path = self::path($feedback);
        $file = Channel::root() . '/' . $path;

        // The queue is a directory git does not keep when it is empty, so a
        // run that empties it takes it with it and the next card has nowhere to
        // land.
        $directory = dirname($file);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create the queue directory: %s', $directory));
        }

        $written = file_put_contents(
            $file,
            sprintf(
                "# %s\n\n**Serves:** %s\n**Priority:** %s\n\n%s\n",
                $title,
                $feedback,
                self::UNJUDGED,
                self::STEP,
            ),
        );
        if ($written === false) {
            throw new \RuntimeException(sprintf('Cannot write the card: %s', $file));
        }

        return $path;
    }
}
