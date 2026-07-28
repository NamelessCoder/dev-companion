<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

/**
 * Derives the translation domain of an XLF label file from its EXT: path.
 *
 * Since the translation-domain API landed, the canonical way to reference a
 * label is the domain form "backend.alt_doc:buttons.confirm.save_and_close",
 * not the file path "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:...".
 * The domain works in TCA labels and descriptions, LanguageService::sL(),
 * f:translate (as separate domain and key attributes), and registration configs.
 *
 * This is a port of the rules in TYPO3\CMS\Core\Localization\TranslationDomainResolver,
 * kept here so the catalog needs no core checkout:
 *
 * - domain format: package[.subdir...].resource
 * - "Resources/Private/Language/" is dropped from the path
 * - "Configuration/Sets/{Name}/labels.xlf" becomes "sets.{name}"
 * - directories are converted from UpperCamelCase to snake_case
 * - "locallang.xlf" becomes "messages", "locallang_{suffix}.xlf" becomes "{suffix}"
 * - a locale prefix such as "de.locallang.xlf" is ignored
 */
final class TranslationDomain
{
    /**
     * Returns the domain for an "EXT:key/..." reference, or null when the
     * reference is not an extension path.
     */
    public static function fromReference(string $reference): ?string
    {
        if (!str_starts_with($reference, 'EXT:')) {
            return null;
        }

        $withoutPrefix = substr($reference, 4);
        $slash = strpos($withoutPrefix, '/');
        if ($slash === false) {
            return null;
        }

        $extensionKey = substr($withoutPrefix, 0, $slash);
        $path = substr($withoutPrefix, $slash + 1);

        return $extensionKey . '.' . self::resource($path);
    }

    /**
     * Files that map to the same domain are resolved by precedence: a file
     * without the "locallang" prefix wins over "locallang_*", which wins over a
     * plain "locallang.xlf".
     */
    public static function precedence(string $reference): int
    {
        $fileName = self::stripLocale(basename($reference));
        $withoutExtension = self::withoutExtension($fileName);

        if (!str_contains($withoutExtension, 'locallang')) {
            return 3;
        }

        return str_starts_with($withoutExtension, 'locallang_') ? 2 : 1;
    }

    private static function resource(string $path): string
    {
        $fileName = self::stripLocale(basename($path));
        $path = dirname($path) === '.' ? $fileName : dirname($path) . '/' . $fileName;

        if (preg_match('#Configuration/Sets/([^/]+)/labels\.xlf$#', $path, $matches) === 1) {
            return 'sets.' . self::snakeCase($matches[1]);
        }

        if (str_starts_with($path, 'Resources/Private/Language/')) {
            $path = substr($path, strlen('Resources/Private/Language/'));
        }

        $parts = explode('/', $path);
        $fileName = array_pop($parts);
        $resourceParts = array_map(self::snakeCase(...), $parts);

        $withoutExtension = self::withoutExtension($fileName);
        if ($withoutExtension === 'locallang') {
            $resourceParts[] = 'messages';
        } elseif (str_starts_with($withoutExtension, 'locallang_')) {
            $resourceParts[] = self::snakeCase(substr($withoutExtension, strlen('locallang_')));
        } else {
            $resourceParts[] = self::snakeCase($withoutExtension);
        }

        return implode('.', $resourceParts);
    }

    /** Drops a locale prefix such as "de." or "de_AT." from a file name. */
    private static function stripLocale(string $fileName): string
    {
        if (substr_count($fileName, '.') > 1 && preg_match('/^[a-z]{2}([_-][A-z]{2,3})?\./', $fileName) === 1) {
            return substr($fileName, (int) strpos($fileName, '.') + 1);
        }

        return $fileName;
    }

    private static function withoutExtension(string $fileReference): string
    {
        return preg_replace('/\.[a-z0-9]+$/i', '', $fileReference) ?? $fileReference;
    }

    private static function snakeCase(string $input): string
    {
        $result = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $input) ?? $input;

        return str_replace('-', '_', strtolower($result));
    }
}
