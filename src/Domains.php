<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Derives the technical domains a task touches from its paths and description.
 *
 * Used to keep answers inside the domain that was actually asked about: a PHP
 * bugfix should never get a Sass build recommended, and a task that only names
 * PHP paths should not be answered with backend TypeScript conventions.
 */
final class Domains
{
    public const PHP = 'php';
    public const FRONTEND = 'frontend';
    public const DOCS = 'docs';
    public const XLIFF = 'xliff';

    /** @var array<string, array<int, string>> Domain to file extensions. */
    private const EXTENSIONS = [
        self::PHP => ['php', 'yaml', 'yml'],
        self::FRONTEND => ['ts', 'js', 'scss', 'sass', 'css', 'html'],
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
        self::FRONTEND => [
            'typescript', 'javascript', 'web component', 'custom element',
            'sass', 'scss', 'css', 'stylesheet', 'styling', 'frontend build',
            'backend ui', 'lit', 'template', 'fluid',
        ],
        self::DOCS => [
            'changelog', 'rst', 'documentation', 'deprecation', 'breaking change',
        ],
        self::XLIFF => [
            'xlf', 'xliff', 'label', 'translation', 'locallang', 'wording',
        ],
    ];

    /**
     * @param array<int, string> $paths
     * @return array<int, string> The matched domains, or every domain when the
     *                            input carries no signal at all.
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

        // Directory conventions that the extension alone does not reveal.
        if (str_contains($haystack, 'build/sources/typescript') || str_contains($haystack, 'build/sources/sass')) {
            $detected[self::FRONTEND] = true;
        }
        if (str_contains($haystack, 'documentation/changelog')) {
            $detected[self::DOCS] = true;
        }
        if (str_contains($haystack, 'resources/private/language')) {
            $detected[self::XLIFF] = true;
        }

        // Without any signal, assume PHP: that is what most of the core is, and
        // an unrelated Sass or TypeScript recommendation is worse than a
        // slightly narrow one.
        if ($detected === []) {
            return [self::PHP];
        }

        // Anything that is not purely about assets, labels or docs involves PHP.
        if (!isset($detected[self::PHP]) && (isset($detected[self::DOCS]) || isset($detected[self::XLIFF]))) {
            $detected[self::PHP] = true;
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
        if (in_array(self::FRONTEND, $domains, true)) {
            $categories[] = 'TypeScript';
            $categories[] = 'JavaScript';
            $categories[] = 'CSS';
        }

        return $categories;
    }
}
