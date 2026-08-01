<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Documentation;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\ToolResult;

/**
 * The official, versioned TYPO3 manuals at docs.typo3.org — the one tool that
 * reaches outside this package.
 */
final class DocumentationLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_documentation_lookup';
    }

    public static function description(): string
    {
        return 'Search or read the official live TYPO3 documentation for a covered TYPO3 line. Search with several short English queries; every result carries a canonical URL. Pass one of those URLs back as page with the same targetVersion to receive that page as text, including headings and code examples. This reaches docs.typo3.org, unlike the bundled convention lookups.';
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
                    'description' => 'Short search queries in English. Pass alternatives separately, for example ["page title event", "page title provider"].',
                ],
                'page' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Canonical page URL returned by an earlier search. Pass it with the same targetVersion and without queries to read the page as text.',
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
            ], ['title', 'url', 'document', 'documentTitle', 'documentVersion', 'section', 'excerpt', 'content'])),
            'unavailable' => [
                'type' => ['object', 'null'],
                'properties' => [
                    'reason' => Schema::string(),
                ],
                'required' => ['reason'],
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
            foreach ($answer['results'] as $result) {
                $lines[] = '';
                $lines[] = '## ' . $result['title'];
                $lines[] = sprintf('%s · %s · %s', $result['document'], $result['documentVersion'], $result['url']);
                if ($result['excerpt'] !== '') {
                    $lines[] = $result['excerpt'];
                }
            }
        }

        return ToolResult::create(implode("\n", $lines), $answer);
    }
}
