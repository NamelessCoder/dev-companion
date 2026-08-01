<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge;

use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Search\TermSearch;

/**
 * Reads and searches the bundled markdown knowledge documents.
 *
 * Search works on whole `##` sections, not on single lines: a section is
 * returned with its heading and its original formatting (code fences included),
 * so the answer stays readable and quotable. A section only counts as a match
 * when it covers enough of the query, so a lookup that found nothing relevant
 * says so instead of returning the nearest unrelated prose.
 */
final class Documents
{
    /**
     * Share of the query's meaningful terms a section has to contain. Below it,
     * a section is noise rather than an answer.
     */
    private const MIN_COVERAGE = 0.5;

    /**
     * A term in the heading weighs more than the same term in the body: a
     * section titled "Design Tokens" is about design tokens, and one that
     * mentions them in passing is not.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['heading' => 4, 'body' => 1];

    /**
     * Section length that still counts for what its terms say.
     *
     * A section is cut at MAX_SECTION_LENGTH, which is roughly this many words,
     * so a section at that length is an ordinary one rather than an outlier and
     * nothing here is long enough to contain a term by accident. The hint
     * corpus is the other case and sets its own reference.
     */
    private const UNDILUTED_WORDS = 400;

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
        $terms = TermSearch::terms($query);
        if ($terms === []) {
            return [];
        }

        $candidates = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::sections($content) as $section) {
                $candidates[] = [
                    'id' => $document['id'],
                    'title' => $document['title'],
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                ];
            }
        }

        $weights = TermSearch::weights($terms, array_map(self::searchable(...), $candidates));
        $askedFor = array_sum($weights);

        $matches = [];
        foreach ($candidates as $candidate) {
            [$score, $covered] = TermSearch::score(
                self::searchable($candidate),
                $weights,
                self::FIELD_WEIGHTS,
                self::UNDILUTED_WORDS,
            );
            $coverage = $askedFor > 0.0 ? $covered / $askedFor : 0.0;
            if ($coverage < self::MIN_COVERAGE) {
                continue;
            }

            $matches[] = $candidate + [
                'score' => $score,
                'coverage' => $coverage,
                'truncated' => false,
            ];
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

        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $inFence = !$inFence;
            }

            if (!$inFence && preg_match('/^##\s+(.+)$/', $line, $matches) === 1) {
                $sections = self::flushSection($sections, $heading, $buffer);
                $buffer = [];
                $heading = trim($matches[1]);
                continue;
            }

            // Skip the document title; it is carried separately.
            if (!$inFence && preg_match('/^#\s+/', $line) === 1) {
                continue;
            }

            $buffer[] = $line;
        }

        return self::flushSection($sections, $heading, $buffer);
    }

    /**
     * Appends the buffered section, unless it has no body: a heading with
     * nothing under it is not an answer.
     *
     * @param array<int, array{heading: string, body: string}> $sections
     * @param array<int, string> $buffer
     * @return array<int, array{heading: string, body: string}>
     */
    private static function flushSection(array $sections, string $heading, array $buffer): array
    {
        $body = trim(implode("\n", $buffer));
        if ($body !== '') {
            $sections[] = ['heading' => $heading, 'body' => $body];
        }

        return $sections;
    }

    /**
     * The fields of a section the matcher reads, keyed the way FIELD_WEIGHTS
     * names them.
     *
     * @param array<string, mixed> $candidate
     * @return array<string, string>
     */
    private static function searchable(array $candidate): array
    {
        return ['heading' => (string) $candidate['heading'], 'body' => (string) $candidate['body']];
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
