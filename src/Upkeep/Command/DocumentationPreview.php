<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Site;

/**
 * The whole site on this machine, in one call, to look at.
 *
 * `documentation:prepare` is what a deployment needs and it stops at the copy —
 * `D-DOC-028`. This is the other half for whoever is writing a page and wants
 * to see it: it fetches the renderer into a gitignored directory beside the
 * build, renders, runs the theme's finish step and says where to read the
 * result.
 *
 * The renderer is fetched where it is missing and not otherwise, because a
 * preview is run again after every paragraph. What a deployment renders with is
 * resolved fresh every time, in the workflow, and `.site/` is deleted to get
 * that here.
 *
 * Every step that leaves this process is printed as the command a person could
 * have typed, and a failure quotes it with its output. The first one on a cold
 * machine is a `composer require`, and a preview that dies has to say which
 * step.
 */
#[AsCommand(
    name: 'documentation:preview',
    description: 'render the whole site on this machine and say where to read it',
)]
final class DocumentationPreview
{
    /** The branch is named because the theme publishes no tagged release yet. */
    private const THEME = 'typo3/soul-guides-theme:dev-main';

    /** Where the renderer is fetched to, below the build directory it renders into. */
    private const RENDERER = 'renderer';

    private readonly CommandRunner $runner;

    public function __construct(?CommandRunner $runner = null)
    {
        $this->runner = $runner ?? new SystemRunner();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument('where the site is built; `documentation/guides.xml` renders the default one')]
        string $into = Site::ROOT,
    ): int {
        if ((new DocumentationPrepare())($output, $into) !== 0) {
            return 1;
        }

        $renderer = $into . '/' . self::RENDERER;
        foreach ($this->fetch($renderer) as $step) {
            if (!$this->step($output, $step)) {
                return 1;
            }
        }

        $steps = [
            [$renderer . '/vendor/bin/guides', '--no-progress', '-c', Site::SOURCE],
            ['node', $renderer . '/vendor/typo3/soul-guides-theme/resources/dist/soul-finish.js', $into . '/html'],
        ];
        foreach ($steps as $step) {
            if (!$this->step($output, $step)) {
                return 1;
            }
        }

        // Over a server rather than by opening the file: the search fetches its
        // index beside the pages, which a browser refuses over `file://`.
        $output->writeln(sprintf('read it: php -S localhost:8000 -t %s', $into . '/html'));

        return 0;
    }

    /**
     * What it takes to have a renderer at that path, or nothing where there is
     * one already.
     *
     * Composer has no way to require into an empty directory, so the manifest
     * is written by `init` before the require that fills it in. It is not this
     * repository's to keep: what asks for the theme is here, and the theme
     * brings phpDocumentor Guides with it.
     *
     * @return list<list<string>>
     */
    private function fetch(string $renderer): array
    {
        // The build directory is an argument and may be anywhere, which is what
        // lets the suite drive this without writing into the checkout.
        $where = str_starts_with($renderer, '/') ? $renderer : Paths::root() . '/' . $renderer;
        if (is_file($where . '/vendor/bin/guides')) {
            return [];
        }

        if (!is_dir($where)) {
            mkdir($where, 0777, true);
        }
        $directory = '--working-dir=' . $renderer;

        return [
            ['composer', 'init', $directory, '--no-interaction', '--name=typo3/dev-companion-render'],
            ['composer', 'require', $directory, '--no-interaction', '--no-progress', self::THEME],
        ];
    }

    /**
     * One step, with whatever it said either way.
     *
     * A step that succeeded is not silent: the renderer's warnings and the
     * finish step's tally are what a person reads to see the render was the one
     * they meant.
     *
     * @param list<string> $command
     */
    private function step(OutputInterface $output, array $command): bool
    {
        $output->writeln(implode(' ', $command));
        $result = $this->runner->run($command, Paths::root());
        if ($result['exitCode'] !== 0) {
            Cli::errors($output)->writeln(sprintf(
                "%s failed:\n%s",
                implode(' ', $command),
                trim($result['output'] . $result['error']),
            ));

            return false;
        }

        $said = trim($result['output'] . $result['error']);
        if ($said !== '') {
            $output->writeln($said);
        }

        return true;
    }
}
