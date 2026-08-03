<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Checkouts;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Environments;
use Typo3CmsMcp\Upkeep\ToolAnswers;
use Typo3CmsMcp\Upkeep\ToolSurface;

/**
 * Calls every tool once and writes down what came back.
 *
 * `tools:index` renders the surface, which is derivable and therefore checked.
 * This is the other half — what a filled answer looks like — and it is neither:
 * half of these answers belong to the installation being read, so the recording
 * is evidence rather than a derivation. It is in the shape of `catalog:check`
 * for that reason: a command run against a checkout of somebody's own, whose
 * result is committed as data.
 *
 * The default root is the newest covered core checkout below `.checkouts/`,
 * because that is the one installation this repository can recreate — `bin/cli
 * checkouts:update` makes it, so the recording is repeatable by whoever reads
 * it. It has no reachable console, which is itself worth recording: those tools
 * answer from the packages or say they could not be asked, and both shapes are
 * ones a client meets.
 *
 * What it cannot show is the third: what a booted TYPO3 answers, which is the
 * ordinary case and was on no page at all. So the installation-backed tools are
 * recorded a second time, against the `E-SITE` below `.environments/` — the
 * other installation this repository recreates, by `bin/cli environment:create`
 * (`D-EVI-004`). Recording everything against that one instead was measured and
 * is a trade rather than a gain: `D-DOC-006` has what each side of it costs.
 *
 * Nothing here takes a path to somebody's own site for the second root. A
 * recording pointed at one is repeatable by the machine that holds it and by
 * nobody else, which is the reason the first root defaults to a checkout.
 */
#[AsCommand(
    name: 'tools:record',
    description: 'call every tool against a checkout and write what came back to documentation/clients/',
)]
final class ToolRecord
{
    /** @param list<string> $tools */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the installation to answer from, defaulting to the newest core checkout below .checkouts/')]
        ?string $root = null,
        #[Argument('the date the recording carries, defaulting to today')]
        ?string $today = null,
        #[Argument('the tools to answer for, defaulting to all of them')]
        array $tools = [],
    ): int {
        $root ??= self::newestCheckout();
        if (!is_dir($root)) {
            Cli::errors($output)->writeln(sprintf('%s is not a directory — run bin/cli checkouts:update, or name an installation.', $root));

            return 2;
        }

        Instance::discoverFrom($root);
        Typo3Cli::forget();
        $found = Instance::root();
        if ($found === null) {
            Cli::errors($output)->writeln(sprintf('No TYPO3 installation was found from %s, so there is nothing to record against.', $root));

            return 2;
        }

        $output->writeln(sprintf('Answering from %s (TYPO3 %s)', $found, Instance::typo3Version() ?? 'unknown'));

        $installation = $this->consoleAnswering($output, $found);
        $pages = ToolAnswers::rendered($today ?? date('Y-m-d'), $found, $installation, $tools);
        if (!is_dir(ToolSurface::directory())) {
            mkdir(ToolSurface::directory(), 0777, true);
        }
        foreach ($pages as $file => $contents) {
            file_put_contents($file, $contents);
        }

        // Only where the whole surface was written. Named tools leave every
        // other page alone, and a page nothing wrote this run is not a page
        // nothing writes any more.
        if ($tools === []) {
            foreach (ToolSurface::written() as $written) {
                if (!isset($pages[$written->getPathname()])) {
                    unlink($written->getPathname());
                    $output->writeln(sprintf('removed %s, which the registry no longer offers', $written->getFilename()));
                }
            }
        }

        $output->writeln(sprintf(
            '%s — %d pages',
            substr(ToolSurface::directory(), strlen(Paths::root()) + 1),
            count($pages) - 1,
        ));

        return 0;
    }

    /**
     * The made `E-SITE`, where it is there and its console answers, and null
     * with a reason where it is not.
     *
     * A silent absence is the failure this guards against: the pages would come
     * back with one answer per call and nothing on them would say the second
     * recording had been skipped, so a reader would take the console-answering
     * shape as still missing rather than as not recorded today. The console is
     * asked rather than assumed, because a stopped DDEV project is a directory
     * that is there and answers nothing — and a second recording of the same
     * `unsupported` object is lines that say what the first already said.
     */
    private function consoleAnswering(OutputInterface $output, string $primary): ?string
    {
        $path = Environments::path('E-SITE');
        $steps = 'bin/cli environment:create E-SITE makes it, and this command then records against both.';

        if (!is_dir($path)) {
            $output->writeln('No E-SITE below .environments/, so nothing records what a booted TYPO3 answers.');
            $output->writeln('    ' . $steps);

            return null;
        }
        if (realpath($path) === realpath($primary)) {
            return null;
        }

        Instance::discoverFrom($path);
        Typo3Cli::forget();
        if (!Typo3Cli::isAvailable()) {
            $output->writeln(sprintf("The E-SITE is there and its console is not: %s\n    %s", Typo3Cli::reason(), $steps));

            return null;
        }

        $output->writeln(sprintf('Answering the installation-backed tools a second time from %s', $path));

        return $path;
    }

    /**
     * The newest covered branch that is released, which is the version a client
     * is most likely to be on. `main` is covered too and is a development line:
     * recording against it would make the sample say `15.0.0-dev`, which is
     * true of nobody's installation.
     */
    private static function newestCheckout(): string
    {
        $released = array_values(array_filter(
            Versions::covered(),
            static fn(array $version): bool => $version['status'] !== 'development',
        ));
        $newest = $released === [] ? Versions::covered() : $released;

        return Checkouts::directory() . '/' . ($newest[count($newest) - 1]['branch'] ?? '');
    }
}
