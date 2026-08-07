<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Prose;

/**
 * What the prose rule in AGENTS.md costs when nothing reads it.
 *
 * Every other rule that file states is held by something. "One point per
 * sentence" was held by whoever reread the paragraph, and the result was a
 * requirement whose opening sentence ran to 96 words before anybody counted.
 *
 * This counts. It fails on one thing only — the bold sentence a requirement or
 * a decision opens with, because that one has a job the rest of the file does
 * not — and reports the rest, in the shape `hints:coverage` already uses for a
 * corpus measured against a number: how many, where, and the worst one by name.
 */
#[AsCommand(
    name: 'prose:check',
    description: 'the sentences over ' . Prose::MEASURE . ' words, and the leads that may not be',
)]
final class ProseCheck
{
    /** How many files the report names before it stops naming them. */
    private const NAMED = 10;

    /**
     * The measure over the whole corpus, worst file first.
     *
     * A long sentence in the body is reported and nothing else: it can be the
     * right sentence, and a rewrite made to satisfy a counter produces two
     * short ones saying what one said. What the number is for is the file that
     * has twenty of them, which is a file nobody has reread since it was
     * written.
     */
    public function __invoke(OutputInterface $output): int
    {
        $measured = array_map(Prose::measure(...), Prose::documents());
        usort($measured, static fn(array $a, array $b): int => count($b['over']) <=> count($a['over']));

        $sentences = array_sum(array_column($measured, 'sentences'));
        $over = array_sum(array_map(static fn(array $file): int => count($file['over']), $measured));

        $output->writeln(sprintf(
            '%d of %d sentences run past %d words, in %d files.',
            $over,
            $sentences,
            Prose::MEASURE,
            count(array_filter($measured, static fn(array $file): bool => $file['over'] !== [])),
        ));

        foreach (array_slice(array_filter($measured, static fn(array $file): bool => $file['over'] !== []), 0, self::NAMED) as $file) {
            $output->writeln(sprintf('  %3d  %s (longest %d)', count($file['over']), $file['file'], $file['over'][0]['words']));
        }

        // The other half, and the one nothing counted: what a client is handed
        // at connect. It is reported beside the corpus rather than folded into
        // it, because the number a reader wants here is the weight — a caller
        // pays for all of it before it has asked anything — and not which of
        // 578 markdown files is worst.
        $payload = Prose::payloadOverTheMeasure();
        $output->writeln('');
        $output->writeln(sprintf(
            'A client is handed %d characters of prose at connect. %d of those sentences run past %d words.',
            Prose::payloadWeight(),
            count($payload),
            Prose::MEASURE,
        ));
        foreach (array_slice($payload, 0, self::NAMED) as $entry) {
            $output->writeln(sprintf('  %3d  %s', $entry['words'], $entry['where']));
        }

        $leads = Prose::leadsOverTheMeasure();
        if ($leads === []) {
            $output->writeln('');
            $output->writeln('Every requirement and decision opens within the measure.');

            return 0;
        }

        $output->writeln('');
        $output->writeln(sprintf('%d open with a sentence a reader cannot stop after:', count($leads)));
        foreach ($leads as $lead) {
            $output->writeln(sprintf('  %-10s %3d words  %s…', $lead['id'], $lead['words'], substr($lead['text'], 0, 60)));
        }

        return 1;
    }
}
