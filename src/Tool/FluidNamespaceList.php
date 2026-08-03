<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\FluidNamespaces;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unsupported;

/**
 * The globally registered Fluid namespaces of the installation.
 */
final class FluidNamespaceList extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_fluid_namespace_list';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'List the Fluid ViewHelper namespaces that are globally available in the TYPO3 installation you are working in, so a template knows which prefixes it may use without declaring them. Every other namespace has to be declared per template with an xmlns attribute. Where the console cannot be reached, the Configuration/Fluid/Namespaces.php the installed packages declare is read instead.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'namespaces' => Schema::listOf(Schema::object([
                'prefix' => Schema::string('The prefix usable in a template without declaring it, for example "core".'),
                'phpNamespaces' => Schema::listOf(Schema::string(), 'The PHP namespaces it resolves ViewHelpers from.'),
            ], ['prefix', 'phpNamespaces'])),
        ], ['matchCount', 'answeredBy', 'namespaces'], []);
    }

    public static function answer(array $args): ToolResult
    {
        $answer = Typo3Cli::json(['fluid:namespaces', '--json']);
        $answeredBy = 'installation';
        $declared = is_array($answer['data']) ? $answer['data'] : [];
        if (!$answer['ok'] || !is_array($answer['data'])) {
            // The declarations are files in the same packages, so a console
            // that cannot boot does not have to end the question. What the
            // files cannot say is what the container did with them.
            $declared = FluidNamespaces::all();
            if ($declared === []) {
                return Unsupported::because($answer['error']);
            }
            $answeredBy = 'packages';
        }

        $namespaces = [];
        foreach ($declared as $prefix => $classNames) {
            $namespaces[] = [
                'prefix' => (string) $prefix,
                'phpNamespaces' => array_map('strval', (array) $classNames),
            ];
        }
        usort($namespaces, static fn(array $a, array $b): int => strcmp($a['prefix'], $b['prefix']));

        $lines = [sprintf('%d globally registered Fluid namespace(s):', count($namespaces))];
        foreach ($namespaces as $namespace) {
            $lines[] = '- ' . $namespace['prefix'] . ': ' . implode(', ', $namespace['phpNamespaces']);
        }
        $lines[] = '';
        $lines[] = 'These prefixes work in any template without being declared. Every other namespace is declared '
            . 'in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root '
            . 'element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.';
        if ($answeredBy === 'packages') {
            $lines[] = '';
            $lines[] = sprintf(
                'Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not '
                . 'be asked (%s). That is what the packages declare, not what the container assembled from them.',
                $answer['error'],
            );
        }

        return ToolResult::create(implode("\n", $lines), [
            'matchCount' => count($namespaces),
            'namespaces' => $namespaces,
            'answeredBy' => $answeredBy,
        ]);
    }
}
