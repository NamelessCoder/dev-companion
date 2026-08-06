<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * A scenario handed to whoever runs it: what has to be pasted, and what the
 * session is judged against.
 *
 * Two commands print this and differ in one word — `scenarios:show` for an open
 * forward review, `scenarios:contract` for a targeted case that is read rather
 * than run — so what they have in common is stated once here. That word is the
 * label above the status line, because the two claim their state on different
 * evidence: a forward review on a run somebody recorded, a contract case on the
 * test that holds it.
 */
abstract class ScenarioReport
{
    /**
     * @param array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string} $scenario
     */
    protected function report(OutputInterface $output, array $scenario, string $label): int
    {
        $output->writeln(sprintf('%s — %s', $scenario['id'], $scenario['title']));
        $output->writeln($scenario['file']);
        $output->writeln('');
        $output->writeln(sprintf('Environment  %s', $scenario['environment']));
        $output->writeln(sprintf(
            '%-12s %s%s',
            $label,
            $scenario['status'],
            $scenario['requirements'] === [] ? '' : ' — ' . implode(', ', $scenario['requirements']),
        ));
        if ($scenario['heldBy'] !== '') {
            // A case nobody runs claims its state on the strength of this line.
            $output->writeln(sprintf('Held by      %s', str_replace('`', '', $scenario['heldBy'])));
        }
        $output->writeln(sprintf('Criteria     %s', $scenario['criteria']));

        // Verbatim, on its own, with nothing around it: a prompt read off a screen
        // that also explains what it is testing is no longer the prompt.
        $output->writeln('');
        $output->writeln('Paste this and add nothing:');
        $output->writeln('');
        $output->writeln($scenario['prompt']);

        if ($scenario['needs'] !== []) {
            $output->writeln('');
            $output->writeln('What the agent needs from this server');
            foreach ($scenario['needs'] as $need) {
                $output->writeln(sprintf('  - %s', $need));
            }
        }

        foreach ([['outcomes', 'What has to come out of it'], ['failures', 'How it fails']] as [$section, $heading]) {
            $output->writeln('');
            $output->writeln($heading);
            foreach ($scenario[$section] as $index => $criterion) {
                $output->writeln(sprintf('  %s %d  %s', $section === 'outcomes' ? 'met' : 'avoided', $index + 1, $criterion));
            }
        }

        return 0;
    }
}
