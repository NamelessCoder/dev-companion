<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Catalog;

use Typo3CmsMcp\Paths;

/**
 * Provenance of the static catalogs.
 *
 * The catalogs are snapshots of a moving target: identifiers, label keys and
 * component classes change with every core release. Without the branch and
 * commit they were taken from, a miss is indistinguishable from "the catalog is
 * older than your checkout" — which is exactly the case where a confident
 * answer does the most damage.
 */
final class Meta
{
    /**
     * @return array{
     *     source: array{repository: string, branch: string, version: string, commit: string},
     *     verifiedAt: string,
     *     verifyCommand: string,
     *     scope: array<string, string>,
     *     counts: array<string, int>
     * }
     */
    public static function read(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('meta.json')), true);
        if (!is_array($decoded) || !isset($decoded['source'])) {
            throw new \RuntimeException('Invalid catalog/meta.json');
        }

        /** @var array{source: array{repository: string, branch: string, version: string, commit: string}, verifiedAt: string, verifyCommand: string, scope: array<string, string>, counts: array<string, int>} $decoded */
        return $decoded;
    }

    /** One-line provenance, appended to every catalog answer. */
    public static function line(): string
    {
        $meta = self::read();

        return sprintf(
            'Catalog snapshot: TYPO3 %s (%s) @ %s, verified %s. A miss means "not in this snapshot", not "does not exist" — verify against the checkout before concluding an identifier is invalid.',
            $meta['source']['version'],
            $meta['source']['branch'],
            substr($meta['source']['commit'], 0, 12),
            $meta['verifiedAt'],
        );
    }
}
