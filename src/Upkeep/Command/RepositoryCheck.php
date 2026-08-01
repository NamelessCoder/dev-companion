<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Every check this checkout can answer on its own, one after the other, and
 * what none of them fails on.
 *
 * The checks hold the files to their shape, and a file can be perfectly shaped
 * and still say that nobody has built the requirement or been back to the
 * decision. That state is legitimate, so it cannot be an error — but it was
 * invisible, and an entry sat in requirements/ unbuilt from the day the
 * directory was created because nothing ever read it out. The closing block is
 * that reading. It changes no exit code.
 */
#[AsCommand(
    name: 'repository:check',
    description: 'requirements, decisions, scenarios, todo, prose, and what none of them fails on',
)]
final class RepositoryCheck
{
    /**
     * The checks this runs, which are the ones that need nothing but this
     * checkout. `catalog:check` reads .checkouts/ and `hints:coverage` reports
     * gaps rather than failures, so both are asked for by name.
     */
    private const CHECKED = ['requirements', 'decisions', 'scenarios', 'todo', 'prose'];

    public function __invoke(OutputInterface $output, Application $application): int
    {
        $worst = 0;
        foreach (self::CHECKED as $subject) {
            $output->writeln(sprintf('── %s', $subject));
            $worst = max($worst, $application->doRun(new StringInput($subject . ':check'), $output));
            $output->writeln('');
        }

        $output->writeln('── unresolved');
        $application->doRun(new StringInput('backlog:list'), $output);

        return $worst;
    }
}
