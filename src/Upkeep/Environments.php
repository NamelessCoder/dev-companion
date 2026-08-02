<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Paths;

/**
 * The environments a scenario is run in, and which of them this checkout makes
 * for itself.
 *
 * `scenarios/readme.md` names five kinds of working directory and says what
 * each one is. What it could not say until now is where one comes from: all
 * five sat on one machine and were written down in `todo/reference/`, so a run
 * was reproducible for whoever owned that machine and for nobody else, and the
 * half of this server that reaches an installation through `ddev exec` was
 * exercised by no test at all.
 *
 * The line this draws is `D-EVI-004`. Where what a run needs from the directory
 * is a property this repository can state — a Composer installation under DDEV,
 * on a covered version, whose console answers — it is made here, below
 * `.environments/`, gitignored and re-creatable the way `.checkouts/` is. Where
 * what a run needs is a property of somebody else's repository — an extension
 * with real infrastructure at a real revision — it stays declared, because a
 * scaffold of it would be this repository grading itself against its own idea
 * of the thing.
 *
 * So each id carries how it is come by, and asking for one that is not made
 * here answers with the reason rather than with a directory.
 */
final class Environments
{
    /** Made here, by `bin/cli environment:create`. */
    public const MADE = 'made';

    /** Named in `todo/reference/`, because no scaffold produces what it plays. */
    public const DECLARED = 'declared';

    /** Made here, by a command of its own. */
    public const ELSEWHERE = 'elsewhere';

    /** Not a directory at all: a state the environment above it is put into. */
    public const STATE = 'state';

    /**
     * What DDEV registers a created project under.
     *
     * The name is global to the machine — `ddev list` is one namespace — while
     * the directory is per checkout, so two checkouts asking for the same
     * environment ask for one name. That is refused rather than taken over,
     * naming the directory that already holds it.
     */
    public const PROJECT = 'typo3-mcp-e-site';

    /**
     * The password the created installation's admin user gets.
     *
     * Written down rather than generated: the environment exists to be logged
     * into by whoever runs a scenario in it, and a secret nobody can read is a
     * step back to the machine this is replacing. It guards a throwaway site on
     * `*.ddev.site`, reachable from the machine that made it.
     */
    public const ADMIN_PASSWORD = 'Environment.Created.Here.1';

    /**
     * TYPO3's own starting site, which is what the installation is built from.
     *
     * Not a `composer.json` written here. The shape of a site installation is
     * TYPO3's to decide and it moves with the major; a copy of it in this
     * repository would be one more thing to keep true, and wrong in the way
     * that is hardest to see — plausibly out of date.
     */
    public const DISTRIBUTION = 'typo3/cms-base-distribution';

    /**
     * What the base distribution does not bring and this server asks for.
     *
     * `typo3_label_lookup` runs `language:domain:search` and the scope tools
     * run `configuration:show`; both are `EXT:lowlevel` commands, and the base
     * distribution does not require it. Without this the environment is a site
     * whose console answers "There are no commands defined in the
     * language:domain namespace", which is the one thing `scenarios/readme.md`
     * says an `E-SITE` has to have.
     *
     * @var array<int, string>
     */
    public const REQUIRED = ['typo3/cms-lowlevel'];

    /**
     * The PHP the containers run.
     *
     * Pinned rather than left to DDEV, which defaults to whatever is current
     * when it is installed. The covered stable major declares `^8.2`, so this
     * is inside the range with room above it, and the environment does not
     * change PHP version because somebody upgraded DDEV.
     */
    public const PHP = '8.4';

    /**
     * How each environment in `scenarios/readme.md` is come by.
     *
     * The ids are not repeated here — `EnvironmentsTest` holds these keys to
     * the table that defines them, so an environment added there and forgotten
     * here is a failure rather than a `null`.
     *
     * @return array<string, string>
     */
    public static function sources(): array
    {
        return [
            'E-CORE' => self::ELSEWHERE,
            'E-SITE' => self::MADE,
            'E-EXT' => self::DECLARED,
            'E-NONE' => self::MADE,
            'E-STOPPED' => self::STATE,
        ];
    }

    /**
     * Every environment id, as `scenarios/readme.md` defines them.
     *
     * @return array<int, string>
     */
    public static function ids(): array
    {
        return Scenarios::vocabulary('Id');
    }

    /**
     * Why an environment that is not made here is not, in the words somebody
     * asking for it needs — which is where to get it instead.
     */
    public static function reason(string $id): string
    {
        return match ($id) {
            'E-CORE' => 'A core checkout is `bin/cli checkouts:update`, which puts one worktree per' . "\n"
                . 'covered branch below `.checkouts/`. Those are read rather than run: nothing'
                . "\n" . 'installs their dependencies, so a case needing a booted core still needs a'
                . "\n" . 'checkout of its own.',
            'E-EXT' => 'An extension repository stays declared. What a run needs from one is its real'
                . "\n" . 'infrastructure at a real revision — complete in one, incomplete in another, a'
                . "\n" . 'major behind in the third — and a scaffold would supply this repository\'s own'
                . "\n" . 'idea of all three. `todo/reference/` names the checkouts that play it.',
            'E-STOPPED' => sprintf(
                "Not a directory of its own: it is `E-SITE` with its DDEV project down. Make\n"
                . "that one, then stop it.\n\n    ddev stop %s",
                self::PROJECT,
            ),
            default => 'Nothing here makes it.',
        };
    }

    /** Where a made environment lives, below the checkout and ignored by git. */
    public static function directory(): string
    {
        return Paths::root() . '/.environments';
    }

    /** Where one environment lives, whether or not it is there yet. */
    public static function path(string $id): string
    {
        return self::directory() . '/' . strtolower($id);
    }

    /**
     * The branch a made installation is built on: the covered version that is
     * stable.
     *
     * Read off `knowledge/versions.json` rather than written down, because the
     * environment a run validates in has to be the version this server answers
     * for. A repository that starts covering a new stable and keeps making
     * installations of the old one measures itself in the wrong place.
     */
    public static function branch(): string
    {
        foreach (Versions::covered() as $version) {
            if ($version['status'] === 'stable') {
                return $version['branch'];
            }
        }

        throw new \RuntimeException('knowledge/versions.json covers no stable version to build an installation of');
    }

    /**
     * Every DDEV project this machine knows, by name.
     *
     * `ddev list` is the one place a project registered somewhere else is
     * visible, and both commands here need it: one to refuse a name that is
     * taken, the other to say whether the environment it made is up.
     *
     * @return array<string, array{name: string, status: string, approot: string, url: string}>
     */
    public static function projects(): array
    {
        [$exitCode, $said] = self::run(['ddev', 'list', '--json-output']);
        if ($exitCode !== 0) {
            return [];
        }

        // `--json-output` is one JSON document per line and more than one of
        // them: the human table is a `msg` on the error stream, the projects are
        // a `raw` on the standard one, and both arrive here as one string.
        // Decoding from the first brace therefore reads whichever came first
        // and fails on the rest, so the lines are taken one at a time.
        $listed = null;
        foreach (preg_split('/\R/', $said) ?: [] as $line) {
            $decoded = json_decode(trim($line), true);
            if (is_array($decoded) && is_array($decoded['raw'] ?? null)) {
                $listed = $decoded['raw'];
                break;
            }
        }

        if ($listed === null) {
            return [];
        }

        $projects = [];
        foreach ($listed as $project) {
            if (!is_array($project) || !is_string($project['name'] ?? null)) {
                continue;
            }
            $projects[$project['name']] = [
                'name' => $project['name'],
                'status' => (string) ($project['status'] ?? ''),
                'approot' => (string) ($project['approot'] ?? ''),
                'url' => (string) ($project['primary_url'] ?? ''),
            ];
        }

        return $projects;
    }

    /**
     * Whether a registration points at a checkout that is no longer there.
     *
     * The project name is global to this machine and the directory is per
     * checkout, so a worktree that made an environment and was then removed
     * leaves the name held by an approot DDEV itself reports as `project
     * directory missing`. Nothing can reach what it names: the code, the
     * settings file and the DDEV config went with the directory, and what is
     * left is a name and a database volume. An `rm -rf .environments` in this
     * checkout leaves the same thing behind, which is why the question is
     * whether the approot is there rather than whose it was.
     *
     * @param array{name: string, status: string, approot: string, url: string} $project
     */
    public static function abandoned(array $project): bool
    {
        return !is_dir($project['approot']);
    }

    /**
     * What clears a registration nothing can reach, and the database with it.
     *
     * `ddev stop --unlist` is the smaller command and the wrong one. Stop is
     * documented as non-destructive and leaves the database, which is a volume
     * named after the project rather than after the directory — so the name is
     * freed, the next build registers it again, attaches to the same volume,
     * and the setup step meets the tables the last installation left. `delete`
     * takes both, and takes them where the directory is already gone: measured
     * on 2026-08-02 against a registration whose approot had been removed, on
     * DDEV 1.25.1.
     *
     * @return array<int, string>
     */
    public static function discard(string $project): array
    {
        return ['ddev', 'delete', '--omit-snapshot', '-y', $project];
    }

    /** Whether the tool a made environment is built with is on this machine. */
    public static function ddev(): bool
    {
        [$exitCode] = self::run(['ddev', '--version']);

        return $exitCode === 0;
    }

    /**
     * One step of a build, with both its streams as one string.
     *
     * `Checkouts::run` is the same shape and is not reused, for one reason:
     * stdin. That one leaves it inherited, which is right for the git it was
     * named for and wrong here — `ddev` and the TYPO3 console both read stdin
     * where they think a person is there, and a step that blocks on a terminal
     * nobody is watching is a build that never returns. `R-DIS-018` is the same
     * failure from the server's side, found by a run rather than by a test.
     *
     * @param array<int, string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function run(array $command, ?string $cwd = null): array
    {
        $process = proc_open(
            $command,
            [0 => ['file', '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $cwd,
        );
        if (!is_resource($process)) {
            return [1, ''];
        }

        $output = (string) stream_get_contents($pipes[1]) . (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $output];
    }

    /**
     * The build, as the commands in the order they are run.
     *
     * Every one of them is idempotent or forced, so a build that failed
     * halfway is finished by running this again rather than by deleting the
     * directory. That is not a convenience: a `composer create-project` of a
     * TYPO3 is minutes and a hundred packages, and a step that has to start
     * over is a step nobody repeats.
     *
     * The setup step is where that stops holding, and `--force` is not the
     * exception it reads as. It forces the settings file and nothing else —
     * `prepareSystemSettings()` is its only use in 14.3 — while the database
     * is guarded by a validator that refuses any table at all, on the
     * non-interactive path as much as the asked one. A build that meets a
     * populated database is finished by `discard()` and never by running
     * again.
     *
     * @return array<string, array<int, string>>
     */
    public static function build(string $project): array
    {
        $branch = self::branch();
        $steps = [
            'A DDEV project of the type TYPO3, serving from public/' => [
                'ddev', 'config', '--auto',
                '--project-name=' . $project,
                '--project-type=typo3',
                '--docroot=public',
                '--php-version=' . self::PHP,
                '--disable-upload-dirs-warning',
            ],
            'The containers, and the database with them' => ['ddev', 'start', '-y'],
            'TYPO3 ' . $branch . ', from its own base distribution' => [
                'ddev', 'composer', 'create-project', self::DISTRIBUTION . ':^' . $branch,
            ],
        ];

        foreach (self::REQUIRED as $package) {
            $steps['The console commands this server asks for, from ' . $package] = [
                'ddev', 'composer', 'require', $package . ':^' . $branch, '--no-interaction',
            ];
        }

        $steps['The installation itself: database, admin user, site configuration'] = [
            'ddev', 'exec', 'vendor/bin/typo3', 'setup',
            '--no-interaction',
            // The settings file and nothing else. This is what lets a
            // half-built environment be finished rather than stopping on the
            // file the first attempt wrote, and it does not reach the database
            // an earlier installation populated.
            '--force',
            // DDEV's own database service, at the names it gives it.
            '--driver=mysqli', '--host=db', '--port=3306',
            '--dbname=db', '--username=db', '--password=db',
            '--admin-username=admin',
            '--admin-user-password=' . self::ADMIN_PASSWORD,
            '--admin-email=admin@example.com',
            '--project-name=TYPO3 MCP scenario environment',
            // Not optional, whatever the option definition says. Its default is
            // read through the same fallback as the environment variable, so
            // with --no-interaction and nothing passed the validator is handed
            // `false` and 14.3.5 dies on the type — measured on 2026-08-02.
            // `other` is the answer for the nginx DDEV runs anyway.
            '--server-type=other',
            '--create-site=https://' . $project . '.ddev.site/',
        ];
        $steps['The extensions set up against that database'] = [
            'ddev', 'exec', 'vendor/bin/typo3', 'extension:setup',
        ];

        return $steps;
    }
}
