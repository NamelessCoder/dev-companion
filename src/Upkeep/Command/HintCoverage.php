<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Domains;
use Typo3CmsMcp\Upkeep\Scenarios;

/**
 * What the architecture hint corpus cannot be found by. It never writes.
 *
 * Three numbers nobody otherwise knows: hints that their own title does not
 * reach, hints no scenario prompt reaches, and scenario prompts that reach
 * nothing. Then two the corpus can grow into rather than out of — what the
 * always-selected category supplies, and the body lengths against the matcher's
 * dilution reference. Only the last one fails.
 */
#[AsCommand(
    name: 'hints:coverage',
    description: 'the hints no title and no scenario prompt reaches, what General supplies, and the body lengths',
)]
final class HintCoverage
{
    public function __invoke(OutputInterface $output): int
    {
        $hints = ArchitectureHints::load();

        $output->writeln('Hints their own title does not reach');
        $unreachable = [];
        foreach ($hints as $hint) {
            if (!in_array($hint['id'], self::reaches($hint['title']), true)) {
                $unreachable[] = $hint;
            }
        }
        if ($unreachable === []) {
            $output->writeln('  none');
        }
        foreach ($unreachable as $hint) {
            // Almost always the domain gate rather than the scoring: a title with
            // no signal in it falls back to PHP, and a hint in any other category
            // is then never a candidate. The hint is not badly written; it is
            // filed where the query cannot see it.
            $output->writeln(sprintf(
                '  %-34s %-16s (candidates: %s)',
                $hint['id'],
                $hint['category'],
                implode(', ', Domains::hintCategories(Domains::detect([], $hint['title']))),
            ));
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
        $output->writeln('');
        $output->writeln(sprintf(
            'Scenario prompts that reach nothing (%d of %d, from the prompt text alone)',
            count($silent),
            count($prompts),
        ));
        $output->writeln($silent === [] ? '  none' : '  ' . implode(', ', $silent));

        $never = array_values(array_filter(
            array_column($hints, 'id'),
            static fn(string $id): bool => !isset($reached[$id]),
        ));
        $output->writeln('');
        $output->writeln(sprintf('Hints no scenario prompt reaches (%d of %d)', count($never), count($hints)));
        $output->writeln($never === [] ? '  none' : '  ' . implode("\n  ", $never));

        // What D-KNW-001's second half asks for. General is the one category no
        // query has to earn, so a hint filed there is reachable from every task
        // there is — and the entries that go in are the ones that cross the
        // domains, which is also what makes them long. The share is the number
        // that says how far that has gone; the failure state it walks towards
        // is every answer being made of it, so nothing fails on it here and the
        // decision is where the reading belongs.
        $answers = 0;
        $fromGeneral = 0;
        $onlyGeneral = 0;
        $matched = 0;
        foreach ($prompts as $prompt) {
            // The limit typo3_task_guide composes a brief with, rather than the
            // wider one the reachability counts above need.
            $categories = array_column(ArchitectureHints::find([], $prompt, 4)['matchedHints'], 'category');
            if ($categories === []) {
                continue;
            }
            $answers++;
            $matched += count($categories);
            $fromGeneral += count(array_filter($categories, static fn(string $c): bool => $c === 'General'));
            $onlyGeneral += array_unique($categories) === ['General'] ? 1 : 0;
        }
        $general = count(array_filter(
            $hints,
            static fn(array $hint): bool => $hint['category'] === 'General',
        ));
        $output->writeln('');
        $output->writeln(sprintf(
            "What the always-selected category supplies, over the scenario prompts\n"
            . "  General holds %d of %d hints and supplies %d of %d matched (%.0f%%)\n"
            . '  %d of %d answers are made of General alone (every answer is what D-KNW-001 called wrong)',
            $general,
            count($hints),
            $fromGeneral,
            $matched,
            $matched > 0 ? 100 * $fromGeneral / $matched : 0.0,
            $onlyGeneral,
            $answers,
        ));

        // The tripwire D-ANS-002 asks for. The matcher discounts a term found in a
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
        $output->writeln('');
        $output->writeln(sprintf(
            "Hint body length vs. the matcher's %d-word dilution reference\n"
            . "  mean %d, median %d, longest %d, over the reference: %d of %d\n"
            . '  %d words of headroom before the reference has to be measured again (ceiling %d)',
            $reference,
            $mean,
            $lengths[intdiv(count($lengths), 2)] ?? 0,
            max($lengths),
            count(array_filter($lengths, static fn(int $n): bool => $n > $reference)),
            count($lengths),
            ArchitectureHints::MAX_MEAN_BODY_WORDS - $mean,
            ArchitectureHints::MAX_MEAN_BODY_WORDS,
        ));

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
