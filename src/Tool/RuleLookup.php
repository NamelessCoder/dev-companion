<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Knowledge;
use Typo3CmsMcp\Result\Prose;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\ToolResult;

/**
 * The local TYPO3 core contribution rules and script notes, by topic.
 */
final class RuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_rule_lookup';
    }

    public static function description(): string
    {
        return 'Search the local TYPO3 core contribution rules and script notes by topic.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Topic to look up, in English, for example testing, review, deprecation, or code style.'],
            ],
            'required' => ['query'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::knowledgeLookup();
    }

    public static function answer(array $args): ToolResult
    {
        $query = (string) ($args['query'] ?? '');
        $results = Knowledge::search($query);

        // The prose and the architecture hints are two corpora, and which one
        // holds a subject is this server's business, not the caller's: site
        // sets are a hint, the Gerrit workflow is prose, and the question is
        // phrased the same way either way.
        $hints = ArchitectureHints::find([], $query, 3)['matchedHints'];

        if ($results === [] && $hints === []) {
            return Prose::noMatch($query);
        }

        $text = $results === []
            ? sprintf('No section of the knowledge documents matched "%s".', $query)
            : Prose::sections($results);
        if ($hints !== []) {
            $text .= "\n\nThe architecture hints also cover this — call typo3_architecture_lookup with the id:\n"
                . implode("\n", array_map(
                    static fn(array $hint): string => '- ' . $hint['id'] . ' — ' . $hint['title'],
                    $hints,
                ));
        }

        return ToolResult::create($text, [
            'query' => $query,
            'matchCount' => count($results),
            'matches' => Prose::records($results),
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
        ]);
    }
}
