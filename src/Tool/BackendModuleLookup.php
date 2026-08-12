<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * The backend modules the installation has registered.
 *
 * Read from the booted container rather than from `debug:backend:modules`,
 * whose CSV carries neither the navigation component a module resolves to nor
 * any route beyond the module's own path — and which the two maintained LTS
 * lines do not have at all, the command being TYPO3 v14 and up. `D-ANS-077`.
 */
final class BackendModuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_backend_module_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'List the backend modules registered in the TYPO3 installation you are working in, with the extension that declares each one, its place in the module tree, its labels, its access level, the route each one answers on and every sub-route it registers. It carries the navigation component as the module tree resolves it, which is the value a Configuration/Backend/Modules.php cannot give you: it is inherited from the parent module, so reading the registration files says a module is not page-tree navigated when it is. A project extension\'s modules are in it, because the installation is booted and asked rather than a snapshot read.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Module identifier, label, route, navigation component, or extension name to filter by. Omit to list every module.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'modules' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'parents' => Schema::listOf(Schema::string(), 'The modules it sits under, outermost first.'),
                'extension' => Schema::string('The package that declares it.'),
                'labels' => Schema::string('Its label, with the translation domain reference behind it.'),
                'path' => Schema::string('The backend route it answers on.'),
                'position' => Schema::string('Its declared before/after position, if any.'),
                'navigationComponent' => Schema::string('The navigation component as resolved, inheritance included — "@typo3/backend/tree/page-tree-element" is the page tree. Empty where the module has none. The value differs between TYPO3 versions, which is why it is read from the installation.'),
                'access' => Schema::string('Who may call it: "user", "admin", "systemMaintainer".'),
                'routes' => Schema::listOf(Schema::object([
                    'name' => Schema::string('The name the registration gives it; "_default" is what the module opens with.'),
                    'identifier' => Schema::string('The route identifier it is registered under: the module identifier for "_default", "<module>.<name>" for every other one.'),
                    'path' => Schema::string(),
                    'target' => Schema::string('Controller::method it dispatches to.'),
                ], ['name', 'identifier', 'path', 'target']), 'Every route the module registers. Empty for a first-level module that is not standalone, which registers none.'),
            ], ['identifier', 'parents', 'extension', 'path', 'navigationComponent', 'routes'])),
        ], ['query', 'matchCount', 'answeredBy', 'modules'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = mb_strtolower(trim((string) ($args['query'] ?? '')));

        $topic = Typo3Runtime::topic('modules');
        if (!is_array($topic) || !is_array($topic['modules'] ?? null)) {
            $reason = Typo3Runtime::reason();
            if ($reason === '') {
                // The boot came up and this one topic did not, which the probe
                // says why of. Every other topic of the same reading answered.
                $reason = is_array($topic) && is_string($topic['unavailable'] ?? null)
                    ? 'the installation booted and its module registry could not be read: ' . $topic['unavailable']
                    : 'the installation booted and answered nothing about its backend modules';
            }

            return Unsupported::because($reason, ['query' => $query]);
        }

        $modules = [];
        foreach ($topic['modules'] as $module) {
            if (!is_array($module)) {
                continue;
            }
            $module = self::shape($module);
            $haystack = mb_strtolower(implode(' ', array_merge(
                $module['parents'],
                array_column($module['routes'], 'identifier'),
                [
                    $module['identifier'],
                    $module['extension'],
                    $module['labels'],
                    $module['path'],
                    $module['navigationComponent'],
                ],
            )));
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
            if ($module['navigationComponent'] !== '') {
                $lines[] = '  navigation: ' . $module['navigationComponent'];
            }
            foreach ($module['routes'] as $route) {
                if ($route['name'] === '_default') {
                    continue;
                }
                $lines[] = '  route ' . $route['identifier'] . '  ' . $route['path'];
            }
        }
        $lines[] = '';
        $lines[] = 'A module is declared in its extension\'s Configuration/Backend/Modules.php; the label in '
            . 'brackets is a translation domain reference. The navigation component is the resolved one: a module '
            . 'inherits its parent\'s, so the registration file of a page-tree navigated module often names none.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($modules),
            'modules' => $modules,
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * One module of the topic, in the shape the schema declares.
     *
     * @param array<mixed> $module
     * @return array{identifier: string, parents: array<int, string>, extension: string, labels: string, path: string, position: string, navigationComponent: string, access: string, routes: array<int, array{name: string, identifier: string, path: string, target: string}>}
     */
    private static function shape(array $module): array
    {
        $routes = [];
        foreach (is_array($module['routes'] ?? null) ? $module['routes'] : [] as $route) {
            if (!is_array($route)) {
                continue;
            }
            $routes[] = [
                'name' => (string) ($route['name'] ?? ''),
                'identifier' => (string) ($route['identifier'] ?? ''),
                'path' => (string) ($route['path'] ?? ''),
                'target' => (string) ($route['target'] ?? ''),
            ];
        }

        return [
            'identifier' => (string) ($module['identifier'] ?? ''),
            'parents' => array_values(array_map(
                'strval',
                is_array($module['parents'] ?? null) ? $module['parents'] : [],
            )),
            'extension' => (string) ($module['extension'] ?? ''),
            'labels' => (string) ($module['labels'] ?? ''),
            'path' => (string) ($module['path'] ?? ''),
            'position' => (string) ($module['position'] ?? ''),
            'navigationComponent' => (string) ($module['navigationComponent'] ?? ''),
            'access' => (string) ($module['access'] ?? ''),
            'routes' => $routes,
        ];
    }
}
