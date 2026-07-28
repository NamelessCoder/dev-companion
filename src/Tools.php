<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\Icons;
use Typo3CmsMcp\Catalog\Labels;

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

    /** Extra domain signal carried by the change type itself. */
    private const CHANGE_TYPE_TERMS = [
        'documentation' => 'documentation changelog rst',
        'test' => 'unit test functional test',
        'feature' => 'changelog',
        'bugfix' => '',
        'cleanup' => '',
        'unknown' => '',
    ];

    /** @return array<int, array{name: string, description: string, inputSchema: array<string, mixed>}> */
    public static function definitions(): array
    {
        $definitions = [
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
                'name' => 'typo3_component_lookup',
                'description' => 'Look up TYPO3 backend UI components by name or topic. Returns canonical markup, variant/modifier/sub-component classes, the custom-property contract, and the styleguide demo and Sass source paths.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Component name, class, or topic, for example badge, card, search box, or input-group. Omit to list the catalog.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_icon_lookup',
                'description' => 'Validate and discover TYPO3 core icon identifiers (the registered T3Icons names such as actions-open or module-web-list). Returns matching identifiers grouped by category so unknown identifiers are caught before runtime.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Identifier fragment or keywords, for example "status warning", "delete", or "actions-open". Omit to list categories.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 40, 'description' => 'Maximum number of identifiers to return.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_label_lookup',
                'description' => 'Search registered TYPO3 core labels (XLF trans-units) across the core sysexts. Returns the fully-qualified LLL reference, English source text, and any x-unused-since marker, so existing labels can be reused instead of inventing new keys.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Words from the label key or its English text, for example "save document" or "labels.title".'],
                        'mode' => ['type' => 'string', 'enum' => ['keys', 'domains'], 'default' => 'keys', 'description' => 'keys: search individual labels. domains: list registered XLF translation domains.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 25, 'description' => 'Maximum number of results to return.'],
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

        // Only offered from a standalone checkout — see Feedback.
        if (Feedback::isAvailable()) {
            array_push($definitions, ...self::feedbackDefinitions());
        }

        return $definitions;
    }

    /** @return array<int, array{name: string, description: string, inputSchema: array<string, mixed>}> */
    private static function feedbackDefinitions(): array
    {
        return [
            [
                'name' => 'typo3_make_me_better',
                'description' => 'Leave a note about a gap, wrong answer, or missing capability of this knowledge server. The note is stored as markdown in this project so it can be implemented later. Use it whenever an answer was incomplete or a lookup found nothing that should have been there.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'observation' => ['type' => 'string', 'minLength' => 1, 'description' => 'What was missing, wrong, or unhelpful. Be specific enough to act on later.'],
                        'category' => ['type' => 'string', 'enum' => Feedback::CATEGORIES, 'default' => 'idea', 'description' => 'missing-knowledge: the knowledge base lacks the answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the need. bug: the server misbehaved. idea: anything else.'],
                        'tool' => ['type' => 'string', 'description' => 'The tool the observation is about, for example typo3_icon_lookup.'],
                        'query' => ['type' => 'string', 'description' => 'The query or arguments that produced the unsatisfying result.'],
                        'suggestion' => ['type' => 'string', 'description' => 'What the server should have answered or should be able to do.'],
                    ],
                    'required' => ['observation'],
                ],
            ],
            [
                'name' => 'typo3_feedback_list',
                'description' => 'List improvement notes recorded via typo3_make_me_better, newest first, so they can be worked off.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => ['type' => 'string', 'enum' => ['open', 'all'], 'default' => 'open', 'description' => 'open: only notes still marked open. all: every recorded note.'],
                        'category' => ['type' => 'string', 'enum' => Feedback::CATEGORIES, 'description' => 'Restrict the list to one category.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20, 'description' => 'Maximum number of notes to return.'],
                    ],
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
            'typo3_component_lookup' => self::componentLookup($args),
            'typo3_icon_lookup' => self::iconLookup($args),
            'typo3_label_lookup' => self::labelLookup($args),
            'typo3_commit_message_help' => self::commitMessageHelp($args),
            'typo3_make_me_better' => self::makeMeBetter($args),
            'typo3_feedback_list' => self::feedbackList($args),
            default => throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name)),
        };
    }

    /** @param array<string, mixed> $args */
    private static function makeMeBetter(array $args): string
    {
        $file = Feedback::record($args);

        return sprintf(
            "Thanks — noted in %s.\n\nIt will be picked up when the knowledge base is next improved; "
            . 'nothing about the current answer changes.',
            $file,
        );
    }

    /** @param array<string, mixed> $args */
    private static function feedbackList(array $args): string
    {
        $status = is_string($args['status'] ?? null) ? $args['status'] : 'open';
        $category = is_string($args['category'] ?? null) ? $args['category'] : null;
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 20;

        $notes = Feedback::notes($status, $category, $limit);

        if ($notes === []) {
            return $status === 'open'
                ? 'No open improvement notes.'
                : 'No improvement notes recorded yet.';
        }

        $lines = array_map(static function (array $note): string {
            $date = substr($note['date'], 0, 10);
            $about = $note['tool'] === '' ? '' : ' — ' . $note['tool'];

            return sprintf(
                "- [%s] %s%s\n  %s\n  %s",
                $note['category'],
                $date,
                $about,
                $note['title'],
                $note['file'],
            );
        }, $notes);

        return sprintf("%d improvement note(s):\n\n%s", count($notes), implode("\n", $lines));
    }

    /** @param array<string, mixed> $args */
    private static function ruleLookup(array $args): string
    {
        $query = (string) ($args['query'] ?? '');
        $results = Knowledge::search($query);

        if ($results === []) {
            return self::noKnowledgeMatch($query);
        }

        return self::renderSections($results);
    }

    /** @param array<string, mixed> $args */
    private static function scriptHelp(array $args): string
    {
        $task = (string) ($args['task'] ?? '');
        $results = Knowledge::search($task, ['typo3-core-scripts']);

        if ($results !== []) {
            return self::renderSections($results);
        }

        // Nothing about scripts matched. Say so, and route to the documents that
        // do cover the topic instead of answering with the nearest script prose.
        $message = sprintf(
            'No section of the TYPO3 core script notes matched "%s". They cover: %s.',
            $task,
            self::topicList('typo3-core-scripts')
        );

        $elsewhere = Knowledge::search($task);
        if ($elsewhere !== []) {
            $titles = array_values(array_unique(array_map(
                static fn(array $result): string => $result['title'],
                $elsewhere
            )));
            $message .= sprintf(
                "\n\nOther knowledge documents do match this query — call typo3_rule_lookup for: %s.",
                implode(', ', $titles)
            );
        }

        return $message;
    }

    /**
     * Renders matched knowledge sections as coherent excerpts: the section
     * keeps its own heading and original formatting, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, coverage: float, truncated: bool}> $results
     */
    private static function renderSections(array $results): string
    {
        return implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $source = sprintf(
                'Source: %s (typo3://core/%s) — matches %d%% of the query terms',
                $result['title'],
                $result['id'],
                (int) round($result['coverage'] * 100),
            );

            $body = $result['body'];
            if ($result['truncated']) {
                $body .= "\n\n(section truncated — read typo3://core/" . $result['id'] . ' for the rest)';
            }

            return '## ' . $heading . "\n" . $source . "\n\n" . $body;
        }, $results));
    }

    private static function noKnowledgeMatch(string $query): string
    {
        $documents = implode("\n", array_map(
            static fn(array $document): string => '- ' . $document['title'] . ': ' . implode(', ', $document['topics']),
            Knowledge::topics()
        ));

        return sprintf(
            "No knowledge section matched \"%s\".\n\nThis knowledge base covers:\n%s\n\n"
            . 'For registered components, icons, or labels use typo3_component_lookup, typo3_icon_lookup, '
            . 'or typo3_label_lookup instead. If the topic should be covered here, record it with typo3_make_me_better.',
            $query,
            $documents
        );
    }

    private static function topicList(string $documentId): string
    {
        foreach (Knowledge::topics() as $document) {
            if ($document['id'] === $documentId) {
                return implode(', ', $document['topics']);
            }
        }

        return '';
    }

    /** @param array<string, mixed> $args */
    private static function taskBrief(array $args): string
    {
        $task = (string) ($args['task'] ?? '');
        $area = isset($args['area']) ? trim((string) $args['area']) : '';
        $changeType = (string) ($args['changeType'] ?? 'unknown');

        $subject = trim($task . ' ' . $area);
        $paths = $area === '' ? [] : [$area];
        $domains = Domains::detect($paths, $task . ' ' . (self::CHANGE_TYPE_TERMS[$changeType] ?? ''));
        $intents = TaskIntents::detect($subject . ' ' . $changeType);

        $architecture = ArchitectureHints::find($paths, $task, 4);
        $testHints = array_slice(TestSuiteHints::find($subject, $domains), 0, 4);

        $lines = [
            'Task: ' . $task,
            'Area: ' . ($area === '' ? 'unknown' : $area),
            'Change type: ' . $changeType,
            'Domains: ' . implode(', ', $domains),
        ];
        if ($intents !== []) {
            $lines[] = 'Recognized as: ' . implode(', ', array_map(
                static fn(array $intent): string => (string) $intent['title'],
                $intents
            ));
        }

        $lines[] = '';
        $lines[] = 'Architecture hints:';
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
            $lines[] = '- No architecture hint matched this task text. That means no convention was recognized, '
                . 'not that none applies: call typo3_architecture_hint again with the concrete file paths once they are known.';
        }

        $rules = TaskIntents::rules($intents);
        if ($rules !== []) {
            $lines[] = '';
            $lines[] = 'Rules that apply to this task:';
            $lines[] = '';
            $lines[] = self::renderSections($rules);
        }

        $lines[] = '';
        $lines[] = 'Relevant TYPO3 core checks:';
        $intentChecks = [];
        foreach ($intents as $intent) {
            foreach ($intent['checks'] as $check) {
                $intentChecks[$check] = true;
            }
        }
        foreach (array_keys($intentChecks) as $check) {
            $lines[] = '- `' . $check . '`';
        }
        if ($testHints !== []) {
            foreach ($testHints as $hint) {
                $lines[] = '## ' . $hint['suite'];
                $lines[] = '`' . $hint['command'] . '`';
                if ($hint['targeted'] !== null) {
                    $lines[] = 'Targeted: `' . $hint['targeted'] . '`';
                }
                $lines[] = $hint['whenToUse'];
            }
        } elseif ($intentChecks === []) {
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
        foreach ($intents as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $lines[] = '- ' . $entry;
            }
        }
        $lines[] = '- Summarize changed behavior, affected area, and executed commands.';

        $lines[] = '';
        $lines[] = 'Next lookups for this task:';
        foreach (self::nextTools($intents, $domains) as $suggestion) {
            $lines[] = '- ' . $suggestion;
        }

        return implode("\n", $lines);
    }

    /**
     * Routes to the specialised tools, so an agent that starts here learns that
     * they exist instead of writing markup or label keys from memory.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    private static function nextTools(array $intents, array $domains): array
    {
        $suggestions = [];
        foreach ($intents as $intent) {
            foreach ($intent['tools'] as $tool) {
                $suggestions[$tool] = true;
            }
        }

        if (in_array(Domains::FRONTEND, $domains, true)) {
            $suggestions['typo3_component_lookup, before writing backend markup or CSS classes'] = true;
        }
        $suggestions['typo3_architecture_hint with the concrete file paths, once they are known'] = true;
        $suggestions['typo3_core_run_tests_help, for the targeted runTests.sh invocation'] = true;
        $suggestions['typo3_make_me_better, when one of these answers was wrong or incomplete'] = true;

        return array_keys($suggestions);
    }

    /** @param array<string, mixed> $args */
    private static function runTestsHelp(array $args): string
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $hints = TestSuiteHints::find($query);

        $blocks = [];
        if ($hints === []) {
            $blocks[] = sprintf(
                'No runTests.sh suite matched "%s". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".',
                (string) $query
            );
        } else {
            foreach ($hints as $hint) {
                $block = ['## ' . $hint['suite'], 'Command from the TYPO3 core root:', '`' . $hint['command'] . '`'];
                if ($hint['targeted'] !== null) {
                    $block[] = 'Targeted run while iterating:';
                    $block[] = '`' . $hint['targeted'] . '`';
                }
                $block[] = '';
                $block[] = $hint['description'];
                $block[] = $hint['whenToUse'];
                $blocks[] = implode("\n", $block);
            }
        }

        $blocks[] = self::invocationBlock();

        return implode("\n\n", $blocks);
    }

    /**
     * The invocation rules that apply to every suite. Emitted with every answer:
     * without CI=true and the passthrough form, a suite command alone is rarely
     * what a patch actually needs.
     */
    private static function invocationBlock(): string
    {
        $invocation = TestSuiteHints::invocation();

        $lines = ['## Invoking runTests.sh'];
        foreach ($invocation['notes'] as $note) {
            $lines[] = '- ' . $note;
        }

        $lines[] = '';
        $lines[] = 'Options:';
        foreach ($invocation['options'] as $option) {
            $lines[] = '- `' . $option['option'] . '` — ' . $option['description'];
        }

        $lines[] = '';
        $lines[] = 'Examples:';
        foreach ($invocation['examples'] as $example) {
            $lines[] = '- ' . $example['purpose'] . ':';
            $lines[] = '  `' . $example['command'] . '`';
        }

        return implode("\n", $lines);
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
        $lines[] = 'Domains: ' . implode(', ', $result['domains'])
            . ' (hints outside these domains are not shown)';
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
                    if ($hint['checks'] !== []) {
                        $block[] = 'Relevant checks:';
                        foreach ($hint['checks'] as $check) {
                            $block[] = '- ' . $check;
                        }
                    }
                    $hintTexts[] = implode("\n", $block);
                }
                $sectionTexts[] = '### ' . $section['category'] . "\n\n" . implode("\n\n", $hintTexts);
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } elseif ($result['knowledgeSections'] !== []) {
            $lines[] = 'No structured hint matched; the closest architecture notes are:';
            $lines[] = '';
            $lines[] = self::renderSections($result['knowledgeSections']);
        } else {
            $lines[] = 'No architecture hint matched. Add a more specific path or topic, or extend knowledge/typo3-core-architecture.md.';
        }

        return implode("\n", $lines);
    }

    /** @param array<string, mixed> $args */
    private static function componentLookup(array $args): string
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $components = Components::find($query);

        if ($components === []) {
            return sprintf(
                'No TYPO3 component matched "%s". Try a component name (badge, card), a class (input-group), or a topic (search box). Extend knowledge/catalog/components.json to add more.',
                (string) $query
            );
        }

        // A specific query returns the best matches; an empty query lists names.
        if ($query === null || trim($query) === '') {
            $names = implode("\n", array_map(
                static fn(array $c): string => '- ' . $c['name'] . ' — ' . $c['title'],
                $components
            ));
            return "TYPO3 backend component catalog:\n" . $names;
        }

        $blocks = array_map(static function (array $c): string {
            $lines = ['## ' . $c['title'] . ' (`' . $c['rootClass'] . '`)'];
            if ($c['summary'] !== '') {
                $lines[] = $c['summary'];
            }

            $lines[] = '';
            $lines[] = 'Markup:';
            $lines[] = '```html';
            $lines[] = $c['markup'];
            $lines[] = '```';

            $appendList = static function (string $label, array $items) use (&$lines): void {
                if ($items !== []) {
                    $lines[] = $label . ': ' . implode(', ', $items);
                }
            };
            $appendList('Variants', $c['variants']);
            $appendList('Modifiers', $c['modifiers']);
            $appendList('Sub-components', $c['subComponents']);
            $appendList('Custom properties', $c['customProperties']);

            $lines[] = 'Sass source: ' . $c['sassPath'];
            $lines[] = 'Styleguide demo: ' . ($c['demoPath'] ?? 'none (not a styleguide component)');

            if ($c['examples'] !== []) {
                $lines[] = '';
                $lines[] = 'Examples:';
                foreach ($c['examples'] as $example) {
                    $lines[] = '```html';
                    $lines[] = $example;
                    $lines[] = '```';
                }
            }

            return implode("\n", $lines);
        }, array_slice($components, 0, 3));

        $checklist = Components::checklist();
        $checklistLines = ['## ' . $checklist['title']];
        if ($checklist['intro'] !== '') {
            $checklistLines[] = $checklist['intro'];
        }
        foreach ($checklist['items'] as $item) {
            $checklistLines[] = '- [ ] ' . $item;
        }
        $blocks[] = implode("\n", $checklistLines);

        return implode("\n\n", $blocks);
    }

    /** @param array<string, mixed> $args */
    private static function iconLookup(array $args): string
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $limit = (int) ($args['limit'] ?? 40);

        if ($query === null || trim($query) === '') {
            $categories = Icons::categories();
            return "Icon categories (query an identifier fragment to list icons):\n"
                . implode("\n", array_map(static fn(string $c): string => '- ' . $c, $categories));
        }

        $matches = Icons::find($query);
        if ($matches === []) {
            return sprintf(
                'No TYPO3 icon identifier matched "%s". Identifiers follow the <category>-<name> convention, for example actions-open or module-web-list.',
                $query
            );
        }

        $total = count($matches);
        $shown = array_slice($matches, 0, $limit);
        $lines = array_map(
            static fn(array $icon): string => '- ' . $icon['identifier'] . '  (' . $icon['category'] . ')',
            $shown
        );

        $header = sprintf('%d icon identifier(s) matched "%s"', $total, $query);
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d, refine the query to narrow down', count($shown));
        }

        return $header . ":\n" . implode("\n", $lines);
    }

    /** @param array<string, mixed> $args */
    private static function labelLookup(array $args): string
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $mode = (string) ($args['mode'] ?? 'keys');
        $limit = (int) ($args['limit'] ?? 25);

        if ($mode === 'domains') {
            $domains = Labels::domains($query);
            if ($domains === []) {
                return sprintf('No registered label domain matched "%s".', (string) $query);
            }
            $lines = array_map(
                static fn(array $d): string => sprintf('- %s  (%s, %d labels)', $d['ref'], $d['ext'], $d['count']),
                array_slice($domains, 0, $limit)
            );
            return sprintf('%d label domain(s):', count($domains)) . "\n" . implode("\n", $lines);
        }

        if ($query === null || trim($query) === '') {
            return 'Provide a query to search labels, or pass mode "domains" to list registered XLF files.';
        }

        $labels = Labels::find($query);
        if ($labels === []) {
            return sprintf('No TYPO3 core label matched "%s". Try words from the key or its English text, or extend the catalog scope.', $query);
        }

        $total = count($labels);
        $shown = array_slice($labels, 0, $limit);
        $lines = array_map(static function (array $label): string {
            $line = '- ' . $label['ref'] . "\n  \"" . $label['source'] . '"';
            if ($label['unusedSince'] !== null) {
                $line .= "\n  (unused since " . $label['unusedSince'] . ')';
            }
            return $line;
        }, $shown);

        $header = sprintf('%d label(s) matched "%s"', $total, $query);
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d', count($shown));
        }

        return $header . ":\n" . implode("\n", $lines);
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
