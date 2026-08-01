<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\CommitMessage;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\ToolResult;

/**
 * A TYPO3 commit message, drafted from parts or checked and corrected.
 */
final class CommitMessageGuide extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_commit_message_guide';
    }

    public static function description(): string
    {
        return 'Draft and check a TYPO3 commit message. Either assemble one from parts (changeType plus summary) or pass an existing message to check and correct it. The returned draft is ready to commit: the body is wrapped at 72 characters, with fenced code, indented blocks, list structure, and long URLs left intact. Defaults to the core contribution rules; pass workflow="project" in a project or extension repository of your own, where the subject and body conventions apply but the Forge issue, the Releases: trailer and the changelog do not.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string', 'minLength' => 1, 'description' => 'A complete existing commit message to check, subject and trailers included. Unknown trailers such as Change-Id are kept, so an amended patch set stays valid.'],
                'workflow' => ['type' => 'string', 'enum' => ['core', 'project'], 'default' => 'core', 'description' => 'Which rules to apply. "core": a patch against the TYPO3 core, with the Forge issue and the Releases: trailer required. "project": any other repository — the keyword, the 52/72 character limits and the wrapping are checked, no trailer is added or demanded, and [SECURITY] is allowed.'],
                'changeType' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS', 'SECURITY'], 'description' => 'TYPO3 commit message keyword. [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with workflow="project".'],
                'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix.'],
                'issue' => ['type' => 'string', 'description' => 'Forge issue number, with or without leading #.'],
                'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Optional related Forge issue numbers.'],
                'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Target releases, for example main or 13.4. Left out, the draft carries a RELEASE_TARGET placeholder and the checks ask for it — the branches a change is released on are not guessed.'],
                'body' => ['type' => 'string', 'description' => 'Optional commit body. It is wrapped at 72 characters in the draft.'],
                'isBreaking' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a breaking change requiring [!!!].'],
                'isDeprecation' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a deprecation.'],
            ],
            'anyOf' => [
                ['required' => ['message']],
                ['required' => ['changeType', 'summary']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'message' => Schema::string('The commit message, ready to use.'),
            'checks' => Schema::listOf(Schema::object([
                'level' => ['type' => 'string', 'enum' => ['error', 'warning', 'info']],
                'code' => Schema::string('Stable identifier of the check, for example summary-too-long.'),
                'message' => Schema::string(),
            ], ['level', 'code', 'message'])),
            'workflow' => [
                'type' => 'string',
                'enum' => ['core', 'project'],
                'description' => 'Which rules the draft was written and checked against. "core" adds the Forge '
                    . 'issue and the Releases: trailer and demands them; "project" applies the subject and body '
                    . 'rules alone.',
            ],
        ], ['message', 'checks', 'workflow']);
    }

    public static function answer(array $args): ToolResult
    {
        $existing = isset($args['message']) ? trim((string) $args['message']) : '';
        $workflow = CommitMessage::workflow($args['workflow'] ?? null);

        $parseChecks = [];
        if ($existing !== '') {
            $parsed = CommitMessage::parse($existing, $workflow);
            // Explicit arguments still win, so a message can be checked and
            // amended in one call: pass the message plus issue=12345.
            $input = array_merge($parsed['input'], array_intersect_key($args, array_flip([
                'changeType', 'summary', 'issue', 'relatedIssues', 'releases', 'isBreaking', 'isDeprecation',
            ])));
            $parseChecks = $parsed['checks'];
        } else {
            $input = $args;
        }
        $input['workflow'] = $workflow;

        if (!isset($input['summary']) || trim((string) $input['summary']) === '') {
            throw new \InvalidArgumentException(
                'Provide either a complete message, or changeType and summary.'
            );
        }

        /** @var array{changeType: string, summary: string} $input */
        $result = CommitMessage::create($input);

        $checks = $result['checks'];
        if ($parseChecks !== []) {
            // "Nothing to complain about" only holds when nothing complained.
            $checks = array_values(array_filter(
                $checks,
                static fn(array $check): bool => $check['level'] !== 'info'
            ));
        }

        $checks = array_merge($parseChecks, $checks);

        $heading = $existing === '' ? 'Commit message draft:' : 'Commit message, corrected:';
        $lines = [$heading, '```text', $result['message'], '```', '', 'Checks:'];
        foreach ($checks as $check) {
            $lines[] = '- ' . strtoupper($check['level']) . ': ' . $check['message'];
        }

        // Which rules were applied belongs in the answer, because the two sets
        // differ in what they demand rather than in how strict they are: a
        // caller who did not know about the other one reads a missing Forge
        // issue as a defect in their commit message.
        $lines[] = '';
        $lines[] = $workflow === CommitMessage::WORKFLOW_PROJECT
            ? 'Checked without the core workflow: keyword, 52/72 limits and wrapping apply, the Forge issue and '
                . 'the Releases: trailer do not. workflow="core" for a patch against the TYPO3 core.'
            : 'Checked against the core contribution rules, trailers included. workflow="project" applies the same '
                . 'subject and body rules without the Forge issue and the Releases: trailer.';

        return ToolResult::create(implode("\n", $lines), [
            'message' => $result['message'],
            'checks' => $checks,
            'workflow' => $workflow,
        ]);
    }
}
