<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\CommitMessage;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * A TYPO3 commit message, drafted from parts or checked and corrected.
 */
final class CommitMessageGuide extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_commit_message_guide';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Draft and check a TYPO3 commit message. Either assemble one from parts (changeType plus summary) or pass an existing message to check and correct it. The returned draft is ready to commit: the body is wrapped at 72 characters, and the checks name every run of lines the wrapping joined and every line it could not bring under the width. Defaults to a repository of your own, where the subject and body conventions apply but the Forge issue, the Releases: trailer and the changelog do not; pass workflow="core" for a patch against the TYPO3 core, where the Releases: trailer is also held against the branches that take a patch today.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string', 'minLength' => 1, 'description' => 'A complete existing commit message to check, subject and trailers included. Unknown trailers such as Change-Id are kept, so an amended patch set stays valid.'],
                'workflow' => ['type' => 'string', 'enum' => ['core', 'project'], 'default' => 'project', 'description' => 'Which rules to apply. "project", the default: any repository of your own — the keyword, the 52/72 character limits and the wrapping are checked, no trailer is added or demanded, and [SECURITY] is allowed. "core": a patch against the TYPO3 core, with the Forge issue and the Releases: trailer required.'],
                'changeType' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS', 'SECURITY'], 'description' => 'TYPO3 commit message keyword. [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with workflow="project".'],
                'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix.'],
                'issue' => ['type' => 'string', 'description' => 'Forge issue number, with or without leading #.'],
                'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Optional related Forge issue numbers.'],
                'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Target releases, for example main or 13.4. Left out, the draft carries a RELEASE_TARGET placeholder and the checks name the lines taking a patch today — the branches a change is released on are not guessed. Each one passed is held against those lines: a branch out of regular support is an error, since ELTS releases come from the ELTS partners rather than from a patch to that branch.'],
                'body' => ['type' => 'string', 'description' => 'Optional commit body. It is wrapped at 72 characters in the draft: indent a block to keep the line breaks you wrote, and keep those lines under the width yourself.'],
                'isBreaking' => ['type' => 'boolean', 'description' => 'Whether this is a breaking change requiring [!!!]. Left out, the checks say the classification was assumed: it is a property of the diff, which this tool never sees.'],
                'isDeprecation' => ['type' => 'boolean', 'description' => 'Whether this is a deprecation. Left out, it is assumed the same way and the checks say so.'],
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
            // Dropped by code rather than by level, because what the wrapping
            // did to the body is reported at info too and is not a complaint.
            $checks = array_values(array_filter(
                $checks,
                static fn(array $check): bool => $check['code'] !== 'no-issues-found'
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
