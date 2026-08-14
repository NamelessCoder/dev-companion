<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Matched sections of the markdown knowledge documents, as an answer.
 *
 * Three tools render the same corpus — the rule lookup, the script lookup and
 * the task guide — and a caller that learns to read one of those answers has to
 * find the next one built the same way.
 */
final class Prose
{
    /**
     * Where a range that is not this corpus's own is carried.
     *
     * A section says what it holds for beside itself since `D-VER-005`, so this
     * no longer stands in for a binding. What it still answers is the question
     * the ranges here cannot: a runTests.sh command is bound to the suite in
     * test-suite-hints.json rather than to the section quoting it, and a
     * convention is bound to the statement in the hints — a 12.4 reader
     * following the wrong one of those finds nothing.
     */
    public const BOUND_ELSEWHERE = 'A section carries the range it holds for where it has one. '
        . 'What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and '
        . 'typo3_test_run_guide with targetVersion for a runTests.sh command.';

    /**
     * Renders matched knowledge sections as coherent excerpts: the section
     * keeps its own heading and original formatting, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    public static function sections(array $results): string
    {
        return self::BOUND_ELSEWHERE . "\n\n" . self::excerpts($results) . "\n\n" . self::readWhole($results);
    }

    /**
     * The page every match came out of, instead of the matches.
     *
     * A search whose hits all sit in one document has established which page
     * answers the task, and cutting it there buys nothing: the second search is
     * the expensive part, not the text. `feedback/2026-08-10-182523` searched
     * `core/contribution/commit-messages` twice within minutes for two of its
     * ten headings — 2940 and 3346 bytes over two calls, against 11742 for the
     * page in one — and had been told at the foot of the first answer which
     * heading the second search then went looking for (`D-ANS-076`).
     *
     * The page is handed over as written, which is what `documentId` does and
     * for the same reason: a section left out for a major it does not hold on
     * is a hole in a page, and every bound section carries its own range under
     * its heading.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    public static function wholePage(string $documentId, array $results): string
    {
        $matched = self::matchedHeadings($results);
        $headings = Documents::headings($documentId);

        return self::BOUND_ELSEWHERE . "\n\n"
            . sprintf(
                'Every section this query matched is in one document, so this is the page rather than the '
                    . 'excerpts — %s (%s), %d headings, of which the query matched %s%s. It is the page as written, so '
                    . "a section that holds on other majors than yours is in it and says so under its heading.\n\n",
                $results[0]['title'],
                Documents::uri($documentId),
                count($headings),
                $matched === [] ? 'none' : implode(', ', $matched),
                self::matchedTheOpening($results) ? ', and the opening above the first heading' : '',
            )
            . Documents::read($documentId);
    }

    /**
     * The headings the query matched, for the answer that hands the page over.
     *
     * The text above a page's first heading is a section this corpus returns
     * and carries no heading, so it is not one of these. Listed as one it named
     * nothing at all: "of which the query matched The Probe, .". The count it
     * is read against is `Documents::headings()`, which leaves the opening out
     * for the same reason.
     *
     * @param array<int, array<string, mixed>> $results
     * @return array<int, string>
     */
    public static function matchedHeadings(array $results): array
    {
        return array_values(array_unique(array_filter(array_column($results, 'heading'))));
    }

    /**
     * Whether one of the matches is the text above the page's first heading.
     *
     * @param array<int, array<string, mixed>> $results
     */
    private static function matchedTheOpening(array $results): bool
    {
        return in_array('', array_column($results, 'heading'), true);
    }

    /**
     * One whole document as the single match it is, for the two answers that
     * hand a page over rather than excerpts from it.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function pageRecords(string $documentId, string $title): array
    {
        $body = Documents::read($documentId);

        return [[
            'documentId' => $documentId,
            'title' => $title,
            'uri' => Documents::uri($documentId),
            'heading' => $title,
            'body' => $body,
            'versions' => '',
            'coverage' => 1.0,
            'score' => 0,
            'truncated' => false,
        ]];
    }

    /**
     * The pages the excerpts were cut out of, as the resources they are readable
     * whole as.
     *
     * A search answers the section the query matched, and a procedure is several
     * of them: the session that reported this had the Gerrit push page in three
     * different answers and never learned it was one page — `D-AUD-007`. The
     * `Source:` line above each excerpt carries the same uri and was read as
     * attribution, which is what this says out loud instead.
     *
     * Each page says what the search left of it, because naming the page did
     * not take on its own: the session in `feedback/2026-08-08-224406` had this
     * line, read two of nine headings of one document and searched no further —
     * the same sentence stands under an answer that matched most of a page and
     * under one that matched a ninth of it (`D-ANS-070`). The headings rather
     * than the share alone, because the next query is picked out of them.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    private static function readWhole(array $results): string
    {
        $titles = [];
        $returned = [];
        foreach ($results as $result) {
            $titles[$result['id']] = $result['title'];
            $returned[$result['id']][] = $result['heading'];
        }

        $pages = [];
        foreach ($titles as $id => $title) {
            $headings = Documents::headings($id);
            $left = array_values(array_diff($headings, $returned[$id]));
            $pages[] = sprintf(
                '- %s — %s: %s',
                $id,
                $title,
                $left === []
                    ? sprintf('all %d of its headings are above.', count($headings))
                    : sprintf(
                        '%d of its %d headings are not above — %s.',
                        count($left),
                        count($headings),
                        implode(', ', $left),
                    ),
            );
        }

        // Named as a call rather than as an address. The uri was here and three
        // core sessions held one without ever fetching the page, one of them
        // finishing a full patch review having read no document end to end and
        // going looking in the checkout for a section the same document
        // carried. A `typo3://guides` address is delivery to a client that
        // renders MCP resources; `typo3_rule_lookup` reaches every client
        // there is — `D-ANS-061`, `R-ANS-028`.
        return 'Each excerpt above is one section of a longer document, and each page below carries the `##` '
            . 'headings that are not above. Where the task is the whole procedure rather than the fact you '
            . 'searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:'
            . "\n" . implode("\n", $pages);
    }

    /**
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    private static function excerpts(array $results): string
    {
        return implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $versions = Versions::label($result['since'] ?? null, $result['until'] ?? null);
            $source = sprintf(
                'Source: %s (%s)%s — matches %d%% of the query terms',
                $result['title'],
                Documents::uri($result['id']),
                $versions === '' ? '' : ' [' . $versions . ']',
                (int) round($result['coverage'] * 100),
            );

            $body = $result['body'];
            if ($result['truncated']) {
                // Named as the call the line below names, rather than as the
                // uri it used to be: a cut is exactly where a caller stops, and
                // a client that lists no resources cannot act on an address.
                $body .= "\n\n(section truncated — read " . $result['id'] . ' whole for the rest)';
            }

            return '## ' . $heading . "\n" . $source . "\n\n" . $body;
        }, $results));
    }

    /**
     * The same matched sections as data: the document they come from, how much
     * of the query they cover, and the resource holding the full text.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     * @return array<int, array<string, mixed>>
     */
    public static function records(array $results): array
    {
        return array_map(static fn(array $result): array => [
            'documentId' => $result['id'],
            'title' => $result['title'],
            'uri' => Documents::uri($result['id']),
            'heading' => $result['heading'] === '' ? $result['title'] : $result['heading'],
            'body' => $result['body'],
            'versions' => Versions::label($result['since'] ?? null, $result['until'] ?? null),
            'coverage' => round($result['coverage'], 3),
            'score' => $result['score'],
            'truncated' => $result['truncated'],
        ], $results);
    }

    /** The topics one document covers, for an answer that has to say what it searched. */
    public static function topics(string $documentId): string
    {
        foreach (Documents::topics() as $document) {
            if ($document['id'] === $documentId) {
                return implode(', ', $document['topics']);
            }
        }

        return '';
    }
}
