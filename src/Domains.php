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
    public const CSS = 'css';
    public const FLUID = 'fluid';
    public const DOCS = 'docs';
    public const XLIFF = 'xliff';

    /** @var array<string, array<int, string>> Domain to file extensions. */
    private const EXTENSIONS = [
        self::PHP => ['php', 'yaml', 'yml'],
        self::TYPESCRIPT => ['ts', 'js'],
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
        self::DOCS => [
            'changelog', 'rst', 'documentation', 'deprecation', 'breaking change',
        ],
        self::XLIFF => [
            'xlf', 'xliff', 'label', 'translation', 'locallang', 'wording',
        ],
    ];

    /** @var array<string, array<int, string>> Domain to directory conventions the extension alone does not reveal. */
    private const DIRECTORIES = [
        self::TYPESCRIPT => ['build/sources/typescript', 'resources/public/javascript'],
        self::CSS => ['build/sources/sass', 'resources/public/css'],
        self::FLUID => [
            'resources/private/templates', 'resources/private/partials',
            'resources/private/layouts', 'classes/viewhelpers',
        ],
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

        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::KEYWORDS as $domain => $keywords) {
            foreach ($keywords as $keyword) {
                if (Text::containsWord($haystack, $keyword)) {
                    $detected[$domain] = true;
                    break;
                }
            }
        }

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
        if (in_array(self::TYPESCRIPT, $domains, true)) {
            $categories[] = 'TypeScript';
            $categories[] = 'JavaScript';
        }
        if (in_array(self::CSS, $domains, true)) {
            $categories[] = 'CSS';
        }

        return $categories;
    }
}
