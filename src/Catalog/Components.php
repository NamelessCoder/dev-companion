<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use Typo3CmsMcp\Paths;

/**
 * Loads and ranks the curated TYPO3 backend component catalog from
 * catalog/components.json. Each entry records a component's canonical markup,
 * its variant/modifier/sub-component classes, custom-property contract, and the
 * concrete styleguide demo and Sass source paths in the TYPO3 core checkout.
 *
 * The data is hand-curated from the core sources (no runtime core dependency);
 * search mirrors the term-scoring approach used by TestSuiteHints.
 */
final class Components
{
    /** Generic words that carry no component signal. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'add', 'new', 'use', 'using', 'component',
        'components', 'css', 'class', 'classes', 'backend', 'typo3', 'style',
        'styles', 'how', 'what', 'show', 'find',
    ];

    /**
     * @return array<int, array{
     *     name: string, title: string, summary: string, rootClass: string,
     *     variants: array<int, string>, modifiers: array<int, string>,
     *     subComponents: array<int, string>, customProperties: array<int, string>,
     *     markup: string, examples: array<int, string>,
     *     sassPath: ?string, demoPath: ?string, keywords: array<int, string>
     * }>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('components.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/components.json');
        }

        return array_map(static fn(array $entry): array => [
            'name' => (string) $entry['name'],
            'title' => (string) $entry['title'],
            'summary' => (string) ($entry['summary'] ?? ''),
            'rootClass' => (string) ($entry['rootClass'] ?? ''),
            'variants' => array_map('strval', $entry['variants'] ?? []),
            'modifiers' => array_map('strval', $entry['modifiers'] ?? []),
            'subComponents' => array_map('strval', $entry['subComponents'] ?? []),
            'customProperties' => array_map('strval', $entry['customProperties'] ?? []),
            'markup' => (string) ($entry['markup'] ?? ''),
            'examples' => array_map('strval', $entry['examples'] ?? []),
            'sassPath' => isset($entry['sassPath']) ? (string) $entry['sassPath'] : null,
            'demoPath' => isset($entry['demoPath']) ? (string) $entry['demoPath'] : null,
            'keywords' => array_map('strval', $entry['keywords'] ?? []),
        ], $decoded);
    }

    /**
     * The shared "Definition of Done" that applies to every backend component.
     * Maintained once in catalog/component-checklist.json, not per component.
     *
     * @return array{title: string, intro: string, items: array<int, string>}
     */
    public static function checklist(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('component-checklist.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/component-checklist.json');
        }

        return [
            'title' => (string) ($decoded['title'] ?? 'Component Definition of Done'),
            'intro' => (string) ($decoded['intro'] ?? ''),
            'items' => array_map('strval', $decoded['items'] ?? []),
        ];
    }

    /**
     * Ranks components against a free-text query. Without a query, returns the
     * full catalog for browsing.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function find(?string $query): array
    {
        $components = self::load();
        $terms = self::meaningfulTerms(trim($query ?? ''));

        if ($terms === []) {
            usort($components, static fn(array $a, array $b): int => strcmp($a['name'], $b['name']));
            return $components;
        }

        $scored = [];
        foreach ($components as $component) {
            [$score, $matched, $why] = self::scoreComponent($component, $terms);
            if ($score > 0) {
                $scored[] = [
                    'component' => $component + ['matchedIn' => $why],
                    'score' => $score,
                    'matched' => $matched,
                ];
            }
        }

        // A component that covers every query term beats one that matched a
        // single term deep in a sub-component class list.
        $complete = array_values(array_filter(
            $scored,
            static fn(array $entry): bool => $entry['matched'] >= count($terms)
        ));
        if ($complete !== []) {
            $scored = $complete;
        }

        // As long as a component matched by its own name or keywords, do not
        // spend result slots on ones that only appeared through a sub-component
        // class or a word in their description.
        $direct = array_values(array_filter(
            $scored,
            static fn(array $entry): bool => array_intersect(['name', 'keywords'], $entry['component']['matchedIn']) !== []
        ));
        if ($direct !== []) {
            $scored = $direct;
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['matched'] <=> $a['matched']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['component']['name'], $b['component']['name']);
        });

        return array_map(static fn(array $entry): array => $entry['component'], $scored);
    }

    /** @return array<int, string> */
    private static function meaningfulTerms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/\s+/', mb_strtolower($query)) ?: [] as $term) {
            $term = preg_replace('/[^a-z0-9-]/', '', $term) ?? '';
            if ($term !== '' && strlen($term) >= 2 && !in_array($term, self::STOPWORDS, true)) {
                $terms[] = $term;
            }
        }

        return array_values(array_unique($terms));
    }

    /**
     * Returns [weightedScore, distinctTermsMatched, whereTheyMatched]. Name,
     * root class, and keywords weigh most; class lists and prose less.
     *
     * @param array<string, mixed> $component
     * @param array<int, string> $terms
     * @return array{0: int, 1: int, 2: array<int, string>}
     */
    private static function scoreComponent(array $component, array $terms): array
    {
        $name = mb_strtolower($component['name'] . ' ' . $component['rootClass']);
        $keywords = mb_strtolower(implode(' ', $component['keywords']));
        $classes = mb_strtolower(implode(' ', array_merge(
            $component['variants'],
            $component['modifiers'],
            $component['subComponents'],
        )));
        $prose = mb_strtolower($component['title'] . ' ' . $component['summary']);

        $score = 0;
        $matched = 0;
        $why = [];
        foreach ($terms as $term) {
            if (str_contains($name, $term)) {
                $score += 5;
                ++$matched;
                $why['name'] = true;
            } elseif (str_contains($keywords, $term)) {
                $score += 3;
                ++$matched;
                $why['keywords'] = true;
            } elseif (str_contains($classes, $term)) {
                $score += 2;
                ++$matched;
                $why['sub-component classes'] = true;
            } elseif (str_contains($prose, $term)) {
                $score += 1;
                ++$matched;
                $why['description'] = true;
            }
        }

        return [$score, $matched, array_keys($why)];
    }
}
