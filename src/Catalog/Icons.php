<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use Typo3CmsMcp\Paths;

/**
 * Loads and searches the TYPO3 core icon identifier catalog from
 * catalog/icons.json. Identifiers are the registered T3Icons names (the SVG
 * file names, e.g. actions-open, module-web-list, mimetypes-pdf), grouped by
 * category. The catalog exists so icon identifiers can be validated up front
 * instead of failing at runtime when an unknown identifier is requested.
 */
final class Icons
{
    /**
     * Flat list of every registered identifier with its category.
     *
     * @return array<int, array{identifier: string, category: string}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('icons.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/icons.json');
        }

        $icons = [];
        foreach ($decoded as $category => $identifiers) {
            foreach ((array) $identifiers as $identifier) {
                $icons[] = ['identifier' => (string) $identifier, 'category' => (string) $category];
            }
        }

        return $icons;
    }

    /** @return array<int, string> Available category names. */
    public static function categories(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('icons.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/icons.json');
        }

        $categories = array_keys($decoded);
        sort($categories);

        return array_map('strval', $categories);
    }

    /**
     * Ranks icon identifiers against a free-text query. An empty query returns
     * the whole catalog.
     *
     * @return array<int, array{identifier: string, category: string}>
     */
    public static function find(?string $query): array
    {
        $icons = self::load();
        $terms = self::terms(trim($query ?? ''));

        if ($terms === []) {
            return $icons;
        }

        $normalizedQuery = implode('-', $terms);

        $scored = [];
        foreach ($icons as $icon) {
            [$matched, $score] = self::scoreIcon($icon['identifier'], $icon['category'], $terms);
            if ($matched === 0) {
                continue;
            }
            // An exact identifier hit always wins.
            if ($icon['identifier'] === $normalizedQuery) {
                $score += 1000;
            }
            $scored[] = ['icon' => $icon, 'matched' => $matched, 'score' => $score];
        }

        // Rank by how many distinct query terms matched first, then by weight.
        usort($scored, static function (array $a, array $b): int {
            return $b['matched'] <=> $a['matched']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['icon']['identifier'], $b['icon']['identifier']);
        });

        return array_map(static fn(array $entry): array => $entry['icon'], $scored);
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
     * Returns [distinctTermsMatched, weightedScore]. A term that is a whole
     * hyphen-segment of the identifier scores higher than a mere substring;
     * matching the category name contributes a little. A category-only hit does
     * not count as a matched term, so generic prefixes like "actions" do not
     * make every icon in that category a match on their own.
     *
     * @param array<int, string> $terms
     * @return array{0: int, 1: int}
     */
    private static function scoreIcon(string $identifier, string $category, array $terms): array
    {
        $segments = explode('-', $identifier);

        $matched = 0;
        $score = 0;
        foreach ($terms as $term) {
            if (in_array($term, $segments, true)) {
                $matched++;
                $score += 4;
            } elseif (str_contains($identifier, $term)) {
                $matched++;
                $score += 2;
            } elseif ($term === $category) {
                $score += 1;
            }
        }

        return [$matched, $score];
    }
}
