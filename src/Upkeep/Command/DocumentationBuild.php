<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * Writes `documentation/` out as the source the published site is generated
 * from.
 *
 * Only the copy is publishable. The tree it comes from is written for a reader
 * who has the checkout, and every link that leaves it is rewritten here to the
 * file on GitHub — what that costs and what it buys is `Site`.
 *
 * This stops at the markdown. `guides.xml` is what turns the copy into a site,
 * and the workflow runs the two one after the other, so a page can be seen the
 * way it will be published without anything being deployed.
 */
#[AsCommand(
    name: 'documentation:build',
    description: 'write documentation/ out as the source the published site is generated from',
)]
final class DocumentationBuild
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('where the copy is written; guides.xml reads the default one')]
        string $target = Site::TARGET,
    ): int {
        $built = Site::build($target);

        foreach ($built['removed'] as $removed) {
            $output->writeln(sprintf('removed %s, which documentation/ no longer has', $removed));
        }

        $output->writeln(sprintf('%s — %d files, %s', $target, count($built['written']), Site::repository()));

        return 0;
    }
}
