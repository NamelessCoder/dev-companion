<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Knowledge\Domains;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\TaskIntents;
use Typo3CmsMcp\Knowledge\TestSuiteHints;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\Hints;
use Typo3CmsMcp\Result\Prose;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\VersionScope;

/**
 * A task checklist, enriched with the architecture hints and core checks that
 * match it.
 */
final class TaskGuide extends ReadOnlyTool
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

    /**
     * The tool that answers a matched subject from the installation instead of
     * from memory. These are invented or misregistered in bulk when nobody
     * points at them, and fail at runtime rather than at build time.
     *
     * @var array<string, string>
     */
    private const HINT_TOOLS = [
        'backend-modules' => 'typo3_backend_module_lookup, to compare the declaration with modules registered '
            . 'by the active installation',
        'language-files' => 'typo3_label_lookup with the XLF resource used at the consuming code, while writing '
            . 'labels: a matching label elsewhere in the installation is not reusable in that context',
        'icon-usage' => 'typo3_icon_lookup, before writing an icon identifier: an unknown one renders an empty box',
    ];

    public static function name(): string
    {
        return 'typo3_task_guide';
    }

    public static function description(): string
    {
        return 'Build a task checklist enriched with matching architecture hints and relevant core checks. Built from bundled conventions only: it does not read your checkout, so it also names what you have to establish there yourself and routes to the lookups that fit the task. Work that reads as a project or third-party extension is answered with what transfers only — the core checks, checklist items and steps that name something only the core repository has are left out rather than handed over.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'Short description of the TYPO3 core task, in English.'],
                'area' => ['type' => 'string', 'description' => 'Affected subsystem or extension, if known.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version this task is for, for example "13.4" or "14". State one only to narrow to it: conventions that do not hold there are then left out, including those the repository needs for another major it declares. Left out, the answer holds for every major this repository declares typo3/cms-core for, which is what one codebase serving two of them needs; where there is no declaration to read, for the version of the installation this server was started in.'],
                'changeType' => ['type' => 'string', 'enum' => ['bugfix', 'feature', 'cleanup', 'test', 'documentation', 'unknown'], 'default' => 'unknown'],
            ],
            'required' => ['task'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'task' => Schema::string(),
            'area' => Schema::nullableString('Affected subsystem or path, if one was given.'),
            'changeType' => Schema::string(),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this repository runs — stated by the caller, or read from the installation. Null means nothing was filtered by version. Where the repository serves several majors, targetVersions is what the answer holds for.'],
            'targetVersions' => Schema::listOf(['type' => 'integer'], 'Every TYPO3 major the answer holds for. One entry is the ordinary case. Several mean this repository declares typo3/cms-core for more than one of them, so a statement was kept when it holds on any — and where two statements about the same subject differ, the difference is the constraint the code lives under rather than drift. Empty when nothing was filtered by version.'),
            'domains' => Schema::listOf(Schema::string()),
            'scope' => Schema::scope('Which kind of work the task reads as. Anything but core means the answer holds core conventions that may transfer, not a checklist for the task.'),
            'intents' => Schema::listOf(Schema::object([
                'id' => Schema::string(),
                'title' => Schema::string(),
                'confidence' => ['type' => 'string', 'enum' => ['strong', 'weak'], 'description' => 'weak: a word named the subject without naming the work, or the intent is a core-only one and nothing in the task says this is core work. Either way it applies only under its condition.'],
                'condition' => Schema::string('When a weakly matched intent applies. Empty for a strong match.'),
            ], ['id', 'title', 'confidence', 'condition']), 'The kinds of core work recognized in the task text.'),
            'architectureHints' => Schema::listOf(Schema::architectureHintRecord()),
            'rules' => Schema::listOf(Schema::knowledgeMatch(), 'Rule sections that apply to this task.'),
            'checks' => Schema::listOf(Schema::string(), 'Commands to run, ready to execute from the core root.'),
            'conditionalChecks' => Schema::listOf(Schema::object([
                'title' => Schema::string(),
                'condition' => Schema::string(),
                'checks' => Schema::listOf(Schema::string()),
            ], ['title', 'condition', 'checks']), 'Checks that only apply if the task really is the kind of work a weakly matched intent suggests.'),
            'testSuites' => Schema::listOf(Schema::testSuiteRecord()),
            'checklist' => Schema::listOf(Schema::string()),
            'checkoutDiscovery' => Schema::listOf(Schema::object([
                'establish' => Schema::string(),
                'how' => Schema::string(),
            ], ['establish', 'how']), 'What this server cannot see and the agent has to establish itself.'),
            'nextTools' => Schema::listOf(Schema::object([
                'tool' => Schema::string(),
                'when' => Schema::string(),
            ], ['tool', 'when'])),
        ], ['task', 'changeType', 'domains', 'architectureHints', 'checks', 'checklist', 'nextTools']);
    }

    public static function answer(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');
        $area = isset($args['area']) ? trim((string) $args['area']) : '';
        $changeType = (string) ($args['changeType'] ?? 'unknown');

        $subject = trim($task . ' ' . $area);
        $paths = $area === '' ? [] : [$area];
        $domains = Domains::detect($paths, $task . ' ' . (self::CHANGE_TYPE_TERMS[$changeType] ?? ''));

        // Several of the conventions below — the changelog, the Gerrit
        // workflow, the runTests.sh suites — do not exist outside the core, so
        // handing them over as a checklist for a project extension is worse
        // than saying the question is outside what this server knows. One area
        // is one question: this tool cannot be asked about two at once, which
        // is why it decides once where the path tools decide per path.
        $scope = Scope::of($area, $subject, $area);
        $outsideCore = $scope->isOutsideTheCore();

        $intents = TaskIntents::scoped(
            TaskIntents::detect($subject . ' ' . $changeType),
            $scope,
            Scope::isCoreWork($paths, $subject)
        );
        $confirmed = TaskIntents::confirmed($intents);
        $conditional = array_values(array_filter(
            $intents,
            static fn(array $intent): bool => !in_array($intent, $confirmed, true)
        ));

        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);
        $architecture = ArchitectureHints::find($paths, $task, 4, null, $targets);
        $testHints = array_slice(TestSuiteHints::find($subject, $domains, $target), 0, 4);
        if ($outsideCore) {
            $architecture['matchedHints'] = ArchitectureHints::withoutChecks($architecture['matchedHints']);
        }

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' Take what follows as conventions that may transfer, not as '
                . 'a checklist for this task. '
                . 'typo3_server_scope states the boundary.';
            $lines[] = '';
        }
        // The checklist below is the one payload of this server that states a
        // process as the process, so where nothing placed the work it says so
        // before stating it — the changelog and the Gerrit route are steps a
        // caller in their own repository cannot take at all.
        if ($scope === Scope::Uncertain) {
            $lines[] = Scope::UNCERTAIN_NOTICE . ' Name the area or the path, and this brief is composed '
                . 'for the repository it is in.';
            $lines[] = '';
        }

        $lines = array_merge($lines, [
            'Task: ' . $task,
            'Area: ' . ($area === '' ? 'unknown' : $area),
            'Change type: ' . $changeType,
            'Domains: ' . implode(', ', $domains),
        ]);
        // Silent on the ordinary task, where one version is the whole question
        // and saying so is noise. It speaks for the repository that serves
        // several majors — whether the answer holds for all of them, or was
        // narrowed to one because the caller stated it.
        if (count($targets) > 1 || VersionScope::severalDeclared() !== []) {
            $lines[] = VersionScope::line($targets);
        }
        if ($confirmed !== []) {
            $lines[] = 'Recognized as: ' . implode(', ', array_map(
                static fn(array $intent): string => (string) $intent['title'],
                $confirmed
            ));
        }
        foreach ($conditional as $intent) {
            $lines[] = 'Possibly also: ' . $intent['title'] . ', ' . $intent['condition']
                . '. Its checklist items are marked as conditional below and its checks are listed separately.';
        }

        $lines[] = '';
        $lines[] = 'Architecture hints:';
        if ($architecture['matchedHints'] !== []) {
            $examples = Hints::examples($target);
            foreach (ArchitectureHints::groupByCategory($architecture['matchedHints']) as $section) {
                $lines[] = '### ' . $section['category'];
                foreach ($section['hints'] as $hint) {
                    $lines[] = '## ' . $hint['title'];
                    $notice = Hints::scopeNotice($hint, $outsideCore);
                    if ($notice !== null) {
                        $lines[] = $notice;
                    }
                    foreach ($hint['hints'] as $entry) {
                        $lines[] = '- ' . Hints::statement($entry, $outsideCore);
                    }
                    if (isset($examples[$hint['id']])) {
                        $lines[] = $examples[$hint['id']];
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

        // Only the confirmed intents may state a rule as applying: a
        // conditionally matched one would fill the whole section with rules for
        // work the task may not contain at all.
        $rules = TaskIntents::rules($confirmed);
        if ($rules !== []) {
            $lines[] = '';
            $lines[] = 'Rules that apply to this task:';
            $lines[] = '';
            $lines[] = Prose::sections($rules);
        }

        // The checks of a matched architecture hint belong in the list as much
        // as the ones an intent carries. Leaving them out dropped the functional
        // suite from a FormEngine brief while the FormEngine hint that names it
        // was right there in the same answer.
        $checks = self::mergedChecks($confirmed, $architecture['matchedHints'], $target);
        $conditionalChecks = self::conditionalChecks($conditional, $checks, $target);

        // Every check this server knows is a runTests.sh invocation against a
        // script in the core repository. Reporting a scope outside the core and then listing
        // four of them was the whole complaint: the flag said the answer knew,
        // and the payload said it had not acted on it.
        if ($outsideCore) {
            $checks = [];
            $conditionalChecks = [];
            $testHints = [];
        }

        $lines[] = '';
        if ($outsideCore) {
            $lines[] = 'Checks: none of the core\'s own apply here, so none is listed. Verify with what this '
                . 'repository provides — the scripts in its composer.json, its package.json, and its CI '
                . 'configuration are where its own suites are declared.';
        } else {
            $lines[] = 'Relevant TYPO3 core checks:';
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
            } elseif ($checks === []) {
                $lines[] = '- No topic-specific check matched. Run the narrowest relevant suite, then broaden before review.';
            }

            foreach ($conditionalChecks as $entry) {
                $lines[] = '';
                $lines[] = 'Checks for ' . $entry['title'] . ', ' . $entry['condition'] . ':';
                foreach ($entry['checks'] as $check) {
                    $lines[] = '- `' . $check . '`';
                }
            }
        }

        $checklist = [
            $outsideCore
                ? 'Confirm the target branch and the issue context of this repository.'
                : 'Confirm the target TYPO3 core branch and issue context.',
            'Inspect nearby code, tests, and established subsystem conventions.',
            'Keep the patch focused on the stated task.',
            'Add or update the narrowest useful test coverage.',
            'Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.',
        ];
        foreach (self::CHANGE_TYPE_CHECKLIST[$changeType] ?? [] as $entry) {
            $checklist[] = $entry;
        }
        foreach ($confirmed as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = (string) $entry;
            }
        }
        foreach ($conditional as $intent) {
            foreach ($intent['checklist'] as $entry) {
                $checklist[] = ucfirst((string) $intent['condition']) . ': ' . lcfirst((string) $entry);
            }
        }
        $checklist[] = $outsideCore
            ? 'Write the commit message with typo3_commit_message_guide and workflow="project": '
                . 'summarize the changed behavior, the affected area and the commands you ran, and it '
                . 'hands back a draft that is wrapped and checked.'
            : 'Write the commit message with typo3_commit_message_guide: summarize the changed behavior, '
                . 'the affected area and the commands you ran, and it hands back a draft that carries '
                . 'the keyword, the trailers and the wrapping.';

        // Per line, not per section: a checklist mixes "reproduce the bug with
        // a failing test" — true anywhere — with a changelog file below
        // typo3/sysext/, which is a path the caller's repository does not have.
        if ($outsideCore) {
            $checklist = array_values(array_filter(
                $checklist,
                static fn(string $entry): bool => !Scope::isCoreOnly($entry)
            ));
        }

        $lines[] = '';
        $lines[] = 'Suggested checklist:';
        foreach ($checklist as $entry) {
            $lines[] = '- ' . $entry;
        }

        // The brief is assembled from bundled knowledge alone, so everything
        // that depends on the working tree is the agent's job. Saying which
        // parts those are — and how to get them — is more useful than letting
        // the checklist read as if the brief had already looked.
        $checkoutDiscovery = Coverage::read()['checkoutDiscovery'];
        if ($outsideCore) {
            $checkoutDiscovery = array_values(array_filter(
                $checkoutDiscovery,
                static fn(array $entry): bool => !Scope::isCoreOnly($entry['establish'] . ' ' . $entry['how'])
            ));
        }
        $lines[] = '';
        $lines[] = 'Establish in your checkout — this server cannot see it:';
        foreach ($checkoutDiscovery as $entry) {
            $lines[] = '- ' . $entry['establish'] . "\n  " . $entry['how'];
        }

        $nextTools = self::nextTools(
            $intents,
            $domains,
            array_column($architecture['matchedHints'], 'id'),
            $target,
            $outsideCore
        );
        if ($outsideCore) {
            $nextTools = array_values(array_filter(
                $nextTools,
                static fn(array $suggestion): bool => !Scope::isCoreOnly($suggestion['tool'] . ' ' . $suggestion['when'])
            ));
        }
        $lines[] = '';
        $lines[] = 'Next lookups for this task:';
        foreach ($nextTools as $suggestion) {
            $lines[] = '- ' . $suggestion['tool']
                . ($suggestion['when'] === '' ? '' : ' — ' . $suggestion['when']);
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task,
            'area' => $area === '' ? null : $area,
            'changeType' => $changeType,
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $domains,
            'scope' => $scope->value,
            'intents' => array_map(static fn(array $intent): array => [
                'id' => (string) $intent['id'],
                'title' => (string) $intent['title'],
                'confidence' => (string) $intent['confidence'],
                'condition' => (string) $intent['condition'],
            ], $intents),
            'architectureHints' => Hints::records($architecture['matchedHints']),
            'rules' => Prose::records($rules),
            'checks' => $checks,
            'conditionalChecks' => $conditionalChecks,
            'testSuites' => TestSuiteHints::records($testHints),
            'checklist' => $checklist,
            'checkoutDiscovery' => $checkoutDiscovery,
            'nextTools' => $nextTools,
        ]);
    }

    /**
     * The checks a brief states as applying: those of the confirmed intents and
     * those of every matched architecture hint, in that order and deduplicated.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, string>
     */
    private static function mergedChecks(array $intents, array $hints, ?int $target): array
    {
        $checks = [];
        foreach ($intents as $intent) {
            foreach ($intent['checks'] as $check) {
                $checks[(string) $check] = true;
            }
        }
        foreach ($hints as $hint) {
            foreach ($hint['checks'] as $check) {
                $checks[(string) $check] = true;
            }
        }

        // The hints arrive already filtered; the intents do not, and both name
        // suites that a given branch's runTests.sh may not have.
        return TestSuiteHints::checksFor(array_keys($checks), $target);
    }

    /**
     * The checks of the conditionally matched intents, minus the ones already
     * stated as applying.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $stated
     * @return array<int, array{title: string, condition: string, checks: array<int, string>}>
     */
    private static function conditionalChecks(array $intents, array $stated, ?int $target): array
    {
        $entries = [];
        foreach ($intents as $intent) {
            $checks = array_values(array_diff(
                TestSuiteHints::checksFor(array_map('strval', $intent['checks']), $target),
                $stated,
            ));
            if ($checks === []) {
                continue;
            }
            $entries[] = [
                'title' => (string) $intent['title'],
                'condition' => (string) $intent['condition'],
                'checks' => $checks,
            ];
        }

        return $entries;
    }

    /**
     * Routes to the specialised tools, so an agent that starts here learns that
     * they exist instead of writing markup or label keys from memory.
     *
     * @param array<int, array<string, mixed>> $intents
     * @param array<int, string> $domains
     * @param array<int, string> $hintIds ids of the architecture hints this brief matched
     * @return array<int, array{tool: string, when: string}>
     */
    private static function nextTools(
        array $intents,
        array $domains,
        array $hintIds,
        ?int $target,
        bool $outsideCore,
    ): array {
        $candidates = [];
        foreach ($intents as $intent) {
            foreach ($intent['tools'] as $tool) {
                $candidates[] = (string) $tool;
            }
        }

        if (array_intersect([Domains::CSS, Domains::FLUID], $domains) !== []) {
            $candidates[] = 'typo3_component_lookup, before writing backend markup or CSS classes';
        }
        // A subject whose hint matched is a subject the caller is about to write
        // in, and both of these answer from the installation rather than from
        // memory. The pointer is in the hint text as well, which is exactly the
        // place nobody rereads while writing the fortieth label key.
        foreach (self::HINT_TOOLS as $hintId => $suggestion) {
            if (in_array($hintId, $hintIds, true)) {
                $candidates[] = $suggestion;
            }
        }
        $candidates[] = $target === null
            ? 'typo3_changelog_lookup, for what the version you are building on changed about this area'
            : sprintf(
                'typo3_changelog_lookup, for what %d changed about this area — the first stop when you have not '
                    . 'built on it recently, not only a lookup after the fact',
                $target
            );
        $candidates[] = 'typo3_architecture_lookup with the concrete file paths, once they are known';
        $candidates[] = 'typo3_test_run_guide, for the targeted runTests.sh invocation';
        // The one step this brief describes and never pointed at. A caller who
        // read the routing table at the start of a session is committing hours
        // later, from this list — and outside the core it is the follow-up call
        // that has to carry the workflow, because the guide defaults to the
        // core's and cannot read a repository off a commit message. The
        // checklist above says it; a list of calls that leaves it out is one
        // answer disagreeing with itself about the same step.
        $candidates[] = $outsideCore
            ? 'typo3_commit_message_guide with workflow="project", before committing — the default is the '
                . 'core\'s own and demands an issue number and a release trailer this repository has none of'
            : 'typo3_commit_message_guide, before committing';
        if (Channel::isAvailable()) {
            $candidates[] = 'typo3_feedback_record, when one of these answers was wrong or incomplete';
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
                // The candidates are written as one sentence, "tool, when", so
                // the separator has to come off with the tool name — otherwise
                // both halves carry it and the answer reads "tool , when".
                'when' => ltrim(substr($candidate, strlen($tool)), ' ,'),
            ];
        }

        return array_values($suggestions);
    }
}
