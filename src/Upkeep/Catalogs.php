<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Paths;

/**
 * The bundled catalogs below knowledge/catalog/, as they are on disk.
 *
 * Read rather than validated: what a catalog has to say is checked against the
 * core checkouts by `catalog:check`, and against one checkout of the caller's
 * own by `catalog:paths`. Both start here.
 */
final class Catalogs
{
    /** @return array<int, array<string, mixed>> */
    public static function read(string $name): array
    {
        $path = Paths::root() . '/knowledge/catalog/' . $name . '.json';

        return json_decode((string) file_get_contents($path), true);
    }
}
