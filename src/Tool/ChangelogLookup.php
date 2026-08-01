<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Changelog;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unanswered;
use Typo3CmsMcp\Search\LabelSearch;

/**
 * What a TYPO3 version changed, from the changelog that installation ships.
 *
 * The one question the knowledge base cannot answer from conventions: what a
 * given release broke, deprecated or added is a list, and the list is on disk in
 * every installation.
 */
final class ChangelogLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_changelog_lookup';
    }

    public static function description(): string
    {
        return 'Search the TYPO3 changelog of the installation you are working in: one entry per breaking change, deprecation, feature and important note, in the version it was released in. This is the first stop when building on a major you have not built on recently, not only a lookup after the fact — what separates a current answer from a two-major-old one is written down here and almost nowhere else. Answers "what did this version deprecate", "what changed about X", "which release introduced Y". Read from the core package on disk, so it covers exactly the versions that installation ships and grows with a Composer update. Every word of the query has to be carried by an entry; narrow further with type and version.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Words the entry has to carry, matched against its title. Omit to list a version or a type as a whole.'],
                'type' => ['type' => 'string', 'enum' => ['breaking', 'deprecation', 'feature', 'important'], 'description' => 'Restrict to one kind of change. Breaking and deprecation are what affects existing code.'],
                'version' => ['type' => 'string', 'description' => 'Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4" covers 13.4 and 13.4.x.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20, 'description' => 'Maximum number of entries.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::string(),
            'matchCount' => Schema::integer('Entries carrying every word of the query, before the limit.'),
            'entries' => Schema::listOf(Schema::object([
                'type' => ['type' => 'string', 'enum' => ['Breaking', 'Deprecation', 'Feature', 'Important']],
                'version' => Schema::string('The version directory it was released in.'),
                'issue' => Schema::string('Forge issue number.'),
                'title' => Schema::string(),
                'tags' => Schema::listOf(Schema::string(), 'Index tags. FullyScanned or PartiallyScanned means the extension scanner has a matcher for it.'),
                'file' => Schema::string('EXT: reference of the entry, to read the description and the migration.'),
            ], ['type', 'version', 'issue', 'title', 'tags', 'file'])),
            'versions' => Schema::listOf(Schema::string(), 'The versions this installation ships changelog entries for, newest first. Anything outside them is not in this answer.'),
            'answeredBy' => Schema::answeredBy(),
            'unavailable' => Schema::unavailable(),
        ], ['query', 'matchCount', 'entries', 'versions', 'answeredBy']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $type = trim((string) ($args['type'] ?? ''));
        $version = trim((string) ($args['version'] ?? ''));
        $limit = (int) ($args['limit'] ?? 20);

        if (Changelog::directory() === null) {
            return Unanswered::because(
                'no TYPO3 installation was found whose core package ships the changelog',
                ['query' => $query, 'matchCount' => 0, 'entries' => [], 'versions' => []],
            );
        }

        $terms = LabelSearch::terms($query);
        $matching = LabelSearch::carryingEvery(Changelog::entries($type, $version), $terms);
        usort($matching, static fn(array $a, array $b): int => version_compare($b['version'], $a['version'])
            ?: strcmp($a['key'], $b['key']));

        $shown = array_slice($matching, 0, $limit);
        $entries = array_map(static function (array $entry): array {
            $read = Changelog::read($entry);

            return [
                'type' => $entry['type'],
                'version' => $entry['version'],
                'issue' => $entry['issue'],
                'title' => $read['title'] === '' ? $entry['source'] : $read['title'],
                'tags' => $read['tags'],
                'file' => 'EXT:core/Documentation/Changelog/' . $entry['version'] . '/' . $entry['key'] . '.rst',
            ];
        }, $shown);

        $versions = Changelog::versions();
        if ($entries === []) {
            $lines = [sprintf(
                'No changelog entry in this installation %s.',
                $terms === [] ? 'matched those filters' : 'carries all of ' . LabelSearch::quoted($terms),
            )];
            $reached = array_values(array_filter(
                LabelSearch::perTermCounts(Changelog::entries($type, $version), $terms),
                static fn(array $term): bool => $term['matchCount'] > 0,
            ));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = 'On its own, ' . implode(', ', array_map(
                    static fn(array $term): string => sprintf('"%s" reaches %d entr(ies)', $term['term'], $term['matchCount']),
                    $reached,
                )) . '.';
            }
            $lines[] = sprintf(
                'The changelog here covers %s. A version this installation does not ship is not in it — read that '
                . 'one in the core repository or at https://docs.typo3.org.',
                $versions === [] ? 'nothing' : implode(', ', array_slice($versions, 0, 8)) . ' and older',
            );

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'entries' => [],
                'versions' => $versions,
                'answeredBy' => 'packages',
            ]);
        }

        $lines = [sprintf(
            '%d changelog entr%s%s%s:',
            count($matching),
            count($matching) === 1 ? 'y' : 'ies',
            $query === '' ? '' : sprintf(' carrying %s', LabelSearch::quoted($terms)),
            count($matching) > count($entries) ? sprintf(' — showing the first %d', count($entries)) : '',
        )];
        foreach ($entries as $entry) {
            $lines[] = sprintf('- %s %s: %s (#%s)', $entry['version'], $entry['type'], $entry['title'], $entry['issue']);
            $lines[] = '  ' . $entry['file'] . ($entry['tags'] === [] ? '' : ' — ' . implode(', ', $entry['tags']));
        }
        $lines[] = '';
        $lines[] = 'Read the file for the description and the migration. A Deprecation or Breaking entry tagged '
            . 'FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can '
            . 'find the call sites for you.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($matching),
            'entries' => $entries,
            'versions' => $versions,
            'answeredBy' => 'packages',
        ]);
    }
}
