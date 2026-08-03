<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Knowledge\Hints;

/**
 * What one query reaches in the hint corpus, and why.
 *
 * `catalog:check` exists because a core update invalidates a catalog entry
 * silently. A hint decays the same way and even more quietly: nothing about it
 * changes, the query nobody phrased right simply comes back empty, and the
 * caller reads that as "this server does not know" rather than as "I said it
 * differently". This is one of the two readings that make it loud.
 */
#[AsCommand(
    name: 'hints:probe',
    description: 'what that query reaches, in order, and which way in earned each hit',
)]
final class HintProbe
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the query to read the corpus back through')]
        string $query,
    ): int {
        $result = Hints::find([], $query, 10);

        $output->writeln(sprintf('Query:    %s', $query));
        $output->writeln(sprintf('Domains:  %s', implode(', ', $result['domains']) ?: '(none)'));
        if ($result['withheldCategories'] !== []) {
            $output->writeln(sprintf('Withheld: %s — the query reads as frontend work', implode(', ', $result['withheldCategories'])));
        }

        if ($result['matchedHints'] === []) {
            // Not a failure of this command, and not necessarily one of the
            // matcher: a miss is a legitimate answer, and what makes it one is that
            // the caller is told what there would have been to find.
            $output->writeln('');
            $output->writeln(sprintf('Nothing matched. %d hints were candidates, and are returned as the index.', count($result['availableHints'])));

            return 0;
        }

        $output->writeln('');
        foreach ($result['matchedHints'] as $hint) {
            // Which way in earned it. A hit on the curated vocabulary means
            // somebody anticipated this phrasing; a hit on the text alone means
            // the hint answers a question nobody indexed it for, and that is the
            // one worth reading — it is either a good catch or a false one.
            $how = $hint['matchedOn']['keywords'] > 0
                ? sprintf('appliesTo(%d) + text(%d)', $hint['matchedOn']['keywords'], $hint['matchedOn']['score'])
                : sprintf('text only(%d)', $hint['matchedOn']['score']);

            $output->writeln(sprintf('  %-34s %-16s %s', $hint['id'], $hint['category'], $how));
        }

        return 0;
    }
}
