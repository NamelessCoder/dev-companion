<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Typo3CmsMcp\Instance;

/**
 * Re-reads the component contract from the packages of the active installation.
 *
 * The bundled catalog remains the curated index: it supplies names, summaries,
 * keywords, and the mapping from one component to its styleguide demo. The
 * installed backend CSS decides whether that component and its recorded class
 * and custom-property names actually exist. Where the styleguide package is
 * installed, its first matching example replaces the snapshot markup too.
 */
final class InstalledComponents
{
    public static function isAvailable(?int $target): bool
    {
        $installedVersion = Instance::typo3Version();
        $installedMajor = Instance::typo3Major();
        $backend = Instance::packages()['backend'] ?? null;

        return $installedVersion !== null
            && $installedMajor !== null
            && $target === $installedMajor
            && $backend !== null
            && is_file($backend . '/Resources/Public/Css/backend.css');
    }

    /**
     * @param array<int, array<string, mixed>> $components
     * @return array<int, array<string, mixed>>|null
     */
    public static function derive(array $components, ?int $target): ?array
    {
        if (!self::isAvailable($target)) {
            return null;
        }

        $installedVersion = (string) Instance::typo3Version();
        $packages = Instance::packages();
        $cssFile = $packages['backend'] . '/Resources/Public/Css/backend.css';
        $css = (string) file_get_contents($cssFile);
        $cssClasses = self::tokens($css, '/\.([a-z][a-z0-9_-]*)/i');
        $customProperties = self::tokens($css, '/(--[a-z][a-z0-9_-]*)/i');
        $javascript = self::javascript($packages);

        foreach ($components as &$component) {
            $rootClass = (string) $component['rootClass'];
            $customElementSource = self::sourceCarrying($javascript, $rootClass);
            $component['_installedPresent'] = isset($cssClasses[$rootClass]) || $customElementSource !== null;
            $component['_installed'] = true;
            $component['contractVersion'] = $installedVersion;
            $component['classes'] = self::componentClasses($component, $cssClasses);
            foreach (['variants', 'modifiers', 'subComponents'] as $field) {
                $component[$field] = array_values(array_filter(
                    $component[$field],
                    static fn(string $class): bool => isset($cssClasses[$class]),
                ));
            }
            $component['customProperties'] = self::componentProperties($component, $customProperties);

            $sources = ['EXT:backend/Resources/Public/Css/backend.css'];
            if ($customElementSource !== null) {
                $sources[] = $customElementSource;
            }
            foreach ($component['sassPaths'] as $path) {
                if (Instance::describe()['kind'] === Instance::KIND_CORE_CHECKOUT
                    && is_file((string) Instance::root() . '/' . $path)
                ) {
                    $sources[] = $path;
                }
            }

            $component['markupSource'] = 'catalog';
            $demo = self::installedPath((string) ($component['demoPath'] ?? ''), $packages);
            if ($demo !== null) {
                [$file, $reference] = $demo;
                $sources[] = $reference;
                $examples = self::examples((string) file_get_contents($file), $rootClass);
                if ($examples !== []) {
                    $component['markup'] = array_shift($examples);
                    $component['examples'] = $examples;
                    $component['markupSource'] = 'installation';
                }
            }
            $component['sourceFiles'] = array_values(array_unique($sources));
        }
        unset($component);

        return $components;
    }

    /**
     * @param array<string, mixed> $component
     * @param array<string, true> $classes
     * @return array<int, string>
     */
    private static function componentClasses(array $component, array $classes): array
    {
        $roots = [(string) $component['rootClass'], (string) $component['name']];
        $recorded = array_merge(
            $component['variants'],
            $component['modifiers'],
            $component['subComponents'],
        );

        $found = [];
        foreach (array_keys($classes) as $class) {
            foreach ($roots as $root) {
                if ($class === $root || str_starts_with($class, $root . '-')) {
                    $found[$class] = true;
                }
            }
        }
        foreach ($recorded as $class) {
            if (isset($classes[$class])) {
                $found[$class] = true;
            }
        }
        ksort($found);

        return array_keys($found);
    }

    /**
     * @param array<string, mixed> $component
     * @param array<string, true> $properties
     * @return array<int, string>
     */
    private static function componentProperties(array $component, array $properties): array
    {
        $needles = array_filter([
            str_replace('typo3-backend-', '', (string) $component['rootClass']),
            (string) $component['name'],
        ]);
        $variantNames = [];
        foreach ($component['variants'] as $variant) {
            foreach ($needles as $needle) {
                if (str_starts_with($variant, $needle . '-')) {
                    $variantNames[] = substr($variant, strlen($needle) + 1);
                }
            }
        }
        $found = [];
        foreach (array_keys($properties) as $property) {
            foreach ($variantNames as $variant) {
                if (str_contains($property, '-' . $variant . '-')) {
                    continue 2;
                }
            }
            foreach ($needles as $needle) {
                if (str_contains($property, $needle)) {
                    $found[$property] = true;
                }
            }
        }
        foreach ($component['customProperties'] as $property) {
            if (isset($properties[$property])) {
                $found[$property] = true;
            }
        }
        ksort($found);

        return array_keys($found);
    }

    /**
     * @return array<string, true>
     */
    private static function tokens(string $source, string $pattern): array
    {
        preg_match_all($pattern, $source, $matches);
        $tokens = array_fill_keys(array_map('strtolower', $matches[1] ?? []), true);
        ksort($tokens);

        return $tokens;
    }

    /**
     * @param array<string, string> $packages
     * @return array<string, string> source contents to EXT: reference
     */
    private static function javascript(array $packages): array
    {
        $sources = [];
        foreach ($packages as $key => $package) {
            $directory = $package . '/Resources/Public/JavaScript';
            if (!is_dir($directory)) {
                continue;
            }
            $entries = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            );
            foreach ($entries as $entry) {
                if (!$entry instanceof SplFileInfo || !$entry->isFile() || $entry->getExtension() !== 'js') {
                    continue;
                }
                $relative = substr($entry->getPathname(), strlen($package) + 1);
                $sources[(string) file_get_contents($entry->getPathname())] = 'EXT:' . $key . '/' . $relative;
            }
        }

        return $sources;
    }

    /**
     * @param array<string, string> $sources
     */
    private static function sourceCarrying(array $sources, string $token): ?string
    {
        if (!str_starts_with($token, 'typo3-')) {
            return null;
        }
        foreach ($sources as $source => $reference) {
            if (str_contains($source, $token)) {
                return $reference;
            }
        }

        return null;
    }

    /**
     * @param array<string, string> $packages
     * @return array{0: string, 1: string}|null
     */
    private static function installedPath(string $corePath, array $packages): ?array
    {
        if (preg_match('#^typo3/sysext/([^/]+)/(.+)$#', $corePath, $matches) !== 1) {
            return null;
        }
        $package = $packages[$matches[1]] ?? null;
        if ($package === null || !is_file($package . '/' . $matches[2])) {
            return null;
        }

        return [$package . '/' . $matches[2], 'EXT:' . $matches[1] . '/' . $matches[2]];
    }

    /**
     * The styleguide wraps each copyable example in sg:example. Keep only
     * examples that actually contain this component, not the page chrome.
     *
     * @return array<int, string>
     */
    private static function examples(string $template, string $rootClass): array
    {
        preg_match_all('#<sg:example\b[^>]*>(.*?)</sg:example>#si', $template, $matches);
        $all = [];
        $matching = [];
        foreach ($matches[1] as $example) {
            $contains = str_starts_with($rootClass, 'typo3-')
                ? str_contains($example, '<' . $rootClass)
                : preg_match(
                    '/class=["\'][^"\']*(?<![a-z0-9_-])'
                        . preg_quote($rootClass, '/')
                        . '(?![a-z0-9_-])[^"\']*["\']/i',
                    $example,
                ) === 1;
            $lines = preg_split('/\R/', trim($example, "\r\n")) ?: [];
            $indent = null;
            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }
                preg_match('/^\s*/', $line, $leading);
                $width = strlen($leading[0] ?? '');
                $indent = $indent === null ? $width : min($indent, $width);
            }
            $clean = trim(implode("\n", array_map(
                static fn(string $line): string => substr($line, $indent ?? 0),
                $lines,
            )));
            if ($clean === '') {
                continue;
            }
            $all[] = $clean;
            if ($contains) {
                $matching[] = $clean;
            }
        }

        return array_slice(array_values(array_unique($matching === [] ? $all : $matching)), 0, 4);
    }
}
