<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Search;

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
     * Plain substring matching is hard to police: "preview" contains "review",
     * "success" contains "css". The word boundary keeps prefix matching ("label"
     * finds "labels") without them; two words are joined by a hyphen as readily
     * as by a space (`D-ANS-022`); a separator inside one word is matched as
     * written (`D-ANS-006`); and the match ends where the needle's word does,
     * give or take an inflection (`D-ANS-050`), where a stem takes startsWord().
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
     * Only a needle ending in a letter can run into the next word at all: `f:`
     * and `typo3/sysext/` already end where their word does, so their right side
     * is left as it was. A letter closes it rather than a word character,
     * because a needle running into an underscore is inside one identifier
     * rather than in the next word — `sys_file` reaching `sys_file_reference` is
     * `D-ANS-006`'s side of the same question.
     */
    private static function ending(string $needle): string
    {
        if (preg_match('/\p{L}$/u', $needle) !== 1) {
            return '';
        }

        return '(?:' . implode('|', self::INFLECTIONS) . ')?(?!\p{L})';
    }
}
