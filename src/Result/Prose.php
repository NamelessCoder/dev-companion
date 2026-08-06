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
     * The pages the excerpts were cut out of, as the resources they are readable
     * whole as.
     *
     * A search answers the section the query matched, and a procedure is several
     * of them: the session that reported this had the Gerrit push page in three
     * different answers and never learned it was one page — `D-AUD-007`. The
     * `Source:` line above each excerpt carries the same uri and was read as
     * attribution, which is what this says out loud instead.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    private static function readWhole(array $results): string
    {
        $documents = [];
        foreach ($results as $result) {
            $documents[$result['id']] = sprintf('%s (%s)', $result['title'], Documents::uri($result['id']));
        }

        return 'Each excerpt above is one section of a longer document. Where the task is the whole procedure '
            . 'rather than the fact you searched for, read the page: ' . implode(', ', $documents)
            . '. A client may render no resource list, so that address is how one is reached.';
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
                $body .= "\n\n(section truncated — read " . Documents::uri($result['id']) . ' for the rest)';
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
