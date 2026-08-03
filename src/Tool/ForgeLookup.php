<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Contribution\Forge;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * What a Forge issue actually says, including the part that decides it.
 *
 * Four round trips and a trap by hand: the request refused, a browser-shaped
 * one answered 200 with a challenge page, then JSON whose decision sits in the
 * journal rather than in the description (`D-FBK-027`).
 */
final class ForgeLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_forge_lookup';
    }

    public static function description(): string
    {
        return 'Read a TYPO3 issue from the tracker at forge.typo3.org before writing a patch for it: subject, tracker, status, target version, the TYPO3 and PHP versions it was reported against, related issues, and the comments — where a maintainer who closed or reassigned it said why, which the description never says. An issue that does not exist is answered as such, and so is a tracker that could not be reached. Reading only, and no credential: commenting, assigning and closing stay yours.';
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
                    'description' => 'Forge issue number, with or without the leading #, for example "110348".',
                ],
            ],
            'required' => ['issue'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']],
            'source' => Schema::string('The tracker the answer came from.'),
            'url' => Schema::string('What was read, so the same question can be asked again by hand.'),
            'issue' => [
                'type' => ['object', 'null'],
                'description' => 'The issue, where status says answered. Null otherwise.',
                'properties' => [
                    'id' => Schema::integer(),
                    'subject' => Schema::string(),
                    'status' => Schema::string('New, Accepted, Resolved, Closed, Rejected — the tracker\'s own word.'),
                    'tracker' => Schema::string('Bug, Feature, Task, Epic.'),
                    'priority' => Schema::string(),
                    'targetVersion' => Schema::string('The release it is scheduled for, empty where none is set.'),
                    'typo3Version' => Schema::string('The TYPO3 version it was reported against, which is not the version it still reproduces on.'),
                    'phpVersion' => Schema::string(),
                    'createdOn' => Schema::string(),
                    'updatedOn' => Schema::string(),
                    'url' => Schema::string('Where a person reads it.'),
                    'description' => Schema::string('The report as it was written, which is what the reporter saw and not what was decided.'),
                    'relations' => Schema::listOf(Schema::object([
                        'issue' => Schema::integer('The other issue.'),
                        'relation' => Schema::string('duplicates, relates, blocked, precedes.'),
                    ], ['issue', 'relation']), 'Issues this one is filed against, which is where a duplicate or a blocker is named.'),
                    'noteCount' => Schema::integer('How many comments the issue carries in total.'),
                    'notes' => Schema::listOf(Schema::object([
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'note' => Schema::string(),
                    ], ['author', 'on', 'note']), 'The most recent comments, oldest first. A closure, a reassignment and a "we will not do this" are here rather than in the description.'),
                ],
                'required' => ['id', 'subject', 'status', 'tracker', 'priority', 'targetVersion', 'typo3Version', 'phpVersion', 'createdOn', 'updatedOn', 'url', 'description', 'relations', 'noteCount', 'notes'],
            ],
            'unavailable' => [
                'type' => ['object', 'null'],
                'description' => 'Why nothing was answered, where status says unavailable. Null otherwise.',
                'properties' => [
                    'cause' => [
                        'type' => 'string',
                        'enum' => ['source-not-answering', 'source-not-parseable'],
                        'description' => 'source-not-answering: the tracker did not answer this time. '
                            . 'source-not-parseable: something answered with a page rather than with the API, '
                            . 'which is what the bot protection in front of it looks like from here.',
                    ],
                    'reason' => Schema::string(),
                ],
                'required' => ['cause', 'reason'],
            ],
        ], ['status', 'source', 'url', 'issue', 'unavailable']);
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $answer = (new Forge())->issue($issue);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'issue' => $answer['issue'],
            'unavailable' => $answer['cause'] === null ? null : [
                'cause' => $answer['cause'],
                'reason' => $answer['cause'] === 'source-not-answering'
                    ? 'The tracker did not answer. It is reachable at ' . Forge::HOST . ' in a browser; nothing here can answer this offline.'
                    : 'Something answered with a page rather than with the API. The tracker sits behind bot protection, and what it challenges is a browser-shaped request.',
            ],
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create('TYPO3 issue tracker: ' . $answer['url'] . "\nCould not answer: " . $data['unavailable']['reason'], $data);
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create('TYPO3 issue tracker: no issue ' . $issue . ' at ' . Forge::HOST . '.', $data);
        }

        $found = $answer['issue'];
        $lines = [
            sprintf('#%d %s', $found['id'], $found['subject']),
            sprintf('%s · %s · priority %s · %s', $found['tracker'], $found['status'], $found['priority'], $found['url']),
        ];
        if ($found['targetVersion'] !== '') {
            $lines[] = 'Target version: ' . $found['targetVersion'];
        }
        if ($found['typo3Version'] !== '') {
            $lines[] = 'Reported against TYPO3 ' . $found['typo3Version']
                . ($found['phpVersion'] !== '' ? ', PHP ' . $found['phpVersion'] : '')
                . ' — which is what the reporter had, not what it still reproduces on.';
        }
        foreach ($found['relations'] as $relation) {
            $lines[] = sprintf('Relation: %s #%d', $relation['relation'], $relation['issue']);
        }
        $lines[] = '';
        $lines[] = '## Reported';
        $lines[] = $found['description'];
        if ($found['notes'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Comments (%d of %d, oldest first)', count($found['notes']), $found['noteCount']);
            $lines[] = 'What was decided is here rather than above.';
            foreach ($found['notes'] as $note) {
                $lines[] = '';
                $lines[] = sprintf('**%s**, %s', $note['author'], $note['on']);
                $lines[] = $note['note'];
            }
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }
}
