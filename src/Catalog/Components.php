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
     *     sassPath: string, demoPath: ?string, keywords: array<int, string>
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
            'sassPath' => (string) ($entry['sassPath'] ?? ''),
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
            $score = self::scoreComponent($component, $terms);
            if ($score > 0) {
                $scored[] = ['component' => $component, 'score' => $score];
            }
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
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
     * Name, root class, and keywords weigh most; class lists and prose less.
     *
     * @param array<string, mixed> $component
     * @param array<int, string> $terms
     */
    private static function scoreComponent(array $component, array $terms): int
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
        foreach ($terms as $term) {
            if (str_contains($name, $term)) {
                $score += 5;
            } elseif (str_contains($keywords, $term)) {
                $score += 3;
            } elseif (str_contains($classes, $term)) {
                $score += 2;
            } elseif (str_contains($prose, $term)) {
                $score += 1;
            }
        }

        return $score;
    }
}
