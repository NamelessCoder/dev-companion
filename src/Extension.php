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
     *     contentElements: array<int, array{identifier: string, templateName: ?string, source: ?string}>,
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

        $overrides = self::overrides($path);

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
            // what the file does — see overrides().
            'tcaTables' => self::baseNames($path . '/Configuration/TCA/*.php'),
            'tcaOverrides' => $overrides['tables'],
            'contentElements' => self::contentElements($overrides['contentElements'], $path),
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
     * What the override files do: the tables they extend, and the content
     * elements they add.
     *
     * The file name is no answer here — `102_tt_content.php` and
     * `600_ext_container.php` are both ordinary — because the number is what
     * fixes the order the overrides load in. What the file touches is either
     * $GLOBALS['TCA']['<table>'] or the first argument of one of the
     * ExtensionManagementUtility calls above, and both survive tokenising.
     *
     * @return array{tables: array<int, string>, contentElements: array<int, string>}
     */
    private static function overrides(string $path): array
    {
        $tables = [];
        $elements = [];
        foreach (glob($path . '/Configuration/TCA/Overrides/*.php') ?: [] as $file) {
            $found = self::declarationsIn((string) file_get_contents($file));
            if ($found['tables'] === []) {
                // Nothing recognisable: the conventional file name is the best
                // that is left, and only where it looks like a table at all.
                $name = substr(basename($file), 0, -strlen('.php'));
                $found['tables'] = preg_match('/^[a-z][a-z0-9_]*$/', $name) === 1 ? [$name] : [];
            }
            foreach ($found['tables'] as $table) {
                $tables[$table] = true;
            }
            foreach ($found['contentElements'] as $element) {
                $elements[$element] = true;
            }
        }

        $names = array_keys($tables);
        sort($names);
        $identifiers = array_keys($elements);
        sort($identifiers);

        return ['tables' => $names, 'contentElements' => $identifiers];
    }

    /** @return array{tables: array<int, string>, contentElements: array<int, string>} */
    private static function declarationsIn(string $code): array
    {
        $tables = [];
        $elements = [];
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

            if ($token[0] !== T_STRING || !in_array($token[1], self::TABLE_FIRST_METHODS, true)) {
                continue;
            }

            $arguments = self::arguments($tokens, $index);
            $table = self::firstLiteral($arguments[0] ?? []);
            if ($table === null) {
                continue;
            }
            $tables[] = $table;

            // A content element is an item of tt_content's CType. Every other
            // select item this call adds is a value in some other field, and
            // handing those over as content elements would be worse than the
            // pointer at tt_content the answer already carried.
            if (
                $token[1] === 'addTcaSelectItem'
                && $table === 'tt_content'
                && self::firstLiteral($arguments[1] ?? []) === 'CType'
            ) {
                $identifier = self::selectItemValue($arguments[2] ?? []);
                if ($identifier !== null) {
                    $elements[] = $identifier;
                }
            }
        }

        return [
            'tables' => array_values(array_filter(
                array_unique($tables),
                static fn(string $table): bool => preg_match('/^[a-z][a-z0-9_]*$/', $table) === 1,
            )),
            'contentElements' => array_values(array_unique($elements)),
        ];
    }

    /**
     * The identifier the item array of an addTcaSelectItem() call carries.
     *
     * Its shape changed inside the covered range: keyed by `value`, and
     * positional before that, where the value is the second entry after the
     * label. Both are read, because an extension is written for the line it
     * supports rather than for the newest one. An item whose value comes from
     * a constant or a variable has no identifier that a file can be read for.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $item
     */
    private static function selectItemValue(array $item): ?string
    {
        $literals = [];
        $isKey = [];
        foreach ($item as $position => $token) {
            if (!is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            $isKey[] = self::followedByArrow($item, $position);
            $literals[] = trim($token[1], "'\"");
        }

        $keyed = false;
        foreach ($literals as $position => $literal) {
            if ($isKey[$position] !== true) {
                continue;
            }
            $keyed = true;
            if ($literal === 'value' && ($isKey[$position + 1] ?? true) === false) {
                return $literals[$position + 1];
            }
        }

        // Positional: the label comes first and the value second.
        return $keyed ? null : ($literals[1] ?? null);
    }

    /** @param array<int, array{0: int, 1: string, 2: int}|string> $tokens */
    private static function followedByArrow(array $tokens, int $position): bool
    {
        for ($next = $position + 1; isset($tokens[$next]); ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && $token[0] === T_WHITESPACE) {
                continue;
            }

            return is_array($token) && $token[0] === T_DOUBLE_ARROW;
        }

        return false;
    }

    /**
     * The arguments of the call whose name is at $index, one token slice each.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<int, array<int, array{0: int, 1: string, 2: int}|string>>
     */
    private static function arguments(array $tokens, int $index): array
    {
        $count = count($tokens);
        for ($next = $index + 1; $next < $count && $tokens[$next] !== '('; ++$next) {
            // Whitespace and comments may stand between the two; anything else
            // means this name was not a call.
            if (!is_array($tokens[$next])) {
                return [];
            }
        }

        $arguments = [];
        $current = [];
        $depth = 0;
        for (; $next < $count; ++$next) {
            $token = $tokens[$next];
            if ($token === '(' || $token === '[') {
                if (++$depth === 1) {
                    continue;
                }
            } elseif ($token === ')' || $token === ']') {
                if (--$depth === 0) {
                    $arguments[] = $current;
                    break;
                }
            } elseif ($token === ',' && $depth === 1) {
                $arguments[] = $current;
                $current = [];
                continue;
            }
            $current[] = $token;
        }

        return $arguments;
    }

    /**
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function firstLiteral(array $tokens): ?string
    {
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
                return trim($token[1], "'\"");
            }
        }

        return null;
    }

    /**
     * The next $wanted string literals after $index, in order, stopping at the
     * end of the subscript they belong to.
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

    /**
     * The content elements it adds, each with the template it renders through.
     *
     * Which one that is is the next question after which ones there are, and
     * the answer is in this extension's own TypoScript — `templateName` under
     * the identifier. Where it says nothing the template stays unknown rather
     * than being derived from the identifier: the convention is a convention,
     * and a guessed file name sends the caller to a file that is not there.
     *
     * @param array<int, string> $identifiers
     * @return array<int, array{identifier: string, templateName: ?string, source: ?string}>
     */
    private static function contentElements(array $identifiers, string $path): array
    {
        if ($identifiers === []) {
            return [];
        }

        $typoScript = self::typoScriptValues($path);

        return array_map(static function (string $identifier) use ($typoScript): array {
            $set = $typoScript['tt_content.' . $identifier . '.templateName'] ?? null;

            return [
                'identifier' => $identifier,
                'templateName' => $set['value'] ?? null,
                'source' => $set['file'] ?? null,
            ];
        }, $identifiers);
    }

    /**
     * Every value this extension's TypoScript sets, by its full path.
     *
     * Not a TypoScript parser: it tracks the nesting so that a value can be
     * addressed however it was written — `tt_content.x.templateName = T`, a
     * `tt_content.x { }` block, or a `tt_content { x { } }` one. Conditions are
     * ignored rather than evaluated, so a value set only inside one reads as
     * though it were set outright; the file it came from travels with it, which
     * is where a caller checks that.
     *
     * @return array<string, array{value: string, file: string}>
     */
    private static function typoScriptValues(string $path): array
    {
        $values = [];
        foreach (self::typoScriptFiles($path) as $file) {
            $stack = [];
            $inMultiline = false;
            foreach (preg_split('/\R/', (string) file_get_contents($file)) ?: [] as $raw) {
                $line = trim($raw);
                if ($inMultiline) {
                    $inMultiline = $line !== ')';
                    continue;
                }
                if ($line === '' || $line[0] === '#' || $line[0] === '[' || str_starts_with($line, '//')) {
                    continue;
                }
                if ($line === '}') {
                    array_pop($stack);
                    continue;
                }
                if (preg_match('/^([\w.\-]+)\s*\{$/', $line, $matches) === 1) {
                    $stack[] = $matches[1];
                    continue;
                }
                if (preg_match('/^([\w.\-]+)\s*=\s*(.*)$/', $line, $matches) !== 1) {
                    continue;
                }
                if ($matches[2] === '(') {
                    $inMultiline = true;
                    continue;
                }
                $key = ($stack === [] ? '' : implode('.', $stack) . '.') . $matches[1];
                $values[$key] = [
                    'value' => trim($matches[2]),
                    'file' => substr($file, strlen($path) + 1),
                ];
            }
        }

        return $values;
    }

    /**
     * The TypoScript files it ships, from both places it can put them: the
     * Configuration/TypoScript/ directory an extension is included from, and
     * the site sets a site depends on.
     *
     * @return array<int, string>
     */
    private static function typoScriptFiles(string $path): array
    {
        $files = [];
        foreach (['/Configuration/TypoScript', '/Configuration/Sets'] as $directory) {
            if (!is_dir($path . $directory)) {
                continue;
            }
            $entries = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path . $directory, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($entries as $entry) {
                if ($entry instanceof \SplFileInfo && $entry->getExtension() === 'typoscript') {
                    $files[] = $entry->getPathname();
                }
            }
        }
        sort($files);

        return $files;
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
