<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Defines the knowledge tools and renders their text output. Mirrors the
 * behaviour of the former TypeScript tools.
 */
final class Tools
{
    /** @var array<string, array<int, string>> */
    private const CHANGE_TYPE_CHECKLIST = [
        'bugfix' => [
            'Reproduce the bug first, ideally with a failing test that the fix turns green.',
            'Check whether the bug also affects maintained older release branches.',
        ],
        'feature' => [
            'Add a changelog feature file under typo3/sysext/core/Documentation/Changelog/ for public API additions.',
            'Cover the new behaviour with functional tests, not only unit tests.',
        ],
        'cleanup' => ['Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.'],
        'test' => ['Confirm the test fails without the fix and passes with it; avoid asserting on incidental output.'],
        'documentation' => ['Run ./Build/Scripts/runTests.sh -s checkRst to validate ReST syntax.'],
        'unknown' => [],
    ];

    /** @return array<int, array{name: string, description: string, inputSchema: array<string, mixed>}> */
    public static function definitions(): array
    {
        return [
            [
                'name' => 'typo3_rule_lookup',
                'description' => 'Search the local TYPO3 core contribution rules and script notes by topic.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Topic to look up, for example testing, review, deprecation, or code style.'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'typo3_script_help',
                'description' => 'Find notes for TYPO3 core scripts and commands.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'The TYPO3 core task, for example unit tests, functional tests, CGL, npm, or dependency install.'],
                    ],
                    'required' => ['task'],
                ],
            ],
            [
                'name' => 'typo3_core_task_brief',
                'description' => 'Build a task checklist enriched with matching architecture hints and relevant core checks.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'Short description of the TYPO3 core task.'],
                        'area' => ['type' => 'string', 'description' => 'Affected subsystem or extension, if known.'],
                        'changeType' => ['type' => 'string', 'enum' => ['bugfix', 'feature', 'cleanup', 'test', 'documentation', 'unknown'], 'default' => 'unknown'],
                    ],
                    'required' => ['task'],
                ],
            ],
            [
                'name' => 'typo3_core_run_tests_help',
                'description' => 'Recommend Build/Scripts/runTests.sh commands by topic.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Test or script topic, for example functional, phpstan, TypeScript, composer, or CGL.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_architecture_hint',
                'description' => 'Return architecture hints for TYPO3 core paths or task topics, grouped by section.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'TYPO3 core file paths related to the task, relative to the core checkout.'],
                        'task' => ['type' => 'string', 'description' => 'Short task description or architecture topic.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6, 'description' => 'Maximum number of architecture hints.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_commit_message_help',
                'description' => 'Draft and check a TYPO3 core commit message against the contribution rules.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'changeType' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS'], 'description' => 'TYPO3 commit message keyword.'],
                        'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix.'],
                        'issue' => ['type' => 'string', 'description' => 'Forge issue number, with or without leading #.'],
                        'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Optional related Forge issue numbers.'],
                        'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => ['main'], 'description' => 'Target releases, for example main or 13.4.'],
                        'body' => ['type' => 'string', 'description' => 'Optional commit body.'],
                        'isBreaking' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a breaking change requiring [!!!].'],
                        'isDeprecation' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a deprecation.'],
                    ],
                    'required' => ['changeType', 'summary'],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    public static function call(string $name, array $args): string
    {
        return match ($name) {
            'typo3_rule_lookup' => self::ruleLookup($args),
            'typo3_script_help' => self::scriptHelp($args),
            'typo3_core_task_brief' => self::taskBrief($args),
            'typo3_core_run_tests_help' => self::runTestsHelp($args),
            'typo3_architecture_hint' => self::architectureHint($args),
            'typo3_commit_message_help' => self::commitMessageHelp($args),
            default => throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name)),
        };
    }

    /** @param array<string, mixed> $args */
    private static function ruleLookup(array $args): string
    {
        $query = (string) ($args['query'] ?? '');
        $results = Knowledge::search($query);

        if ($results === []) {
            return sprintf(
                'No local TYPO3 core rule entries matched "%s". Add details to knowledge/typo3-core-rules.md or knowledge/typo3-core-scripts.md.',
                $query
            );
        }

        return implode("\n\n", array_map(static function (array $result): string {
            $excerpts = implode("\n", array_map(static fn(string $e): string => '- ' . $e, $result['excerpts']));
            return '## ' . $result['title'] . "\n" . $excerpts;
        }, $results));
    }

    /** @param array<string, mixed> $args */
    private static function scriptHelp(array $args): string
    {
        $task = (string) ($args['task'] ?? '');
        $results = array_filter(Knowledge::search($task), static fn(array $r): bool => $r['id'] === 'typo3-core-scripts');

        if ($results === []) {
            return sprintf('No script entry matched "%s". Check knowledge/typo3-core-scripts.md and add the project-specific command.', $task);
        }

        return implode("\n\n", array_map(static function (array $result): string {
            $excerpts = implode("\n", array_map(static fn(string $e): string => '- ' . $e, $result['excerpts']));
            return '## ' . $result['title'] . "\n" . $excerpts;
        }, $results));
    }

    /** @param array<string, mixed> $args */
    private static function taskBrief(array $args): string
    {
        $task = (string) ($args['task'] ?? '');
        $area = isset($args['area']) ? (string) $args['area'] : null;
        $changeType = (string) ($args['changeType'] ?? 'unknown');

        $architecture = ArchitectureHints::find($area !== null && $area !== '' ? [$area] : [], $task, 4);
        $testHints = array_slice(TestSuiteHints::find(trim($task . ' ' . ($area ?? ''))), 0, 4);

        $lines = [
            'Task: ' . $task,
            'Area: ' . ($area ?? 'unknown'),
            'Change type: ' . $changeType,
            '',
            'Architecture hints:',
        ];

        if ($architecture['matchedHints'] !== []) {
            foreach (ArchitectureHints::groupByCategory($architecture['matchedHints']) as $section) {
                $lines[] = '### ' . $section['category'];
                foreach ($section['hints'] as $hint) {
                    $lines[] = '## ' . $hint['title'];
                    foreach ($hint['hints'] as $entry) {
                        $lines[] = '- ' . $entry;
                    }
                    if ($hint['checks'] !== []) {
                        $lines[] = 'Checks:';
                        foreach ($hint['checks'] as $check) {
                            $lines[] = '- ' . $check;
                        }
                    }
                }
            }
        } else {
            $lines[] = '- No specific architecture hint matched. Inspect nearby code and subsystem conventions.';
        }

        $lines[] = '';
        $lines[] = 'Relevant TYPO3 core checks:';
        if ($testHints !== []) {
            foreach ($testHints as $hint) {
                $lines[] = '## ' . $hint['suite'];
                $lines[] = '`' . $hint['command'] . '`';
                $lines[] = $hint['whenToUse'];
            }
        } else {
            $lines[] = '- No topic-specific check matched. Run the narrowest relevant suite, then broaden before review.';
        }

        $lines[] = '';
        $lines[] = 'Suggested checklist:';
        $lines[] = '- Confirm the target TYPO3 core branch and issue context.';
        $lines[] = '- Inspect nearby code, tests, and established subsystem conventions.';
        $lines[] = '- Keep the patch focused on the stated task.';
        $lines[] = '- Add or update the narrowest useful test coverage.';
        $lines[] = '- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.';
        foreach (self::CHANGE_TYPE_CHECKLIST[$changeType] ?? [] as $entry) {
            $lines[] = '- ' . $entry;
        }
        $lines[] = '- Summarize changed behavior, affected area, and executed commands.';

        return implode("\n", $lines);
    }

    /** @param array<string, mixed> $args */
    private static function runTestsHelp(array $args): string
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $hints = TestSuiteHints::find($query);

        if ($hints === []) {
            return sprintf('No runTests.sh hint matched "%s". Try "unit", "functional", "phpstan", "build", "composer", or "npm".', (string) $query);
        }

        return implode("\n\n", array_map(static function (array $hint): string {
            return '## ' . $hint['suite'] . "\nCommand from TYPO3 core root:\n`" . $hint['command'] . "`\n\n" . $hint['description'] . "\n" . $hint['whenToUse'];
        }, $hints));
    }

    /** @param array<string, mixed> $args */
    private static function architectureHint(array $args): string
    {
        $paths = array_map('strval', $args['paths'] ?? []);
        $task = isset($args['task']) ? (string) $args['task'] : null;
        $limit = (int) ($args['limit'] ?? 6);

        $result = ArchitectureHints::find($paths, $task ?? '', $limit);

        $lines = [];
        if ($task !== null && $task !== '') {
            $lines[] = 'Task: ' . $task;
        }
        if ($paths !== []) {
            $lines[] = "Paths:\n" . implode("\n", array_map(static fn(string $p): string => '- ' . $p, $paths));
        }
        $lines[] = '';
        $lines[] = 'Architecture hints:';

        if ($result['matchedHints'] !== []) {
            $sectionTexts = [];
            foreach (ArchitectureHints::groupByCategory($result['matchedHints']) as $section) {
                $hintTexts = [];
                foreach ($section['hints'] as $hint) {
                    $block = ['## ' . $hint['title'], 'Hints:'];
                    foreach ($hint['hints'] as $entry) {
                        $block[] = '- ' . $entry;
                    }
                    $block[] = 'Relevant checks:';
                    foreach ($hint['checks'] as $check) {
                        $block[] = '- ' . $check;
                    }
                    $hintTexts[] = implode("\n", $block);
                }
                $sectionTexts[] = '### ' . $section['category'] . "\n\n" . implode("\n\n", $hintTexts);
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } else {
            $lines[] = 'No architecture hint matched. Add a more specific path or topic, or extend knowledge/typo3-core-architecture.md.';
        }

        if ($result['knowledgeExcerpts'] !== []) {
            $lines[] = '';
            $lines[] = 'Knowledge excerpts:';
            foreach ($result['knowledgeExcerpts'] as $entry) {
                $lines[] = '## ' . $entry['title'];
                foreach ($entry['excerpts'] as $excerpt) {
                    $lines[] = '- ' . $excerpt;
                }
            }
        }

        return implode("\n", $lines);
    }

    /** @param array<string, mixed> $args */
    private static function commitMessageHelp(array $args): string
    {
        /** @var array{changeType: string, summary: string} $input */
        $input = $args;
        $result = CommitMessage::create($input);

        $lines = ['Commit message draft:', '```text', $result['message'], '```', '', 'Checks:'];
        foreach ($result['checks'] as $check) {
            $lines[] = '- ' . strtoupper($check['level']) . ': ' . $check['message'];
        }

        return implode("\n", $lines);
    }
}
