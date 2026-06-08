<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Loads and ranks runTests.sh suite hints from test-suite-hints.json.
 */
final class TestSuiteHints
{
    /** Generic words that appear in task descriptions but carry no suite signal. */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'add', 'new', 'change', 'changes', 'update',
        'fix', 'fixes', 'core', 'typo3', 'file', 'files', 'code', 'support',
    ];

    /** @return array<int, array{suite: string, command: string, description: string, whenToUse: string}> */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('test-suite-hints.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid test-suite-hints.json');
        }

        return array_map(static fn(array $entry): array => [
            'suite' => (string) $entry['suite'],
            'command' => (string) $entry['command'],
            'description' => (string) $entry['description'],
            'whenToUse' => (string) $entry['whenToUse'],
        ], $decoded);
    }

    /** @return array<int, array{suite: string, command: string, description: string, whenToUse: string}> */
    public static function find(?string $query): array
    {
        $hints = self::load();
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
