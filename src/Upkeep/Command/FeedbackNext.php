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
 * One at a time rather than all of it, and rather than a handful. The directory
 * holds what every session everywhere reported and it grows on its own; the
 * reading a session owes it does not grow with it, or it stops being a reading
 * and becomes a backlog nobody starts. What a portion of several bought was
 * that the judgements could be read together by somebody who did not make them,
 * and that is now what `decisions/` carries — the entry a judgement was made
 * against is updated, or a new one is written where nothing says it yet.
 */
#[AsCommand(
    name: 'feedback:next',
    description: 'the oldest feedback no todo has judged',
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
     * It is printed with its category, the model that left it and its own first
     * line, because the judgement is what is being reviewed, and a filename
     * alone cannot be disagreed with.
     */
    public function __invoke(OutputInterface $output): int
    {
        $unjudged = array_values(array_filter(OpenFeedback::all(), static fn(array $feedback): bool => !$feedback['judged']));
        if ($unjudged === []) {
            $output->writeln('Every open feedback has had its judgement.');

            return 0;
        }

        $feedback = $unjudged[0];
        $output->writeln(sprintf('%d unjudged. The oldest:', count($unjudged)));
        $output->writeln(sprintf("%s\n    %s · %s · %s", $feedback['file'], $feedback['category'], $feedback['model'], $feedback['title']));
        if (count($unjudged) > 1) {
            $output->writeln(sprintf('%d wait behind it — `bin/cli feedback:list`.', count($unjudged) - 1));
        }

        return 1;
    }
}
