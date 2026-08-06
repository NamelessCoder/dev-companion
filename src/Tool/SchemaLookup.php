<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * The columns TYPO3 adds to a table by itself, which an ext_tables.sql may
 * leave out.
 *
 * The core derives a table's technical columns from its TCA: `uid` and `pid`,
 * the timestamps, the delete and disable fields, the language and versioning
 * columns, and one column per TCA field whose type says what it stores. A
 * declaration that repeats them is the thing a review cannot check without
 * asking the installation, which is what `REVIEW-02` run 5 stopped at.
 *
 * Bounded to that side on purpose (D-DIS-008): what is derived, never what the
 * database has. The live schema is a different question, it needs a schema to
 * exist, and this one is asked while writing the file that creates it.
 */
final class SchemaLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_schema_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'List the columns TYPO3 derives for a table from its TCA — uid, pid, the timestamps, the delete and disable fields, the language and versioning columns, and one column per TCA field. Those are exactly the columns an ext_tables.sql does not have to declare, so this is what a redundant declaration is checked against. The core is asked for them by booting the installation; it says so rather than answering empty when it cannot. It describes what TYPO3 would create, never what the database currently has.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'description' => 'The table to list the derived columns of, for example "tt_content". Omit to list every table TYPO3 derives columns for, with how many each gets.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'table' => Schema::nullableString('The table asked about. Null where none was named and the answer is the list of them.'),
            'matchCount' => Schema::integer('Columns for a named table, tables for a call that named none. Zero means the name is not a TCA table in this installation, never that TYPO3 derives nothing.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'columns' => Schema::listOf(Schema::object([
                'name' => Schema::string(),
                'type' => Schema::string('The Doctrine type the core declares it as: integer, string, text, datetime, json, blob.'),
                'notnull' => ['type' => 'boolean'],
                'default' => ['description' => 'The default the core gives it, null where it declares none.'],
                'length' => ['type' => ['integer', 'null'], 'description' => 'Length where the type carries one.'],
            ], ['name', 'type', 'notnull']), 'Empty where no table was named.'),
            'tables' => Schema::listOf(Schema::object([
                'table' => Schema::string(),
                'columnCount' => Schema::integer(),
                'relationTable' => ['type' => 'boolean', 'description' => 'True where TYPO3 creates the table itself for an MM relation. No ext_tables.sql declares one at all.'],
            ], ['table', 'columnCount', 'relationTable']), 'Every table TYPO3 derives columns for. Returned on a call that named none, and on one whose name is not among them.'),
        ], ['table', 'matchCount', 'answeredBy', 'columns', 'tables'], ['table']);
    }

    public static function answer(array $args): ToolResult
    {
        $table = trim((string) ($args['table'] ?? ''));
        $echo = ['table' => $table === '' ? null : $table];

        if (!Instance::isAvailable()) {
            return Unsupported::because(
                'no TYPO3 installation was found from the directory this server was started in',
                $echo,
            );
        }

        // Two ways not to have an answer, and the caller acts on them
        // differently: no reading at all is the boot — a container that stayed
        // failsafe, a project that is down — while a reading carrying
        // `unavailable` is the enrichment itself, which is the database.
        $derived = Typo3Runtime::topic('derivedColumns');
        if (!is_array($derived)) {
            return Unsupported::because(Typo3Runtime::reason(), $echo);
        }
        if (isset($derived['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not derive its columns: ' . (string) $derived['unavailable'],
                $echo,
            );
        }

        /** @var array<string, array{columns: array<int, array<string, mixed>>, relationTable: bool}> $tables */
        $tables = is_array($derived['tables'] ?? null) ? $derived['tables'] : [];
        $index = array_map(static fn(string $name): array => [
            'table' => $name,
            'columnCount' => count($tables[$name]['columns']),
            'relationTable' => (bool) $tables[$name]['relationTable'],
        ], array_keys($tables));

        if ($table === '') {
            return ToolResult::create(
                implode("\n", [
                    sprintf('TYPO3 derives columns for %d tables in this installation.', count($index)),
                    'Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.',
                    '',
                    ...array_map(
                        static fn(array $entry): string => sprintf(
                            '- %s: %d columns%s',
                            $entry['table'],
                            $entry['columnCount'],
                            $entry['relationTable'] ? ' (created for an MM relation; declare nothing for it)' : '',
                        ),
                        $index,
                    ),
                ]),
                $echo + [
                    'matchCount' => count($index),
                    'answeredBy' => 'installation',
                    'columns' => [],
                    'tables' => $index,
                ],
            );
        }

        if (!isset($tables[$table])) {
            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        '"%s" is not a table this installation has TCA for, so TYPO3 derives no columns for it. A '
                        . 'table an extension declares without TCA is entirely its own.',
                        $table,
                    ),
                    '',
                    'The tables it does derive for: ' . implode(', ', array_column($index, 'table')) . '.',
                ]),
                $echo + [
                    'matchCount' => 0,
                    'answeredBy' => 'installation',
                    'columns' => [],
                    'tables' => $index,
                ],
            );
        }

        $columns = $tables[$table]['columns'];

        return ToolResult::create(
            implode("\n", [
                sprintf(
                    'TYPO3 derives %d columns for %s from its TCA%s. An ext_tables.sql that declares one of them '
                    . 'again is declaring what the core already creates.',
                    count($columns),
                    $table,
                    $tables[$table]['relationTable']
                        ? ', and creates the table itself for an MM relation'
                        : '',
                ),
                '',
                ...array_map(static fn(array $column): string => sprintf(
                    '- %s %s%s%s',
                    $column['name'],
                    $column['type'],
                    ($column['notnull'] ?? false) === true ? ' NOT NULL' : '',
                    array_key_exists('default', $column) && $column['default'] !== null
                        ? ' DEFAULT ' . var_export($column['default'], true)
                        : '',
                ), $columns),
            ]),
            $echo + [
                'matchCount' => count($columns),
                'answeredBy' => 'installation',
                'columns' => $columns,
                'tables' => [],
            ],
        );
    }
}
