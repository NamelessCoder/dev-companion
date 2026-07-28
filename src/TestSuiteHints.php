<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Loads and ranks runTests.sh suite hints from test-suite-hints.json, plus the
 * invocation notes (CI=true, targeted runs, option flags) that apply to every
 * suite.
 */
final class TestSuiteHints
{
    /** Generic words that appear in task descriptions but carry no suite signal. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'add', 'new', 'change', 'changes', 'update',
        'fix', 'fixes', 'core', 'typo3', 'file', 'files', 'code', 'support',
    ];

    /**
     * @return array{
     *     invocation: array{notes: array<int, string>, options: array<int, array{option: string, description: string}>, examples: array<int, array{purpose: string, command: string}>},
     *     suites: array<int, array{suite: string, command: string, description: string, whenToUse: string, domains: array<int, string>, targeted: ?string}>
     * }
     */
    private static function data(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('test-suite-hints.json')), true);
        if (!is_array($decoded) || !isset($decoded['suites'], $decoded['invocation'])) {
            throw new \RuntimeException('Invalid test-suite-hints.json');
        }

        $invocation = $decoded['invocation'];

        return [
            'invocation' => [
                'notes' => array_map('strval', $invocation['notes'] ?? []),
                'options' => array_map(static fn(array $o): array => [
                    'option' => (string) $o['option'],
                    'description' => (string) $o['description'],
                ], $invocation['options'] ?? []),
                'examples' => array_map(static fn(array $e): array => [
                    'purpose' => (string) $e['purpose'],
                    'command' => (string) $e['command'],
                ], $invocation['examples'] ?? []),
            ],
            'suites' => array_map(static fn(array $entry): array => [
                'suite' => (string) $entry['suite'],
                'command' => (string) $entry['command'],
                'description' => (string) $entry['description'],
                'whenToUse' => (string) $entry['whenToUse'],
                'domains' => array_map('strval', $entry['domains'] ?? []),
                'targeted' => isset($entry['targeted']) ? (string) $entry['targeted'] : null,
            ], $decoded['suites']),
        ];
    }

    /** @return array<int, array{suite: string, command: string, description: string, whenToUse: string, domains: array<int, string>, targeted: ?string}> */
    public static function load(): array
    {
        return self::data()['suites'];
    }

    /**
     * The invocation guidance that applies regardless of the chosen suite.
     *
     * @return array{notes: array<int, string>, options: array<int, array{option: string, description: string}>, examples: array<int, array{purpose: string, command: string}>}
     */
    public static function invocation(): array
    {
        return self::data()['invocation'];
    }

    /**
     * Ranks suites against a query. When $domains is given, only suites touching
     * one of those domains are considered — so a PHP-only task never gets a
     * Sass build recommended.
     *
     * @param array<int, string> $domains
     * @return array<int, array{suite: string, command: string, description: string, whenToUse: string, domains: array<int, string>, targeted: ?string}>
     */
    public static function find(?string $query, array $domains = []): array
    {
        $hints = self::load();
        $narrowed = false;

        if ($domains !== []) {
            $hints = array_values(array_filter(
                $hints,
                static fn(array $hint): bool => array_intersect($hint['domains'], $domains) !== []
            ));
            $narrowed = true;
        }

        $terms = self::meaningfulTerms(trim($query ?? ''));

        // No query (or only stopwords): list everything for browsing.
        if ($terms === []) {
            return $hints;
        }

        $scored = [];
        foreach ($hints as $hint) {
            $score = self::scoreHint($hint, $terms);
            if ($score > 0) {
                $scored[] = ['hint' => $hint, 'score' => $score];
            }
        }

        // A query phrased as a request rather than a suite name ("recommend the
        // narrow iteration check") scores nothing. Once the domains have
        // narrowed the list, that list is still the right answer; only an
        // unnarrowed miss is a real miss.
        if ($scored === []) {
            return $narrowed ? $hints : [];
        }

        usort($scored, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['suite'], $b['hint']['suite']);
        });

        return array_map(static fn(array $entry): array => $entry['hint'], $scored);
    }

    /** @return array<int, string> */
    private static function meaningfulTerms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/\s+/', mb_strtolower($query)) ?: [] as $term) {
            $term = preg_replace('/[^a-z0-9-]/', '', $term) ?? '';
            if (strlen($term) >= 3 && !in_array($term, self::STOPWORDS, true)) {
                $terms[] = $term;
            }
        }

        return $terms;
    }

    /**
     * Matches in the suite name weigh more than matches in the prose.
     *
     * @param array{suite: string, description: string, whenToUse: string} $hint
     * @param array<int, string> $terms
     */
    private static function scoreHint(array $hint, array $terms): int
    {
        $suite = mb_strtolower($hint['suite']);
        $prose = mb_strtolower($hint['description'] . ' ' . $hint['whenToUse']);

        $score = 0;
        foreach ($terms as $term) {
            if (str_contains($suite, $term)) {
                $score += 2;
            } elseif (str_contains($prose, $term)) {
                $score += 1;
            }
        }

        return $score;
    }
}
