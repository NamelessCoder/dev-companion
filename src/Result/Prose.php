<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Knowledge\Documents;

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
     * What the prose cannot say of itself.
     *
     * The architecture hints carry since/until per statement and are filtered
     * or labelled by version. The markdown documents are the long form of the
     * same subjects and carry nothing, so a section describing a shape that
     * arrived in v13 reads on v12 exactly as it reads on main. Rather than
     * building a second binding mechanism for prose, every prose answer says
     * which of the two the caller is holding.
     */
    public const NOT_VERSION_BOUND = 'These sections are prose and are not filtered by version. '
        . 'Where a subsystem changed inside the covered range, the statement that changed carries the range in the '
        . 'architecture hints — call typo3_architecture_lookup with targetVersion for the form that holds on yours.';

    /**
     * Renders matched knowledge sections as coherent excerpts: the section
     * keeps its own heading and original formatting, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
     */
    public static function sections(array $results): string
    {
        return self::NOT_VERSION_BOUND . "\n\n" . implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $source = sprintf(
                'Source: %s (typo3://core/%s) — matches %d%% of the query terms',
                $result['title'],
                $result['id'],
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
     * @param array<int, array{id: string, title: string, heading: string, body: string, score: int, coverage: float, truncated: bool}> $results
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
