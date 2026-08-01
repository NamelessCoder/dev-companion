<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Feedback;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\ToolResult;

/**
 * This server's only write: one markdown feedback per call, in its own
 * checkout, never touching an existing one.
 */
final class FeedbackRecord implements Tool
{
    public static function name(): string
    {
        return 'typo3_feedback_record';
    }

    public static function description(): string
    {
        return 'Leave feedback about a gap, wrong answer, or missing capability of this knowledge server — and about what it did well, because what worked is what must not be broken later. The feedback is stored as markdown in this server\'s own checkout — not in the project you are working in, so do not look for the file there — and is read back with typo3_feedback_list. Use it whenever an answer was incomplete or a lookup found nothing that should have been there. One feedback per subject: a feedback carrying three complaints is worked off three times over or not at all.';
    }

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => false,
            'destructiveHint' => false,
            'idempotentHint' => false,
            'openWorldHint' => false,
        ];
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'observation' => ['type' => 'string', 'minLength' => 1, 'description' => 'What was missing, wrong, or unhelpful. Be specific enough to act on later, and open with one line naming the task you were given, so the feedback can be traced back to what exposed it. Written in English, like everything else here.'],
                'model' => ['type' => 'string', 'minLength' => 1, 'description' => 'The model recording this feedback, as it identifies itself, for example claude-opus-5 or gpt-5.3-codex. Read it where it is written down — what your client reports for the current session, or the person running you — rather than from what you remember about yourself. A feedback about what a session did or did not do is evidence about one model\'s behaviour, and one filed as "unknown" cannot be told apart from another model\'s. That fallback is for a session that looked and could not find out; an invented identifier is still worse than none.'],
                'category' => ['type' => 'string', 'enum' => Feedback::CATEGORIES, 'default' => 'idea', 'description' => 'missing-knowledge: the knowledge base lacks the answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the need. bug: the server misbehaved. idea: anything else.'],
                'tool' => ['type' => ['string', 'array'], 'items' => ['type' => 'string'], 'description' => 'The tool the observation is about, for example typo3_component_lookup, or the skill it activated, for example typo3-extension-conformance. Several may be named, as a list or separated by commas.'],
                'query' => ['type' => 'string', 'description' => 'The arguments that produced the unsatisfying result, or the task text where a whole session is what produced it. This is what lets somebody re-run the feedback against a later version of the server instead of reading it.'],
                'suggestion' => ['type' => 'string', 'description' => 'What the server should have answered or should be able to do instead.'],
            ],
            'required' => ['observation', 'model'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'file' => Schema::string('Path of the recorded feedback, relative to this server\'s own checkout.'),
            'path' => Schema::string('The same feedback as an absolute path. It is in the server\'s checkout, not in the project the feedback was recorded from.'),
        ], ['file', 'path']);
    }

    public static function answer(array $args): ToolResult
    {
        $file = Feedback::record($args);
        // The absolute path, because the relative one is relative to somewhere
        // the caller has never been. A feedback recorded from a site package was
        // reported back as feedback/<name>.md, looked for under that project,
        // not found, and written off as a failed write — the file was there the
        // whole time, one checkout over.
        $path = Paths::root() . '/' . $file;

        return ToolResult::create(
            sprintf(
                "Thanks — noted in %s.\n\nThat is this server's own checkout, not the project you are working in: "
                . "nothing was written there, so the file will not be found under it.\n\n"
                . 'It will be picked up when the knowledge base is next improved; '
                . 'nothing about the current answer changes.',
                $path,
            ),
            ['file' => $file, 'path' => $path],
        );
    }
}
