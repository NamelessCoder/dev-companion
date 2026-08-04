<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Upkeep\Environments;

/**
 * Every environment a scenario names, and where this checkout stands on it.
 *
 * The list is the whole of the answer to "which of these do I have". Four of
 * the five used to be a paragraph in `todo/reference/` naming somebody's home
 * directory, and a session that read it could not tell whether the machine it
 * was on had any of them.
 *
 * A made one is reported by what it is registered as rather than by whether its
 * directory exists, because the state that matters is the one a case turns on:
 * `E-STOPPED` is `E-SITE` with the project down, and the two are the same files.
 *
 * `E-SITE` is one installation per covered version (`D-EVI-006`), so it is a
 * row per version: which of them are installed here and which are missing.
 * Every covered line is made now, the development one included, so a row that
 * declines is a version argument this repository stopped covering.
 */
#[AsCommand(
    name: 'environment:status',
    description: 'which working directory a scenario can be run in, and which is only declared',
)]
final class EnvironmentStatus
{
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln(sprintf('Environments below %s', Environments::directory()));

        $sources = Environments::sources();
        $projects = Environments::projects();
        foreach (Environments::ids() as $id) {
            if ($id === 'E-SITE') {
                $output->writeln('  E-SITE     one installation per covered version');
                foreach (Versions::covered() as $version) {
                    $branch = $version['branch'];
                    $output->writeln(sprintf('    %-8s %s', $branch, Environments::refusal($branch) === ''
                        ? $this->site($branch, Environments::DEFAULT_DRIVER, $projects)
                        : 'not made here — `bin/cli environment:create E-SITE ' . $branch . '` says why'));
                    // One row per database that has actually been made. Every
                    // covered line on every driver would be four rows of
                    // "missing" per version for something almost nobody asks
                    // for, and what this command answers is "which of these do
                    // I have" rather than "which could I have".
                    foreach (Environments::drivers() as $driver) {
                        if ($driver === Environments::DEFAULT_DRIVER || !Environments::installed($branch, $driver)) {
                            continue;
                        }
                        $output->writeln(sprintf('    %-8s %s', '  ' . $driver, $this->site($branch, $driver, $projects)));
                    }
                }

                continue;
            }

            $output->writeln(sprintf('  %-10s %s', $id, $this->state($id, $sources[$id] ?? '')));
        }

        $output->writeln('');
        $output->writeln('`bin/cli environment:create <id> [version] [database]` makes the ones this');
        $output->writeln('repository makes, and says where the rest come from. The database defaults');
        $output->writeln(sprintf('to %s; %s are the rest.', Environments::DEFAULT_DRIVER, implode(', ', array_filter(
            Environments::drivers(),
            static fn(string $driver): bool => $driver !== Environments::DEFAULT_DRIVER,
        ))));

        return 0;
    }

    /** What this checkout has of an environment that is not the versioned one. */
    private function state(string $id, string $source): string
    {
        if ($source !== Environments::MADE) {
            return match ($source) {
                Environments::ELSEWHERE => 'bin/cli checkouts:update',
                Environments::STATE => 'a state of E-SITE — stop one of its DDEV projects',
                default => 'declared — todo/reference/ names what plays it',
            };
        }

        $path = Environments::path($id);

        return is_dir($path) ? $path : 'missing — run bin/cli environment:create ' . $id;
    }

    /**
     * @param array<string, array{name: string, status: string, approot: string, url: string}> $projects
     */
    private function site(string $branch, string $driver, array $projects): string
    {
        $path = Environments::path('E-SITE', $branch, $driver);
        $name = Environments::project($branch, $driver);
        $project = $projects[$name] ?? null;
        if (!Environments::installed($branch, $driver)) {
            // Where another live checkout holds the name, `create` refuses, so
            // naming it is the answer rather than the command that would. One
            // held for a checkout that is gone is not reported here: `create`
            // clears that itself, which makes the command the true answer.
            if (
                $project !== null
                && !Environments::abandoned($project)
                && rtrim($project['approot'], '/') !== rtrim($path, '/')
            ) {
                return sprintf('missing here — %s is the one in %s', $name, $project['approot']);
            }

            return 'missing — run bin/cli environment:create E-SITE ' . $branch;
        }

        return sprintf(
            '%s — DDEV project %s is %s',
            $path,
            $name,
            $project === null ? 'not registered on this machine' : $project['status'],
        );
    }
}
