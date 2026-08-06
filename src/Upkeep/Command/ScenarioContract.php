<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Scenarios;

/**
 * One targeted contract case, which is read rather than run forward.
 *
 * The same handover as `scenarios:show`, for a case that names its own task
 * shape: what it claims is held by a test rather than by a session somebody
 * recorded, which is why it is never recorded as a run.
 */
#[AsCommand(
    name: 'scenarios:contract',
    description: 'the same for a targeted case, which is read rather than run forward',
)]
final class ScenarioContract extends ScenarioReport
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the contract case to hand over')]
        string $id,
    ): int {
        $id = strtoupper($id);
        $case = Scenarios::contracts()[$id] ?? null;
        if ($case === null) {
            Cli::errors($output)->writeln(isset(Scenarios::load()[$id])
                ? sprintf('%s is an open forward review: bin/cli scenarios:show %s', $id, $id)
                : sprintf('There is no contract case %s.', $id));

            return 2;
        }

        return $this->report($output, $case, 'Contract');
    }
}
