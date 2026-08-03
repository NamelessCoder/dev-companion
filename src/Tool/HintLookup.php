<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Hints;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\MatchedHints;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\VersionScope;

/**
 * Hints for TYPO3 core paths or task topics, grouped by section.
 */
final class HintLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_hint_lookup';
    }

    public static function description(): string
    {
        return 'Return hints for TYPO3 core paths or task topics, grouped by section. Where the paths read as a project or third-party extension the hints still come back, because the conventions transfer. The "Backend CSS" and "Backend TypeScript and JavaScript" sections describe the TYPO3 backend interface and are withheld, with the reason, where the task names the frontend.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'default' => [], 'description' => 'File paths related to the task, as they are in the repository they belong to. Each is placed on its own, so a core path and an extension path in one call are matched separately, and a statement is labelled where it obliges the other one.'],
                'task' => ['type' => 'string', 'description' => 'Short task description or topic, in English. Matching is lexical against English text, so another language reaches only the loanwords.'],
                'id' => ['type' => 'string', 'description' => 'Ask for one hint by its id, for example language-files, instead of matching. Every answer that returns no hint lists the ids there are, so a subject that exists can be requested by name rather than guessed at.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold for, for example "13.4" or "14". Statements that do not hold there are left out, including those the repository needs for another major it declares. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server was started in where there is no declaration; where there is neither, nothing is filtered and every statement carries the versions it holds for.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6, 'description' => 'Maximum number of hints.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'task' => Schema::nullableString(),
            'paths' => Schema::listOf(Schema::string()),
            'scopes' => Schema::scopes('Which kind of work each path is. Paths of different scope are matched separately, so a hint that came back for one of them is about that path.'),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this repository runs — stated by the caller, or read from the installation. Null means nothing was filtered and every statement carries its own range. Where the repository serves several majors, targetVersions is what the answer holds for.'],
            'targetVersions' => Schema::listOf(['type' => 'integer'], 'Every TYPO3 major the answer holds for. One entry is the ordinary case. Several mean this repository declares typo3/cms-core for more than one of them, so a statement was kept when it holds on any — and where two statements about the same subject differ, the difference is the constraint the code lives under rather than drift. Empty when nothing was filtered by version.'),
            'domains' => Schema::listOf(Schema::string(), 'Hints outside these domains are not returned.'),
            'withheldCategories' => Schema::listOf(Schema::string(), 'Categories that matched the domains but were left out because the task names the frontend. "Backend CSS" and "Backend TypeScript and JavaScript" describe the TYPO3 backend interface and are wrong advice for what a website renders; see docs.typo3.org for frontend theming.'),
            'hints' => Schema::listOf(Schema::hintRecord()),
            'availableHints' => Schema::listOf(Schema::object([
                'id' => Schema::string('Ask for this hint outright by passing it as id.'),
                'title' => Schema::string(),
                'category' => Schema::string(),
            ], ['id', 'title', 'category']), 'The hints that exist in the searched domains, returned when none matched. Empty on a hit.'),
        ], ['paths', 'domains', 'withheldCategories', 'scopes', 'hints', 'availableHints']);
    }

    public static function answer(array $args): ToolResult
    {
        $paths = array_map('strval', $args['paths'] ?? []);
        $task = isset($args['task']) ? (string) $args['task'] : null;
        $limit = (int) ($args['limit'] ?? 6);
        $id = isset($args['id']) ? trim((string) $args['id']) : '';
        $stated = isset($args['targetVersion']) ? (string) $args['targetVersion'] : null;
        $target = Versions::target($stated);
        $targets = Versions::targets($stated);

        // Paths of different scope are asked separately, so a statement that
        // declares whose it is can be labelled against the paths it was matched
        // for. Matched together, an extension path would be answered under the
        // core path's scope.
        $scopes = Scope::ofEach($paths, $task ?? '');
        $groups = Scope::groups($paths, $scopes, $task ?? '');
        $outside = Scope::pathsOf($scopes, Scope::Project, Scope::Extension);
        $outsideCore = count($groups) === 1 && $groups[0]['scope']->isOutsideTheCore();

        $found = [];
        foreach ($groups as $group) {
            $matched = Hints::find($group['paths'], $task ?? '', $limit, $id, $targets);
            $found[] = ['scope' => $group['scope'], 'paths' => $group['paths'], 'result' => $matched];
        }
        $result = MatchedHints::merged($found);

        $lines = [];
        if ($outsideCore) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE . ' The hints below are conventions that may transfer. '
                . 'typo3_server_scope states the boundary.';
            $lines[] = '';
        } elseif ($outside !== []) {
            $lines[] = Scope::outsideCoreAmong($outside) . ' The conventions matched there transfer.';
            $lines[] = '';
        }
        if (Scope::pathsOf($scopes, Scope::Uncertain) !== []) {
            $lines[] = Scope::UNCERTAIN_NOTICE;
            $lines[] = '';
        }
        if ($result['withheldCategories'] !== []) {
            $lines[] = sprintf(
                'This task names the frontend, so %s is withheld: it describes the TYPO3 backend interface — its '
                . 'Sass sources, its --typo3-* properties, its color schemes — and would be inverted advice for '
                . 'what a website renders. Frontend theming: https://docs.typo3.org. Name the backend in the task '
                . 'if you are styling a backend module, or the styleguide if the work is a backend component and '
                . 'its demo.',
                implode(' and ', $result['withheldCategories']),
            );
            $lines[] = '';
        }
        if ($id !== '') {
            $lines[] = 'Hint requested by id: ' . $id;
        }
        if ($task !== null && $task !== '') {
            $lines[] = 'Task: ' . $task;
        }
        if ($paths !== []) {
            $lines[] = "Paths:\n" . implode("\n", array_map(
                static fn(array $entry): string => '- ' . $entry['path']
                    . ($entry['scope'] === Scope::Core ? '' : ' (' . $entry['scope']->value . ')'),
                $scopes,
            ));
        }
        $lines[] = VersionScope::line($targets);
        if ($result['domains'] !== []) {
            $lines[] = 'Domains: ' . implode(', ', $result['domains'])
                . ' (hints outside these domains are not shown'
                . ($result['withheldCategories'] === []
                    ? ')'
                    : ', and ' . implode(' and ', $result['withheldCategories']) . ' was withheld inside them)');
        }
        $lines[] = '';
        $lines[] = 'Hints:';

        if ($result['matchedHints'] !== []) {
            // One block per scope, and the heading only where there is more
            // than one of them: the caller asked about two repositories, and
            // which half of the answer is about which path is the answer.
            $sectionTexts = [];
            foreach ($found as $group) {
                if ($group['result']['matchedHints'] === []) {
                    continue;
                }
                if (count($found) > 1) {
                    $sectionTexts[] = sprintf(
                        '# For %s%s',
                        implode(' and ', $group['paths']),
                        $group['scope'] === Scope::Core ? '' : ' — ' . $group['scope']->value,
                    );
                }
                $sectionTexts[] = MatchedHints::sections(
                    $group['result']['matchedHints'],
                    $group['scope'],
                    $target,
                );
            }
            $lines[] = implode("\n\n", $sectionTexts);
        } elseif ($result['withheldCategories'] !== []) {
            $lines[] = 'Nothing is left to show: the only domain this task touched is one this server answers for '
                . 'the backend alone.';
        } elseif ($id !== '') {
            $lines[] = sprintf('There is no hint with the id "%s".', $id);
        } else {
            $lines[] = 'No hint matched. Name a path or a more specific topic, or ask for one of the ids below.';
        }

        // The index is the difference between "nothing matched your words" and
        // "nobody wrote this down". Without it both answers read the same, and
        // the caller tries another phrasing for a subject that does not exist —
        // or gives up on one that does.
        if ($result['availableHints'] !== []) {
            $lines[] = '';
            $lines[] = $id !== ''
                ? 'The ids there are:'
                : 'Hints that exist in these domains, requestable by id:';
            foreach ($result['availableHints'] as $entry) {
                $lines[] = '- ' . $entry['id'] . ' — ' . $entry['title'] . ' (' . $entry['category'] . ')';
            }
        }

        return ToolResult::create(implode("\n", $lines), [
            'task' => $task === '' ? null : $task,
            'paths' => array_values($paths),
            'scopes' => $scopes,
            'targetVersion' => $target,
            'targetVersions' => $targets,
            'domains' => $result['domains'],
            'withheldCategories' => $result['withheldCategories'],
            'hints' => MatchedHints::records($result['matchedHints']),
            'availableHints' => $result['availableHints'],
        ]);
    }
}
