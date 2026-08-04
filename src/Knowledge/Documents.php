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

    /**
     * How a section says which majors it holds for.
     *
     * Two labelled lines directly under the heading, the shape a todo head is
     * written in, so one habit covers both. They are data rather than a
     * sentence — `D-VER-001` — and they are stripped before the body is handed
     * over, because a section whose body is a file would otherwise carry them
     * into the file the caller writes out.
     */
    private const BINDING = '/^\*\*(Since|Until):\*\*\s*(\d+)\s*$/';

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
        $covered = self::covered($id);

        // A document the coverage does not announce. Held against by ScopeTest, so
        // this is what an unnoticed one gets in the meantime: withheld outside
        // the core, because the corpus is the contribution material by default
        // and handing it over is the mistake worth avoiding.
        return $covered === null || $covered['scope'] === Scope::Core;
    }

    /**
     * What a client reads to understand what it is being offered, for a
     * document offered as a resource.
     *
     * A resource is picked out of a list rather than called mid-task, so the
     * list is the whole of what the choice is made on — `R-ANS-022`. It says
     * the subject and who the answers oblige, and both are read off the
     * coverage rather than written a second time here, for the reason
     * isCoreOnly is.
     */
    public static function description(string $id): ?string
    {
        $covered = self::covered($id);
        if ($covered === null) {
            return null;
        }

        return $covered['topic'] . '. ' . match ($covered['scope']) {
            Scope::Core => "The TYPO3 core's own process, which does not transfer to extension or site work.",
            // Named rather than folded into the sentence below it: a document
            // about setting a package up answers for the package, and telling a
            // core contributor it holds for their work too is how the core's own
            // harness gets rebuilt by hand.
            Scope::Extension => 'Answers for a package rather than for the core repository, whose own harness is a different one.',
            Scope::Project => 'Answers for the repository around an installation rather than for the core repository.',
            default => 'Holds for core contribution, extension development and site work alike.',
        };
    }

    /**
     * The covered topic naming this document as its source, where one does.
     *
     * @return array{topic: string, depth: string, tools: array<int, string>, source: string, scope: Scope}|null
     */
    private static function covered(string $id): ?array
    {
        foreach (Coverage::read()['covers'] as $entry) {
            if (str_contains($entry['source'], 'typo3://core/' . $id)) {
                return $entry;
            }
        }

        return null;
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
            // Deduplicated, because one subject bound to two ranges is two
            // sections under one heading, and this list is what the corpus
            // covers rather than how many variants of it there are.
            'topics' => array_values(array_unique(array_map(
                static fn(array $section): string => $section['heading'],
                array_values(array_filter(
                    self::sections((string) file_get_contents($document['path'])),
                    static fn(array $section): bool => $section['heading'] !== ''
                ))
            ))),
        ], self::documents());
    }

    /**
     * Ranks whole document sections against a free-text query. Sections below
     * the coverage threshold are dropped, and a section repeated across
     * documents is returned once.
     *
     * @param array<int, string> $documentIds Restrict the search to these documents.
     * @param int|array<int, int>|null $target The major, or the majors a repository serves at once.
     * @return array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}>
     */
    public static function search(string $query, array $documentIds = [], int $limit = 6, int|array|null $target = null): array
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
            foreach (self::forVersion(self::sections($content), $target) as $section) {
                $candidates[] = [
                    'id' => $document['id'],
                    'title' => $document['title'],
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                    'since' => $section['since'],
                    'until' => $section['until'],
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
     * The sections that hold on the target, the way `Hints::forVersion()` keeps
     * a statement.
     *
     * The target is one major, or the several a repository serves at once: a
     * package declaring `^13.4 || ^14.3` has to see both variants of a file it
     * ships on both, and the range beside each is what says which is which.
     * Without a target nothing is filtered and every variant comes back with
     * its range, which is the honest answer when nobody said which version this
     * is for.
     *
     * @param array<int, array{heading: string, body: string, since: ?int, until: ?int}> $sections
     * @param int|array<int, int>|null $target
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
     */
    private static function forVersion(array $sections, int|array|null $target): array
    {
        $targets = is_array($target) ? array_values($target) : ($target === null ? [] : [$target]);
        if ($targets === []) {
            return $sections;
        }

        return array_values(array_filter($sections, static function (array $section) use ($targets): bool {
            foreach ($targets as $major) {
                if (Versions::holds($section['since'], $section['until'], $major)) {
                    return true;
                }
            }

            return false;
        }));
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
     * @param int|array<int, int>|null $target The majors the same call searched.
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReachingSubsets(string $query, array $documentIds = [], int|array|null $target = null): array
    {
        $texts = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::forVersion(self::sections($content), $target) as $section) {
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
     * are kept as written, so code fences and nested lists survive, and the
     * binding lines below the heading are read off and removed.
     *
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
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
     * nothing under it is not an answer. A section that is nothing but its
     * binding is one of those, and it is dropped rather than returned empty.
     *
     * @param array<int, array{heading: string, body: string, since: ?int, until: ?int}> $sections
     * @param array<int, string> $buffer
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
     */
    private static function flushSection(array $sections, string $heading, array $buffer): array
    {
        $bound = self::readBinding($buffer);
        $body = trim(implode("\n", $bound['lines']));
        if ($body !== '') {
            $sections[] = [
                'heading' => $heading,
                'body' => $body,
                'since' => $bound['since'],
                'until' => $bound['until'],
            ];
        }

        return $sections;
    }

    /**
     * The binding declared at the top of a section, and the section without it.
     *
     * Only the run of lines before the first line of content is read, so a
     * `**Since:**` written further down is body text and binds nothing — the
     * declaration has one place, which is what keeps a reader from having to
     * search a section for the range it holds on.
     *
     * @param array<int, string> $lines
     * @return array{since: ?int, until: ?int, lines: array<int, string>}
     */
    private static function readBinding(array $lines): array
    {
        $since = null;
        $until = null;

        foreach ($lines as $index => $line) {
            if (trim($line) === '') {
                continue;
            }
            if (preg_match(self::BINDING, trim($line), $matches) !== 1) {
                break;
            }

            if ($matches[1] === 'Since') {
                $since = (int) $matches[2];
            } else {
                $until = (int) $matches[2];
            }
            unset($lines[$index]);
        }

        return ['since' => $since, 'until' => $until, 'lines' => array_values($lines)];
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
