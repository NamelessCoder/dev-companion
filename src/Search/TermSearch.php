<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Search;

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
    /**
     * Words that carry no topic signal.
     *
     * The two-letter ones are here because MIN_LENGTH is two. Until it was,
     * the floor did this list's work for every word that short and the list
     * only ever had to name the ones somebody happened to write down —
     * "set it up from scratch" then reached Setting Up Backend Groups in five
     * of the 41 scenario prompts, on the word "up" alone.
     *
     * "if" is deliberately not one of them, and it is the reason the floor
     * moved: it names a ViewHelper and a TypoScript function, so it is the
     * word a caller asking about `f:if` has left. "be" is, and stays so
     * against the same argument — it is the backend namespace on 17 pages of
     * the ViewHelper reference, and it is also the English verb in every
     * sentence saying what something should be.
     */
    private const STOPWORDS = [
        'am', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'can', 'do',
        'does', 'for', 'from', 'go', 'he', 'how', 'in', 'is', 'it', 'its',
        'me', 'my', 'no', 'not', 'of', 'on', 'or', 'so', 'the', 'their',
        'them', 'then', 'there', 'these', 'this', 'to', 'up', 'us', 'was',
        'we', 'what', 'when', 'where', 'which', 'why', 'will', 'with', 'you',
        'your', 'typo3', 'core',
    ];

    /**
     * Longest term still matched as a whole word rather than as a prefix.
     *
     * Prefix matching is how a stem finds every form of its word — "label"
     * finds "labels". At three characters there is no word form left to find
     * and the prefix matches whatever happens to start with those letters, so
     * the tolerance turns into noise. Measured over the hint corpus: "fal", the
     * File Abstraction Layer, prefix-matched seven hints through "fallback" and
     * "false" and the right one once. And because a term is weighed by how few
     * documents carry it, an accident that lands in exactly one document
     * becomes the most discriminating term in the query — "ist", which occurs
     * in no hint as a word at all, outweighed everything in "die Beschriftung
     * im Frontend ist falsch" and decided the answer.
     */
    private const PREFIX_FROM_LENGTH = 4;

    /**
     * Shortest word of a query that is searched for at all.
     *
     * It is two rather than three because of the line above: a word this short
     * is matched as a whole word, so none of what makes a short prefix noisy
     * applies to it. "if" is the case that showed the floor was set against the
     * wrong risk — `Global/If.html` of the ViewHelper reference is titled "if",
     * and no query naming `f:if` reached it while every word under three
     * characters was dropped.
     *
     * One character is where it stops. A single letter is a whole word in the
     * corpus as readily as in the query — the `f` of `f:if` matches the `f` of
     * every other ViewHelper written out — so it separates nothing and is
     * carried by whatever happens to spell it out.
     */
    private const MIN_LENGTH = 2;

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
            if ($word === '' || strlen($word) < self::MIN_LENGTH || in_array($word, self::STOPWORDS, true)) {
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
                    if (self::carries($text, $term)) {
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
     * Returns [score, weight covered, the field each term was found in]. A term
     * counts by what it says, by the weight of the field it was found in, and
     * against how much other text it was found among; the strongest field wins,
     * so a term in both the title and the body is not counted twice.
     *
     * That strongest field is the third number's whole content, because a
     * caller told what a result was matched on is being told which words of the
     * query reached the document and where — the terms it does not carry are
     * the ones absent from it. Working that out anywhere else means deciding
     * the same tie a second time.
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
     * @return array{0: int, 1: float, 2: array<string, string>}
     */
    public static function score(array $document, array $weights, array $fieldWeights, int $undilutedWords): array
    {
        $score = 0.0;
        $covered = 0.0;
        $matched = [];
        foreach ($weights as $term => $weight) {
            $best = 0.0;
            $dilution = 1.0;
            $strongest = null;
            foreach ($fieldWeights as $field => $fieldWeight) {
                $text = $document[$field] ?? '';
                if (!self::carries($text, (string) $term)) {
                    continue;
                }
                $diluted = self::dilution($text, $undilutedWords);
                if ($fieldWeight / $diluted >= $best) {
                    $best = $fieldWeight / $diluted;
                    $dilution = $diluted;
                    $strongest = $field;
                }
            }
            if ($best <= 0.0 || $strongest === null) {
                continue;
            }
            $covered += $weight / $dilution;
            $score += $weight * $best;
            $matched[(string) $term] = $strongest;
        }

        return [(int) round($score * 10), $covered, $matched];
    }

    /**
     * Whether the text carries the term: as a word prefix from
     * PREFIX_FROM_LENGTH characters up, as a whole word below it.
     *
     * Public because the curated vocabulary needs the same rule for the same
     * reason. It is matched the other way round — the pattern is looked for in
     * the query rather than the query in the document — but "needle occurs in
     * haystack" is the same question, and a bare short pattern goes wrong there
     * in exactly the same way.
     */
    public static function carries(string $text, string $term): bool
    {
        if (strlen($term) >= self::PREFIX_FROM_LENGTH) {
            return Text::containsWord($text, $term);
        }

        return preg_match('/\b' . preg_quote($term, '/') . '\b/i', $text) === 1;
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
