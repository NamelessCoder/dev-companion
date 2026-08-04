<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Versions;

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
        return self::BOUND_ELSEWHERE . "\n\n" . implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $versions = Versions::label($result['since'] ?? null, $result['until'] ?? null);
            $source = sprintf(
                'Source: %s (typo3://core/%s)%s — matches %d%% of the query terms',
                $result['title'],
                $result['id'],
                $versions === '' ? '' : ' [' . $versions . ']',
                (int) round($result['coverage'] * 100),
            );

            $body = $result['body'];
            if ($result['truncated']) {
                $body .= "\n\n(section truncated — read typo3://core/" . $result['id'] . ' for the rest)';
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
            'uri' => 'typo3://core/' . $result['id'],
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
