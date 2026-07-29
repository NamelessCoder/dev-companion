<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Scores free-text queries against a corpus of field-addressed documents.
 *
 * Two corpora are searched this way — the prose sections and the architecture
 * hints — and they used to score differently, which is why the same question
 * reached one of them and not the other. What they share is the whole method: a
 * term is worth what it separates one document from the rest, it is matched at
 * a word boundary rather than as a substring, and where in the document it
 * appears decides how much it counts. What they do not share is the field
 * layout, so that is the parameter.
 *
 * A document is `['field name' => 'text', ...]`; the caller says what each
 * field is worth.
 */
final class TermSearch
{
    /** Words that carry no topic signal. */
    private const STOPWORDS = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'can', 'do',
        'does', 'for', 'from', 'how', 'i', 'in', 'is', 'it', 'its', 'me', 'my',
        'not', 'of', 'on', 'or', 'the', 'their', 'them', 'then', 'there',
        'these', 'this', 'to', 'was', 'what', 'when', 'where', 'which', 'why',
        'will', 'with', 'you', 'your', 'typo3', 'core',
    ];

    /**
     * The meaningful terms of a query, reduced to a stem so that word forms of
     * the same word are one term: "deprecate", "deprecated" and "deprecations"
     * all become "deprec" and match the "Deprecations" section.
     *
     * @return array<int, string>
     */
    public static function terms(string $query): array
    {
        $terms = [];
        foreach (preg_split('/[^\p{L}\p{N}_.-]+/u', mb_strtolower(trim($query))) ?: [] as $word) {
            $word = trim($word, '.-');
            if ($word === '' || strlen($word) < 3 || in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            $terms[] = self::stem($word);
        }

        return array_values(array_unique($terms));
    }

    /**
     * How much each term separates one document of the corpus from the rest.
     *
     * "content", "structure" and "element" are in half the knowledge base and
     * say almost nothing about which document answers the question; "tsconfig"
     * says nearly everything. Weighing them the same is what let a query about
     * site sets be answered with the backend's Sass class naming, at a
     * confident three quarters of the query terms.
     *
     * @param array<int, string> $terms
     * @param array<int, array<string, string>> $documents
     * @return array<string, float>
     */
    public static function weights(array $terms, array $documents): array
    {
        $total = count($documents);
        $weights = [];
        foreach ($terms as $term) {
            $carrying = 0;
            foreach ($documents as $document) {
                foreach ($document as $text) {
                    if (Text::containsWord($text, $term)) {
                        ++$carrying;
                        break;
                    }
                }
            }
            if ($total === 0) {
                $weights[$term] = 0.0;
                continue;
            }

            // A term nothing carries counts as if the square root of the corpus
            // held it — halfway between the rarest term there is and no term at
            // all. It can never be covered, so what it does is lower the
            // coverage of everything else, and the fraction is what decides
            // which way that cuts: weighing it nothing let "how do I write a
            // good sonnet" decay into a query about writing and about good, and
            // something always answers that. Weighing it fully let one unknown
            // word sink a query the corpus does answer — nobody wrote "upload",
            // and the storage hint stopped being the answer to "file upload
            // storage configuration".
            $weights[$term] = $carrying === 0
                ? log($total) / 2
                : log($total / $carrying);
        }

        return $weights;
    }

    /**
     * Returns [score, weight covered]. A term counts by what it says, by the
     * weight of the field it was found in, and against how much other text it
     * was found among; the strongest field wins, so a term in both the title
     * and the body is not counted twice.
     *
     * The covered weight is the second number because it answers a different
     * question than the score: the score ranks, and the coverage says whether
     * the document is about the query at all. Which field carried the term is
     * a ranking signal and deliberately not an aboutness one — a rare word
     * anywhere in a document is evidence that it is about it, and demoting that
     * to a quarter of a title hit is what dropped "my extension service is not
     * found at runtime" below the floor while its own hint said exactly that.
     *
     * @param array<string, string> $document
     * @param array<string, float> $weights
     * @param array<string, int> $fieldWeights
     * @param int $undilutedWords Field length that still counts for what the
     *                            term says; see the callers for why a corpus
     *                            has the value it has.
     * @return array{0: int, 1: float}
     */
    public static function score(array $document, array $weights, array $fieldWeights, int $undilutedWords): array
    {
        $score = 0.0;
        $covered = 0.0;
        foreach ($weights as $term => $weight) {
            $best = 0.0;
            $dilution = 1.0;
            foreach ($fieldWeights as $field => $fieldWeight) {
                $text = $document[$field] ?? '';
                if (!Text::containsWord($text, (string) $term)) {
                    continue;
                }
                $diluted = self::dilution($text, $undilutedWords);
                if ($fieldWeight / $diluted >= $best) {
                    $best = $fieldWeight / $diluted;
                    $dilution = $diluted;
                }
            }
            if ($best <= 0.0) {
                continue;
            }
            $covered += $weight / $dilution;
            $score += $weight * $best;
        }

        return [(int) round($score * 10), $covered];
    }

    /**
     * How much longer than the corpus's ordinary field this one is, on a log
     * scale; never below 1, so a short field is not sharpened.
     *
     * A term says the same thing wherever it appears, but finding it among a
     * thousand other words is weaker evidence than finding it among fifty — a
     * long enough text contains anything.
     */
    private static function dilution(string $text, int $undilutedWords): float
    {
        $words = max(1, str_word_count($text));

        return $words <= $undilutedWords ? 1.0 : log($words / $undilutedWords) + 1.0;
    }

    /**
     * Cuts a plural ending and shortens long words, so the remaining stem is a
     * substring of every form of that word. Words are only shortened while at
     * least four characters remain, so short words like "css" stay intact.
     */
    private static function stem(string $word): string
    {
        if (strlen($word) >= 6 && str_ends_with($word, 'ies')) {
            $word = substr($word, 0, -3);
        } elseif (strlen($word) >= 6 && str_ends_with($word, 'es')) {
            $word = substr($word, 0, -2);
        } elseif (strlen($word) >= 5 && str_ends_with($word, 's')) {
            $word = substr($word, 0, -1);
        }

        return strlen($word) > 6 ? substr($word, 0, 6) : $word;
    }
}
