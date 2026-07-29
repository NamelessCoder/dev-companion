<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Symfony\Component\Yaml\Yaml;

/**
 * What one installed extension registers, read from its own files.
 *
 * typo3_project_scope names the extensions and where they are. A maintenance
 * question is almost never about that — it is about what is inside one of them:
 * which tables its TCA defines and which it extends, which backend modules and
 * icons it brings, which site sets it ships, what it hangs into the container.
 * All of that is declarative and sits in files with fixed names, so it is
 * readable without a console and without a database, the same way the sites are.
 *
 * Nothing here is included or executed. The declaration files are tokenised for
 * their keys (see PhpArray) and the YAML is parsed; the extension's own code
 * never enters this process.
 */
final class Extension
{
    /** Directories below Classes/ whose name says what the code in them is. */
    private const CLASS_KINDS = [
        'Command', 'Controller', 'DataProcessing', 'Domain', 'Event', 'EventListener',
        'Form', 'Hooks', 'Middleware', 'Service', 'Updates', 'Upgrades', 'ViewHelpers',
    ];

    /** Files an extension is recognised by, each one a registration point. */
    private const ROOT_FILES = [
        'ext_localconf.php', 'ext_tables.php', 'ext_tables.sql', 'ext_emconf.php',
        'Configuration/page.tsconfig', 'Configuration/user.tsconfig',
        'Configuration/RequestMiddlewares.php', 'Configuration/Services.yaml',
        'Configuration/JavaScriptModules.php', 'Configuration/Fluid/Namespaces.php',
        'Initialisation/data.t3d', 'Initialisation/data.xml',
    ];

    /**
     * @return array{
     *     key: string,
     *     path: string,
     *     origin: string,
     *     composerName: ?string,
     *     description: ?string,
     *     requires: array<int, array{package: string, constraint: string}>,
     *     tcaTables: array<int, string>,
     *     tcaOverrides: array<int, string>,
     *     backendModules: array<int, string>,
     *     backendRoutes: array<int, string>,
     *     icons: array<int, string>,
     *     siteSets: array<int, array{name: string, path: string}>,
     *     middlewares: array<int, string>,
     *     serviceTags: array<int, string>,
     *     fluidRoots: array<int, string>,
     *     fluidNamespaces: array<int, string>,
     *     typoScript: array<int, string>,
     *     classes: array<int, array{kind: string, files: int}>,
     *     files: array<int, string>
     * }|null
     */
    public static function describe(string $key): ?array
    {
        $path = Instance::packages()[$key] ?? null;
        if ($path === null) {
            return null;
        }

        $manifest = self::json($path . '/composer.json');
        $requires = [];
        foreach ($manifest['require'] ?? [] as $package => $constraint) {
            $requires[] = ['package' => (string) $package, 'constraint' => (string) $constraint];
        }

        return [
            'key' => $key,
            'path' => $path,
            'origin' => self::origin($key, $path),
            'composerName' => isset($manifest['name']) ? (string) $manifest['name'] : null,
            'description' => isset($manifest['description']) ? (string) $manifest['description'] : null,
            'requires' => $requires,
            // A file below Configuration/TCA/ is named after the table it
            // defines. A file below Overrides/ is not: extensions number them
            // to fix their load order, so which table it extends is read from
            // what the file does — see overriddenTables().
            'tcaTables' => self::baseNames($path . '/Configuration/TCA/*.php'),
            'tcaOverrides' => self::overriddenTables($path),
            'backendModules' => PhpArray::keys($path . '/Configuration/Backend/Modules.php'),
            'backendRoutes' => array_merge(
                PhpArray::keys($path . '/Configuration/Backend/Routes.php'),
                PhpArray::keys($path . '/Configuration/Backend/AjaxRoutes.php'),
            ),
            'icons' => PhpArray::keys($path . '/Configuration/Icons.php'),
            'siteSets' => self::siteSets($path),
            // The outer keys are the request scopes; the identifiers a caller
            // orders its own middleware against are one level below them.
            'middlewares' => PhpArray::keys($path . '/Configuration/RequestMiddlewares.php', 2),
            'serviceTags' => self::serviceTags($path),
            'fluidRoots' => self::fluidRoots($path),
            'fluidNamespaces' => array_keys(InstalledFluidNamespaces::declaredBy($path)),
            'typoScript' => self::baseNames($path . '/Configuration/TypoScript/*.typoscript', ''),
            'classes' => self::classes($path),
            'files' => self::files($path),
        ];
    }

    /**
     * Whether this is TYPO3's own, the project's, or a dependency it pulled in
     * — the same three the project scope draws, so the two answers agree.
     */
    private static function origin(string $key, string $path): string
    {
        if (Instance::isSystemExtension($key) === true) {
            return 'system';
        }

        return str_contains(str_replace('\\', '/', $path), '/vendor/')
            ? Project::ORIGIN_THIRD_PARTY
            : Project::ORIGIN_PROJECT;
    }

    /**
     * ExtensionManagementUtility methods whose first argument is the table.
     *
     * Deliberately only these: addStaticFile() and addPiFlexFormValue() take a
     * first argument of exactly the same shape that is not a table, and a list
     * of tables with an extension key in it is worse than a shorter list.
     *
     * @var array<int, string>
     */
    private const TABLE_FIRST_METHODS = [
        'addToAllTCAtypes', 'addTCAcolumns', 'addFieldsToPalette',
        'addTcaSelectItem', 'addTcaSelectItemGroup', 'allowTableOnStandardPages',
    ];

    /**
     * The tables the override files extend, read from what they do.
     *
     * The file name is no answer here — `102_tt_content.php` and
     * `600_ext_container.php` are both ordinary — because the number is what
     * fixes the order the overrides load in. What the file touches is either
     * $GLOBALS['TCA']['<table>'] or the first argument of one of the
     * ExtensionManagementUtility calls above, and both survive tokenising.
     *
     * @return array<int, string>
     */
    private static function overriddenTables(string $path): array
    {
        $tables = [];
        foreach (glob($path . '/Configuration/TCA/Overrides/*.php') ?: [] as $file) {
            $found = self::tablesIn((string) file_get_contents($file));
            if ($found === []) {
                // Nothing recognisable: the conventional file name is the best
                // that is left, and only where it looks like a table at all.
                $name = substr(basename($file), 0, -strlen('.php'));
                $found = preg_match('/^[a-z][a-z0-9_]*$/', $name) === 1 ? [$name] : [];
            }
            foreach ($found as $table) {
                $tables[$table] = true;
            }
        }

        $names = array_keys($tables);
        sort($names);

        return $names;
    }

    /** @return array<int, string> */
    private static function tablesIn(string $code): array
    {
        $tables = [];
        $tokens = @token_get_all($code);
        $count = count($tokens);
        for ($index = 0; $index < $count; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token)) {
                continue;
            }

            if ($token[0] === T_VARIABLE && $token[1] === '$GLOBALS') {
                $keys = self::followingStrings($tokens, $index, 2);
                if (($keys[0] ?? '') === 'TCA' && isset($keys[1])) {
                    $tables[] = $keys[1];
                }
                continue;
            }

            if ($token[0] === T_STRING && in_array($token[1], self::TABLE_FIRST_METHODS, true)) {
                $first = self::followingStrings($tokens, $index, 1);
                if (isset($first[0])) {
                    $tables[] = $first[0];
                }
            }
        }

        return array_values(array_filter(
            array_unique($tables),
            static fn(string $table): bool => preg_match('/^[a-z][a-z0-9_]*$/', $table) === 1,
        ));
    }

    /**
     * The next $wanted string literals after $index, in order, stopping at the
     * end of the call or subscript they belong to.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<int, string>
     */
    private static function followingStrings(array $tokens, int $index, int $wanted): array
    {
        $found = [];
        $count = count($tokens);
        for ($next = $index + 1; $next < $count && count($found) < $wanted; ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
                $found[] = trim($token[1], "'\"");
                continue;
            }
            if ($token === ';' || $token === ')') {
                break;
            }
        }

        return $found;
    }

    /** @return array<int, array{name: string, path: string}> */
    private static function siteSets(string $path): array
    {
        $sets = [];
        foreach (glob($path . '/Configuration/Sets/*/config.yaml') ?: [] as $file) {
            $directory = basename(dirname($file));
            $sets[] = [
                'name' => (string) (self::yaml($file)['name'] ?? $directory),
                'path' => 'Configuration/Sets/' . $directory . '/',
            ];
        }

        return $sets;
    }

    /**
     * The tags this extension's services carry, deduplicated.
     *
     * A tag is where an extension hangs itself into a core mechanism —
     * data.processor, event.listener, console.command — so the list says what
     * kind of extension this is in one line, without naming every service.
     *
     * @return array<int, string>
     */
    private static function serviceTags(string $path): array
    {
        $services = self::yaml($path . '/Configuration/Services.yaml')['services'] ?? null;
        if (!is_array($services)) {
            return [];
        }

        $tags = [];
        foreach ($services as $definition) {
            foreach ((is_array($definition) ? $definition['tags'] ?? [] : []) as $tag) {
                $name = is_array($tag) ? ($tag['name'] ?? null) : $tag;
                if (is_string($name) && $name !== '') {
                    $tags[$name] = true;
                }
            }
        }

        $names = array_keys($tags);
        sort($names);

        return $names;
    }

    /** @return array<int, string> */
    private static function fluidRoots(string $path): array
    {
        $roots = [];
        foreach (['Templates', 'Partials', 'Layouts'] as $kind) {
            if (is_dir($path . '/Resources/Private/' . $kind)) {
                $roots[] = 'Resources/Private/' . $kind . '/';
            }
        }

        return $roots;
    }

    /** @return array<int, array{kind: string, files: int}> */
    private static function classes(string $path): array
    {
        $classes = [];
        foreach (self::CLASS_KINDS as $kind) {
            $directory = $path . '/Classes/' . $kind;
            if (!is_dir($directory)) {
                continue;
            }
            $classes[] = ['kind' => $kind, 'files' => self::countPhpFiles($directory)];
        }

        return $classes;
    }

    private static function countPhpFiles(string $directory): int
    {
        $count = 0;
        $entries = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($entries as $entry) {
            if ($entry instanceof \SplFileInfo && $entry->getExtension() === 'php') {
                ++$count;
            }
        }

        return $count;
    }

    /** @return array<int, string> */
    private static function files(string $path): array
    {
        return array_values(array_filter(
            self::ROOT_FILES,
            static fn(string $file): bool => is_file($path . '/' . $file),
        ));
    }

    /**
     * The file names below a glob, without their extension by default: a TCA
     * file is named after its table, and the table is what is wanted.
     *
     * @return array<int, string>
     */
    private static function baseNames(string $pattern, string $suffix = '.php'): array
    {
        $names = [];
        foreach (glob($pattern) ?: [] as $file) {
            $name = basename($file);
            $names[] = $suffix === '' ? $name : substr($name, 0, -strlen($suffix));
        }
        sort($names);

        return $names;
    }

    /** @return array<string, mixed> */
    private static function json(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $decoded = json_decode((string) file_get_contents($file), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * A YAML file, or an empty one where it cannot be read — an extension
     * mid-edit is a state it is genuinely in, and one unreadable file must not
     * cost the rest of the answer.
     *
     * @return array<string, mixed>
     */
    private static function yaml(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }

        try {
            $parsed = Yaml::parseFile($file);
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsed) ? $parsed : [];
    }
}
