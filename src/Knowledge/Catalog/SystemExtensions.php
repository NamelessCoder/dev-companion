<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Knowledge\Catalog;

use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;

/**
 * Which extensions the TYPO3 core ships, and on which of the covered versions.
 *
 * "Is this extension part of the core" is asked in both directions and answered
 * from memory in both: a community package cited as evidence of core direction,
 * and a system extension nobody knew existed. Neither is answerable from an
 * installation — the interesting case is an extension that is not installed,
 * which is exactly when the question comes up.
 *
 * So it is a catalog rather than a lookup against the installation: read off one
 * checkout per covered version, with the range each key holds on, and
 * re-derivable by bin/cli catalog check when a core release adds or drops one.
 */
final class SystemExtensions
{
    /**
     * @return array<int, array{key: string, package: string, description: string, since: ?int, until: ?int}>
     */
    public static function load(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('system-extensions.json')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid catalog/system-extensions.json');
        }

        return array_map(static fn(array $entry): array => [
            'key' => (string) $entry['key'],
            'package' => (string) $entry['package'],
            'description' => (string) ($entry['description'] ?? ''),
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
        ], $decoded);
    }

    /**
     * The entries a query names, on the version it asks about.
     *
     * A query is matched against the extension key and the Composer package
     * name before the description, because that is what a caller has in hand:
     * "typo3/cms-content-blocks", "theme_camino", "impexp". Both spellings of
     * the same thing therefore find it — the key with underscores and the
     * package with dashes.
     *
     * @return array<int, array{key: string, package: string, description: string, since: ?int, until: ?int}>
     */
    public static function find(string $query, ?int $target = null): array
    {
        $entries = array_values(array_filter(
            self::load(),
            static fn(array $entry): bool => Versions::holds($entry['since'], $entry['until'], $target),
        ));

        $query = trim(mb_strtolower($query));
        if ($query === '') {
            return $entries;
        }

        $needle = str_replace(['typo3/cms-', 'typo3/', '-'], ['', '', '_'], $query);

        $named = array_values(array_filter(
            $entries,
            static fn(array $entry): bool => $entry['key'] === $needle
                || mb_strtolower($entry['package']) === $query
                || str_contains($entry['key'], $needle),
        ));
        if ($named !== []) {
            return $named;
        }

        return array_values(array_filter(
            $entries,
            static fn(array $entry): bool => str_contains(mb_strtolower($entry['description']), $query),
        ));
    }
}
