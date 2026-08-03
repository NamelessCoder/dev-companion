<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Changelog;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unsupported;
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
    /**
     * What an entry that states no removal leaves to be said.
     *
     * The removal version is what an upgrade audit decides on, and a field
     * carrying only what the entry states is empty for 31 of the 75
     * deprecations of one major. An empty field beside a populated one is read
     * as "no removal planned" — the silence-as-verdict failure `D-ANS-009` was
     * built against — so the rule that covers the silence travels with the
     * answer, as data and not only as text: a client that renders
     * `structuredContent` and drops the text block is what `R-ANS-002` is
     * written against.
     *
     * The rule is stated, never applied per entry. `13.4` #105297 names v15 and
     * skips v14, and the core kept it: a number derived from the rule would
     * have been wrong there, in the field a caller acts on.
     */
    private const REMOVAL_RULE = 'A deprecated API keeps working until the next major release. An entry that '
        . 'states a removal version overrides that, and some state one more than a major away. An empty removal '
        . 'is what the entry states, not a promise that no removal is planned.';

    public static function name(): string
    {
        return 'typo3_changelog_lookup';
    }

    public static function description(): string
    {
        return 'Search the TYPO3 changelog of the installation you are working in: one entry per breaking change, deprecation, feature and important note, in the version it was released in. Answers "what did this version deprecate", "what changed about X", "which release introduced Y". This is the first stop when building on a major you have not built on recently: what separates a current answer from a two-major-old one is written down here and almost nowhere else. A deprecation carries the version it stops working in where the entry states one, and the rule that answers the rest beside it. Read from the core package on disk, so it covers exactly the versions that installation ships and grows with a Composer update. Every word of the query has to be carried by an entry; narrow further with type and version.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Words the entry has to carry, matched against its title. When no entry carries all of them, the answer names the largest part of the query that does reach entries, which is what to ask again with. Omit to list a version or a type as a whole.'],
                'type' => ['type' => 'string', 'enum' => ['breaking', 'deprecation', 'feature', 'important'], 'description' => 'Restrict to one kind of change. Breaking and deprecation are what affects existing code.'],
                'version' => ['type' => 'string', 'description' => 'Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4" covers 13.4 and 13.4.x.'],
                'tag' => ['type' => 'string', 'description' => 'Restrict to entries carrying this index tag: "ext:form" for the system extension a change is in, "FullyScanned" or "NotScanned" for what the Extension Scanner has a matcher for, "PHP-API", "TCA", "Backend", "Frontend" for the surface. This is what a sweep is bounded by where words are not: every entry of a version and type is read for its tags. The changelog says nothing about which third-party extension a change affects, so an extension key of your own matches no tag.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20, 'description' => 'Maximum number of entries.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer('Entries carrying every word of the query and the tag, before the limit.'),
            'tags' => Schema::listOf(Schema::string(), 'Every index tag the entries of this version and type carry, with the ones already filtered by among them. Returned where a tag was asked for, so a tag that matched nothing can be replaced by one that exists.'),
            'entries' => Schema::listOf(Schema::object([
                'type' => ['type' => 'string', 'enum' => ['Breaking', 'Deprecation', 'Feature', 'Important']],
                'version' => Schema::string('The version directory it was released in.'),
                'issue' => Schema::string('Forge issue number.'),
                'title' => Schema::string(),
                'removal' => Schema::string('The version a Deprecation states the deprecated thing stops working in — what an upgrade decides on. Empty on the other three types, and on a deprecation whose entry states none, which is most of a major and is not "no removal planned": removalRule is what answers it there.'),
                'tags' => Schema::listOf(Schema::string(), 'Index tags. FullyScanned or PartiallyScanned means the extension scanner has a matcher for it.'),
                'file' => Schema::string('EXT: reference of the entry, to read the description and the migration.'),
            ], ['type', 'version', 'issue', 'title', 'removal', 'tags', 'file'])),
            'removalRule' => Schema::string('When a deprecation stops working where the entry itself does not say. Returned where the answer carries a deprecation.'),
            'versions' => Schema::listOf(Schema::string(), 'The versions this installation ships changelog entries for, newest first. Anything outside them is not in this answer.'),
            'answeredBy' => Schema::answeredBy(),
        ], ['query', 'matchCount', 'entries', 'versions', 'answeredBy'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $type = trim((string) ($args['type'] ?? ''));
        $version = trim((string) ($args['version'] ?? ''));
        $tag = trim((string) ($args['tag'] ?? ''));
        $limit = (int) ($args['limit'] ?? 20);

        if (Changelog::directory() === null) {
            return Unsupported::because(
                'no TYPO3 installation was found whose core package ships the changelog',
                ['query' => $query],
            );
        }

        $terms = LabelSearch::terms($query);
        $narrowed = Changelog::entries($type, $version);
        $matching = LabelSearch::carryingEvery($narrowed, $terms);

        // The tags are inside the file, so narrowing by one costs a read of
        // every entry that survived the type and the version — 23 ms for the
        // deprecations of one major, six hundred for the whole changelog. That
        // is the sweep this exists for, and it is why the filter is a field of
        // its own rather than more words in the query.
        $tags = [];
        if ($tag !== '') {
            $carrying = [];
            foreach ($matching as $entry) {
                $carried = Changelog::read($entry)['tags'];
                foreach ($carried as $carriedTag) {
                    $tags[$carriedTag] = true;
                }
                foreach ($carried as $carriedTag) {
                    if (strcasecmp($carriedTag, $tag) === 0) {
                        $carrying[] = $entry;
                        break;
                    }
                }
            }
            $matching = $carrying;
        }
        ksort($tags);
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
                'removal' => $read['removal'],
                'tags' => $read['tags'],
                'file' => 'EXT:core/Documentation/Changelog/' . $entry['version'] . '/' . $entry['key'] . '.rst',
            ];
        }, $shown);

        $versions = Changelog::versions();
        if ($entries === []) {
            $lines = [sprintf(
                'No changelog entry in this installation %s%s.',
                $terms === [] ? 'matched those filters' : 'carries all of ' . LabelSearch::quoted($terms),
                $tag === '' ? '' : sprintf(' and the tag "%s"', $tag),
            )];
            if ($tag !== '') {
                $lines[] = $tags === []
                    ? 'Nothing narrowed by that version and type carries any tag at all.'
                    : 'The tags those entries carry: ' . implode(', ', array_keys($tags)) . '.';
            }
            // What the caller can act on is a query rather than five numbers:
            // the words that do reach something together. Offered where no tag
            // was asked for, because the peel reads file names while a tag is
            // inside the file — a subset counted without the tag would promise
            // entries the same call does not return.
            $subsets = $tag === '' ? LabelSearch::largestReachingSubsets($narrowed, $terms) : [];
            $reached = array_values(array_filter(
                LabelSearch::perTermCounts($narrowed, $terms),
                static fn(array $term): bool => $term['matchCount'] > 0,
            ));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = 'On its own, ' . implode(', ', array_map(
                    static fn(array $term): string => sprintf('"%s" reaches %d entr(ies)', $term['term'], $term['matchCount']),
                    $reached,
                )) . ($subsets === [] ? ' — ask again with the one that narrows best.' : '.');
            }
            if ($subsets !== []) {
                $shown = array_slice($subsets, 0, 4);
                $lines[] = sprintf(
                    'No entry carries more than %d of the %d words: %s%s — ask again with the one that narrows best.',
                    count($subsets[0]['terms']),
                    count($terms),
                    implode(', ', array_map(static fn(array $subset): string => sprintf(
                        '"%s" reaches %d entr%s',
                        implode(' ', $subset['terms']),
                        $subset['matchCount'],
                        $subset['matchCount'] === 1 ? 'y' : 'ies',
                    ), $shown)),
                    count($subsets) > count($shown) ? sprintf(', and %d more', count($subsets) - count($shown)) : '',
                );
            }
            $lines[] = sprintf(
                'The changelog here covers %s. A version this installation does not ship is not in it — read that '
                . 'one in the core repository or at https://docs.typo3.org.',
                $versions === [] ? 'nothing' : implode(', ', array_slice($versions, 0, 8)) . ' and older',
            );

            return ToolResult::create(implode("\n", $lines), [
                'query' => $query,
                'matchCount' => 0,
                'tags' => array_keys($tags),
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
        if ($tag !== '') {
            $lines[0] = sprintf(
                '%d of the %d entries narrowed by version and type are tagged "%s"%s:',
                count($matching),
                count($narrowed),
                $tag,
                count($matching) > count($entries) ? sprintf(' — showing the first %d', count($entries)) : '',
            );
        }
        foreach ($entries as $entry) {
            $lines[] = sprintf(
                '- %s %s: %s (#%s)%s',
                $entry['version'],
                $entry['type'],
                $entry['title'],
                $entry['issue'],
                $entry['removal'] === '' ? '' : sprintf(' — removed in v%s', $entry['removal']),
            );
            $lines[] = '  ' . $entry['file'] . ($entry['tags'] === [] ? '' : ' — ' . implode(', ', $entry['tags']));
        }
        $lines[] = '';
        $lines[] = 'Read the file for the description and the migration. A Deprecation or Breaking entry tagged '
            . 'FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can '
            . 'find the call sites for you.';

        $data = [
            'query' => $query,
            'matchCount' => count($matching),
            'tags' => array_keys($tags),
            'entries' => $entries,
            'versions' => $versions,
            'answeredBy' => 'packages',
        ];
        if (in_array('Deprecation', array_column($entries, 'type'), true)) {
            $lines[] = self::REMOVAL_RULE;
            $data['removalRule'] = self::REMOVAL_RULE;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }
}
