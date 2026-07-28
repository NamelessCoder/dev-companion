<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\Icons;
use Typo3CmsMcp\Catalog\Labels;
use Typo3CmsMcp\Catalog\Meta as CatalogMeta;

/**
 * Defines the knowledge tools and builds their answers.
 *
 * Every tool returns a ToolResult: the rendered text and the same answer as
 * data, matching the output schema declared for that tool in ToolSchemas. The
 * text is what makes an answer usable; the data is what makes it composable.
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

    /** Appended when a catalog lookup finds nothing at all. */
    private const CATALOG_MISS_NOTE = 'Call typo3_catalog_status for what this snapshot covers.';

    /** Extra domain signal carried by the change type itself. */
    private const CHANGE_TYPE_TERMS = [
        'documentation' => 'documentation changelog rst',
        'test' => 'unit test functional test',
        'feature' => 'changelog',
        'bugfix' => '',
        'cleanup' => '',
        'unknown' => '',
    ];

    /**
     * Every tool but typo3_make_me_better only reads bundled knowledge: same
     * arguments, same answer, no side effect, nothing outside this package.
     *
     * @var array<string, bool>
     */
    private const READ_ONLY_ANNOTATIONS = [
        'readOnlyHint' => true,
        'destructiveHint' => false,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * @return array<int, array{
     *     name: string,
     *     description: string,
     *     inputSchema: array<string, mixed>,
     *     annotations: array<string, bool>,
     *     outputSchema: array<string, mixed>|null
     * }>
     */
    public static function definitions(): array
    {
        $definitions = [
            [
                'name' => 'typo3_server_scope',
                'description' => 'Orientation for this server: what it covers and at which depth, what it deliberately does not cover, and which tool to call when. Start here when it is unclear whether this server can answer a question at all, or which of the lookups is the right one.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
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
                'name' => 'typo3_script_lookup',
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
                'name' => 'typo3_task_guide',
                'description' => 'Build a task checklist enriched with matching architecture hints and relevant core checks. Built from bundled conventions only: it does not read your checkout, so it also names what you have to establish there yourself and routes to the lookups that fit the task.',
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
                'name' => 'typo3_test_run_guide',
                'description' => 'Recommend Build/Scripts/runTests.sh commands by topic.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Test or script topic, for example functional, phpstan, TypeScript, composer, or CGL.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_architecture_lookup',
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
                'description' => 'Validate and discover TYPO3 core icon identifiers (the registered T3Icons names such as actions-open or module-web-list). Matches identifier fragments, registered aliases, and concept keywords ("warning" finds actions-exclamation-triangle), and states why each result matched. Results come from a versioned snapshot, so a miss means "not in the snapshot", not "does not exist".',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Identifier fragment, alias, or concept, for example "warning", "delete", or "actions-open". Omit to list categories and concept keywords.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 40, 'description' => 'Maximum number of identifiers to return.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_label_lookup',
                'description' => 'Search registered TYPO3 core labels (XLF trans-units) across the core sysexts. Returns the translation domain reference (backend.alt_doc:key), which is the canonical form for TCA, LanguageService::sL() and f:translate, plus the legacy LLL file path, the English source text, and any x-unused-since marker. Use it to reuse an existing label instead of inventing a key. Results come from a versioned snapshot of a subset of the core label files.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Words from the label key or its English text, for example "save document" or "labels.title".'],
                        'mode' => ['type' => 'string', 'enum' => ['keys', 'domains'], 'default' => 'keys', 'description' => 'keys: search individual labels. domains: list registered translation domains.'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 25, 'description' => 'Maximum number of results to return.'],
                    ],
                ],
            ],
            [
                'name' => 'typo3_catalog_status',
                'description' => 'Report which TYPO3 core revision the component, icon, and label catalogs were taken from, what they cover, and how to re-check them against a checkout. Call this to judge whether a catalog miss is authoritative for the branch you are working on.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
            [
                'name' => 'typo3_commit_message_guide',
                'description' => 'Draft and check a TYPO3 core commit message against the contribution rules. Either assemble one from parts (changeType plus summary) or pass an existing message to check and correct it. The returned draft is ready to commit: the body is wrapped at 72 characters, with fenced code, indented blocks, list structure, and long URLs left intact.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'message' => ['type' => 'string', 'minLength' => 1, 'description' => 'A complete existing commit message to check, subject and trailers included. Unknown trailers such as Change-Id are kept, so an amended patch set stays valid.'],
                        'changeType' => ['type' => 'string', 'enum' => ['BUGFIX', 'FEATURE', 'TASK', 'DOCS'], 'description' => 'TYPO3 commit message keyword.'],
                        'summary' => ['type' => 'string', 'minLength' => 1, 'description' => 'Summary text without the TYPO3 keyword prefix.'],
                        'issue' => ['type' => 'string', 'description' => 'Forge issue number, with or without leading #.'],
                        'relatedIssues' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'Optional related Forge issue numbers.'],
                        'releases' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => ['main'], 'description' => 'Target releases, for example main or 13.4.'],
                        'body' => ['type' => 'string', 'description' => 'Optional commit body. It is wrapped at 72 characters in the draft.'],
                        'isBreaking' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a breaking change requiring [!!!].'],
                        'isDeprecation' => ['type' => 'boolean', 'default' => false, 'description' => 'Whether this is a deprecation.'],
                    ],
                    'anyOf' => [
                        ['required' => ['message']],
                        ['required' => ['changeType', 'summary']],
                    ],
                ],
            ],
        ];

        // Only offered from a standalone checkout — see Feedback.
        if (Feedback::isAvailable()) {
            array_push($definitions, ...self::feedbackDefinitions());
        }

        return array_map(static function (array $definition): array {
            $definition['annotations'] ??= self::READ_ONLY_ANNOTATIONS;
            $definition['outputSchema'] = ToolSchemas::forTool($definition['name']);

            return $definition;
        }, $definitions);
    }

    /** @return array<int, array{name: string, description: string, inputSchema: array<string, mixed>, annotations?: array<string, bool>}> */
    private static function feedbackDefinitions(): array
    {
        return [
            [
                'name' => 'typo3_make_me_better',
                'description' => 'Leave a note about a gap, wrong answer, or missing capability of this knowledge server. The note is stored as markdown in this project so it can be implemented later. Use it whenever an answer was incomplete or a lookup found nothing that should have been there.',
                // The one tool that writes: a new note file per call, never
                // touching an existing one.
                'annotations' => [
                    'readOnlyHint' => false,
                    'destructiveHint' => false,
                    'idempotentHint' => false,
                    'openWorldHint' => false,
                ],
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
    public static function call(string $name, array $args): ToolResult
    {
        return match ($name) {
            'typo3_server_scope' => self::serverScope(),
            'typo3_rule_lookup' => self::ruleLookup($args),
            'typo3_script_lookup' => self::scriptLookup($args),
            'typo3_task_guide' => self::taskGuide($args),
            'typo3_test_run_guide' => self::testRunGuide($args),
            'typo3_architecture_lookup' => self::architectureLookup($args),
            'typo3_component_lookup' => self::componentLookup($args),
            'typo3_icon_lookup' => self::iconLookup($args),
            'typo3_label_lookup' => self::labelLookup($args),
            'typo3_catalog_status' => self::catalogStatus($args),
            'typo3_commit_message_guide' => self::commitMessageGuide($args),
            'typo3_make_me_better' => self::makeMeBetter($args),
            'typo3_feedback_list' => self::feedbackList($args),
            default => throw new \InvalidArgumentException(sprintf('Unknown tool: %s', $name)),
        };
    }

    private static function serverScope(): ToolResult
    {
        $scope = Scope::read();

        $lines = [$scope['purpose'], '', 'Covered, and how deeply:'];
        foreach ($scope['covers'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['depth'];
            $lines[] = 'Tools: ' . implode(', ', $entry['tools']);
            $lines[] = 'Source: ' . $entry['source'];
        }

        $lines[] = '';
        $lines[] = 'Deliberately not covered:';
        foreach ($scope['doesNotCover'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['why'];
            $lines[] = 'Instead: ' . $entry['instead'];
        }

        $lines[] = '';
        $lines[] = 'Which tool to call when:';
        foreach ($scope['routing'] as $entry) {
            $lines[] = '- ' . $entry['when'] . ' → ' . $entry['call'];
        }

        $lines[] = '';
        $lines[] = 'Everything is read-only and answered from the bundled knowledge base; '
            . 'nothing is fetched, executed, or looked up online.';
        if (Feedback::isAvailable()) {
            $lines[] = 'Missing something that belongs here? Record it with typo3_make_me_better.';
        }

        return ToolResult::create(implode("\n", $lines), $scope);
    }

    /** @param array<string, mixed> $args */
    private static function makeMeBetter(array $args): ToolResult
    {
        $file = Feedback::record($args);

        return ToolResult::create(
            sprintf(
                "Thanks — noted in %s.\n\nIt will be picked up when the knowledge base is next improved; "
                . 'nothing about the current answer changes.',
                $file,
            ),
            ['file' => $file],
        );
    }

    /** @param array<string, mixed> $args */
    private static function feedbackList(array $args): ToolResult
    {
        $status = is_string($args['status'] ?? null) ? $args['status'] : 'open';
        $category = is_string($args['category'] ?? null) ? $args['category'] : null;
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 20;

        $notes = Feedback::notes($status, $category, $limit);

        if ($notes === []) {
            return ToolResult::create(
                $status === 'open' ? 'No open improvement notes.' : 'No improvement notes recorded yet.',
                ['count' => 0, 'notes' => []],
            );
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

        return ToolResult::create(
            sprintf("%d improvement note(s):\n\n%s", count($notes), implode("\n", $lines)),
            ['count' => count($notes), 'notes' => $notes],
        );
    }

    /** @param array<string, mixed> $args */
    private static function ruleLookup(array $args): ToolResult
    {
        $query = (string) ($args['query'] ?? '');
        $results = Knowledge::search($query);

        if ($results === []) {
            return self::noKnowledgeMatch($query);
        }

        return ToolResult::create(self::renderSections($results), [
            'query' => $query,
            'matchCount' => count($results),
            'matches' => self::matchRecords($results),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function scriptLookup(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');
        $results = Knowledge::search($task, ['typo3-core-scripts']);

        if ($results !== []) {
            return ToolResult::create(self::renderSections($results), [
                'query' => $task,
                'matchCount' => count($results),
                'matches' => self::matchRecords($results),
            ]);
        }

        // Nothing about scripts matched. Say so, and route to the documents that
        // do cover the topic instead of answering with the nearest script prose.
        $message = sprintf(
            'No section of the TYPO3 core script notes matched "%s". They cover: %s.',
            $task,
            self::topicList('typo3-core-scripts')
        );

        $elsewhere = Knowledge::search($task);
        $titles = array_values(array_unique(array_map(
            static fn(array $result): string => $result['title'],
            $elsewhere
        )));
        if ($titles !== []) {
            $message .= sprintf(
                "\n\nOther knowledge documents do match this query — call typo3_rule_lookup for: %s.",
                implode(', ', $titles)
            );
        }

        return ToolResult::create($message, [
            'query' => $task,
            'matchCount' => 0,
            'matches' => [],
            'elsewhere' => $titles,
        ]);
    }

    /**
     * Renders matched knowledge sections as coherent excerpts: the section
     * keeps its own heading and original formatting, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
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

    /**
     * The same matched sections as data: the document they come from, how much
     * of the query they cover, and the resource holding the full text.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
     * @return array<int, array<string, mixed>>
     */
    private static function matchRecords(array $results): array
    {
        return array_map(static fn(array $result): array => [
            'documentId' => $result['id'],
            'title' => $result['title'],
            'uri' => 'typo3://core/' . $result['id'],
            'heading' => $result['heading'] === '' ? $result['title'] : $result['heading'],
            'body' => $result['body'],
            'coverage' => round($result['coverage'], 3),
            'score' => $result['score'],
            'truncated' => $result['truncated'],
        ], $results);
    }

    private static function noKnowledgeMatch(string $query): ToolResult
    {
        $documents = implode("\n", array_map(
            static fn(array $document): string => '- ' . $document['title'] . ': ' . implode(', ', $document['topics']),
            Knowledge::topics()
        ));

        $text = sprintf(
            "No knowledge section matched \"%s\".\n\nThis knowledge base covers:\n%s\n\n"
            . 'For registered components, icons, or labels use typo3_component_lookup, typo3_icon_lookup, '
            . 'or typo3_label_lookup instead, and call typo3_server_scope for what this server covers at all. '
            . 'If the topic should be covered here, record it with typo3_make_me_better.',
            $query,
            $documents
        );

        return ToolResult::create($text, [
            'query' => $query,
            'matchCount' => 0,
            'matches' => [],
            'documents' => Knowledge::topics(),
        ]);
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
    private static function taskGuide(array $args): ToolResult
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
                . 'not that none applies: call typo3_architecture_lookup again with the concrete file paths once they are known.';
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
        $checks = array_keys($intentChecks);
        foreach ($checks as $check) {
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

        $checklist = [
            'Confirm the target TYPO3 core branch and issue context.',
            'Inspect nearby code, tests, and established subsystem conventions.',
            'Keep the patch focused on the stated task.',
            'Add or update the narrowest useful test coverage.',
            'Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.',
        ];
        foreach (self::CHANGE_TYPE_CHECKLIST[$changeType] ?? [] as $entry) {
            $checklist[] = $entry;
        }
        foreach ($intents as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = (string) $entry;
            }
        }
        $checklist[] = 'Summarize changed behavior, affected area, and executed commands.';

        $lines[] = '';
        $lines[] = 'Suggested checklist:';
        foreach ($checklist as $entry) {
            $lines[] = '- ' . $entry;
        }

        // The brief is assembled from bundled knowledge alone, so everything
        // that depends on the working tree is the agent's job. Saying which
        // parts those are — and how to get them — is more useful than letting
        // the checklist read as if the brief had already looked.
        $checkoutDiscovery = Scope::read()['checkoutDiscovery'];
        $lines[] = '';
        $lines[] = 'Establish in your checkout — this server cannot see it:';
        foreach ($checkoutDiscovery as $entry) {
            $lines[] = '- ' . $entry['establish'] . "\n  " . $entry['how'];
        }

        $nextTools = self::nextTools($intents, $domains);
        $lines[] = '';
        $lines[] = 'Next lookups for this task:';
        foreach ($nextTools as $suggestion) {
            $lines[] = '- ' . $suggestion['tool']
                . ($suggestion['when'] === '' ? '' : ' ' . $suggestion['when']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task,
            'area' => $area === '' ? null : $area,
            'changeType' => $changeType,
            'domains' => $domains,
            'intents' => array_map(static fn(array $intent): array => [
                'id' => (string) $intent['id'],
                'title' => (string) $intent['title'],
            ], $intents),
            'architectureHints' => self::hintRecords($architecture['matchedHints']),
            'rules' => self::matchRecords($rules),
            'checks' => $checks,
            'testSuites' => self::suiteRecords($testHints),
            'checklist' => $checklist,
            'checkoutDiscovery' => $checkoutDiscovery,
            'nextTools' => $nextTools,
        ]);
    }

    /**
     * Routes to the specialised tools, so an agent that starts here learns that
     * they exist instead of writing markup or label keys from memory.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @return array<int, array{tool: string, when: string}>
     */
    private static function nextTools(array $intents, array $domains): array
    {
        $candidates = [];
        foreach ($intents as $intent) {
            foreach ($intent['tools'] as $tool) {
                $candidates[] = (string) $tool;
            }
        }

        if (in_array(Domains::FRONTEND, $domains, true)) {
            $candidates[] = 'typo3_component_lookup, before writing backend markup or CSS classes';
        }
        $candidates[] = 'typo3_architecture_lookup with the concrete file paths, once they are known';
        $candidates[] = 'typo3_test_run_guide, for the targeted runTests.sh invocation';
        if (Feedback::isAvailable()) {
            $candidates[] = 'typo3_make_me_better, when one of these answers was wrong or incomplete';
        }

        // One entry per tool: an intent that already suggested a tool keeps its
        // own wording, the generic fallback for that tool is dropped.
        $suggestions = [];
        foreach ($candidates as $candidate) {
            $tool = strtok($candidate, ' ,');
            if ($tool === false || isset($suggestions[$tool])) {
                continue;
            }
            $suggestions[$tool] = [
                'tool' => $tool,
                'when' => ltrim(substr($candidate, strlen($tool))),
            ];
        }

        return array_values($suggestions);
    }

    /**
     * Matched architecture hints as data, without the internal match patterns.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    private static function hintRecords(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'id' => (string) $hint['id'],
            'title' => (string) $hint['title'],
            'category' => (string) $hint['category'],
            'hints' => array_map('strval', $hint['hints']),
            'checks' => array_map('strval', $hint['checks']),
        ], array_values($hints));
    }

    /**
     * @param array<int, array{suite: string, command: string, description: string, whenToUse: string, domains: array<int, string>, targeted: ?string}> $hints
     * @return array<int, array<string, mixed>>
     */
    private static function suiteRecords(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'suite' => $hint['suite'],
            'command' => $hint['command'],
            'targeted' => $hint['targeted'],
            'description' => $hint['description'],
            'whenToUse' => $hint['whenToUse'],
            'domains' => $hint['domains'],
        ], array_values($hints));
    }

    /** @param array<string, mixed> $args */
    private static function testRunGuide(array $args): ToolResult
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

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'suites' => self::suiteRecords($hints),
            'invocation' => TestSuiteHints::invocation(),
        ]);
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
    private static function architectureLookup(array $args): ToolResult
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

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task === '' ? null : $task,
            'paths' => array_values($paths),
            'domains' => $result['domains'],
            'hints' => self::hintRecords($result['matchedHints']),
            'knowledgeSections' => self::matchRecords($result['knowledgeSections']),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function componentLookup(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $components = Components::find($query);

        if ($components === []) {
            return ToolResult::create(
                sprintf(
                    "No TYPO3 component matched \"%s\". Try a component name (badge, card), a class (input-group), or a topic (search box). %s\n%s",
                    (string) $query,
                    self::CATALOG_MISS_NOTE,
                    self::catalogProvenance(),
                ),
                [
                    'query' => $query,
                    'matchCount' => 0,
                    'components' => [],
                    'catalog' => self::catalogRecord(),
                ],
            );
        }

        // A specific query returns the best matches; an empty query lists names.
        if ($query === null || trim($query) === '') {
            $names = implode("\n", array_map(
                static fn(array $c): string => '- ' . $c['name'] . ' — ' . $c['title'],
                $components
            ));

            return ToolResult::create("TYPO3 backend component catalog:\n" . $names, [
                'query' => $query,
                'matchCount' => count($components),
                // The listing is an overview, so the entries stay lean; query a
                // component by name for its markup and class contract.
                'components' => array_map(static fn(array $c): array => [
                    'name' => $c['name'],
                    'title' => $c['title'],
                    'summary' => $c['summary'],
                    'rootClass' => $c['rootClass'],
                    'sassPath' => $c['sassPath'],
                    'demoPath' => $c['demoPath'],
                ], $components),
                'catalog' => self::catalogRecord(),
            ]);
        }

        // Only the best matches are described in full; the rest stay in the count.
        $described = array_slice($components, 0, 3);

        $blocks = array_map(static function (array $c): string {
            $lines = ['## ' . $c['title'] . ' (`' . $c['rootClass'] . '`)'];
            if (($c['matchedIn'] ?? []) !== []) {
                $lines[] = 'Matched in: ' . implode(', ', $c['matchedIn']);
            }
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

            $lines[] = $c['sassPath'] === null
                ? 'Sass source: none — this is a web component and carries its styles in its element source.'
                : 'Sass source: ' . $c['sassPath'];
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
        }, $described);

        $checklist = Components::checklist();
        $checklistLines = ['## ' . $checklist['title']];
        if ($checklist['intro'] !== '') {
            $checklistLines[] = $checklist['intro'];
        }
        foreach ($checklist['items'] as $item) {
            $checklistLines[] = '- [ ] ' . $item;
        }
        $blocks[] = implode("\n", $checklistLines);
        $blocks[] = self::catalogProvenance();

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'matchCount' => count($components),
            'components' => array_map(static fn(array $c): array => [
                'name' => $c['name'],
                'title' => $c['title'],
                'summary' => $c['summary'],
                'rootClass' => $c['rootClass'],
                'variants' => $c['variants'],
                'modifiers' => $c['modifiers'],
                'subComponents' => $c['subComponents'],
                'customProperties' => $c['customProperties'],
                'markup' => $c['markup'],
                'examples' => $c['examples'],
                'sassPath' => $c['sassPath'],
                'demoPath' => $c['demoPath'],
                'matchedIn' => $c['matchedIn'] ?? [],
            ], $described),
            'checklist' => $checklist,
            'catalog' => self::catalogRecord(),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function iconLookup(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $limit = (int) ($args['limit'] ?? 40);

        if ($query === null || trim($query) === '') {
            $lines = ['Icon categories (query an identifier fragment or a concept to list icons):'];
            foreach (Icons::categories() as $category) {
                $lines[] = '- ' . $category;
            }
            $lines[] = '';
            $lines[] = 'Concept keywords that map to icons: ' . implode(', ', array_keys(Icons::concepts())) . '.';
            $lines[] = '';
            $lines[] = self::catalogProvenance();

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'icons' => [],
                'categories' => Icons::categories(),
                'concepts' => array_keys(Icons::concepts()),
                'catalog' => self::catalogRecord(),
            ]);
        }

        $matches = Icons::find($query);
        if ($matches === []) {
            return ToolResult::create(
                sprintf(
                    "No TYPO3 icon identifier matched \"%s\". Identifiers follow the <category>-<name> convention "
                    . "and spell the shape, not the intent — try a concept keyword such as %s.\n%s",
                    $query,
                    implode(', ', array_slice(array_keys(Icons::concepts()), 0, 8)),
                    self::catalogProvenance(),
                ),
                [
                    'query' => $query,
                    'matchCount' => 0,
                    'icons' => [],
                    'concepts' => array_keys(Icons::concepts()),
                    'catalog' => self::catalogRecord(),
                ],
            );
        }

        $total = count($matches);
        $shown = array_slice($matches, 0, $limit);
        $lines = array_map(static function (array $icon): string {
            $line = '- ' . $icon['identifier'] . '  (' . $icon['category'] . ')';
            if ($icon['aliasOf'] !== null) {
                $line .= "\n  alias of " . $icon['aliasOf'];
            }
            if ($icon['why'] !== []) {
                $line .= "\n  matched: " . implode(', ', $icon['why']);
            }

            return $line;
        }, $shown);

        $header = sprintf('%d icon identifier(s) matched "%s"', $total, $query);
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d, refine the query to narrow down', count($shown));
        }

        return ToolResult::create(
            $header . ":\n" . implode("\n", $lines) . "\n\n" . self::catalogProvenance(),
            [
                'query' => $query,
                'matchCount' => $total,
                'icons' => array_map(static fn(array $icon): array => [
                    'identifier' => $icon['identifier'],
                    'category' => $icon['category'],
                    'aliasOf' => $icon['aliasOf'],
                    'matched' => $icon['matched'],
                    'score' => $icon['score'],
                    'why' => $icon['why'],
                ], $shown),
                'catalog' => self::catalogRecord(),
            ],
        );
    }

    /** @param array<string, mixed> $args */
    private static function catalogStatus(array $args): ToolResult
    {
        $meta = CatalogMeta::read();

        $lines = [
            'Catalog provenance',
            '- Source: ' . $meta['source']['repository'],
            '- Branch: ' . $meta['source']['branch'] . ' (TYPO3 ' . $meta['source']['version'] . ')',
            '- Commit: ' . $meta['source']['commit'],
            '- Verified: ' . $meta['verifiedAt'],
            '- Re-check with: `' . $meta['verifyCommand'] . '`',
            '',
            'Scope',
        ];
        foreach ($meta['scope'] as $catalog => $scope) {
            $lines[] = '- ' . $catalog . ': ' . $scope;
        }

        $lines[] = '';
        $lines[] = 'Counts';
        foreach ($meta['counts'] as $name => $count) {
            $lines[] = '- ' . $name . ': ' . $count;
        }

        $lines[] = '';
        $lines[] = 'A lookup that finds nothing means the entry is not in this snapshot. On a different core '
            . 'branch — a 13.4 backport, for example — verify against the checkout before concluding that an '
            . 'identifier or label does not exist.';

        return ToolResult::create(implode("\n", $lines), [
            'catalog' => self::catalogRecord(),
            'verifyCommand' => $meta['verifyCommand'],
            'scope' => $meta['scope'],
            'counts' => $meta['counts'],
        ]);
    }

    private static function catalogProvenance(): string
    {
        return CatalogMeta::line();
    }

    /**
     * The provenance every catalog answer carries, so a client can tell a miss
     * on an old snapshot from a miss on the branch it works on.
     *
     * @return array<string, string>
     */
    private static function catalogRecord(): array
    {
        $meta = CatalogMeta::read();

        return [
            'repository' => $meta['source']['repository'],
            'branch' => $meta['source']['branch'],
            'version' => $meta['source']['version'],
            'commit' => $meta['source']['commit'],
            'verifiedAt' => $meta['verifiedAt'],
        ];
    }

    /** @param array<string, mixed> $args */
    private static function labelLookup(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $mode = (string) ($args['mode'] ?? 'keys');
        $limit = (int) ($args['limit'] ?? 25);

        if ($mode === 'domains') {
            $domains = Labels::domains($query);
            if ($domains === []) {
                return ToolResult::create(
                    sprintf('No registered label domain matched "%s".', (string) $query),
                    [
                        'query' => $query,
                        'mode' => $mode,
                        'matchCount' => 0,
                        'domains' => [],
                        'catalog' => self::catalogRecord(),
                    ],
                );
            }
            $shownDomains = array_slice($domains, 0, $limit);
            $lines = array_map(
                static fn(array $d): string => sprintf(
                    "- %s  (%d labels)\n  %s",
                    $d['domain'],
                    $d['count'],
                    $d['ref'],
                ),
                $shownDomains
            );

            return ToolResult::create(
                sprintf("%d label domain(s):\n", count($domains)) . implode("\n", $lines),
                [
                    'query' => $query,
                    'mode' => $mode,
                    'matchCount' => count($domains),
                    'domains' => array_map(static fn(array $d): array => [
                        'domain' => $d['domain'],
                        'ref' => $d['ref'],
                        'ext' => $d['ext'],
                        'file' => $d['file'],
                        'count' => $d['count'],
                    ], $shownDomains),
                    'catalog' => self::catalogRecord(),
                ],
            );
        }

        if ($query === null || trim($query) === '') {
            return ToolResult::create(
                'Provide a query to search labels, or pass mode "domains" to list registered translation domains.',
                [
                    'query' => $query,
                    'mode' => $mode,
                    'matchCount' => 0,
                    'labels' => [],
                    'catalog' => self::catalogRecord(),
                ],
            );
        }

        $labels = Labels::find($query);
        $relaxed = false;
        if ($labels === []) {
            // Nothing matched every term; fall back to any-term matching rather
            // than claiming the label does not exist.
            $labels = Labels::find($query, false);
            $relaxed = $labels !== [];
        }

        if ($labels === []) {
            return ToolResult::create(
                sprintf(
                    'No TYPO3 core label matched "%s". Try words from the key or its English text. %s',
                    $query,
                    self::CATALOG_MISS_NOTE,
                ),
                [
                    'query' => $query,
                    'mode' => $mode,
                    'matchCount' => 0,
                    'relaxed' => false,
                    'labels' => [],
                    'catalog' => self::catalogRecord(),
                ],
            );
        }

        $total = count($labels);
        $shown = array_slice($labels, 0, $limit);
        $lines = array_map(static function (array $label): string {
            $line = '- ' . $label['ref'];
            $line .= "\n  \"" . $label['source'] . '"';
            $line .= "\n  legacy: " . $label['legacyRef'];
            $line .= "\n  matched in: " . implode(', ', $label['matchedIn']);
            if ($label['unusedSince'] !== null) {
                $line .= "\n  RETIRED: marked x-unused-since=\"" . $label['unusedSince']
                    . '" — do not use it in new code, and do not delete the trans-unit either.';
            }

            return $line;
        }, $shown);

        $header = sprintf('%d label(s) matched "%s"', $total, $query);
        if ($relaxed) {
            $header .= ' on at least one query term (no label matched all of them)';
        }
        if ($total > count($shown)) {
            $header .= sprintf(' — showing the top %d', count($shown));
        }

        $text = $header . ":\n" . implode("\n", $lines)
            . "\n\nReference labels by the domain form shown first (package.resource:key). "
            . 'It works in TCA labels and descriptions, LanguageService::sL(), f:translate (domain= and key=), '
            . "and registration configs.\n" . self::catalogProvenance();

        return ToolResult::create($text, [
            'query' => $query,
            'mode' => $mode,
            'matchCount' => $total,
            'relaxed' => $relaxed,
            'labels' => array_map(static fn(array $label): array => [
                'ref' => $label['ref'],
                'legacyRef' => $label['legacyRef'],
                'key' => $label['id'],
                'source' => $label['source'],
                'unusedSince' => $label['unusedSince'],
                'matchedIn' => $label['matchedIn'],
            ], $shown),
            'catalog' => self::catalogRecord(),
        ]);
    }

    /** @param array<string, mixed> $args */
    private static function commitMessageGuide(array $args): ToolResult
    {
        $existing = isset($args['message']) ? trim((string) $args['message']) : '';

        $parseChecks = [];
        if ($existing !== '') {
            $parsed = CommitMessage::parse($existing);
            // Explicit arguments still win, so a message can be checked and
            // amended in one call: pass the message plus issue=12345.
            $input = array_merge($parsed['input'], array_intersect_key($args, array_flip([
                'changeType', 'summary', 'issue', 'relatedIssues', 'releases', 'isBreaking', 'isDeprecation',
            ])));
            $parseChecks = $parsed['checks'];
        } else {
            $input = $args;
        }

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

        return ToolResult::create(implode("\n", $lines), [
            'message' => $result['message'],
            'checks' => $checks,
        ]);
    }
}
