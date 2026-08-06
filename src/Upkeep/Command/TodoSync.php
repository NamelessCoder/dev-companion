<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\OpenFeedback;

/**
 * A card on the board for every open feedback that has none.
 *
 * The feedback arrive from every session everywhere and this repository has one
 * board. What used to bridge the two was a sighting that ran only once the
 * queue was empty, which meant the pile was visible exactly when there was
 * nothing else to do; what bridges them now is a card per feedback, sitting in
 * the queue with everything else and carrying the lowest priority there is,
 * because nobody has yet said it is worth more.
 *
 * The card points and does not copy. `**Serves:**` names the file, the heading
 * is the feedback's own so that a listing says which one it is, and the step is
 * the same on every card because it is the same step: judge it. What the
 * feedback reports is read in the feedback, where it cannot drift away from a
 * second copy of itself.
 *
 * Which feedback already has one is not tracked here either. `Todo::serves()`
 * reads the queue, what is in hand and what waits, so a card in any of the
 * three is what marks a feedback as taken on — the same relation
 * `typo3_feedback_list` has always shown, and one place rather than two.
 */
#[AsCommand(
    name: 'todo:sync',
    description: 'write a todo for every open feedback that has none',
)]
final class TodoSync
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
    private const UNJUDGED = 'low';

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
        search afterwards. `documentation/feedback/judging.md` is the ladder and the one
        question it opens with, and what this feedback actually says is in the file it
        serves rather than here.
        TEXT;

    public function __invoke(OutputInterface $output): int
    {
        $written = 0;
        foreach (OpenFeedback::all() as $feedback) {
            if ($feedback['judged']) {
                continue;
            }

            // The feedback's own name is already a stamp and a slug, which is
            // what a card is named by — so the two are visibly one pair, and
            // the card's age is when the report arrived rather than when
            // somebody got round to writing it down.
            $path = 'todo/open/' . basename($feedback['file']);
            $file = Paths::root() . '/' . $path;

            // The queue is a directory git does not keep when it is empty, so a
            // run that empties it takes it with it and the next card has
            // nowhere to land. Reported written and not written is the worst of
            // the three outcomes: the hook stages nothing, says nothing, and
            // the missing card surfaces in CI as somebody else's failure.
            $directory = dirname($file);
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Cannot create the queue directory: %s', $directory));
            }

            $bytes = file_put_contents(
                $file,
                sprintf(
                    "# %s\n\n**Serves:** %s\n**Priority:** %s\n\n%s\n",
                    $feedback['title'],
                    $feedback['file'],
                    self::UNJUDGED,
                    self::STEP,
                ),
            );
            if ($bytes === false) {
                throw new \RuntimeException(sprintf('Cannot write the card: %s', $file));
            }
            $output->writeln($path);
            ++$written;
        }

        $output->writeln($written === 0
            ? 'Every open feedback has a todo.'
            : sprintf('%d written. What is on the board and unjudged is what nobody has decided about yet.', $written));

        return 0;
    }
}
