<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unanswered;

/**
 * An effective TYPO3_CONF_VARS value: what it is at runtime after every
 * extension has had its say, which is rarely what the shipped default says.
 */
final class ConfigurationLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_configuration_lookup';
    }

    public static function description(): string
    {
        return 'Read an effective TYPO3_CONF_VARS value from the installation you are working in — the value as it is at runtime after every extension has had its say, not the shipped default. Use it for configuration whose assembled shape matters, such as SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => ['type' => 'string', 'minLength' => 1, 'description' => 'Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or "SYS/formEngine/formDataGroup".'],
            ],
            'required' => ['path'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'path' => Schema::string('The TYPO3_CONF_VARS path that was read.'),
            'found' => ['type' => ['boolean', 'null'], 'description' => 'Whether the installation has a value at that path. Null when nothing was consulted — see unavailable; false is a statement about the installation and is never made without one.'],
            'value' => ['description' => 'The effective runtime value, of whatever shape the configuration has.'],
            'answeredBy' => Schema::answeredBy(),
            'unavailable' => Schema::unavailable(),
        ], ['path', 'found', 'answeredBy']);
    }

    public static function answer(array $args): ToolResult
    {
        $path = trim((string) ($args['path'] ?? ''), " \t/");

        $answer = Typo3Cli::json(['configuration:show', $path, '--type=active', '--json']);

        // A path that does not exist is a legitimate answer, not a breakage,
        // and the console says which of the two happened in its exit code.
        if (!$answer['ok'] && $answer['exitCode'] === 1 && str_contains($answer['error'], 'No configuration found')) {
            return ToolResult::create(
                sprintf('The installation has no configuration at "%s".', $path),
                ['path' => $path, 'found' => false, 'value' => null, 'answeredBy' => 'installation'],
            );
        }
        if (!$answer['ok']) {
            // found stays null rather than false: false is a statement about
            // the installation — "it has no value at that path" — and nothing
            // was consulted to make it.
            return Unanswered::because($answer['error'], ['path' => $path, 'found' => null, 'value' => null]);
        }

        return ToolResult::create(
            sprintf(
                "Effective value of TYPO3_CONF_VARS/%s in this installation:\n\n```json\n%s\n```\n\n"
                . 'This is the assembled runtime value, not the default the core ships.',
                $path,
                json_encode($answer['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            ),
            ['path' => $path, 'found' => true, 'value' => $answer['data'], 'answeredBy' => 'installation'],
        );
    }
}
