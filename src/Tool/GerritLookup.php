<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * Whether a core patch already exists, read from the review server.
 *
 * The question is asked before every core task and answerable from no checkout,
 * and the sessions that asked it paid a round trip for the search and another
 * for the XSSI prefix the response opens with. This is one call
 * (`D-FBK-027`).
 */
final class GerritLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_gerrit_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Find out whether a TYPO3 core patch already exists, from the review server at review.typo3.org. Pass issue with a Forge issue number to search the commit messages of every change for it — the question "has somebody already fixed this" — or change with the Change-Id from a commit message, or the change number a review URL ends with, to read the one it names. Answers with the change number, subject, status, target branch, review URL, and the patch set that is current on the server with the commit it is — which is what says whether a checkout is the revision under review. A call carries issue or change, never both. This reaches the network, and it reads: reviewing, voting and uploading stay yours.';
    }

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => true,
            'destructiveHint' => false,
            'idempotentHint' => true,
            'openWorldHint' => true,
        ];
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'issue' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Forge issue number, with or without the leading #, for example "105403". Searches every change whose commit message names it, which is where Resolves: and Related: put it. A call carries issue or change, never both.',
                ],
                'change' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'One change to read, named either by the Change-Id its commit message carries, for example "I0f4c5b9a3e2d1c7b8a6f5e4d3c2b1a0f9e8d7c6b", or by the change number a review URL ends with, for example "89011". Prefer the Change-Id where the commit is in front of you: it is part of the patch being read, it survives being amended into a new patch set, and it cannot be mistaken for the Forge issue number the way a bare change number can. A call carries issue or change, never both.',
                ],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 25, 'default' => 10],
            ],
            'oneOf' => [
                ['required' => ['issue']],
                ['required' => ['change']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']],
            'source' => Schema::string('The review server the answer came from.'),
            'query' => Schema::string('The Gerrit query this was answered with, so the same question can be asked again by hand.'),
            'changes' => Schema::listOf(Schema::object([
                'number' => Schema::integer('Change number, the digits its review URL ends with.'),
                'subject' => Schema::string('The commit subject.'),
                'status' => Schema::string('NEW while it is open, MERGED once it landed, ABANDONED when it was given up.'),
                'branch' => Schema::string('The branch the change targets.'),
                'patchSet' => Schema::integer('The patch set that is current on the server, counting from 1. Zero where the server named none.'),
                'commit' => Schema::string('The commit the current patch set is. A checkout whose HEAD is another commit is not the revision under review.'),
                'project' => Schema::string('The Gerrit project it was pushed to.'),
                'updated' => Schema::string('When the change last moved.'),
                'url' => Schema::string('Where a person reads the review.'),
            ]), 'The changes that matched, newest activity first.'),
            'unavailable' => [
                'type' => ['object', 'null'],
                'description' => 'Why nothing was answered, where status says unavailable. Null otherwise.',
                'properties' => [
                    'cause' => [
                        'type' => 'string',
                        'enum' => ['source-not-answering', 'source-not-parseable'],
                        'description' => 'source-not-answering: review.typo3.org did not answer this time, and the '
                            . 'same call may answer the next. source-not-parseable: something answered and it was '
                            . 'not the review API, which is what a proxy or a captive portal looks like from here.',
                    ],
                    'reason' => Schema::string(),
                ],
                'required' => ['cause', 'reason'],
            ],
            // Not required, unlike `unavailable` beside it, and the difference
            // is what each one is. A caller branches on `unavailable`, so it is
            // always there to be read; this is a qualification on an answer
            // that is otherwise complete, so absent and null say the same
            // thing and an older recording stays a valid answer.
            'indistinguishable' => [
                'type' => ['string', 'null'],
                'description' => 'Present where the answer is empty and cannot be read as an absence. This server '
                    . 'reads the review server without credentials, so a change that is private or work in '
                    . 'progress is invisible to it and looks exactly like one nobody pushed. Null where empty '
                    . 'really does mean nothing matched.',
            ],
        ], ['status', 'source', 'query', 'changes', 'unavailable']);
    }

    /**
     * Which of the two forms `change` was given in.
     *
     * A Change-Id is the `I` and forty hex digits a commit message carries; a
     * change number is the digits a review URL ends with. Saying "no change
     * with this number" back to a caller that passed a Change-Id is wrong
     * twice over, and one review read it as its own commit never having been
     * pushed (`feedback/2026-08-07-132416`).
     */
    private static function isChangeId(string $change): bool
    {
        return preg_match('/^I[0-9a-f]{40}$/i', $change) === 1;
    }

    /**
     * What an empty answer cannot separate, where it cannot separate anything.
     *
     * A caller that named one change has named something it read somewhere — a
     * Change-Id out of a commit message in front of it, a number off a review
     * URL. An empty answer there is a restricted change at least as often as an
     * absent one, and this server cannot tell which: it reads Gerrit
     * anonymously, so private and work-in-progress changes are invisible to it
     * (`R-ANS-027`).
     *
     * An issue search is not the same case and gets none of this. "No change
     * names this issue" is a claim about a query rather than about a record
     * somebody is holding, and its own sentence already carries the caveat.
     *
     * Separated from `answer()` so it can be held without a review server: this
     * is the whole of the judgement and it is a function of two strings.
     */
    public static function indistinguishable(string $status, string $change): ?string
    {
        if ($status !== 'empty' || trim($change) === '') {
            return null;
        }

        return 'This server reads ' . Gerrit::HOST . ' without credentials. A change that is private or work in '
            . 'progress is invisible to it and answers exactly like one that does not exist, so this is either '
            . '"no such change" or "not one an anonymous reader may see", and nothing here separates them. Where '
            . 'the id came from a commit you have, the second is the more likely of the two.';
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $change = is_string($args['change'] ?? null) ? trim($args['change']) : '';
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 10;

        $gerrit = new Gerrit();
        $answer = $issue !== ''
            ? $gerrit->changesForIssue($issue, $limit)
            : $gerrit->change($change, $limit);

        $indistinguishable = self::indistinguishable($answer['status'], $change);

        $data = [
            'status' => $answer['status'],
            'source' => Gerrit::HOST,
            'query' => $answer['query'],
            'changes' => $answer['changes'],
            'indistinguishable' => $indistinguishable,
            'unavailable' => $answer['cause'] === null ? null : [
                'cause' => $answer['cause'],
                'reason' => $answer['cause'] === 'source-not-answering'
                    ? 'The review server did not answer. It is reachable at ' . Gerrit::HOST . ' in a browser; nothing here can answer this question offline.'
                    : 'The host answered with something that is not the review API, which is what a proxy or a login page looks like from here.',
            ],
        ];

        $lines = ['TYPO3 core review server: ' . Gerrit::HOST, 'Query: ' . $answer['query']];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $data['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = $issue !== ''
                ? 'No change names this issue in its commit message. This reads the review server anonymously, so a change pushed as private is invisible here — the answer is that nothing public exists, not that nobody has fixed it.'
                : 'No change an anonymous reader may see matches this ' . (self::isChangeId($change) ? 'Change-Id' : 'change number') . '.';
            if ($indistinguishable !== null) {
                $lines[] = $indistinguishable;
            }
            if ($answer['dropped'] > 0) {
                $lines[] = self::held($answer['dropped']);
            }
        } else {
            $named = false;
            foreach ($answer['changes'] as $entry) {
                $lines[] = '';
                $lines[] = sprintf('## %s (%s)', $entry['subject'], $entry['status']);
                $lines[] = sprintf('Change %d · %s · %s', $entry['number'], $entry['branch'], $entry['url']);
                if ($entry['patchSet'] > 0) {
                    $named = $named || $entry['commit'] !== '';
                    $lines[] = $entry['commit'] === ''
                        ? sprintf('Patch set %d', $entry['patchSet'])
                        : sprintf('Patch set %d · %s', $entry['patchSet'], $entry['commit']);
                }
                if ($entry['updated'] !== '') {
                    $lines[] = 'Last moved: ' . $entry['updated'];
                }
            }
            // The one thing this answer knows and the checkout does not: which
            // revision the review is of. Nothing here can read a local `HEAD`,
            // so the comparison is the caller's and the sentence is what says
            // there is one to make.
            if ($named) {
                $lines[] = '';
                $lines[] = 'Hold the commit against `git rev-parse HEAD` in the checkout. Where the two '
                    . 'differ, the checkout is not the revision under review, and a review says which of '
                    . 'the two it read.';
            }
            if ($answer['dropped'] > 0) {
                $lines[] = '';
                $lines[] = self::held($answer['dropped']);
            }
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * What the same query answers by hand and this one does not.
     *
     * The `query` field is there so the question can be asked again outside
     * this server, and a hand-run one comes back with more than this — so what
     * was held back is said, rather than left as a difference the caller finds
     * and reads as this answer being short.
     */
    private static function held(int $dropped): string
    {
        return sprintf(
            '%d change%s the review server matched by its own change number rather than by its commit message %s '
                . 'held back. The number a query carries is indexed both ways there, so a search for an issue '
                . 'answers with the change of the same number whatever it is about.',
            $dropped,
            $dropped === 1 ? '' : 's',
            $dropped === 1 ? 'was' : 'were',
        );
    }
}
