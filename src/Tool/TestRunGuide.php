<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Domains;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\TestSuiteHints;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * Which Build/Scripts/runTests.sh suites a change actually needs.
 */
final class TestRunGuide extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_test_run_guide';
    }

    public static function description(): string
    {
        return 'Recommend Build/Scripts/runTests.sh commands by topic. Pass the changed paths and the answer is narrowed to the suites that can actually fail on them — a Sass-only change gets the CSS suites, not the PHP ones. Which suites the script offers changes between majors, so a suite that branch does not have is left out rather than handed over as a command. The script belongs to the core repository, so paths that read as a project or third-party extension get no suite at all rather than commands that cannot run there.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Test or script topic, for example functional, phpstan, TypeScript, composer, or CGL.'],
                'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'The changed file paths, as they are in the repository they belong to. Given, only suites touching their domains are returned. Each path is placed on its own: one outside the core narrows nothing and is named in the answer, because runTests.sh is not in its repository.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the commands have to run on, for example "13.4" or "14". Suites that branch\'s runTests.sh does not have are left out. Defaults to the version of the installation this server was started in; where there is none, every suite is listed.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::nullableString(),
            'paths' => Schema::listOf(Schema::string(), 'The paths the answer was narrowed by, given ones and ones named in the query.'),
            'scopes' => Schema::scopes('Which kind of work each path is. Only core paths can run a suite: runTests.sh is not in a project or an extension repository, so the others are named in the answer and narrow nothing.'),
            'domains' => Schema::listOf(Schema::string(), 'Domains those paths touch. Empty means nothing was narrowed.'),
            'suites' => Schema::listOf(Schema::testSuiteRecord()),
            'invocation' => Schema::object([
                'notes' => Schema::listOf(Schema::string()),
                'options' => Schema::listOf(Schema::object([
                    'option' => Schema::string(),
                    'description' => Schema::string(),
                ], ['option', 'description'])),
                'examples' => Schema::listOf(Schema::object([
                    'purpose' => Schema::string(),
                    'command' => Schema::string(),
                ], ['purpose', 'command'])),
            ], ['notes', 'options', 'examples']),
        ], ['scopes', 'suites', 'invocation']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;

        /** @var array<int, string> $paths */
        $paths = array_map('strval', $args['paths'] ?? []);
        $paths = array_values(array_unique(array_merge($paths, Domains::pathsIn((string) $query))));
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // Every suite this guide knows is a Build/Scripts/runTests.sh
        // invocation, and that script is part of the core repository. Handing
        // it to a site package is worse than declining: the commands look
        // copy-pasteable and none of them exists there. Which paths those are
        // is decided one by one — the other half of a call is not evidence
        // about this path.
        $scopes = Scope::ofEach($paths, (string) $query);
        $outside = Scope::pathsOf($scopes, Scope::Project, Scope::Extension);
        $runnable = Scope::pathsOf($scopes, Scope::Core, Scope::Uncertain);
        $domains = Domains::fromPaths($runnable);

        // Nothing here can run a suite: every path given is outside the core,
        // or none was and what the query says is.
        $nothingRunnable = $paths === []
            ? Scope::of('', (string) $query)->isOutsideTheCore()
            : $runnable === [];
        if ($nothingRunnable) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' Build/Scripts/runTests.sh is part of the core repository, so the '
                . 'suites this guide knows cannot be run from here and are left out rather than handed over. '
                . 'What such a repository needs instead — assembling a phpunit suite of its own, the browser '
                . 'tests that go with it, and the static analysis the phpstan suite is the core\'s half of — is '
                . 'typo3_hint_lookup with id=project-extension-tests, id=browser-tests and '
                . 'id=extension-static-analysis. typo3_server_scope states the boundary.',
                [
                    'query' => $query,
                    'paths' => $paths,
                    'scopes' => $scopes,
                    'domains' => $domains,
                    'suites' => [],
                    'invocation' => ['notes' => [], 'options' => [], 'examples' => []],
                ],
            );
        }

        $hints = TestSuiteHints::find($query, $domains, $target);

        $blocks = [];
        // The other half of the same answer: the suites below are for the paths
        // that can run them, and the paths that cannot are named rather than
        // left to read the answer as theirs.
        if ($outside !== []) {
            $blocks[] = Scope::outsideCoreAmong($outside) . ' Build/Scripts/runTests.sh is not there, so no suite '
                . 'below is about ' . (count($outside) === 1 ? 'that path' : 'those paths') . '. What such a '
                . 'repository needs instead is typo3_hint_lookup with id=project-extension-tests, '
                . 'id=browser-tests and id=extension-static-analysis.';
        }
        // A call that named no path is answered from the core root, and every
        // command below says so itself. A path that was named and could not be
        // placed is the case worth a sentence: the caller believes it said
        // which repository this is.
        if (Scope::pathsOf($scopes, Scope::Uncertain) !== []) {
            $blocks[] = Scope::UNCERTAIN_NOTICE;
        }
        if ($domains !== []) {
            $blocks[] = sprintf(
                'Narrowed to the %s domain(s) the given paths touch. Suites outside them cannot fail on this change; '
                . 'call again without paths to see all of them.',
                implode(' and ', $domains)
            );
        }
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
            'paths' => $paths,
            'scopes' => $scopes,
            'domains' => $domains,
            'suites' => TestSuiteHints::records($hints),
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
}
