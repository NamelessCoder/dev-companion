<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\Unanswered;
use Typo3CmsMcp\ToolResult;
use Typo3CmsMcp\Typo3Cli;

/**
 * The backend modules the installation has registered.
 *
 * The console has no JSON mode here, but it has a CSV one, which is a format
 * rather than a rendering — so nothing is recovered from a drawn table.
 */
final class BackendModuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_backend_module_lookup';
    }

    public static function description(): string
    {
        return 'List the backend modules registered in the TYPO3 installation you are working in, with the extension that declares each one, its place in the module tree, its labels and its route. Answered by the installation, so a project extension\'s modules are in it.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Module identifier, label, route, or extension name to filter by. Omit to list every module.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::string(),
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(),
            'unavailable' => Schema::unavailable(),
            'modules' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'parents' => Schema::listOf(Schema::string(), 'The modules it sits under, outermost first.'),
                'extension' => Schema::string('The package that declares it.'),
                'labels' => Schema::string('Its label, with the translation domain reference behind it.'),
                'path' => Schema::string('The backend route it answers on.'),
                'position' => Schema::string('Its declared before/after position, if any.'),
            ], ['identifier', 'parents', 'extension', 'path'])),
        ], ['query', 'matchCount', 'answeredBy', 'modules']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = mb_strtolower(trim((string) ($args['query'] ?? '')));

        $result = Typo3Cli::run(['debug:backend:modules', '--csv-export']);
        if (!$result['ok']) {
            return Unanswered::because(
                $result['error'] !== '' ? $result['error'] : trim($result['output']),
                ['query' => $query, 'matchCount' => 0, 'modules' => []],
            );
        }

        $modules = [];
        foreach (self::csvRows($result['output']) as $row) {
            // The three level columns are one path through the module tree, so
            // the deepest filled one is the module and the rest are above it.
            $levels = array_values(array_filter([
                $row['Main level'] ?? '',
                $row['Second level'] ?? '',
                $row['Third level'] ?? '',
            ], static fn(string $level): bool => trim($level) !== ''));
            if ($levels === []) {
                continue;
            }

            $module = [
                'identifier' => (string) array_pop($levels),
                'parents' => $levels,
                'extension' => (string) ($row['Pkg'] ?? ''),
                'labels' => (string) ($row['Labels'] ?? ''),
                'path' => (string) ($row['Path'] ?? ''),
                'position' => (string) ($row['Position'] ?? ''),
            ];

            $haystack = mb_strtolower(implode(' ', array_merge($module['parents'], [
                $module['identifier'],
                $module['extension'],
                $module['labels'],
                $module['path'],
            ])));
            if ($query !== '' && !str_contains($haystack, $query)) {
                continue;
            }
            $modules[] = $module;
        }

        if ($modules === []) {
            return ToolResult::create(
                sprintf('No backend module in this installation matches "%s".', $query),
                ['query' => $query, 'matchCount' => 0, 'modules' => [], 'answeredBy' => 'installation'],
            );
        }

        $lines = [sprintf('%d backend module(s)%s:', count($modules), $query === '' ? '' : ' matching "' . $query . '"')];
        foreach ($modules as $module) {
            $lines[] = '- ' . implode(' > ', array_merge($module['parents'], [$module['identifier']]));
            $lines[] = '  ' . $module['path'] . '  (' . $module['extension'] . ')';
            if ($module['labels'] !== '') {
                $lines[] = '  ' . $module['labels'];
            }
        }
        $lines[] = '';
        $lines[] = 'A module is declared in its extension\'s Configuration/Backend/Modules.php; the label in '
            . 'brackets is a translation domain reference.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($modules),
            'modules' => $modules,
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function csvRows(string $output): array
    {
        $lines = preg_split('/\R/', trim($output)) ?: [];
        $headers = null;
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $fields = str_getcsv($line, ';', '"', '\\');
            if ($headers === null) {
                $headers = array_map('strval', $fields);
                continue;
            }
            if (count($fields) !== count($headers)) {
                continue;
            }
            $rows[] = array_map(static fn($v): string => (string) $v, array_combine($headers, $fields));
        }

        return $rows;
    }
}
