<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The generated listing at the foot of a readme.
 *
 * It was a table, and a table earns its border characters by letting a column
 * be scanned vertically — which holds only while every cell fits on one line. A
 * listing carries a title, and a title is a sentence, so the widest column was
 * always going to be one. Aligning the columns is what made it visible: the
 * decisions listing came to 237 characters a row. See D-DOC-001.
 *
 * The link is what made it worse, and a reference link is what fixes it. A
 * markdown link puts a file path in the middle of the line while the rendered
 * text is short; moved to the foot of the file, the path is read by nobody and
 * breaks nothing.
 */
final class Listing
{
    /**
     * One entry per line: the id, linked, then what it says.
     *
     * The id leads because it is what everything else refers to — a commit
     * message, a todo's `Serves:`, another entry. Scanning for `D-SCO-006` is
     * the reason to open one of these files.
     *
     * @param array<int, array{ref: string, path: string, says: string}> $entries
     */
    public static function render(array $entries): string
    {
        if ($entries === []) {
            return '';
        }

        $lines = '';
        $references = '';
        foreach ($entries as $entry) {
            $lines .= sprintf("- [`%s`][%s] — %s\n", $entry['ref'], $entry['ref'], $entry['says']);
            $references .= sprintf("[%s]: %s\n", $entry['ref'], $entry['path']);
        }

        return $lines . "\n" . $references;
    }
}
