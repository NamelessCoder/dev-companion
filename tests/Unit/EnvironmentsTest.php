<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Environments;

/**
 * What `bin/cli environment:create` would do, held without doing it.
 *
 * No case here starts a container, and that is the same rule `D-DIS-007` was
 * written under: what fails in a build of this kind is the command that gets
 * run, and the command is readable without a docker daemon. A suite that
 * needed one would be a suite that does not run in CI, which is a suite that
 * holds nothing.
 */
final class EnvironmentsTest extends TestCase
{
    /**
     * `scenarios/readme.md` defines the environments and this list says where
     * each one comes from, so an environment added there and forgotten here is
     * an id `environment:create` answers `null` for.
     */
    #[Test]
    public function everyEnvironmentAScenarioNamesSaysWhereItComesFrom(): void
    {
        self::assertNotSame([], Environments::ids(), 'scenarios/readme.md defines no environments');
        self::assertEqualsCanonicalizing(
            Environments::ids(),
            array_keys(Environments::sources()),
            'the environments in scenarios/readme.md and the ones Environments places are not the same set',
        );
    }

    /**
     * An environment this repository does not make is one somebody has to get
     * hold of, and a refusal that does not say where from leaves them exactly
     * where the machine-bound reference left them.
     */
    #[Test]
    public function everyEnvironmentThatIsNotMadeHereSaysWhereItComesFromInstead(): void
    {
        foreach (Environments::sources() as $id => $source) {
            if ($source === Environments::MADE) {
                continue;
            }

            $reason = Environments::reason($id);
            self::assertNotSame('Nothing here makes it.', $reason, $id . ' declines without saying where it comes from');
            self::assertNotSame('', trim($reason), $id . ' declines with nothing at all');
        }
    }

    /**
     * The installation is built at the version this server answers for. A
     * repository that starts covering a new stable major and keeps making
     * environments of the old one measures itself against the wrong TYPO3, and
     * nothing about the environment would say so.
     */
    #[Test]
    public function theInstallationIsBuiltAtTheCoveredStableVersion(): void
    {
        $stable = array_values(array_filter(
            Versions::covered(),
            static fn(array $version): bool => $version['status'] === 'stable',
        ));

        self::assertCount(1, $stable, 'knowledge/versions.json covers no single stable version');
        self::assertSame($stable[0]['branch'], Environments::branch());
        self::assertStringContainsString(
            Environments::DISTRIBUTION . ':^' . $stable[0]['branch'],
            implode(' ', array_merge(...array_values(Environments::build(Environments::PROJECT)))),
        );
    }

    /**
     * `scenarios/readme.md` says an `E-SITE` is an installation whose console
     * has `language:domain:search`, and the base distribution does not require
     * the extension that carries it. A build that dropped this step would come
     * out looking finished and answer nothing the label lookup asks.
     */
    #[Test]
    public function theBuildRequiresTheExtensionsWhoseConsoleCommandsThisServerAsksFor(): void
    {
        $build = implode(' ', array_merge(...array_values(Environments::build(Environments::PROJECT))));

        self::assertNotSame([], Environments::REQUIRED);
        foreach (Environments::REQUIRED as $package) {
            self::assertStringContainsString('require ' . $package . ':', $build, $package . ' is never required');
        }
    }

    /**
     * The setup step ran non-interactively and died on a `TypeError` until
     * `--server-type` was passed: 14.3.5 reads that option's default through
     * the same fallback as its environment variable, hands the validator
     * `false` where neither is set, and only asks the question where a person
     * is there to answer it. Nothing about the option's own definition says so.
     */
    #[Test]
    public function theSetupStepPassesEveryOptionItCannotBeAskedFor(): void
    {
        $setup = [];
        foreach (Environments::build(Environments::PROJECT) as $command) {
            if (in_array('setup', $command, true)) {
                $setup = $command;
            }
        }

        self::assertNotSame([], $setup, 'nothing in the build sets the installation up');
        self::assertContains('--no-interaction', $setup);
        self::assertContains('--server-type=other', $setup);
        foreach (['--driver=', '--host=', '--dbname=', '--admin-username=', '--admin-user-password=', '--create-site='] as $option) {
            self::assertNotSame(
                [],
                array_filter($setup, static fn(string $argument): bool => str_starts_with($argument, $option)),
                $setup === [] ? '' : $option . ' is left for a person to be asked for',
            );
        }
    }

    /**
     * The site the installation is created for is the one DDEV routes to it. A
     * base URL naming another project is a frontend that answers nothing, and
     * every case that opens a page in it fails for a reason none of them is
     * about.
     */
    #[Test]
    public function theSiteIsCreatedForTheAddressDdevGivesTheProject(): void
    {
        $created = [];
        foreach (Environments::build(Environments::PROJECT) as $command) {
            foreach ($command as $argument) {
                if (str_starts_with($argument, '--create-site=')) {
                    $created[] = $argument;
                }
                if (str_starts_with($argument, '--project-name=') && in_array('config', $command, true)) {
                    self::assertSame('--project-name=' . Environments::PROJECT, $argument);
                }
            }
        }

        self::assertSame(['--create-site=https://' . Environments::PROJECT . '.ddev.site/'], $created);
    }

    /**
     * Every step is `ddev`, which is what keeps the build inside the containers
     * it declares. A `composer` or a `php` among them would run on whatever the
     * machine happens to have, and the version the environment is of would stop
     * being the one it was asked for.
     */
    #[Test]
    public function everyStepOfTheBuildRunsInTheProjectRatherThanOnTheMachine(): void
    {
        foreach (Environments::build(Environments::PROJECT) as $what => $command) {
            self::assertNotSame('', trim((string) $what), 'a step of the build says nothing about itself');
            self::assertSame('ddev', $command[0] ?? '', $what . ' runs outside the project');
        }
    }

    /**
     * The project name is global to the machine and the directory is per
     * checkout, so a worktree that made an environment and was then removed
     * leaves the name held for an approot that is gone. Measured on
     * 2026-08-02: `typo3-mcp-e-site` registered at a `.worktrees/` path DDEV
     * itself reported as `project directory missing`, and `environment:create`
     * refusing in the name of a checkout nobody could visit.
     */
    #[Test]
    public function aRegistrationWhoseCheckoutIsGoneHoldsNothingBack(): void
    {
        self::assertTrue(Environments::abandoned([
            'name' => Environments::PROJECT,
            'status' => 'project directory missing',
            'approot' => Paths::root() . '/.worktrees/a-checkout-that-was-removed/.environments/e-site',
            'url' => '',
        ]), 'an approot that is gone is read as a checkout still holding the name');

        self::assertFalse(Environments::abandoned([
            'name' => Environments::PROJECT,
            'status' => 'running',
            'approot' => Paths::root(),
            'url' => '',
        ]), 'a checkout that is there would have its environment taken over');
    }

    /**
     * `ddev stop --unlist` frees the name and is the wrong command. Stop is
     * documented as non-destructive, and the database is a volume named after
     * the project rather than after the directory — so the next build under
     * the same name attaches to it and the setup step meets the tables the
     * last installation left. `--force` does not reach that: it forces the
     * settings file alone. `delete` takes the volume with the name.
     */
    #[Test]
    public function clearingARegistrationTakesTheDatabaseThatWouldOutliveIt(): void
    {
        $discard = Environments::discard(Environments::PROJECT);

        self::assertSame('ddev', $discard[0] ?? '', 'the registration is cleared outside the project');
        self::assertSame('delete', $discard[1] ?? '', 'stop leaves the database the next build fails on');
        self::assertNotContains('--unlist', $discard, 'unlisting frees the name and keeps the database');
        self::assertContains('--omit-snapshot', $discard);
        self::assertContains(Environments::PROJECT, $discard);
    }

    /**
     * A made environment is a TYPO3 installation and its database dump, and it
     * belongs in a commit as little as `.checkouts/` does. This is the one
     * failure here that is unrecoverable rather than annoying.
     */
    #[Test]
    public function whatIsMadeHereIsNeverCommitted(): void
    {
        $ignored = preg_split('/\R/', (string) file_get_contents(Paths::root() . '/.gitignore')) ?: [];

        self::assertContains('/.environments', array_map(trim(...), $ignored));
        self::assertStringStartsWith(Environments::directory() . '/', Environments::path('E-SITE'));
    }
}
