<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge;

use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Search\Subsets;
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
     * The document title sits between the two, and it is what a caller naming
     * the subject before the question is matched on — `D-ANS-037`. It says what
     * every section under it is about, so it is worth more than a passing
     * mention and less than the section's own heading, which is the only field
     * that separates one section of a document from the next.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['heading' => 4, 'title' => 2, 'body' => 1];

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

    /** What server-scope.json calls a topic whose answers are the core's own. */

    /** @return array<int, array{id: string, title: string, path: string}> */
    public static function documents(): array
    {
        $documents = [];
        foreach (Finder::create()->files()->in(Paths::documents())->depth(0)->name('*.md')->sortByName() as $file) {
            $content = (string) file_get_contents($file->getPathname());
            $documents[] = [
                'id' => $file->getBasename('.md'),
                'title' => self::readTitle($content) ?? $file->getFilename(),
                'path' => $file->getPathname(),
            ];
        }

        return $documents;
    }

    /**
     * Whether a document is the core repository's own, derived rather than
     * declared twice.
     *
     * `knowledge/server-scope.json` already says what every topic's answers are
     * worth outside the core, and every covered topic names the file behind it.
     * A second list here would be the same statement in a place that can
     * disagree with the first, so the scope is read off the coverage: a document
     * is core-only when the topic naming it is, and `ScopeTest` holds every
     * document to being named by one.
     */
    public static function isCoreOnly(string $id): bool
    {
        foreach (Coverage::read()['covers'] as $entry) {
            if (str_contains($entry['source'], 'typo3://core/' . $id)) {
                return $entry['scope'] === Scope::Core;
            }
        }

        // A document the coverage does not announce. Held against by ScopeTest, so
        // this is what an unnoticed one gets in the meantime: withheld outside
        // the core, because the corpus is the contribution material by default
        // and handing it over is the mistake worth avoiding.
        return true;
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

        $weights = TermSearch::weights($terms, array_map(self::distinguishing(...), $candidates));
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
     * The most of a query some section still carries, as a query that can be
     * asked outright.
     *
     * What a miss here owes that the topic list cannot say: which words emptied
     * it. A query longer than the topic it names is dropped by MIN_COVERAGE
     * whole, so the section that answers part of it is unreachable and nothing
     * in the answer points at it.
     *
     * The matcher is this corpus's own, and the fields are the ones `search()`
     * reads — a subset is only worth offering where the same call returns
     * something for it. What is named back is the caller's own spelling of each
     * term rather than the stem it was reduced to; both reach the same
     * sections.
     *
     * The count is what carries every word of the subset, which here is a floor
     * rather than the length of the answer: `search()` keeps a section covering
     * half the query's weight, so the re-query returns those sections and
     * whatever else clears MIN_COVERAGE. It is one pass and it is comparable
     * across the subsets, which is what the caller picks one by.
     *
     * @param array<int, string> $documentIds Restrict to these documents.
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReachingSubsets(string $query, array $documentIds = []): array
    {
        $texts = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::sections($content) as $section) {
                $texts[] = $section['heading'] . ' ' . $section['body'];
            }
        }

        $words = TermSearch::words($query);
        $subsets = Subsets::largestReaching($texts, TermSearch::terms($query), TermSearch::carries(...));

        return array_map(static fn(array $subset): array => [
            'terms' => array_map(static fn(string $term): string => $words[$term] ?? $term, $subset['terms']),
            'matchCount' => $subset['matchCount'],
        ], $subsets);
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
        return [
            'heading' => (string) $candidate['heading'],
            'title' => (string) $candidate['title'],
            'body' => (string) $candidate['body'],
        ];
    }

    /**
     * The fields that say how much a term separates one section from the next.
     *
     * The title is not one of them, because it is the same string in every
     * section of its document: counting it there makes a term look common in
     * proportion to how many sections that document happens to have, which is a
     * fact about its length rather than about what the term distinguishes. It
     * is enough to sink a query — `commit message sitepackage` answered with
     * the commit conventions until the title of the document carrying them was
     * counted against its own words.
     *
     * @param array<string, mixed> $candidate
     * @return array<string, string>
     */
    private static function distinguishing(array $candidate): array
    {
        $fields = self::searchable($candidate);
        unset($fields['title']);

        return $fields;
    }

    /** @return array{0: string, 1: bool} */
    private static function shorten(string $body): array
    {
        if (strlen($body) <= self::MAX_SECTION_LENGTH) {
            return [$body, false];
        }

        $cut = substr($body, 0, self::MAX_SECTION_LENGTH);
        $lastBreak = strrpos($cut, "\n");
        $cut = $lastBreak === false ? $cut : substr($cut, 0, $lastBreak);

        // A cut that lands between the two fences of a code block hands over an
        // opening ``` with nothing closing it, and every line the caller reads
        // after that is inside a code block that never ends. Which byte the
        // budget falls on is a property of the text above it, so this held only
        // as long as nobody edited the document. The half-open fence goes with
        // the cut.
        if (substr_count($cut, '```') % 2 !== 0) {
            $fence = strrpos($cut, '```');
            $cut = $fence === false ? $cut : substr($cut, 0, $fence);
        }

        return [rtrim($cut), true];
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
