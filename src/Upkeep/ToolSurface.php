<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;

/**
 * The tool surface as a page: what every tool is called, what it takes, and
 * which fields it answers with.
 *
 * A tool declares all of that in the class that answers it, and that is meant
 * to be the only copy — a surface written out a second time by hand stops
 * describing the answer at the first change nobody carried across. So this
 * renders it from Registry::definitions(), and `tools:check` fails where the
 * file no longer says what the registry does.
 *
 * What is not here is a filled answer. That needs an installation to call and
 * no test run discovers one, so what this page promises is the shape a client
 * may validate against, which is the half the classes already declare.
 */
final class ToolSurface
{
    /** The width the rest of the documentation is written at. */
    private const WIDTH = 79;

    /**
     * Where the generated part begins, so the head above it survives a
     * regeneration — the same arrangement the two index commands write under.
     */
    private const SURFACE_STARTS = '/## `typo3_[^\n]*(?:\n.*)?$/s';

    public static function file(): string
    {
        return Paths::root() . '/documentation/clients/tools.md';
    }

    /** The page as it would be written now: the head it is opened by, then every tool. */
    public static function page(): string
    {
        $contents = (string) file_get_contents(self::file());

        return (string) preg_replace(self::SURFACE_STARTS, '', $contents) . self::rendered();
    }

    /** Every offered tool, in the order a client is shown them. */
    private static function rendered(): string
    {
        return implode("\n", array_map(self::tool(...), Registry::definitions()));
    }

    /**
     * @param array{name: string, description: string, inputSchema: array<string, mixed>, annotations: array<string, bool>, outputSchema: array<string, mixed>|null} $definition
     */
    private static function tool(array $definition): string
    {
        $arguments = self::fields($definition['inputSchema'], 0);
        $answer = $definition['outputSchema'] ?? [];

        $lines = [
            '## `' . $definition['name'] . '`',
            '',
            self::wrap($definition['description']),
            '',
            self::annotations($definition['annotations']),
            '',
            ...self::recorded($definition['name']),
            ...self::block('Takes', $arguments),
            '',
            ...self::block('Answers with', self::fields($answer, 0)),
        ];

        $alternatives = self::alternatives($answer);
        if ($alternatives !== '') {
            array_push($lines, '', $alternatives);
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * The page holding what this tool answered, where one was recorded.
     *
     * The fields below say what comes back; that page says what a filled one
     * looks like. It sat one link away in the head of this file, which is a
     * link nobody follows while reading the tool they came for.
     *
     * @return list<string>
     */
    private static function recorded(string $name): array
    {
        if (!is_file(ToolAnswers::file($name))) {
            return [];
        }

        return [
            sprintf('**Answered**, once, against a checkout: [tool-answers/%s.md](tool-answers/%s.md).', $name, $name),
            '',
        ];
    }

    /**
     * One half of a tool: its label, then the fields under it. A tool that
     * takes nothing says so on the label rather than under an empty heading.
     *
     * @param list<string> $fields
     * @return list<string>
     */
    private static function block(string $label, array $fields): array
    {
        return $fields === [] ? ['**' . $label . '** nothing.'] : ['**' . $label . '**', '', ...$fields];
    }

    /**
     * What the tool does to the world, in the words the protocol states it in.
     * The names are the client's, so they are printed rather than translated.
     *
     * @param array<string, bool> $annotations
     */
    private static function annotations(array $annotations): string
    {
        $stated = [];
        foreach ($annotations as $hint => $value) {
            $stated[] = sprintf('`%s: %s`', $hint, $value ? 'true' : 'false');
        }

        return implode(' · ', $stated);
    }

    /**
     * One line per field, and the fields below it indented under it.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function fields(array $schema, int $depth): array
    {
        $required = (array) ($schema['required'] ?? []);
        $indent = str_repeat('  ', $depth);

        $lines = [];
        foreach ((array) ($schema['properties'] ?? []) as $name => $field) {
            $field = (array) $field;
            $says = self::says($field);
            $lines[] = self::wrap(sprintf(
                '%s- `%s` *(%s%s)*%s',
                $indent,
                $name,
                self::type($field),
                in_array($name, $required, true) ? ', required' : '',
                $says === '' ? '' : ' — ' . $says,
            ), $indent . '  ');
            array_push($lines, ...self::fields(self::below($field), $depth + 1));
        }

        return $lines;
    }

    /**
     * The schema whose fields sit under this one: what a list holds, or the
     * object itself. A field that holds neither has nothing below it.
     *
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    private static function below(array $field): array
    {
        return ($field['type'] ?? '') === 'array' ? (array) ($field['items'] ?? []) : $field;
    }

    /** @param array<string, mixed> $field */
    private static function type(array $field): string
    {
        $type = $field['type'] ?? 'object';
        if (is_array($type)) {
            return implode(' or ', $type);
        }
        if ($type === 'array') {
            return 'array of ' . self::type((array) ($field['items'] ?? []));
        }

        return (string) $type;
    }

    /**
     * What the field says about itself, the values it is limited to first: a
     * closed set is what a caller has to pass, and the description behind it
     * explains the cases rather than listing them.
     *
     * @param array<string, mixed> $field
     */
    private static function says(array $field): string
    {
        $description = (string) ($field['description'] ?? '');
        if (!isset($field['enum'])) {
            return $description;
        }

        $values = array_map(
            static fn(mixed $value): string => '`' . ($value ?? 'null') . '`',
            (array) $field['enum'],
        );

        return trim('One of ' . implode(', ', $values) . '. ' . $description);
    }

    /**
     * Where a tool answers with the result or with the reason it could not, the
     * two are alternatives and the schema says so. A field list alone would
     * read as one answer carrying everything.
     *
     * @param array<string, mixed> $schema
     */
    private static function alternatives(array $schema): string
    {
        if (!isset($schema['oneOf'])) {
            return '';
        }

        $sets = array_map(
            static fn(array $branch): string => implode(', ', array_map(
                static fn(string $field): string => '`' . $field . '`',
                (array) ($branch['required'] ?? []),
            )),
            (array) $schema['oneOf'],
        );

        return self::wrap('The answer carries exactly one of these sets of fields: '
            . implode(' — or ', $sets) . '.');
    }

    /** Wrapped where the repository wraps, with what a continued line opens with. */
    private static function wrap(string $text, string $continuation = ''): string
    {
        return wordwrap($text, self::WIDTH - strlen($continuation), "\n" . $continuation);
    }
}
