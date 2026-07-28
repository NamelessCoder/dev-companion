<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use Typo3CmsMcp\Paths;

/**
 * Loads and searches the TYPO3 core icon identifier catalog from
 * catalog/icons.json. Identifiers are the registered T3Icons names (the SVG
 * file names, e.g. actions-open, module-web-list, mimetypes-pdf), grouped by
 * category, plus the registered aliases.
 *
 * The catalog exists for two jobs: validating an identifier before it fails at
 * runtime, and finding one by what it should mean. Identifiers spell shapes
 * ("exclamation-triangle"), not intents ("warning"), so the catalog carries a
 * concept map that connects the two.
 */
final class Icons
{
    /**
     * @return array{
     *     icons: array<string, array<int, string>>,
     *     aliases: array<string, string>,
     *     concepts: array<string, array<int, string>>
     * }
     */
    private static function data(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('icons.json')), true);
        if (!is_array($decoded) || !isset($decoded['icons'])) {
            throw new \RuntimeException('Invalid catalog/icons.json');
        }

        return [
            'icons' => $decoded['icons'],
            'aliases' => $decoded['aliases'] ?? [],
            'concepts' => $decoded['concepts'] ?? [],
        ];
    }

    /**
     * Flat list of every registered identifier with its category.
     *
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string}>
     */
    public static function load(): array
    {
        $data = self::data();

        $icons = [];
        foreach ($data['icons'] as $category => $identifiers) {
            foreach ((array) $identifiers as $identifier) {
                $icons[] = [
                    'identifier' => (string) $identifier,
                    'category' => (string) $category,
                    'aliasOf' => null,
                ];
            }
        }

        foreach ($data['aliases'] as $alias => $target) {
            $icons[] = [
                'identifier' => (string) $alias,
                'category' => (string) (strstr((string) $alias, '-', true) ?: 'alias'),
                'aliasOf' => (string) $target,
            ];
        }

        return $icons;
    }

    /** @return array<int, string> Available category names. */
    public static function categories(): array
    {
        $categories = array_keys(self::data()['icons']);
        sort($categories);

        return array_map('strval', $categories);
    }

    /** @return array<string, array<int, string>> Concept keyword to the icons it suggests. */
    public static function concepts(): array
    {
        return self::data()['concepts'];
    }

    /**
     * Identifier families the core registers that this catalog does not carry,
     * so a query naming one is still recognised as an identifier rather than
     * read as a search phrase.
     *
     * flags-* are registered lazily from the flag SVGs; provider-*,
     * tcarecords-* and theme-* come from a system extension's
     * Configuration/Icons.php rather than from the T3Icons set.
     *
     * @var array<int, string>
     */
    private const FAMILIES_OUTSIDE_THE_CATALOG = ['flags', 'provider', 'tcarecords', 'theme'];

    /**
     * Whether the query is shaped like a registered identifier
     * (<family>-<name>) rather than like a search phrase.
     *
     * The distinction decides what a miss means. "passkey" finding nothing is a
     * search that came up empty; "status-reference-hard" finding nothing is a
     * validation result, and answering it with a ranked list of icons that
     * merely share a name part reads as a confirmation it is not.
     */
    public static function looksLikeIdentifier(string $query): bool
    {
        $query = strtolower(trim($query));
        if (preg_match('/^([a-z][a-z0-9]*)-[a-z0-9]+(-[a-z0-9]+)*$/', $query, $matches) !== 1) {
            return false;
        }

        return in_array($matches[1], self::categories(), true)
            || in_array($matches[1], self::FAMILIES_OUTSIDE_THE_CATALOG, true);
    }

    /** Whether the catalog carries exactly this identifier, alias or not. */
    public static function exists(string $identifier): bool
    {
        $identifier = strtolower(trim($identifier));
        foreach (self::load() as $icon) {
            if ($icon['identifier'] === $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ranks icon identifiers against a free-text query. An empty query returns
     * the whole catalog.
     *
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string, matched: int, score: int, why: array<int, string>}>
     */
    public static function find(?string $query, bool $requireAllTerms = true): array
    {
        $icons = self::load();
        $terms = self::terms(trim($query ?? ''));

        if ($terms === []) {
            return array_map(
                static fn(array $icon): array => $icon + ['matched' => 0, 'score' => 0, 'why' => []],
                $icons
            );
        }

        $normalizedQuery = implode('-', $terms);
        $concepts = self::data()['concepts'];

        // Identifiers a concept in the query points at, with the concept name.
        $suggested = [];
        foreach ($terms as $term) {
            foreach ($concepts[$term] ?? [] as $identifier) {
                $suggested[$identifier][$term] = true;
            }
        }

        $scored = [];
        foreach ($icons as $icon) {
            [$matched, $score, $why] = self::scoreIcon($icon['identifier'], $icon['category'], $terms);

            foreach ($suggested[$icon['identifier']] ?? [] as $concept => $unused) {
                ++$matched;
                $score += 6;
                $why[] = 'concept "' . $concept . '"';
            }

            if ($matched === 0) {
                continue;
            }

            // An exact identifier hit always wins.
            if ($icon['identifier'] === $normalizedQuery) {
                $score += 1000;
                $why[] = 'exact identifier';
            }

            $scored[] = $icon + [
                'matched' => min($matched, count($terms)),
                'score' => $score,
                'why' => $why,
            ];
        }

        if ($requireAllTerms) {
            $complete = array_values(array_filter(
                $scored,
                static fn(array $icon): bool => $icon['matched'] >= count($terms)
            ));
            if ($complete !== []) {
                $scored = $complete;
            }
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['matched'] <=> $a['matched']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['identifier'], $b['identifier']);
        });

        return $scored;
    }

    /** @return array<int, string> */
    private static function terms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/[\s_-]+/', mb_strtolower($query)) ?: [] as $term) {
            $term = preg_replace('/[^a-z0-9]/', '', $term) ?? '';
            if ($term !== '') {
                $terms[] = $term;
            }
        }

        return array_values(array_unique($terms));
    }

    /**
     * Returns [distinctTermsMatched, weightedScore, reasons]. A term that is a
     * whole hyphen-segment of the identifier scores higher than a mere
     * substring; matching the category name contributes a little. A
     * category-only hit does not count as a matched term, so generic prefixes
     * like "actions" or "status" do not make every icon in that category a
     * match on their own.
     *
     * @param array<int, string> $terms
     * @return array{0: int, 1: int, 2: array<int, string>}
     */
    private static function scoreIcon(string $identifier, string $category, array $terms): array
    {
        $segments = explode('-', $identifier);

        $matched = 0;
        $score = 0;
        $why = [];
        foreach ($terms as $term) {
            if (in_array($term, $segments, true)) {
                ++$matched;
                $score += 4;
                $why[] = 'name part "' . $term . '"';
            } elseif (str_contains($identifier, $term)) {
                ++$matched;
                $score += 2;
                $why[] = 'substring "' . $term . '"';
            } elseif ($term === $category) {
                $score += 1;
                $why[] = 'category "' . $category . '"';
            }
        }

        return [$matched, $score, $why];
    }
}
