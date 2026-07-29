<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

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
                if (!str_contains($haystack, $term)) {
                    return false;
                }
            }

            return true;
        }));
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
}
