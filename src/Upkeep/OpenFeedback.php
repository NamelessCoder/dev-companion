<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Feedback\Channel;

/**
 * The feedback that arrived from outside this repository and are still open,
 * each with whether a todo already names it.
 *
 * That flag is the whole difference between a feedback that is waiting and one
 * somebody has taken on, and it is what `feedback:next` counts down and
 * `feedback:list` marks. It is read here rather than in either of them, because
 * a list and a chunk of the same list disagreeing about what is judged would be
 * a difference nobody could see.
 */
final class OpenFeedback
{
    /** Every feedback there is: the size of the directory is what this is about. */
    private const ALL = PHP_INT_MAX;

    /**
     * Every open feedback, oldest first.
     *
     * @return array<int, array{file: string, category: string, model: string, title: string, judged: bool, ...}>
     */
    public static function all(): array
    {
        $queued = Todo::serves();

        return array_map(
            static fn(array $feedback): array => $feedback + ['judged' => in_array($feedback['file'], $queued, true)],
            array_reverse(Channel::all('open', null, self::ALL)),
        );
    }
}
