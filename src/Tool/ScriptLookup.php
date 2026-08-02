<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Result\Prose;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * The TYPO3 core's own scripts and commands, by task.
 */
final class ScriptLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_script_lookup';
    }

    public static function description(): string
    {
        return 'Find notes for TYPO3 core scripts and commands. They are the core checkout\'s own: a query that reads as a project or third-party extension is answered with the boundary instead of with commands that do not exist there.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task' => ['type' => 'string', 'minLength' => 1, 'description' => 'The TYPO3 core task, in English, for example unit tests, functional tests, CGL, npm, or dependency install.'],
            ],
            'required' => ['task'],
        ];
    }

    /**
     * The knowledge lookup shape plus the boundary, because every command in
     * the script note is a command in the core repository.
     */
    public static function outputSchema(): array
    {
        $schema = Schema::knowledgeLookup();
        $schema['properties']['scope'] = Schema::scope();
        $schema['required'][] = 'scope';

        return $schema;
    }

    public static function answer(array $args): ToolResult
    {
        $task = (string) ($args['task'] ?? '');

        // Every command in these feedback runs in a core checkout. Handing them to
        // a repository that has none is the same mistake typo3_test_run_guide
        // used to make, and the same answer applies. This tool is asked about a
        // task rather than about paths, so the call has one scope.
        $scope = Scope::of('', $task);
        if ($scope->isOutsideTheCore()) {
            return ToolResult::create(
                Scope::OUTSIDE_CORE_NOTICE . ' The scripts these notes describe are the core checkout\'s own, so '
                . 'none is returned. What to run here is declared in this repository: its composer.json scripts, '
                . 'its package.json, its CI configuration.',
                ['query' => $task, 'matchCount' => 0, 'matches' => [], 'scope' => $scope->value],
            );
        }

        $results = Documents::search($task, ['typo3-core-scripts']);

        if ($results !== []) {
            $text = Prose::sections($results);
            // Where nothing said which repository this is, the commands are
            // offered under their condition rather than stated as the answer.
            if (!Scope::isCoreWork([], $task)) {
                $text .= "\n\nThese commands run in a TYPO3 core checkout. In any other repository, what to run is "
                    . 'declared in its own composer.json, package.json and CI configuration.';
            }

            return ToolResult::create($text, [
                'query' => $task,
                'matchCount' => count($results),
                'matches' => Prose::records($results),
                'scope' => $scope->value,
            ]);
        }

        // Nothing about scripts matched. Say so, and route to the documents that
        // do cover the topic instead of answering with the nearest script prose.
        $message = sprintf(
            'No section of the TYPO3 core script notes matched "%s". They cover: %s.',
            $task,
            Prose::topics('typo3-core-scripts')
        );

        $elsewhere = Documents::search($task);
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
            'scope' => $scope->value,
        ]);
    }
}
