<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Loads architecture hints from one JSON file per section under
 * knowledge/architecture-hints/ and matches them against paths/topics.
 */
final class ArchitectureHints
{
    /** @var array<string, string> */
    private const SECTION_LABELS = [
        'php' => 'PHP',
        'typescript' => 'TypeScript',
        'javascript' => 'JavaScript',
        'css' => 'CSS',
        'general' => 'General',
    ];

    /** @return array<int, array{id: string, title: string, appliesTo: array<int, string>, hints: array<int, string>, checks: array<int, string>, category: string}> */
    public static function load(): array
    {
        $dir = Paths::knowledge() . '/architecture-hints';
        $files = glob($dir . '/*.json') ?: [];
        sort($files);

        $hints = [];
        foreach ($files as $path) {
            $decoded = json_decode((string) file_get_contents($path), true);
            if (!is_array($decoded)) {
                throw new \RuntimeException(sprintf('Invalid architecture-hints/%s', basename($path)));
            }

            $category = self::sectionLabel(substr(basename($path), 0, -strlen('.json')));
            foreach ($decoded as $entry) {
                $hints[] = [
                    'id' => (string) $entry['id'],
                    'title' => (string) $entry['title'],
                    'appliesTo' => array_map('strval', $entry['appliesTo'] ?? []),
                    'hints' => array_map('strval', $entry['hints'] ?? []),
                    'checks' => array_map('strval', $entry['checks'] ?? []),
                    'category' => $category,
                ];
            }
        }

        return $hints;
    }

    /**
     * Matches hints against paths and task text, restricted to the domains the
     * input actually touches.
     *
     * The prose sections from the architecture documents are a fallback, not an
     * addition: they are only returned when no structured hint matched, because
     * otherwise they restate the hints and bury them.
     *
     * @param array<int, string> $paths
     * @return array{
     *     matchedHints: array<int, array<string, mixed>>,
     *     knowledgeSections: array<int, array{id: string, title: string, heading: string, body: string, coverage: float, truncated: bool}>,
     *     domains: array<int, string>
     * }
     */
    public static function find(array $paths, string $task, int $limit): array
    {
        $task = trim($task);
        $haystack = mb_strtolower(implode("\n", array_merge($paths, [$task])));

        $domains = Domains::detect($paths, $task);
        $categories = Domains::hintCategories($domains);

        $scored = [];
        foreach (self::load() as $hint) {
            if (!in_array($hint['category'], $categories, true)) {
                continue;
            }
            $score = self::scoreHint($hint, $haystack);
            if ($score > 0) {
                $scored[] = ['hint' => $hint, 'score' => $score];
            }
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['title'], $b['hint']['title']);
        });

        $matchedHints = array_map(
            static fn(array $entry): array => $entry['hint'],
            array_slice($scored, 0, $limit)
        );

        $knowledgeSections = [];
        if ($matchedHints === [] && $task !== '') {
            $documents = ['typo3-core-architecture'];
            if (in_array(Domains::FRONTEND, $domains, true)) {
                $documents[] = 'typo3-css-architecture';
            }
            $knowledgeSections = Knowledge::search($task, $documents, $limit);
        }

        return [
            'matchedHints' => $matchedHints,
            'knowledgeSections' => $knowledgeSections,
            'domains' => $domains,
        ];
    }

    /**
     * Groups matched hints into sections, preserving each hint's relative order
     * within a section and ordering sections by the known section order.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array{category: string, hints: array<int, array<string, mixed>>}>
     */
    public static function groupByCategory(array $hints): array
    {
        $grouped = [];
        foreach ($hints as $hint) {
            $grouped[$hint['category']][] = $hint;
        }

        $order = array_values(self::SECTION_LABELS);
        $rank = static function (string $category) use ($order): int {
            $index = array_search($category, $order, true);
            return $index === false ? count($order) : $index;
        };

        $sections = [];
        foreach ($grouped as $category => $sectionHints) {
            $sections[] = ['category' => (string) $category, 'hints' => $sectionHints];
        }

        usort($sections, static function (array $a, array $b) use ($rank): int {
            return $rank($a['category']) <=> $rank($b['category'])
                ?: strcmp($a['category'], $b['category']);
        });

        return $sections;
    }

    /** @param array{appliesTo: array<int, string>} $hint */
    private static function scoreHint(array $hint, string $haystack): int
    {
        $score = 0;
        foreach ($hint['appliesTo'] as $pattern) {
            $normalized = mb_strtolower($pattern);
            if (str_contains($haystack, $normalized)) {
                $score += strlen($normalized);
            }
        }

        return $score;
    }

    private static function sectionLabel(string $fileBaseName): string
    {
        return self::SECTION_LABELS[$fileBaseName] ?? ucfirst($fileBaseName);
    }
}
