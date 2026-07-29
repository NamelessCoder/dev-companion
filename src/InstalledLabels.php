<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

use Typo3CmsMcp\Catalog\TranslationDomain;

/**
 * The labels shipped by the packages of the discovered installation, read from
 * their XLF files.
 *
 * `language:domain:search` is the better answer and stays the first one asked:
 * it knows the assembled runtime state, including the overrides an installation
 * applies through `LANG/resourceOverrides`. But it needs a console that boots,
 * and booting needs a migrated database — while a fresh clone, a colleague
 * onboarding, and CI all spend their time not having one. The labels are in the
 * files either way, next to the icons that are already read from there.
 *
 * Only the source files are read, never a locale variant: `de.locallang.xlf`
 * is a translation of a file that is itself the label.
 */
final class InstalledLabels
{
    /** Where a package keeps label files, relative to its root. */
    private const LANGUAGE_DIRECTORY = 'Resources/Private/Language';

    /**
     * Every label of every installed package, or of one of them.
     *
     * @return array<int, array{ref: string, domain: string, key: string, source: string, resource: string}>
     */
    public static function all(string $extension = ''): array
    {
        $labels = [];
        foreach (Instance::packages() as $key => $path) {
            if ($extension !== '' && $key !== $extension) {
                continue;
            }
            foreach (self::files($path) as $file) {
                $reference = 'EXT:' . $key . '/' . $file;
                $domain = TranslationDomain::fromReference($reference);
                if ($domain === null) {
                    continue;
                }
                foreach (self::units($path . '/' . $file) as $id => $source) {
                    $labels[] = [
                        'ref' => $domain . ':' . $id,
                        'domain' => $domain,
                        'key' => $id,
                        'source' => $source,
                        'resource' => $reference,
                    ];
                }
            }
        }

        return $labels;
    }

    /**
     * The label files of one package, relative to its root: everything below
     * Resources/Private/Language/, and the labels.xlf of every site set.
     *
     * @return array<int, string>
     */
    private static function files(string $packagePath): array
    {
        $patterns = [
            self::LANGUAGE_DIRECTORY . '/*.xlf',
            self::LANGUAGE_DIRECTORY . '/*/*.xlf',
            'Configuration/Sets/*/labels.xlf',
        ];

        $files = [];
        foreach ($patterns as $pattern) {
            foreach (glob($packagePath . '/' . $pattern) ?: [] as $absolute) {
                $relative = substr($absolute, strlen($packagePath) + 1);
                // A locale prefix means a translation of a file that is itself
                // in this list, and its trans-unit ids are the same ones.
                if (preg_match('/^[a-z]{2}([_-][A-Za-z]{2,3})?\./', basename($relative)) === 1) {
                    continue;
                }
                $files[] = $relative;
            }
        }

        return $files;
    }

    /**
     * The trans-unit ids and source texts of one XLF file.
     *
     * Read with local-name(), because the same file is written with and without
     * the XLIFF namespace depending on how old it is, and a namespace-aware
     * query would silently return nothing for half of them.
     *
     * @return array<string, string>
     */
    private static function units(string $file): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string((string) file_get_contents($file));
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            return [];
        }

        $units = [];
        foreach ($xml->xpath('//*[local-name()="trans-unit"]') ?: [] as $unit) {
            $id = (string) ($unit['id'] ?? '');
            $source = $unit->xpath('*[local-name()="source"]');
            if ($id === '' || $source === [] || $source === null) {
                continue;
            }
            $units[$id] = trim((string) $source[0]);
        }

        return $units;
    }
}
