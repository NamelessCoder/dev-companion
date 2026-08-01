<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Domains;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Scenarios;

/**
 * Reads the architecture hint corpus back through its own matcher and reports
 * what cannot be found in it. It never writes.
 *
 * `bin/cli catalog` exists because a core update invalidates a catalog entry
 * silently. A hint decays the same way and even more quietly: nothing about it
 * changes, the query nobody phrased right simply comes back empty, and the
 * caller reads that as "this server does not know" rather than as "I said it
 * differently". These are the two readings that make it loud.
 */
final class Hint implements Subject
{
    public static function about(): string
    {
        return 'what cannot be found in the hint corpus';
    }

    public static function commands(): array
    {
        return [
            'probe' => ['"<query>"', 'what that query reaches, in order, and which way in earned each hit', self::probe(...)],
            'coverage' => ['', 'the hints no title and no scenario prompt reaches, and the body lengths', self::coverage(...)],
        ];
    }

    /**
     * What one query reaches, and why.
     *
     * @param array<int, string> $arguments
     */
    private static function probe(array $arguments): int
    {
        $query = $arguments[0] ?? '';
        if (trim($query) === '') {
            return Cli::usage(self::class, 'probe');
        }

        $result = ArchitectureHints::find([], $query, 10);

        printf("Query:    %s\n", $query);
        printf("Domains:  %s\n", implode(', ', $result['domains']) ?: '(none)');
        if ($result['withheldCategories'] !== []) {
            printf("Withheld: %s — the query reads as frontend work\n", implode(', ', $result['withheldCategories']));
        }

        if ($result['matchedHints'] === []) {
            // Not a failure of this command, and not necessarily one of the
            // matcher: a miss is a legitimate answer, and what makes it one is that
            // the caller is told what there would have been to find.
            printf("\nNothing matched. %d hints were candidates, and are returned as the index.\n", count($result['availableHints']));

            return 0;
        }

        print "\n";
        foreach ($result['matchedHints'] as $hint) {
            // Which way in earned it. A hit on the curated vocabulary means
            // somebody anticipated this phrasing; a hit on the text alone means
            // the hint answers a question nobody indexed it for, and that is the
            // one worth reading — it is either a good catch or a false one.
            $how = $hint['matchedOn']['keywords'] > 0
                ? sprintf('appliesTo(%d) + text(%d)', $hint['matchedOn']['keywords'], $hint['matchedOn']['score'])
                : sprintf('text only(%d)', $hint['matchedOn']['score']);

            printf("  %-34s %-16s %s\n", $hint['id'], $hint['category'], $how);
        }

        return 0;
    }

    /**
     * What the corpus cannot be found by.
     *
     * Three numbers nobody otherwise knows: hints that their own title does not
     * reach, hints no scenario prompt reaches, and scenario prompts that reach
     * nothing. Plus the body lengths against the matcher's dilution reference,
     * which is the one constant the corpus can grow out of.
     */
    private static function coverage(): int
    {
        $hints = ArchitectureHints::load();

        print "Hints their own title does not reach\n";
        $unreachable = [];
        foreach ($hints as $hint) {
            if (!in_array($hint['id'], self::reaches($hint['title']), true)) {
                $unreachable[] = $hint;
            }
        }
        if ($unreachable === []) {
            print "  none\n";
        }
        foreach ($unreachable as $hint) {
            // Almost always the domain gate rather than the scoring: a title with
            // no signal in it falls back to PHP, and a hint in any other category
            // is then never a candidate. The hint is not badly written; it is
            // filed where the query cannot see it.
            printf("  %-34s %-16s (candidates: %s)\n", $hint['id'], $hint['category'], implode(', ', Domains::hintCategories(Domains::detect([], $hint['title']))));
        }

        // The scenarios are the only corpus of real phrasings this repository has,
        // and deliberately not a list kept beside the matcher: a query written to
        // test a matcher is written by someone who knows what it should return.
        $prompts = array_map(
            static fn(array $scenario): string => $scenario['prompt'],
            [...Scenarios::load(), ...Scenarios::contracts()],
        );
        $reached = [];
        $silent = [];
        foreach ($prompts as $id => $prompt) {
            $ids = self::reaches($prompt);
            if ($ids === []) {
                $silent[] = $id;
            }
            foreach ($ids as $hintId) {
                $reached[$hintId] = true;
            }
        }

        // Read with the caveat, or the number says more than it knows: a scenario
        // also has an environment, and a real session brings paths. This is the
        // prompt text alone, which is the weakest signal the matcher ever gets —
        // and the one a first question actually arrives with.
        printf(
            "\nScenario prompts that reach nothing (%d of %d, from the prompt text alone)\n",
            count($silent),
            count($prompts),
        );
        print $silent === [] ? "  none\n" : '  ' . implode(', ', $silent) . "\n";

        $never = array_values(array_filter(
            array_column($hints, 'id'),
            static fn(string $id): bool => !isset($reached[$id]),
        ));
        printf("\nHints no scenario prompt reaches (%d of %d)\n", count($never), count($hints));
        print $never === [] ? "  none\n" : '  ' . implode("\n  ", $never) . "\n";

        // The tripwire D-ANS-2 asks for. The matcher discounts a term found in a
        // body longer than the corpus's ordinary one, and the reference is what
        // "ordinary" was measured at. The mean has since grown past it, which is
        // not itself the failure — the reference is a floor now rather than the
        // mean. The headroom is the number to read: at MAX_MEAN_BODY_WORDS the
        // corpus is close to the length at which a query it has no answer for
        // gets one anyway, and the constant has to be measured again.
        $lengths = array_values(ArchitectureHints::bodyWords());
        sort($lengths);
        $mean = (int) round(array_sum($lengths) / max(1, count($lengths)));
        $reference = ArchitectureHints::UNDILUTED_WORDS;
        printf(
            "\nHint body length vs. the matcher's %d-word dilution reference\n"
            . "  mean %d, median %d, longest %d, over the reference: %d of %d\n"
            . "  %d words of headroom before the reference has to be measured again (ceiling %d)\n",
            $reference,
            $mean,
            $lengths[intdiv(count($lengths), 2)] ?? 0,
            max($lengths),
            count(array_filter($lengths, static fn(int $n): bool => $n > $reference)),
            count($lengths),
            ArchitectureHints::MAX_MEAN_BODY_WORDS - $mean,
            ArchitectureHints::MAX_MEAN_BODY_WORDS,
        );

        return $unreachable === [] && $silent === [] && $never === [] && $mean <= ArchitectureHints::MAX_MEAN_BODY_WORDS
            ? 0
            : 1;
    }

    /** @return array<int, string> */
    private static function reaches(string $query): array
    {
        return array_column(ArchitectureHints::find([], $query, 6)['matchedHints'], 'id');
    }
}
