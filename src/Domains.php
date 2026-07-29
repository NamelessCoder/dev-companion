<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Derives the technical domains a task touches from its paths and description.
 *
 * Used to keep answers inside the domain that was actually asked about: a PHP
 * bugfix should never get a Sass build recommended, and a task that only names
 * PHP paths should not be answered with backend TypeScript conventions.
 *
 * The asset domains are deliberately separate rather than one "frontend": a
 * TypeScript module, a Sass partial, and a Fluid template share a directory
 * tree but not a single convention, test suite, or reviewer. Folding them
 * together made every .ts path pull CSS conventions and every .scss path pull
 * TypeScript ones.
 */
final class Domains
{
    public const PHP = 'php';
    public const TYPESCRIPT = 'typescript';
    public const TYPOSCRIPT = 'typoscript';
    public const CSS = 'css';
    public const FLUID = 'fluid';
    public const DOCS = 'docs';
    public const XLIFF = 'xliff';

    /** @var array<string, array<int, string>> Domain to file extensions. */
    private const EXTENSIONS = [
        self::PHP => ['php', 'yaml', 'yml'],
        self::TYPESCRIPT => ['ts', 'js'],
        self::TYPOSCRIPT => ['typoscript', 'tsconfig'],
        self::CSS => ['scss', 'sass', 'css'],
        self::FLUID => ['html'],
        self::DOCS => ['rst'],
        self::XLIFF => ['xlf', 'xliff'],
    ];

    /** @var array<string, array<int, string>> Domain to words in a task description. */
    private const KEYWORDS = [
        self::PHP => [
            'php', 'class', 'service', 'datahandler', 'tca', 'formengine',
            'middleware', 'repository', 'controller', 'event listener', 'hook',
            'dependency injection', 'unit test', 'functional test', 'phpstan',
        ],
        self::TYPESCRIPT => [
            'typescript', 'javascript', 'web component', 'custom element', 'lit',
            'backend ui',
        ],
        self::CSS => [
            'sass', 'scss', 'css', 'stylesheet', 'styling', 'frontend build',
            'backend ui',
        ],
        self::FLUID => [
            'fluid', 'viewhelper', 'view helper', 'partial',
        ],
        self::TYPOSCRIPT => [
            'typoscript', 'tsconfig', 'site set', 'sitesets', 'settings definition',
            'constants.typoscript', 'setup.typoscript',
        ],
        self::DOCS => [
            'changelog', 'rst', 'documentation', 'deprecation', 'breaking change',
        ],
        self::XLIFF => [
            'xlf', 'xliff', 'label', 'translation', 'locallang', 'wording',
        ],
    ];

    /**
     * Words that place a task in the website output rather than in the backend.
     *
     * @var array<int, string>
     */
    private const FRONTEND_MARKERS = [
        'frontend', 'front-end', 'front end', 'website theme', 'page template',
        'bootstrap 5', 'bootstrap5', 'theme extension',
    ];

    /**
     * Words that place it in the backend after all. They win, because the
     * frontend markers are the weaker signal: a backend module is named
     * outright, while "frontend" also appears in a sentence about the boundary
     * between the two.
     *
     * @var array<int, string>
     */
    private const BACKEND_MARKERS = [
        'backend', 'typo3/sysext/backend', 'install tool', 'styleguide',
    ];

    /** @var array<string, array<int, string>> Domain to directory conventions the extension alone does not reveal. */
    private const DIRECTORIES = [
        self::TYPESCRIPT => ['build/sources/typescript', 'resources/public/javascript'],
        self::CSS => ['build/sources/sass', 'resources/public/css'],
        self::FLUID => [
            'resources/private/templates', 'resources/private/partials',
            'resources/private/layouts', 'classes/viewhelpers',
        ],
        self::TYPOSCRIPT => ['configuration/sets/', 'classes/typoscript/'],
        self::DOCS => ['documentation/changelog'],
        self::XLIFF => ['resources/private/language'],
    ];

    /**
     * @param array<int, string> $paths
     * @return array<int, string> The matched domains, or PHP when the input
     *                            carries no signal at all.
     */
    public static function detect(array $paths, string $text = ''): array
    {
        $detected = [];

        foreach ($paths as $path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            foreach (self::EXTENSIONS as $domain => $extensions) {
                if ($extension !== '' && in_array($extension, $extensions, true)) {
                    $detected[$domain] = true;
                }
            }
        }

        // Keywords are read from the description alone. A path carries its
        // domain in its extension and its directory, and matching words inside
        // it makes a file name mean something it does not: Classes/ViewHelpers/
        // Format/ScssViewHelper.php is PHP, and every word in it is a PHP
        // identifier, not a topic.
        $description = mb_strtolower($text);
        foreach (self::KEYWORDS as $domain => $keywords) {
            foreach ($keywords as $keyword) {
                if (Text::containsWord($description, $keyword)) {
                    $detected[$domain] = true;
                    break;
                }
            }
        }

        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::DIRECTORIES as $domain => $directories) {
            foreach ($directories as $directory) {
                if (str_contains($haystack, $directory)) {
                    $detected[$domain] = true;
                    break;
                }
            }
        }

        // Without any signal, assume PHP: that is what most of the core is, and
        // an unrelated Sass or TypeScript recommendation is worse than a
        // slightly narrow one.
        if ($detected === []) {
            return [self::PHP];
        }

        return array_keys($detected);
    }

    /**
     * The domains carried by paths alone: no free-text keywords, no PHP
     * fallback, and empty when the paths say nothing.
     *
     * Free text is the wrong signal for narrowing a recommendation, because a
     * negated mention reads exactly like a positive one — "without unrelated
     * PHP or TypeScript suites" names both domains it rules out. A path cannot
     * be negated that way.
     *
     * @param array<int, string> $paths
     * @return array<int, string>
     */
    public static function fromPaths(array $paths): array
    {
        $detected = [];
        foreach ($paths as $path) {
            $lowered = mb_strtolower($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            foreach (self::EXTENSIONS as $domain => $extensions) {
                if ($extension !== '' && in_array($extension, $extensions, true)) {
                    $detected[$domain] = true;
                }
            }
            foreach (self::DIRECTORIES as $domain => $directories) {
                foreach ($directories as $directory) {
                    if (str_contains($lowered, $directory)) {
                        $detected[$domain] = true;
                        break;
                    }
                }
            }
        }

        return array_keys($detected);
    }

    /**
     * File paths named inside a free-text description, so a query that spells
     * out the file it is about narrows the answer the same way an explicit
     * paths argument would.
     *
     * @return array<int, string>
     */
    public static function pathsIn(string $text): array
    {
        $extensions = array_merge(...array_values(self::EXTENSIONS));
        $pattern = '#[\w./_-]+\.(' . implode('|', $extensions) . ')\b#i';
        preg_match_all($pattern, $text, $matches);

        return array_values(array_unique($matches[0]));
    }

    /**
     * Whether the task is about what the website renders rather than about the
     * TYPO3 backend.
     *
     * The CSS and TypeScript conventions this server holds are the backend's:
     * its Sass sources, its `--typo3-*` custom properties, its light and dark
     * color schemes, its Bootstrap removal. For a theme extension they are not
     * merely irrelevant, they are the opposite of correct, so the frontend has
     * to be recognisable before an answer is composed.
     *
     * @param array<int, string> $paths
     */
    public static function namesTheFrontend(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::BACKEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return false;
            }
        }

        foreach (self::FRONTEND_MARKERS as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Architecture hint categories that belong to the given domains. General
     * hints always apply.
     *
     * @param array<int, string> $domains
     * @return array<int, string>
     */
    public static function hintCategories(array $domains): array
    {
        $categories = ['General'];
        if (in_array(self::PHP, $domains, true)) {
            $categories[] = 'PHP';
        }
        if (in_array(self::TYPOSCRIPT, $domains, true)) {
            $categories[] = 'TypoScript';
        }
        if (in_array(self::FLUID, $domains, true)) {
            $categories[] = 'Fluid';
        }
        if (in_array(self::TYPESCRIPT, $domains, true)) {
            $categories[] = ArchitectureHints::CATEGORY_TYPESCRIPT;
            $categories[] = 'JavaScript';
        }
        if (in_array(self::CSS, $domains, true)) {
            $categories[] = ArchitectureHints::CATEGORY_CSS;
        }

        return $categories;
    }
}
