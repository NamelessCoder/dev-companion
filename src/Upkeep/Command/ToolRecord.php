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
use Typo3CmsMcp\Upkeep\ToolAnswers;

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
 */
#[AsCommand(
    name: 'tools:record',
    description: 'call every tool against a checkout and write what came back to documentation/clients/',
)]
final class ToolRecord
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the installation to answer from, defaulting to the newest core checkout below .checkouts/')]
        ?string $root = null,
        #[Argument('the date the recording carries, defaulting to today')]
        ?string $today = null,
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
        file_put_contents(ToolAnswers::file(), ToolAnswers::rendered($today ?? date('Y-m-d')));
        $output->writeln(substr(ToolAnswers::file(), strlen(Paths::root()) + 1));

        return 0;
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
