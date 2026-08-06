<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use Symfony\Component\Finder\Finder;

/**
 * Removes a directory a test wrote, with everything below it.
 *
 * Five tests had a copy of this, and the copies had drifted: only one of them
 * knew that a symlink to a directory answers isDir() and would still be there
 * after the rmdir that fails on it.
 */
final class Directory
{
    public static function remove(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        // The finder walks a directory before what is in it, so reversed it
        // hands over the deepest entry first — which is the order the entries
        // can be removed in. Dot files are ordinary files here: one left behind
        // is one the rmdir below fails on.
        $entries = Finder::create()->in($path)->ignoreDotFiles(false)->ignoreVCS(false)->reverseSorting();
        foreach ($entries as $entry) {
            // A symlink to a directory answers isDir() and is removed by
            // unlink(). The walk does not descend into one, so what it points
            // at is left where it is.
            $entry->isDir() && !$entry->isLink()
                ? rmdir($entry->getPathname())
                : unlink($entry->getPathname());
        }

        rmdir($path);
    }
}
