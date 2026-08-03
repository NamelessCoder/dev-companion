<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

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
        return 'Leave feedback about a gap, wrong answer, or missing capability of this knowledge server — and about what it did well, because what worked is what must not be broken later. Stored as markdown in this server\'s own checkout and read back with typo3_feedback_list, not in the project you are working in, so do not look for the file there. One feedback per subject: a feedback carrying three complaints is worked off three times over or not at all.';
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
                'observation' => ['type' => 'string', 'minLength' => 1, 'description' => 'What was missing, wrong, or unhelpful, specific enough to act on later, in English. Open with one line naming the task you were given, so the feedback can be traced back to what exposed it. A finding is the path a value was read at, the shape of what came back, and where it came from — never the value itself where the installation keeps it secret: an encryption key, a password, a token, the credentials in a connection string. This is committed and pushed into a checkout that installation\'s owner is not watching, so a secret pasted here as proof has left it for good. The finding is "the key at SYS/encryptionKey is the active one, hardcoded in config/system/settings.php"; the 96 characters after it establish nothing further.'],
                'model' => ['type' => 'string', 'minLength' => 1, 'description' => 'The model recording this feedback, as it identifies itself, for example claude-opus-5 or gpt-5.3-codex. Read it where it is written down — what your client reports for the current session, or the person running you — rather than from what you remember about yourself: a feedback is evidence about one model\'s behaviour, and one filed as "unknown" cannot be told apart from another model\'s. That fallback is for a session that looked and could not find out; an invented identifier is worse than none.'],
                'category' => ['type' => 'string', 'enum' => Channel::CATEGORIES, 'default' => 'idea', 'description' => 'missing-knowledge: the knowledge base lacks the answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the need. bug: the server misbehaved. idea: anything else.'],
                // One declared type, not the string-or-array it was. That union
                // was the only one in any input schema this server offers, and
                // the one model that has ever recorded feedback without naming a
                // tool is the one that reported being unable to send the
                // argument at all — D-ANS-017. The several still travel: the
                // recorder splits on commas and spaces, so nothing about what a
                // feedback can say was given up, and a client that sends a list
                // anyway is told which type was expected before the tool runs.
                'tool' => ['type' => 'string', 'description' => 'The tool the observation is about, for example typo3_component_lookup, or the skill it activated, for example typo3-extension-conformance. Several are named in one string, separated by commas.'],
                'query' => ['type' => 'string', 'description' => 'The arguments that produced the unsatisfying result, or the task text where a whole session is what produced it, so somebody can re-run the feedback against a later version of the server instead of reading it. The rule from observation holds: the arguments and the path they named, never a value the installation keeps secret. A re-run needs to know that SYS/encryptionKey was asked for and that a key came back, not what the key was; a password or a token that was itself an argument is named rather than quoted.'],
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
            'redacted' => Schema::listOf(
                Schema::string(),
                'What was removed before the feedback was written, one entry per value, naming the field it stood in and the shape it had. Empty where nothing was removed, which is the ordinary case. Each removal stands in the file as a [redacted: ...] marker, so the report says of itself that it was altered.',
            ),
        ], ['file', 'path', 'redacted']);
    }

    public static function answer(array $args): ToolResult
    {
        $redacted = [];
        $file = Channel::record($args, $redacted);
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
            ) . self::redactionNotice($redacted),
            ['file' => $file, 'path' => $path, 'redacted' => $redacted],
        );
    }

    /**
     * What was taken out of the feedback, said back to whoever wrote it.
     *
     * A report that was altered says so, or it stops being a report — the same
     * reason the marker in the file is visible rather than a silent
     * substitution. It is also the only moment the value can still be discussed:
     * the session is standing in the installation the value came from and knows
     * what it was, and a reader of the archive three weeks later does not.
     *
     * @param array<int, string> $redacted
     */
    private static function redactionNotice(array $redacted): string
    {
        if ($redacted === []) {
            return '';
        }

        return "\n\n" . sprintf(
            '%s taken out before it was written — %s. Each stands in the file as a `[redacted: ...]` marker, '
            . 'so a reader can see that something was removed and come and ask. This checkout is committed and '
            . 'pushed, and a value the installation keeps secret would leave it that way: what a finding needs is '
            . 'the path and the shape of the value, never the value. Everything else was stored as you wrote it.',
            count($redacted) === 1 ? 'One value was' : sprintf('%d values were', count($redacted)),
            implode('; ', $redacted),
        );
    }
}
