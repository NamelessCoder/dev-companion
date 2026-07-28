<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads and searches the bundled markdown knowledge documents.
 *
 * Search works on whole `##` sections, not on single lines: a section is
 * returned with its heading and its original formatting (code fences included),
 * so the answer stays readable and quotable. A section only counts as a match
 * when it covers enough of the query, so a lookup that found nothing relevant
 * says so instead of returning the nearest unrelated prose.
 */
final class Knowledge
{
    /**
     * Share of the query's meaningful terms a section has to contain. Below it,
     * a section is noise rather than an answer.
     */
    private const MIN_COVERAGE = 0.5;

    /** Words that carry no topic signal. */
    private const STOPWORDS = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'can', 'do',
        'does', 'for', 'from', 'how', 'i', 'in', 'is', 'it', 'its', 'me', 'my',
        'not', 'of', 'on', 'or', 'the', 'their', 'them', 'then', 'there',
        'these', 'this', 'to', 'was', 'what', 'when', 'where', 'which', 'why',
        'will', 'with', 'you', 'your', 'typo3', 'core',
    ];

    /** Longest section body returned verbatim before it is cut on a line boundary. */
    private const MAX_SECTION_LENGTH = 2400;

    /** @return array<int, array{id: string, title: string, path: string}> */
    public static function documents(): array
    {
        $dir = Paths::knowledge();
        $documents = [];

        foreach (glob($dir . '/*.md') ?: [] as $path) {
            $content = (string) file_get_contents($path);
            $fileName = basename($path);
            $documents[] = [
                'id' => substr($fileName, 0, -strlen('.md')),
                'title' => self::readTitle($content) ?? $fileName,
                'path' => $path,
            ];
        }

        usort($documents, static fn(array $a, array $b): int => strcmp($a['id'], $b['id']));

        return $documents;
    }

    public static function read(string $id): string
    {
        foreach (self::documents() as $document) {
            if ($document['id'] === $id) {
                return (string) file_get_contents($document['path']);
            }
        }

        throw new \RuntimeException(sprintf('Unknown knowledge document: %s', $id));
    }

    /**
     * The topics each document covers, for orientation and for no-match answers.
     *
     * @return array<int, array{id: string, title: string, topics: array<int, string>}>
     */
    public static function topics(): array
    {
        return array_map(static fn(array $document): array => [
            'id' => $document['id'],
            'title' => $document['title'],
            'topics' => array_map(
                static fn(array $section): string => $section['heading'],
                array_values(array_filter(
                    self::sections((string) file_get_contents($document['path'])),
                    static fn(array $section): bool => $section['heading'] !== ''
                ))
            ),
        ], self::documents());
    }

    /**
     * Ranks whole document sections against a free-text query. Sections below
     * the coverage threshold are dropped, and a section repeated across
     * documents is returned once.
     *
     * @param array<int, string> $documentIds Restrict the search to these documents.
     * @return array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}>
     */
    public static function search(string $query, array $documentIds = [], int $limit = 6): array
    {
        $terms = self::terms($query);
        if ($terms === []) {
            return [];
        }

        $matches = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::sections($content) as $section) {
                [$score, $covered] = self::scoreSection($section, $terms);
                $coverage = $covered / count($terms);
                if ($coverage < self::MIN_COVERAGE) {
                    continue;
                }

                $matches[] = [
                    'id' => $document['id'],
                    'title' => $document['title'],
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                    'score' => $score,
                    'coverage' => $coverage,
                    'truncated' => false,
                ];
            }
        }

        usort($matches, static function (array $a, array $b): int {
            return $b['coverage'] <=> $a['coverage']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['heading'], $b['heading']);
        });

        $seen = [];
        $results = [];
        foreach ($matches as $match) {
            $fingerprint = md5(preg_replace('/\s+/', ' ', $match['body']) ?? $match['body']);
            if (isset($seen[$fingerprint])) {
                continue;
            }
            $seen[$fingerprint] = true;

            [$body, $truncated] = self::shorten($match['body']);
            $match['body'] = $body;
            $match['truncated'] = $truncated;
            $results[] = $match;

            if (count($results) >= $limit) {
                break;
            }
        }

        return $results;
    }

    /**
     * Splits a document into its `##` sections. The heading line and the body
     * are kept as written, so code fences and nested lists survive.
     *
     * @return array<int, array{heading: string, body: string}>
     */
    private static function sections(string $content): array
    {
        $lines = preg_split('/\R/', $content) ?: [];

        $sections = [];
        $heading = '';
        $buffer = [];
        $inFence = false;

        $flush = static function () use (&$sections, &$heading, &$buffer): void {
            $body = trim(implode("\n", $buffer));
            if ($body !== '' || $heading !== '') {
                $sections[] = ['heading' => $heading, 'body' => $body];
            }
            $buffer = [];
        };

        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $inFence = !$inFence;
            }

            if (!$inFence && preg_match('/^##\s+(.+)$/', $line, $matches) === 1) {
                $flush();
                $heading = trim($matches[1]);
                continue;
            }

            // Skip the document title; it is carried separately.
            if (!$inFence && preg_match('/^#\s+/', $line) === 1) {
                continue;
            }

            $buffer[] = $line;
        }

        $flush();

        return array_values(array_filter($sections, static fn(array $s): bool => $s['body'] !== ''));
    }

    /**
     * Returns [weightedScore, distinctTermsCovered]. A term in the heading
     * weighs more than the same term in the body.
     *
     * @param array{heading: string, body: string} $section
     * @param array<int, string> $terms
     * @return array{0: int, 1: int}
     */
    private static function scoreSection(array $section, array $terms): array
    {
        $heading = mb_strtolower($section['heading']);
        $body = mb_strtolower($section['body']);

        $score = 0;
        $covered = 0;
        foreach ($terms as $term) {
            if (str_contains($heading, $term)) {
                $score += 4;
                ++$covered;
            } elseif (str_contains($body, $term)) {
                $score += 1;
                ++$covered;
            }
        }

        return [$score, $covered];
    }

    /**
     * The meaningful terms of a query, reduced to a stem so that word forms of
     * the same word are one term: "deprecate", "deprecated" and "deprecations"
     * all become "deprec" and match the "Deprecations" section.
     *
     * @return array<int, string>
     */
    private static function terms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/[^\p{L}\p{N}_.-]+/u', mb_strtolower(trim($query))) ?: [] as $word) {
            $word = trim($word, '.-');
            if ($word === '' || strlen($word) < 3 || in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            $terms[] = self::stem($word);
        }

        return array_values(array_unique($terms));
    }

    /**
     * Cuts a plural ending and shortens long words, so the remaining stem is a
     * substring of every form of that word. Words are only shortened while at
     * least four characters remain, so short words like "css" stay intact.
     */
    private static function stem(string $word): string
    {
        if (strlen($word) >= 6 && str_ends_with($word, 'ies')) {
            $word = substr($word, 0, -3);
        } elseif (strlen($word) >= 6 && str_ends_with($word, 'es')) {
            $word = substr($word, 0, -2);
        } elseif (strlen($word) >= 5 && str_ends_with($word, 's')) {
            $word = substr($word, 0, -1);
        }

        return strlen($word) > 6 ? substr($word, 0, 6) : $word;
    }

    /** @return array{0: string, 1: bool} */
    private static function shorten(string $body): array
    {
        if (strlen($body) <= self::MAX_SECTION_LENGTH) {
            return [$body, false];
        }

        $cut = substr($body, 0, self::MAX_SECTION_LENGTH);
        $lastBreak = strrpos($cut, "\n");

        return [rtrim($lastBreak === false ? $cut : substr($cut, 0, $lastBreak)), true];
    }

    private static function readTitle(string $content): ?string
    {
        foreach (preg_split('/\n/', $content) ?: [] as $line) {
            $line = trim($line);
            if (str_starts_with($line, '# ')) {
                return trim(preg_replace('/^#\s+/', '', $line) ?? $line);
            }
        }

        return null;
    }
}
