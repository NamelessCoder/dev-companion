<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Cli;

use Typo3CmsMcp\Upkeep\Prose as Measure;

/**
 * What the prose rule in AGENTS.md costs when nothing reads it.
 *
 * Every other rule that file states is held by something. "One point per
 * sentence" was held by whoever reread the paragraph, and the result was a
 * requirement whose opening sentence ran to 96 words before anybody counted.
 *
 * This counts. It fails on one thing only — the bold sentence a requirement or
 * a decision opens with, because that one has a job the rest of the file does
 * not — and reports the rest, in the shape `hints coverage` already uses for a
 * corpus measured against a number: how many, where, and the worst one by name.
 */
final class Prose implements Subject
{
    /** How many files the report names before it stops naming them. */
    private const NAMED = 10;

    public static function about(): string
    {
        return 'the prose this repository writes about itself, against its own measure';
    }

    public static function commands(): array
    {
        return [
            'check' => ['', 'the sentences over ' . Measure::MEASURE . ' words, and the leads that may not be', self::check(...)],
        ];
    }

    /**
     * The measure over the whole corpus, worst file first.
     *
     * A long sentence in the body is reported and nothing else: it can be the
     * right sentence, and a rewrite made to satisfy a counter produces two
     * short ones saying what one said. What the number is for is the file that
     * has twenty of them, which is a file nobody has reread since it was
     * written.
     */
    private static function check(): int
    {
        $measured = array_map(Measure::measure(...), Measure::documents());
        usort($measured, static fn(array $a, array $b): int => count($b['over']) <=> count($a['over']));

        $sentences = array_sum(array_column($measured, 'sentences'));
        $over = array_sum(array_map(static fn(array $file): int => count($file['over']), $measured));

        printf(
            "%d of %d sentences run past %d words, in %d files.\n",
            $over,
            $sentences,
            Measure::MEASURE,
            count(array_filter($measured, static fn(array $file): bool => $file['over'] !== [])),
        );

        foreach (array_slice(array_filter($measured, static fn(array $file): bool => $file['over'] !== []), 0, self::NAMED) as $file) {
            printf("  %3d  %s (longest %d)\n", count($file['over']), $file['file'], $file['over'][0]['words']);
        }

        $leads = Measure::leadsOverTheMeasure();
        if ($leads === []) {
            printf("\nEvery requirement and decision opens within the measure.\n");

            return 0;
        }

        printf("\n%d open with a sentence a reader cannot stop after:\n", count($leads));
        foreach ($leads as $lead) {
            printf("  %-10s %3d words  %s…\n", $lead['id'], $lead['words'], substr($lead['text'], 0, 60));
        }

        return 1;
    }
}
