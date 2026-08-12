<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * Turns `documentation/` and the readme into the site, in one call.
 *
 * This was six commands and two of them were installs nobody had run, so the
 * first render on a machine failed on a missing binary — `D-DOC-020`. They are
 * one command because their order is not a choice: the renderer publishes the
 * copy rather than the sources, and the theme's finish step reads the pages the
 * renderer has just written.
 *
 * Every step that leaves this process is a command a person could have typed,
 * printed before it runs and quoted in full when it fails. The first one on a
 * cold machine is minutes of `composer install`, and a render that dies has to
 * say which step.
 */
#[AsCommand(
    name: 'documentation:render',
    description: 'build the published site from the readme and documentation/, installing what it needs',
)]
final class DocumentationRender
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('where the site is built; `guides.xml` renders the default one')]
        string $into = Site::ROOT,
    ): int {
        foreach (Site::installs() as $what => $install) {
            $output->writeln(sprintf('installing %s', $what));
            if (!$this->step($output, $install)) {
                return 1;
            }
        }

        $built = Site::build($into . '/source');
        foreach ($built['removed'] as $removed) {
            $output->writeln(sprintf('removed %s, which documentation/ no longer has', $removed));
        }
        $output->writeln(sprintf('%s — %d files, %s', $into . '/source', count($built['written']), Site::repository()));

        if (!$this->step($output, Site::RENDER)) {
            return 1;
        }

        // The theme's own step, and the one that makes the pages a site. What
        // it carried and what it indexed is its own report, printed as it wrote
        // it rather than counted a second time here.
        if (!$this->step($output, Site::finish($into . '/html'))) {
            return 1;
        }

        $drawings = Site::publishDrawings($into . '/html');
        $output->writeln(sprintf('%s — %d dark drawings', $into . '/html', count($drawings)));
        // Over a server rather than by opening the file: the search fetches its
        // index beside the pages, which a browser refuses over `file://`.
        $output->writeln(sprintf('read it: php -S localhost:8000 -t %s', $into . '/html'));

        return 0;
    }

    /**
     * One step, with whatever it said either way.
     *
     * A step that succeeded is not silent: the renderer's warnings and the
     * finish step's tally are what a person reads to see the render was the one
     * they meant, and swallowing them left the command printing only the
     * commands it had run.
     *
     * @param list<string> $command
     */
    private function step(OutputInterface $output, array $command): bool
    {
        $output->writeln(implode(' ', $command));
        [$exitCode, $result] = Site::run($command);
        if ($exitCode !== 0) {
            Cli::errors($output)->writeln(sprintf("%s failed:\n%s", implode(' ', $command), trim($result)));

            return false;
        }

        if (trim($result) !== '') {
            $output->writeln(trim($result));
        }

        return true;
    }
}
