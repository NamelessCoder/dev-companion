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
     *
     * A needle of several words is separated by a hyphen as readily as by a
     * space, because a compound is written both ways and every curated
     * vocabulary here is written one way. All of them read this — the domain
     * keywords, the hint patterns, the markers — so a caller writing
     * `content-element` used to fail every one at once: the domain fell back to
     * PHP and the hint was never a candidate, and the `content element` pattern
     * was then searched for verbatim anyway.
     *
     * Measured over the 195 multi-word patterns of the hint corpus, asked in
     * both spellings: hyphenated, 110 reached the hint the pattern was written
     * for, and 176 do now — the same number the spaced spelling reaches. Over
     * the 107 texts this repository has to hand, nothing moved at all.
     * `D-ANS-022` has the rest of the sweep and what it ruled out.
     *
     * Only the space between words is loosened. A separator inside one word —
     * `tt_content`, `mod.web_layout`, `list_type` — is what `D-ANS-006` is
     * about, and it is matched as it is written.
     */
    public static function containsWord(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        if (str_contains($needle, ' ')) {
            $words = array_map(
                static fn(string $word): string => preg_quote($word, '/'),
                preg_split('/ +/', $needle) ?: [],
            );

            return preg_match('/\b' . implode('[ -]+', $words) . '/i', $haystack) === 1;
        }

        return preg_match('/\b' . preg_quote($needle, '/') . '/i', $haystack) === 1;
    }
}
