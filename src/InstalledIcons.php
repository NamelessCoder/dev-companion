<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The icon identifiers registered in the discovered installation.
 *
 * Unlike labels or backend modules there is no console command that exposes
 * the icon registry, so this reads the three places TYPO3 assembles it from:
 * the T3Icons set shipped with the core, the Configuration/Icons.php of every
 * installed package, and the flag images the registry adds lazily.
 *
 * The registration files are parsed, never included. They are ordinary PHP that
 * would run in this process, and nothing about answering "is this identifier
 * registered" justifies executing a project's code.
 */
final class InstalledIcons
{
    public const SOURCE_T3ICONS = 't3icons';
    public const SOURCE_FLAGS = 'flags';

    /** @var array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>|null */
    private static ?array $icons = null;

    /**
     * Every registered identifier with the package that registers it.
     *
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>
     */
    public static function all(): array
    {
        if (self::$icons !== null) {
            return self::$icons;
        }

        $packages = Instance::packages();
        if ($packages === []) {
            return self::$icons = [];
        }

        $icons = [];
        foreach (self::fromT3Icons($packages['core'] ?? null) as $icon) {
            $icons[$icon['identifier']] = $icon;
        }
        foreach ($packages as $key => $path) {
            foreach (self::fromRegistrationFile($path) as $identifier) {
                $icons[$identifier] = [
                    'identifier' => $identifier,
                    'category' => self::category($identifier),
                    'aliasOf' => null,
                    'source' => 'EXT:' . $key . '/Configuration/Icons.php',
                ];
            }
        }
        foreach (self::fromFlags($packages['core'] ?? null) as $identifier) {
            $icons[$identifier] = [
                'identifier' => $identifier,
                'category' => 'flags',
                'aliasOf' => null,
                'source' => self::SOURCE_FLAGS,
            ];
        }

        ksort($icons);

        return self::$icons = array_values($icons);
    }

    /** Drops the memoized registry; for tests that move between installations. */
    public static function forget(): void
    {
        self::$icons = null;
    }

    /** @return array<int, string> */
    public static function categories(): array
    {
        $categories = array_values(array_unique(array_column(self::all(), 'category')));
        sort($categories);

        return $categories;
    }

    public static function has(string $identifier): bool
    {
        $identifier = strtolower(trim($identifier));
        foreach (self::all() as $icon) {
            if ($icon['identifier'] === $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the query is shaped like an identifier rather than like a search
     * phrase. The distinction decides what a miss means: "passkey" finding
     * nothing is a search that came up empty, "status-reference-hard" finding
     * nothing is a validation result.
     */
    public static function looksLikeIdentifier(string $query): bool
    {
        $query = strtolower(trim($query));
        if (preg_match('/^([a-z][a-z0-9]*)-[a-z0-9]+(-[a-z0-9]+)*$/', $query, $matches) !== 1) {
            return false;
        }

        return in_array($matches[1], self::categories(), true);
    }

    /**
     * The curated concept map: which meaning is spelled by which shape.
     *
     * Identifiers name shapes, not intents — a warning is
     * actions-exclamation-triangle — so searching for the meaning finds
     * nothing without this. It is the one part of the old bundled catalog worth
     * keeping, because it is judgement rather than an inventory.
     *
     * @return array<string, array<int, string>>
     */
    public static function concepts(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::knowledgeFile('icon-concepts.json')), true);

        return is_array($decoded) ? ($decoded['concepts'] ?? []) : [];
    }

    /**
     * @return array<int, array{identifier: string, category: string, aliasOf: ?string, source: string}>
     */
    private static function fromT3Icons(?string $corePath): array
    {
        if ($corePath === null) {
            return [];
        }

        $file = $corePath . '/Resources/Public/Icons/T3Icons/icons.json';
        if (!is_file($file)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($file), true);
        if (!is_array($decoded)) {
            return [];
        }

        $icons = [];
        foreach (array_keys($decoded['icons'] ?? []) as $identifier) {
            $icons[] = [
                'identifier' => (string) $identifier,
                'category' => self::category((string) $identifier),
                'aliasOf' => null,
                'source' => self::SOURCE_T3ICONS,
            ];
        }
        foreach ($decoded['aliases'] ?? [] as $alias => $target) {
            $icons[] = [
                'identifier' => (string) $alias,
                'category' => self::category((string) $alias),
                'aliasOf' => (string) $target,
                'source' => self::SOURCE_T3ICONS,
            ];
        }

        return $icons;
    }

    /**
     * The identifiers a package registers, read out of its Configuration/Icons.php
     * by tokenising it: the file returns an array keyed by identifier, and the
     * keys are all that is needed.
     *
     * @return array<int, string>
     */
    private static function fromRegistrationFile(string $packagePath): array
    {
        $file = $packagePath . '/Configuration/Icons.php';
        if (!is_file($file)) {
            return [];
        }

        $identifiers = [];
        $depth = 0;
        $tokens = @token_get_all((string) file_get_contents($file));
        foreach ($tokens as $index => $token) {
            if ($token === '[') {
                ++$depth;
                continue;
            }
            if ($token === ']') {
                --$depth;
                continue;
            }
            if ($depth !== 1 || !is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            // Only a string that is a key — the next meaningful token is "=>".
            for ($next = $index + 1; isset($tokens[$next]); ++$next) {
                $following = $tokens[$next];
                if (is_array($following) && in_array($following[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }
                if (is_array($following) && $following[0] === T_DOUBLE_ARROW) {
                    $identifiers[] = strtolower(trim($token[1], "'\""));
                }
                break;
            }
        }

        return $identifiers;
    }

    /** @return array<int, string> */
    private static function fromFlags(?string $corePath): array
    {
        if ($corePath === null) {
            return [];
        }

        $flags = [];
        foreach (glob($corePath . '/Resources/Public/Icons/Flags/*.webp') ?: [] as $file) {
            $flags[] = 'flags-' . strtolower(pathinfo($file, PATHINFO_FILENAME));
        }

        return $flags;
    }

    private static function category(string $identifier): string
    {
        return strstr($identifier, '-', true) ?: 'default';
    }
}
