<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\OpenFeedback;

/**
 * One session's worth of feedback, oldest first, and what waits behind them.
 *
 * Listed rather than read out: what a feedback says is not what it is worth
 * today, and the todo that names this command says what is owed — run each
 * feedback's own query against the server as it is now. A feedback is evidence
 * about a version of this server that may no longer exist.
 *
 * Handed over a few at a time rather than all of it. The directory holds what
 * every session everywhere reported, and it grows on its own; the reading a
 * session owes it does not grow with it, or it stops being a reading and
 * becomes a backlog nobody starts.
 */
#[AsCommand(
    name: 'feedback:next',
    description: 'the ' . OpenFeedback::CHUNK . ' oldest feedback no todo has judged',
)]
final class FeedbackNext
{
    /**
     * Nonzero says there is something to do, which is how `bin/cli todo:next` knows
     * the todo that starts here is still the next thing. Not a failure, and not
     * the count of open feedback either: what is owed a feedback is the judgement, and
     * a feedback some todo already names has had it. Three feedback being worked off
     * in order are not three reasons to stop and read them again — a feedback
     * nobody has looked at is.
     *
     * Oldest first because the queue is a queue. Newest first is what a reader
     * wants and what `feedback:list` gives; a feedback that has waited a
     * fortnight while fresher ones kept arriving in front of it is what
     * oldest-first is for.
     *
     * Each is printed with its category, the model that left it and its own
     * first line, because the judgement is what is being reviewed, and a
     * filename alone cannot be disagreed with.
     */
    public function __invoke(OutputInterface $output): int
    {
        $unjudged = array_values(array_filter(OpenFeedback::all(), static fn(array $feedback): bool => !$feedback['judged']));
        if ($unjudged === []) {
            $output->writeln('Every open feedback has had its judgement.');

            return 0;
        }

        $chunk = array_slice($unjudged, 0, OpenFeedback::CHUNK);
        $output->writeln(sprintf('%d unjudged. These %d, oldest first:', count($unjudged), count($chunk)));
        foreach ($chunk as $feedback) {
            $output->writeln(sprintf("%s\n    %s · %s · %s", $feedback['file'], $feedback['category'], $feedback['model'], $feedback['title']));
        }
        if (count($unjudged) > count($chunk)) {
            $output->writeln(sprintf('%d wait behind them — `bin/cli feedback:list`.', count($unjudged) - count($chunk)));
        }

        return 1;
    }
}
