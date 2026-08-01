<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge\Catalog;

use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;

/**
 * Loads and ranks the curated TYPO3 backend component index from
 * catalog/components.json. Where the active installation can be read,
 * InstalledComponents replaces its contract fields with evidence from the
 * installed backend CSS, JavaScript, and styleguide templates.
 *
 * The searchable index is hand-curated from the core sources; lookup can
 * enrich it from installed package files without loading TYPO3 into this
 * process. Search mirrors the term-scoring approach used by TestSuiteHints.
 *
 * An entry also carries the majors it was verified on, as `since`/`until` — the
 * same binding the architecture hints use. The catalog is taken from one
 * revision, so without that a component whose custom-property contract does not
 * exist on the caller's LTS is handed over as fact; with it, the entry is
 * withheld and what to verify against is named instead.
 */
final class Components
{
    /**
     * Share of the query a component has to cover to be an answer to it, when
     * the query did not name it outright. The same threshold the architecture
     * hints hold their prose matches to, for the same reason: below it the
     * entry shares a word with the question rather than answering it.
     */
    private const MIN_COVERAGE = 0.5;

    /** Generic words that carry no component signal. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'add', 'new', 'use', 'using', 'component',
        'components', 'css', 'class', 'classes', 'backend', 'typo3', 'style',
        'styles', 'markup', 'installed', 'version', 'how', 'what', 'show', 'find',
    ];

    /**
     * @return array<int, array{
     *     name: string, title: string, summary: string, rootClass: string,
     *     variants: array<int, string>, modifiers: array<int, string>,
     *     subComponents: array<int, string>, customProperties: array<int, string>,
     *     markup: string, examples: array<int, string>,
     *     sassPath: ?string, sassPaths: array<int, string>, demoPath: ?string,
     *     demoSelector: ?string,
     *     keywords: array<int, string>, since: ?int, until: ?int
     * }>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('components.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/components.json');
        }

        return array_map(static function (array $entry): array {
            // One component can span several Sass files — the input controls
            // are form-control, form-label, form-text and input-group at once,
            // and naming only the first made the others look like they were not
            // part of it. sassPath stays as the primary one for callers that
            // read a single path.
            $sassPaths = array_map('strval', $entry['sassPaths'] ?? []);
            if ($sassPaths === [] && isset($entry['sassPath'])) {
                $sassPaths = [(string) $entry['sassPath']];
            }

            return [
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
                'sassPath' => $sassPaths[0] ?? null,
                'sassPaths' => $sassPaths,
                'demoPath' => isset($entry['demoPath']) ? (string) $entry['demoPath'] : null,
                'demoSelector' => isset($entry['demoSelector']) ? (string) $entry['demoSelector'] : null,
                'keywords' => array_map('strval', $entry['keywords'] ?? []),
                'since' => isset($entry['since']) ? (int) $entry['since'] : null,
                'until' => isset($entry['until']) ? (int) $entry['until'] : null,
            ];
        }, $decoded);
    }

    /**
     * The entries split by whether they were verified on $target.
     *
     * Without a target nothing is withheld: the caller is told each entry's
     * range instead, which is the honest answer when nobody said which version
     * this is for.
     *
     * @param array<int, array<string, mixed>> $components
     * @return array{holds: array<int, array<string, mixed>>, withheld: array<int, array<string, mixed>>}
     */
    public static function splitByTarget(array $components, ?int $target): array
    {
        $split = ['holds' => [], 'withheld' => []];
        foreach ($components as $component) {
            $holds = ($component['_installed'] ?? false) === true
                ? ($component['_installedPresent'] ?? false) === true
                : Versions::holds($component['since'] ?? null, $component['until'] ?? null, $target);
            $split[$holds ? 'holds' : 'withheld'][] = $component;
        }

        return $split;
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
    public static function find(?string $query, ?int $target = null): array
    {
        $components = self::load();
        $components = InstalledComponents::derive($components, $target) ?? $components;
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

        // What the query does not ask for is not an answer to it. A component
        // the query names is one however the rest of it reads — "add a badge to
        // the module header" is about the badge — and everything else has to
        // cover half of what was asked. Without this a five-word question about
        // something nobody catalogued came back with three components that each
        // shared one word with it, and a miss would have been the true answer.
        $asked = count($terms);
        $scored = array_values(array_filter($scored, static fn(array $entry): bool
            => in_array('name', $entry['component']['matchedIn'], true)
            || $entry['matched'] / $asked >= self::MIN_COVERAGE));

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
            // Three characters, because a term is matched as a substring: "to"
            // is carried by form-check-type-toggle, and a question phrased as a
            // sentence then covers half of itself through its function words.
            // No component name or class in the catalog is shorter.
            if ($term !== '' && strlen($term) >= 3 && !in_array($term, self::STOPWORDS, true)) {
                $terms[] = $term;
            }
        }

        return array_values(array_unique($terms));
    }

    /**
     * Returns [weightedScore, distinctTermsCovered, whereTheyMatched]. Name,
     * root class, and keywords weigh most; class lists and prose less, and
     * prose alone covers nothing.
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
            $component['classes'] ?? [],
        )));
        $prose = mb_strtolower($component['title'] . ' ' . $component['summary']);

        $score = 0;
        $matched = 0;
        $why = [];

        // The whole query naming the component or its root class outright is a
        // different kind of answer from a word appearing somewhere in it:
        // "status indicator" must reach status-indicator, not Badge.
        $phrase = implode(' ', $terms);
        if ($component['name'] === $phrase
            || $component['rootClass'] === $phrase
            || $component['name'] === str_replace(' ', '-', $phrase)
        ) {
            $score += 100;
            $why['name'] = true;
        }

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
                // Scored but not counted as covered: a summary is written in
                // ordinary words — "content", "container", "elements" — and a
                // term found only there says the component was described with
                // that word, not that it is what the word was asking for.
                $score += 1;
                $why['description'] = true;
            }
        }

        return [$score, $matched, array_keys($why)];
    }
}
