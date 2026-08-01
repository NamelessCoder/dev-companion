<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Search;

/**
 * Small text helpers shared by the matchers.
 */
final class Text
{
    /**
     * Whether $needle appears in $haystack at the start of a word.
     *
     * Plain substring matching produces false positives that are hard to spot:
     * "preview" contains "review", "success" contains "css". Anchoring at a word
     * boundary keeps prefix matching ("label" finds "labels") without them.
     */
    public static function containsWord(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        return preg_match('/\b' . preg_quote($needle, '/') . '/i', $haystack) === 1;
    }
}
