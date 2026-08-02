<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
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
            $output->writeln(sprintf('  %-10s %s', $id, $this->state($id, $sources[$id] ?? '', $projects)));
        }

        $output->writeln('');
        $output->writeln('`bin/cli environment:create <id>` makes the ones this repository makes, and');
        $output->writeln('says where the rest come from.');

        return 0;
    }

    /**
     * @param array<string, array{name: string, status: string, approot: string, url: string}> $projects
     */
    private function state(string $id, string $source, array $projects): string
    {
        if ($source !== Environments::MADE) {
            return match ($source) {
                Environments::ELSEWHERE => 'bin/cli checkouts:update',
                Environments::STATE => 'a state of E-SITE — stop its DDEV project',
                default => 'declared — todo/reference/ names what plays it',
            };
        }

        $path = Environments::path($id);
        if ($id === 'E-NONE') {
            return is_dir($path) ? $path : 'missing — run bin/cli environment:create E-NONE';
        }

        $project = $projects[Environments::PROJECT] ?? null;
        if (!is_file($path . '/config/system/settings.php')) {
            // Where another live checkout holds the name, `create` refuses, so
            // naming it is the answer rather than the command that would. One
            // held for a checkout that is gone is not reported here: `create`
            // clears that itself, which makes the command the true answer.
            if (
                $project !== null
                && !Environments::abandoned($project)
                && rtrim($project['approot'], '/') !== rtrim($path, '/')
            ) {
                return sprintf('missing here — %s is the one in %s', Environments::PROJECT, $project['approot']);
            }

            return 'missing — run bin/cli environment:create E-SITE';
        }

        return sprintf(
            '%s — DDEV project %s is %s',
            $path,
            Environments::PROJECT,
            $project === null ? 'not registered on this machine' : $project['status'],
        );
    }
}
