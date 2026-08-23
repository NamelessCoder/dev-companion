<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\TestingFramework;

/**
 * What is below .checkouts/, and how old it is.
 *
 * typo3/testing-framework is reported beside the core branches, because a
 * statement about the harness a project extension tests in is verified against
 * a tag of that package rather than against a core branch (D-KNW-106).
 */
#[AsCommand(
    name: 'checkouts:status',
    description: 'what exists, at which revision',
)]
final class CheckoutStatus
{
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();
        $output->writeln(sprintf('Core checkouts below %s', $checkouts));
        foreach (Versions::covered() as $version) {
            $path = $checkouts . '/' . $version['branch'];
            $output->writeln(sprintf(
                '  %-6s %s',
                $version['branch'],
                is_dir($path . '/typo3/sysext/core') ? Checkouts::revision($path) : 'missing — run bin/cli checkouts:update',
            ));
        }

        $output->writeln('');
        $output->writeln(sprintf('%s, one release line per pin', TestingFramework::PACKAGE));
        foreach (TestingFramework::pairing($checkouts) as $pair) {
            $output->writeln(sprintf(
                '  %-6s %-9s %s',
                $pair['branch'],
                $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                is_dir($pair['path'] . '/Classes') ? Checkouts::revision($pair['path']) : 'missing — run bin/cli checkouts:update',
            ));
        }

        return 0;
    }
}
