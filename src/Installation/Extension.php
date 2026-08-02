<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * What one installed extension registers, read from its own files.
 *
 * typo3_project_scope names the extensions and where they are. A maintenance
 * question is almost never about that — it is about what is inside one of them:
 * which tables its TCA defines and which it extends, which backend modules and
 * icons it brings, which site sets it ships, what it hangs into the container.
 * Most of that is declarative and sits in files with fixed names, so it is
 * readable without a console and without a database, the same way the sites are.
 * What is not declarative is asked of the installation instead: `Typo3Runtime`
 * boots it in a subprocess and reads the registries, because a table added by a
 * PHP call, a content element whose identifier came out of a variable and an
 * icon list built in a `foreach` exist in no file a reader could follow. Those
 * three are attributed back to this extension by the `EXT:<key>/` reference the
 * entry itself carries; where the boot did not happen, the files answer and the
 * answer says so.
 *
 * Nothing is included or executed here. The declaration files are tokenised for
 * their keys (see PhpArray) and the YAML is parsed; where the extension's own
 * code runs, it runs in TYPO3's process, not in this one.
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
     *     files: array<int, string>,
     *     notReadStatically: array<int, string>,
     *     artifacts: array{
     *         manual: ?string,
     *         readme: ?string,
     *         tests: array<int, string>,
     *         languageFiles: array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>
     *     }
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
            'tcaTables' => self::tcaTables($key, $path),
            'tcaOverrides' => $overrides['tables'],
            'contentElements' => self::contentElements(self::cTypes($key, $overrides['contentElements']), $path),
            'backendModules' => PhpArray::keys($path . '/Configuration/Backend/Modules.php'),
            'backendRoutes' => array_merge(
                PhpArray::keys($path . '/Configuration/Backend/Routes.php'),
                PhpArray::keys($path . '/Configuration/Backend/AjaxRoutes.php'),
            ),
            'icons' => self::icons($key, $path),
            'siteSets' => self::siteSets($path),
            // The outer keys are the request scopes; the identifiers a caller
            // orders its own middleware against are one level below them.
            'middlewares' => PhpArray::keys($path . '/Configuration/RequestMiddlewares.php', 2),
            'serviceTags' => self::serviceTags($path),
            'fluidRoots' => self::fluidRoots($path),
            'fluidNamespaces' => array_keys(FluidNamespaces::declaredBy($path)),
            'typoScript' => self::baseNames($path . '/Configuration/TypoScript', '*.typoscript', ''),
            'classes' => self::classes($path),
            'files' => self::files($path),
            'notReadStatically' => self::notReadStatically($path),
            'artifacts' => self::artifacts($path),
        ];
    }

    /**
     * What the extension ships beside its registrations — and what it does not.
     *
     * Everything above this answers what the extension registers, which a
     * caller can only ever find more of by reading further. These four are the
     * ones whose *absence* is the answer, and absence has no file to stumble
     * over: a review that lists the tree cannot see a manual nobody wrote. Each
     * key is therefore always present, null or empty where the artifact is not
     * there, so a caller can tell "looked for and missing" from "not looked
     * for".
     *
     * The source language is a fact about the file and is reported as one. What
     * it ought to be is a convention and stays in the knowledge base.
     *
     * @return array{manual: ?string, readme: ?string, tests: array<int, string>, languageFiles: array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>}
     */
    private static function artifacts(string $path): array
    {
        $manual = null;
        foreach (['Documentation/Index.rst', 'Documentation/index.rst'] as $entry) {
            if (is_file($path . '/' . $entry)) {
                $manual = $entry;
                break;
            }
        }
        if ($manual === null && is_dir($path . '/Documentation')) {
            // A manual whose entry point is missing is not the same as no
            // manual, and the difference is the finding.
            $manual = 'Documentation/';
        }

        $readme = null;
        foreach (['README.rst', 'README.md', 'readme.md', 'README.txt'] as $entry) {
            if (is_file($path . '/' . $entry)) {
                $readme = $entry;
                break;
            }
        }

        $tests = [];
        if (is_dir($path . '/Tests')) {
            foreach (Finder::create()->directories()->in($path . '/Tests')->depth(0)->sortByName() as $directory) {
                $tests[] = $directory->getFilename();
            }
        }

        return [
            'manual' => $manual,
            'readme' => $readme,
            'tests' => $tests,
            'languageFiles' => self::languageFiles($path),
        ];
    }

    /**
     * The XLF files it ships, each with the language its own header declares.
     *
     * A locale-prefixed file is the translation of the one beside it — de.foo.xlf
     * belongs to foo.xlf — so it is listed there rather than as a file of its
     * own, which is also the shape that makes a missing translation visible.
     *
     * @return array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>
     */
    private static function languageFiles(string $path): array
    {
        $directory = $path . '/Resources/Private/Language';
        if (!is_dir($directory)) {
            return [];
        }

        $sources = [];
        $translations = [];
        // The directory itself and one level below it: a language file sits
        // beside its translations, or in a subdirectory with them.
        foreach (Finder::create()->files()->in($directory)->depth('< 2')->name('*.xlf')->sortByName() as $file) {
            $relative = substr($file->getPathname(), strlen($path) + 1);
            if (preg_match('/^([a-z]{2}(?:_[A-Z]{2})?)\.(.+\.xlf)$/', $file->getFilename(), $matches) === 1) {
                $translations[dirname($relative) . '/' . $matches[2]][] = $matches[1];
                continue;
            }
            $sources[$relative] = self::sourceLanguage($file->getPathname());
        }

        $files = [];
        foreach ($sources as $relative => $language) {
            $locales = $translations[$relative] ?? [];
            sort($locales);
            $files[] = ['path' => $relative, 'sourceLanguage' => $language, 'translations' => $locales];
        }

        return $files;
    }

    /** The source-language of an XLF, from its <file> element and nothing else. */
    private static function sourceLanguage(string $file): ?string
    {
        $handle = @fopen($file, 'rb');
        if ($handle === false) {
            return null;
        }
        $head = (string) fread($handle, 4096);
        fclose($handle);

        return preg_match('/<file\b[^>]*\bsource-language="([^"]*)"/', $head, $matches) === 1
            ? $matches[1]
            : null;
    }

    /**
     * Whether this is TYPO3's own, the project's, a dependency it pulled in, or
     * a package its test setup ships — the same four the project scope draws,
     * so the two answers agree.
     */
    private static function origin(string $key, string $path): string
    {
        if (Instance::isSystemExtension($key) === true) {
            return 'system';
        }

        return Project::origin($path);
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
        $directory = $path . '/Configuration/TCA/Overrides';
        if (!is_dir($directory)) {
            return ['tables' => [], 'contentElements' => []];
        }

        $tables = [];
        $elements = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.php')->sortByName() as $file) {
            $found = self::declarationsIn((string) file_get_contents($file->getPathname()));
            if ($found['tables'] === []) {
                // Nothing recognisable: the conventional file name is the best
                // that is left, and only where it looks like a table at all.
                $name = $file->getBasename('.php');
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
        $variables = self::stringVariables($tokens);
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

            if ($token[0] === T_STRING && $token[1] === 'addRecordType') {
                $arguments = self::arguments($tokens, $index);
                // The one registration call that does not name its table first
                // and often does not name it at all: the table is the fifth
                // argument and defaults to tt_content, which is what makes this
                // the short way to register a content element on 13.4 and newer.
                // Read positionally — a call that passes `table:` by name keeps
                // the default here, the same way an identifier that is not a
                // literal is left out rather than guessed.
                $table = self::firstLiteral($arguments[4] ?? [], $variables) ?? 'tt_content';
                $tables[] = $table;
                if ($table === 'tt_content') {
                    $identifier = self::selectItemValue($arguments[0] ?? [], $variables);
                    if ($identifier !== null) {
                        $elements[] = $identifier;
                    }
                }
                continue;
            }

            if ($token[0] !== T_STRING || !in_array($token[1], self::TABLE_FIRST_METHODS, true)) {
                continue;
            }

            $arguments = self::arguments($tokens, $index);
            $table = self::firstLiteral($arguments[0] ?? [], $variables);
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
                && self::firstLiteral($arguments[1] ?? [], $variables) === 'CType'
            ) {
                $identifier = self::selectItemValue($arguments[2] ?? [], $variables);
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
     * The string variables a declaration file assigns to itself, once each.
     *
     * A registration file is read and never executed, so a value that arrives
     * through a variable used to be a value this parser could not see. Most of
     * them do not need executing: `$contentType = 'my_element';` at the top of a
     * TCA override, used further down, is a plain literal that took a detour,
     * and refusing it drops a whole content element from the answer.
     *
     * Only that shape is resolved — one assignment, one string literal, the
     * whole statement. A variable assigned twice, or assigned anything else, is
     * dropped rather than resolved to its first value: what it holds at the call
     * depends on the order the file runs in, and that is the thing this parser
     * deliberately does not know.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<string, string>
     */
    private static function stringVariables(array $tokens): array
    {
        $values = [];
        $ambiguous = [];
        $count = count($tokens);
        for ($index = 0; $index < $count; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token) || $token[0] !== T_VARIABLE || $token[1] === '$GLOBALS') {
                continue;
            }

            $name = $token[1];
            $assignment = self::nextSignificant($tokens, $index);
            if ($assignment === null || $tokens[$assignment] !== '=') {
                continue;
            }

            $value = self::nextSignificant($tokens, $assignment);
            $end = $value === null ? null : self::nextSignificant($tokens, $value);
            $literal = $value !== null && is_array($tokens[$value]) && $tokens[$value][0] === T_CONSTANT_ENCAPSED_STRING
                && $end !== null && $tokens[$end] === ';';
            if (!$literal || isset($values[$name])) {
                $ambiguous[$name] = true;
                continue;
            }

            /** @var array{0: int, 1: string, 2: int} $token */
            $token = $tokens[(int) $value];
            $values[$name] = trim($token[1], "'\"");
        }

        return array_diff_key($values, $ambiguous);
    }

    /**
     * The index of the next token that is not whitespace or a comment.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function nextSignificant(array $tokens, int $index): ?int
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return $next;
        }

        return null;
    }

    /**
     * The identifier the item array of an addTcaSelectItem() call carries.
     *
     * Its shape changed inside the covered range: keyed by `value`, and
     * positional before that, where the value is the second entry after the
     * label. Both are read, because an extension is written for the line it
     * supports rather than for the newest one. An item whose value comes from a
     * constant, or from a variable this file assigns more than once, still has
     * no identifier that reading can establish.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $item
     * @param array<string, string> $variables
     */
    private static function selectItemValue(array $item, array $variables = []): ?string
    {
        $literals = [];
        $isKey = [];
        foreach ($item as $position => $token) {
            if (!is_array($token)) {
                continue;
            }
            if ($token[0] === T_VARIABLE) {
                if (!isset($variables[$token[1]])) {
                    continue;
                }
                $isKey[] = self::followedByArrow($item, $position);
                $literals[] = $variables[$token[1]];
                continue;
            }
            if ($token[0] !== T_CONSTANT_ENCAPSED_STRING) {
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
     * @param array<string, string> $variables
     */
    private static function firstLiteral(array $tokens, array $variables = []): ?string
    {
        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }
            if ($token[0] === T_CONSTANT_ENCAPSED_STRING) {
                return trim($token[1], "'\"");
            }
            if ($token[0] === T_VARIABLE && isset($variables[$token[1]])) {
                return $variables[$token[1]];
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
        $directories = array_values(array_filter(
            [$path . '/Configuration/TypoScript', $path . '/Configuration/Sets'],
            is_dir(...),
        ));
        if ($directories === []) {
            return [];
        }

        $files = [];
        foreach (Finder::create()->files()->in($directories)->name('*.typoscript')->sortByName() as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /** @return array<int, array{name: string, path: string}> */
    private static function siteSets(string $path): array
    {
        $directory = $path . '/Configuration/Sets';
        if (!is_dir($directory)) {
            return [];
        }

        $sets = [];
        foreach (Finder::create()->files()->in($directory)->depth(1)->name('config.yaml')->sortByName() as $file) {
            $set = $file->getRelativePath();
            $sets[] = [
                'name' => (string) (self::yaml($file->getPathname())['name'] ?? $set),
                'path' => 'Configuration/Sets/' . $set . '/',
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

    /**
     * The whole subtree, because a nested helper has no line of its own.
     *
     * `Classes/Updates/Criteria/` is under no kind, so counting the top level
     * alone would leave its files out of the answer altogether. What the number
     * covers is stated where it is rendered and in the schema, because a reader
     * who counts one level gets a different one — `D-ANS-008`.
     */
    private static function countPhpFiles(string $directory): int
    {
        return Finder::create()->files()->in($directory)->name('*.php')->count();
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
     * The registration files whose entries do not stand in their own text.
     *
     * Not a statement about the file — its content is read in full — but about
     * what reading it yields: a list assembled in a `foreach` exists only once
     * the file has run. Every list above is omitted where it is empty, so such
     * a file arrives as the same silence as one that does not exist, and the
     * difference is the whole of what the caller is missing. These are named
     * instead, and the booted installation is what would answer for them.
     *
     * @return array<int, string>
     */
    private static function notReadStatically(string $path): array
    {
        $files = [
            'Configuration/Backend/Modules.php',
            'Configuration/Backend/Routes.php',
            'Configuration/Backend/AjaxRoutes.php',
            'Configuration/RequestMiddlewares.php',
        ];
        // Where the container answered, the icons are its and this file is no
        // longer what the answer rests on. The four above have no such source:
        // no registry hands them over, so reading them is all there ever is.
        if (self::answeredBy() === 'packages') {
            array_unshift($files, 'Configuration/Icons.php');
        }

        return array_values(array_filter(
            $files,
            static fn(string $file): bool => PhpArray::assembledAtRuntime($path . '/' . $file),
        ));
    }

    /**
     * The tables this extension defines, as the installation has them.
     *
     * A file below Configuration/TCA/ is named after the table it defines, so
     * the files answer on their own. What they cannot show is a table an
     * extension adds through a PHP call, and what they cannot check is whether
     * a file that looks like a definition produced one — so where the
     * installation booted, a table is in the answer when TCA has it and either
     * this extension's file declares it or its ctrl title names this extension.
     *
     * @return array<int, string>
     */
    private static function tcaTables(string $key, string $path): array
    {
        $declared = self::baseNames($path . '/Configuration/TCA', '*.php');
        $runtime = Typo3Runtime::topic('tables');
        if (!is_array($runtime) || $runtime === []) {
            return $declared;
        }

        $tables = [];
        foreach ($runtime as $table => $title) {
            if (Typo3Runtime::extensionIn((string) $title) === $key) {
                $tables[] = (string) $table;
            }
        }
        foreach ($declared as $table) {
            if (isset($runtime[$table])) {
                $tables[] = $table;
            }
        }

        return self::sorted($tables);
    }

    /**
     * The content elements this extension adds, as the installation has them.
     *
     * Attribution is the whole difficulty: `tt_content.CType` is one list for
     * every extension at once. An item names its owner twice over — its label
     * is `LLL:EXT:<key>/…` and its icon resolves to a file below `EXT:<key>/` —
     * and where neither names this extension the item is somebody else's.
     *
     * @param array<int, string> $parsed the identifiers read from this extension's own files
     * @return array<int, string>
     */
    private static function cTypes(string $key, array $parsed): array
    {
        $runtime = Typo3Runtime::topic('contentElements');
        if (!is_array($runtime) || $runtime === []) {
            return $parsed;
        }

        $icons = Typo3Runtime::topic('icons');
        $identifiers = [];
        foreach ($runtime as $identifier => $item) {
            $label = is_array($item) ? (string) ($item['label'] ?? '') : '';
            $icon = is_array($item) ? (string) ($item['icon'] ?? '') : '';
            $source = is_array($icons) ? (string) ($icons[$icon] ?? '') : '';
            if (Typo3Runtime::extensionIn($label) === $key || Typo3Runtime::extensionIn($source) === $key) {
                $identifiers[] = (string) $identifier;
            }
        }
        // One this extension's files register and the installation does not have
        // was registered under a condition that did not apply here, and saying
        // it is registered would send a caller to a template nothing renders.
        foreach ($parsed as $identifier) {
            if (isset($runtime[$identifier])) {
                $identifiers[] = $identifier;
            }
        }

        return self::sorted($identifiers);
    }

    /**
     * The icons this extension registers, as the installation has them.
     *
     * @return array<int, string>
     */
    private static function icons(string $key, string $path): array
    {
        $registered = Typo3Runtime::topic('icons');
        if (!is_array($registered) || $registered === []) {
            return PhpArray::keys($path . '/Configuration/Icons.php');
        }

        $icons = [];
        foreach ($registered as $identifier => $source) {
            if (Typo3Runtime::extensionIn((string) $source) === $key) {
                $icons[] = (string) $identifier;
            }
        }

        return self::sorted($icons);
    }

    /**
     * Where this answer comes from, in the vocabulary every tool reports it in.
     *
     * `installation` once the container answered for the registries that have
     * no file behind them; `packages` for the reading that has to leave those
     * out. `Typo3Runtime::reason()` says why, and the answer carries it.
     */
    public static function answeredBy(): string
    {
        return is_array(Typo3Runtime::topic('icons')) ? 'installation' : 'packages';
    }

    /**
     * @param array<int, string> $values
     * @return array<int, string>
     */
    private static function sorted(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values);

        return $values;
    }

    /**
     * The file names in a directory, without their extension by default: a TCA
     * file is named after its table, and the table is what is wanted.
     *
     * @return array<int, string>
     */
    private static function baseNames(string $directory, string $pattern, string $suffix = '.php'): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $names = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name($pattern)->sortByName() as $file) {
            $names[] = $suffix === '' ? $file->getFilename() : $file->getBasename($suffix);
        }

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
