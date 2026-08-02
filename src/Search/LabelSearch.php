<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Search;

/**
 * Turns a label query into something the installation's console can answer.
 *
 * `language:domain:search --search=` is a literal substring match against one
 * label at a time, so a query of more than one word only ever matches where
 * that exact string occurs in one label — which is almost never, while the
 * tool asks for words. The words are therefore handed over as an alternation,
 * which brings back every label carrying at least one of them, and the
 * intersection is taken here: a label answers the query when it carries all of
 * them, in its text or in its trans-unit id, in any order.
 *
 * The alternation is one console call rather than one per word. A console call
 * boots TYPO3, and the union it returns is also what makes "save alone matches
 * 65 labels" answerable without asking again.
 */
final class LabelSearch
{
    /**
     * The words a query is made of.
     *
     * Case is not part of a word here: the trans-unit id of a label is
     * lowercase and its source text is not, and a caller typing "save" means
     * both.
     *
     * @return array<int, string>
     */
    public static function terms(string $query): array
    {
        $terms = preg_split('/\s+/', trim($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_map(
            static fn(string $term): string => mb_strtolower($term),
            $terms
        )));
    }

    /**
     * The terms as an answer names them back, so a miss says what was searched
     * for rather than repeating the query as one string.
     *
     * @param array<int, string> $terms
     */
    public static function quoted(array $terms): string
    {
        return implode(', ', array_map(static fn(string $term): string => '"' . $term . '"', $terms));
    }

    /**
     * The console option that returns every label carrying at least one term.
     *
     * Without terms this is the empty search the console reads as "everything",
     * because an empty alternation would be a regular expression that matches
     * every string by accident rather than by intent.
     *
     * @param array<int, string> $terms
     */
    public static function consoleOption(array $terms): string
    {
        if ($terms === []) {
            return '--search=';
        }

        $alternatives = implode('|', array_map(
            static fn(string $term): string => preg_quote($term, '/'),
            $terms
        ));

        return '--regex=/(' . $alternatives . ')/i';
    }

    /**
     * The labels that carry every term, in the trans-unit id or in the text.
     *
     * No word boundary: a trans-unit id is `labels.save_document`, and an
     * underscore is a word character, so anchoring would drop exactly the ids a
     * caller searches by.
     *
     * @param array<int, array<string, string>> $labels
     * @param array<int, string> $terms
     * @return array<int, array<string, string>>
     */
    public static function carryingEvery(array $labels, array $terms): array
    {
        if ($terms === []) {
            return array_values($labels);
        }

        return array_values(array_filter($labels, static function (array $label) use ($terms): bool {
            $haystack = mb_strtolower(($label['key'] ?? '') . ' ' . ($label['source'] ?? ''));
            foreach ($terms as $term) {
                if (!self::carries($haystack, $term)) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * Whether one haystack carries one term.
     *
     * Plain containment, and one addition for the term that spells an
     * identifier: `ext_tables.php`, `SC_OPTIONS`, `mod.web_layout`. Those are
     * written with their separators where they are used and taken apart where
     * they are titled — the changelog file name is `ExtTablesPhpInExtensions`
     * and its words are "ext tables php in extensions", so the caller's own
     * spelling of the thing reached neither. Compared without the separators,
     * the two are the same string.
     *
     * Only a term carrying one is compared that way, so nothing a query without
     * separators reaches changes, and nothing this ever matched stops matching.
     */
    private static function carries(string $haystack, string $term): bool
    {
        if (str_contains($haystack, $term)) {
            return true;
        }

        $identifier = self::withoutSeparators($term);

        return $identifier !== $term
            && $identifier !== ''
            && str_contains(self::withoutSeparators($haystack), $identifier);
    }

    /** The same string as one word: what separates an identifier is how it is written, not what it is. */
    private static function withoutSeparators(string $text): string
    {
        return (string) preg_replace('/[\s_.\-]+/u', '', $text);
    }

    /**
     * How many of the labels each term reaches on its own.
     *
     * What a caller needs when the intersection is empty: the term that already
     * narrows enough is the one to ask with, and the term that reaches nothing
     * is the one that was misspelled or does not exist here.
     *
     * @param array<int, array<string, string>> $labels
     * @param array<int, string> $terms
     * @return array<int, array{term: string, matchCount: int}>
     */
    public static function perTermCounts(array $labels, array $terms): array
    {
        $counts = [];
        foreach ($terms as $term) {
            $counts[] = ['term' => $term, 'matchCount' => count(self::carryingEvery($labels, [$term]))];
        }

        return $counts;
    }

    /**
     * The most of a query anything still carries, as a query that can be asked.
     *
     * What a caller needs that the per-term counts cannot say: which words have
     * to go. The term with the smallest reach is the one to keep rather than
     * the one to drop — `yaml` reaches 17 of 3766 entries and is the word both
     * target entries carry — and no marginal count says which drop lets the
     * intersection survive, because two of the five had to go before anything
     * matched (`D-ANS-016`).
     *
     * Read as subsets that would be tried one level at a time, this is 15
     * filter passes for a five-word query and a depth nobody has bounded. It is
     * one pass instead: a subset reaches an item exactly when that item carries
     * every word of it, so the largest subsets that reach anything are the
     * largest sets of words a single item carries. 15 ms over the 3766 entries
     * of 14.3 against the 28 ms the peel costs, at any depth, and the count is
     * exact — no item carries more than the largest set, so the items carrying
     * one of them are all a re-query returns.
     *
     * Every largest subset is returned rather than one of them. On
     * `form set yaml registration deprecated` there are two, they reach one
     * entry each, and the one a tie-break picks first is
     * `Unify form setup YAML loading` rather than the deprecation that was
     * being looked for.
     *
     * @param array<int, array<string, string>> $items
     * @param array<int, string> $terms
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReachingSubsets(array $items, array $terms): array
    {
        if (count($terms) < 2) {
            return [];
        }

        $reached = [];
        $largest = 0;
        foreach ($items as $item) {
            $haystack = mb_strtolower(($item['key'] ?? '') . ' ' . ($item['source'] ?? ''));
            $carried = array_values(array_filter(
                $terms,
                static fn(string $term): bool => self::carries($haystack, $term),
            ));
            // A subset, so a query that hits is not answered with itself, and a
            // single word is what the per-term counts already say.
            if (count($carried) < 2 || count($carried) === count($terms)) {
                continue;
            }
            $largest = max($largest, count($carried));
            $key = implode(' ', $carried);
            $reached[$key] = ($reached[$key] ?? 0) + 1;
        }

        $subsets = [];
        foreach ($reached as $key => $matchCount) {
            $carried = explode(' ', (string) $key);
            if (count($carried) === $largest) {
                $subsets[] = ['terms' => $carried, 'matchCount' => $matchCount];
            }
        }
        usort($subsets, static fn(array $a, array $b): int => $a['matchCount'] <=> $b['matchCount']);

        return $subsets;
    }
}
