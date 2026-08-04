<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Manual\Documentation;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * The official, versioned TYPO3 manuals at docs.typo3.org — one of the two
 * tools that reach a host outside this package, beside `typo3_gerrit_lookup`.
 */
final class DocumentationLookup extends ReadOnlyTool
{
    /**
     * The share of a query a result carries before the answer warns about it.
     * It is the value `Documents::search()` drops a section below, so the two
     * corpora this server matches prose against say "half the query" in one
     * number.
     *
     * Here it drops nothing. Over a table of contents no value of it both
     * empties the collisions a six-word question returns and keeps the page
     * that answers a three-word one — `D-ANS-051`.
     */
    private const COVERS_THE_QUESTION = 0.5;

    public static function name(): string
    {
        return 'typo3_documentation_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Search or read the official live TYPO3 documentation for a covered TYPO3 line. Search with several short English queries; every result carries a canonical URL. Pass one of those URLs back as page with the same targetVersion to receive that page as text, including headings and code examples. A query naming a Fluid tag such as f:if is answered from the ViewHelper reference alone; ask without the prefix for the other manuals\' Fluid chapters. This reaches docs.typo3.org, unlike the bundled convention lookups.';
    }

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => true,
            'destructiveHint' => false,
            'idempotentHint' => true,
            'openWorldHint' => true,
        ];
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'queries' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'minLength' => 1],
                    'minItems' => 1,
                    'description' => 'Short search queries in English. Pass alternatives separately, for example ["page title event", "page title provider"]. A call carries queries or page, never both.',
                ],
                'page' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Canonical page URL returned by an earlier search, read as text. Pass it with the same targetVersion. A call carries queries or page, never both.',
                ],
                'targetVersion' => ['type' => 'string', 'minLength' => 1, 'description' => 'Covered TYPO3 version whose official manual must answer, for example "13.4" or "14". There is no fallback to another release.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 6],
            ],
            'required' => ['targetVersion'],
            'oneOf' => [
                ['required' => ['queries']],
                ['required' => ['page']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'mode' => ['type' => 'string', 'enum' => ['search', 'page']],
            'status' => ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']],
            'targetVersion' => Schema::string('The exact documentation release searched.'),
            'source' => Schema::string('The external documentation host.'),
            'queries' => Schema::listOf(Schema::string()),
            'results' => Schema::listOf(Schema::object([
                'title' => Schema::string(),
                'url' => Schema::string('Canonical URL of the matching documentation page.'),
                'document' => Schema::string('Official document identifier.'),
                'documentTitle' => Schema::string(),
                'documentVersion' => Schema::string(),
                'section' => Schema::string(),
                'excerpt' => Schema::string('Short route into the source, empty only when the result page could not be read after its index matched.'),
                'content' => Schema::string('The selected page as text in page mode; empty in search mode.'),
                'coverage' => [
                    'type' => ['number', 'null'],
                    'description' => 'Share of the query\'s weight this page carries, 0 to 1, for the query it is '
                        . 'returned for. Below 0.5 the page carries some words of the question and not its subject, '
                        . 'and the answer says so above the results — it is returned anyway, because over a table of '
                        . 'contents the page that answers a three-word question covers about a third of it. Null in '
                        . 'page mode, where nothing was searched for.',
                ],
                'matched' => Schema::listOf(Schema::object([
                    'term' => Schema::string('The query word, reduced to the stem that was searched for.'),
                    'field' => ['type' => 'string', 'enum' => ['title', 'path', 'manual'], 'description' => 'Where it was found: the page title, the section path it sits in, or the name of the manual.'],
                ], ['term', 'field']), 'What this page was matched on. Every query word missing from it reached this page nowhere, so a result whose match is made of the words around the subject is an aimed answer rather than one about the subject; ask again with the subject alone. Empty in page mode.'),
            ], ['title', 'url', 'document', 'documentTitle', 'documentVersion', 'section', 'excerpt', 'content', 'coverage', 'matched'])),
            'unavailable' => [
                'type' => ['object', 'null'],
                'description' => 'Why nothing was answered, where status says unavailable. Null otherwise.',
                'properties' => [
                    'cause' => [
                        'type' => 'string',
                        'enum' => ['version-not-covered', 'source-not-answering'],
                        'description' => 'version-not-covered: the release asked about is outside the ones this server '
                            . 'knows the manuals for, and asking again changes nothing. source-not-answering: '
                            . 'docs.typo3.org did not answer this time, and the same call may answer the next.',
                    ],
                    'reason' => Schema::string(),
                ],
                'required' => ['cause', 'reason'],
            ],
        ], ['mode', 'status', 'targetVersion', 'source', 'queries', 'results', 'unavailable']);
    }

    public static function answer(array $args): ToolResult
    {
        $queries = is_array($args['queries'] ?? null)
            ? array_values(array_filter($args['queries'], is_string(...)))
            : [];
        $page = is_string($args['page'] ?? null) ? trim($args['page']) : '';
        $statedVersion = is_string($args['targetVersion'] ?? null) ? trim($args['targetVersion']) : '';
        $major = Versions::major($statedVersion);
        $branch = $major === null ? null : Versions::branch($major);
        $limit = is_int($args['limit'] ?? null) ? max(1, min(10, $args['limit'])) : 6;

        if ($statedVersion === '' || ($queries === []) === ($page === '')) {
            throw new \InvalidArgumentException('Pass targetVersion and exactly one of queries or page');
        }

        if ($branch === null) {
            $answer = [
                'mode' => $page === '' ? 'search' : 'page',
                'status' => 'unavailable',
                'targetVersion' => $statedVersion,
                'source' => 'https://docs.typo3.org',
                'queries' => $queries,
                'results' => [],
                'unavailable' => [
                    'cause' => 'version-not-covered',
                    'reason' => sprintf(
                        'TYPO3 %s is outside the covered versions: %s.',
                        $statedVersion,
                        implode(', ', array_map(static fn(array $version): string => $version['branch'], Versions::covered())),
                    ),
                ],
            ];
        } else {
            $documentation = new Documentation();
            $answer = $page === ''
                ? $documentation->lookup($queries, $branch, $limit)
                : $documentation->page($page, $branch);
        }

        $lines = [
            sprintf('Official TYPO3 documentation for %s.', $answer['targetVersion']),
            'Source: ' . $answer['source'],
        ];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $answer['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = $answer['mode'] === 'page'
                ? 'The selected page answered without a readable main article.'
                : 'No matching section was found. The documentation service answered; narrow or rephrase the queries.';
        } elseif ($answer['mode'] === 'page') {
            $result = $answer['results'][0];
            $lines[] = '';
            $lines[] = '## ' . $result['title'];
            $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
            $lines[] = '';
            $lines[] = $result['content'];
        } else {
            // What was searched is a table of contents, so a result matched on
            // everything except the word naming the subject is one of these six
            // and not an answer. Said per result, because that is where the
            // caller reads it (`R-DOC-002`).
            $lines[] = 'Matched against page titles and section paths, never the text of a page.';
            $covered = array_map(
                static fn(array $result): float => (float) ($result['coverage'] ?? 0.0),
                $answer['results'],
            );
            // The share is on every result; this is the sentence, because a
            // number in a payload is not a warning and the answer that reads as
            // "the manual has nothing on this" is the one made of six results
            // each carrying one word of the question (`D-ANS-051`).
            if ($covered !== [] && max($covered) < self::COVERS_THE_QUESTION) {
                $lines[] = sprintf(
                    'Nothing found covers half of a query asked: the best carries %d%% of its weight.',
                    (int) round(max($covered) * 100),
                );
                $lines[] = 'These pages carry words of the question rather than its subject; ask again with the subject alone.';
            }
            foreach ($answer['results'] as $result) {
                $lines[] = '';
                $lines[] = '## ' . $result['title'];
                $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
                $lines[] = sprintf(
                    'Matched on: %s — covers %d%% of the query.',
                    implode(', ', array_map(
                        static fn(array $matched): string => $matched['term'] . ' (' . $matched['field'] . ')',
                        $result['matched'],
                    )),
                    (int) round((float) ($result['coverage'] ?? 0.0) * 100),
                );
                if ($result['excerpt'] !== '') {
                    $lines[] = $result['excerpt'];
                }
            }
        }

        return ToolResult::create(implode("\n", $lines), $answer);
    }
}
