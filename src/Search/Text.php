<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Search;

/**
 * Small text helpers shared by the matchers.
 */
final class Text
{
    /**
     * Endings a word may gain and still be the word a needle names.
     *
     * These are the forms of one word — its plural, its tense, the noun of the
     * act — and not what is derived from it. The line is where the measurement
     * put it rather than where grammar would: over the 169 prompts, contract
     * cases and hint titles this repository has to hand, `-s`, `-es`, `-e`,
     * `-ed` and `-ing` account for 89 matches past the end of a needle and not
     * one of them is a different word, while `-able`, `-er` and `-ers` account
     * for four, of which three are "maintainable" and "maintainers" read as
     * maintenance work. `-ion` and `-ions` are in for `deprecat`, the one
     * needle in the whole curated vocabulary written as less than a word, whose
     * noun is the name of the intent it selects.
     *
     * @var array<int, string>
     */
    private const INFLECTIONS = ['s', 'es', 'e', 'd', 'ed', 'ing', 'ion', 'ions'];

    /**
     * Whether $haystack carries $needle as the word it is.
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
     * the 107 texts this repository had to hand then, nothing moved at all.
     * `D-ANS-022` has the rest of the sweep and what it ruled out.
     *
     * Only the space between words is loosened. A separator inside one word —
     * `tt_content`, `mod.web_layout`, `list_type` — is what `D-ANS-006` is
     * about, and it is matched as it is written.
     *
     * On the right the word ends where the needle does, give or take one of
     * INFLECTIONS. Ending nowhere is what made `test` match "testimonials" and
     * `boot` match "Bootstrap", while prefix matching is what makes `label`
     * find "labels" — so what has to be told apart is not the needle and not
     * the corpus but the rest of the word, which is an ending in the one case
     * and another word in the other (`D-ANS-050`). A needle that is a stem
     * rather than a word is asked with startsWord() instead.
     */
    public static function containsWord(string $haystack, string $needle): bool
    {
        return self::matches($haystack, $needle, self::ending($needle));
    }

    /**
     * Whether $haystack carries $needle at the start of a word.
     *
     * The same rule with its right side open, for a needle that is not a word:
     * `TermSearch::stem()` cuts a query word to six characters, so "testimonials"
     * is searched for as "testim" and "deprecated" as "deprec", and a rule that
     * closed those on the right would search the corpus for a word nobody
     * writes. Which of the two a caller wants is a property of the needle it
     * holds, and only the caller knows it.
     */
    public static function startsWord(string $haystack, string $needle): bool
    {
        return self::matches($haystack, $needle, '');
    }

    private static function matches(string $haystack, string $needle, string $ending): bool
    {
        if ($needle === '') {
            return false;
        }

        $words = array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            preg_split('/ +/', $needle) ?: [],
        );

        return preg_match('/\b' . implode('[ -]+', $words) . $ending . '/i', $haystack) === 1;
    }

    /**
     * What a match may run into past the needle, and where it has to stop.
     *
     * Only a needle ending in a letter can run into the next word at all. One
     * ending in a separator already ends where its word does — `f:` is the
     * Fluid namespace prefix and what follows it is the whole point,
     * `typo3/sysext/` is a path a file name continues — so their right side is
     * left as it was.
     *
     * A letter is what closes it rather than a word character, because a needle
     * running into an underscore is inside one identifier rather than in the
     * next word: `sys_file` reaching `sys_file_reference` is `D-ANS-006`'s side
     * of the same question and is left alone.
     */
    private static function ending(string $needle): string
    {
        if (preg_match('/\p{L}$/u', $needle) !== 1) {
            return '';
        }

        return '(?:' . implode('|', self::INFLECTIONS) . ')?(?!\p{L})';
    }
}
