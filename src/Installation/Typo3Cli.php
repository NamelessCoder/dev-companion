<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

use Typo3CmsMcp\Process\CommandRunner;
use Typo3CmsMcp\Process\SystemRunner;

/**
 * Runs the TYPO3 console of the discovered installation and hands back what it
 * printed.
 *
 * Asking TYPO3 beats reimplementing it. The registries this server is asked
 * about — which labels exist, which modules are registered — are assembled at
 * runtime from the active packages, and the console commands that expose them
 * (language:domain:list, debug:backend:modules, fluid:namespaces) already know
 * things no file scan can work out: which packages are active rather than
 * merely installed, and which language files an installation overrides through
 * resourceOverrides.
 *
 * It runs as a subprocess, never in this process. Loading the installation's
 * autoloader here would put two Composer autoloaders and two sets of Symfony
 * and PSR classes under the same class names into one process, and a platform
 * check failing would take the whole MCP session down instead of one answer.
 * A subprocess brings its own interpreter and its own autoloader, and fails as
 * an exit code.
 */
final class Typo3Cli
{
    /** Invoked through DDEV, which supplies the PHP version the project declares. */
    public const VIA_DDEV = 'ddev';

    /** Invoked directly with an interpreter on this machine. */
    public const VIA_PHP = 'php';

    /** Invoked exactly as the caller said to invoke it. */
    public const VIA_OVERRIDE = 'override';

    /** The command that reaches the console, for the layouts autodiscovery cannot. */
    public const CONSOLE_VARIABLE = 'TYPO3_MCP_CONSOLE';

    /** A TYPO3 bootstrap can hang on a broken configuration; an MCP session must not. */
    private const TIMEOUT_SECONDS = 90;

    /**
     * What leaves this process, and the seam a unit test takes instead.
     *
     * Static because everything on this class is, and settable because
     * `R-COD-003` says a unit test stubs what it needs from outside rather
     * than starting it. `forget()` deliberately leaves it alone: that one
     * drops the memoized resolution, which a test does between installations
     * and which would otherwise take the stub with it.
     */
    private static ?CommandRunner $runner = null;

    /** What a test hands in, so nothing it drives has to exist on the machine. */
    public static function useRunner(?CommandRunner $runner): void
    {
        self::$runner = $runner;
    }

    /**
     * Where DDEV mounts the project inside its web container.
     *
     * The invocation has to be absolute. `ddev exec` runs in the container's
     * configured working directory, which a project may point at its docroot,
     * and a console named relative to the project root is then not there —
     * `D-DIS-002` measured exit 127 for exactly that. `$DDEV_APPROOT` says the
     * same thing without naming a directory, and it is not the safer form: DDEV
     * sets that variable in the web container only from v1.24.5, and sets it to
     * this literal string. Below that version it expands to nothing and the
     * console is looked for at /bin/typo3, which fails where the relative path
     * worked.
     */
    private const DDEV_APPROOT = '/var/www/html';

    /** @var array{command: array<int, string>, via: string, php: string}|null|false */
    private static array|false|null $resolved = false;

    private static string $reason = '';

    private static string $caveat = '';

    /**
     * How this installation's console can be invoked, or null when it cannot.
     *
     * @return array{command: array<int, string>, via: string, php: string}|null
     */
    public static function resolve(): ?array
    {
        // Only a success with nothing limiting it is remembered. A failure and
        // a caveated success are both retried on the next call, because the
        // usual reason for either is a DDEV project that is not running yet —
        // and the caller who reads that and starts it has to be able to ask
        // again in the same session.
        if (is_array(self::$resolved)) {
            return self::$resolved;
        }

        self::$resolved = null;
        self::$reason = '';
        self::$caveat = '';

        $instance = Instance::describe();
        if ($instance === null) {
            self::$reason = 'no TYPO3 installation was found to run it in';

            return null;
        }

        $root = $instance['root'];

        [$invocation, $overrideReason] = self::viaOverride();
        if ($invocation !== null) {
            return self::$resolved = $invocation;
        }
        if ($overrideReason !== '') {
            // A stated command that cannot be used is not quietly replaced by a
            // discovered one. The caller would then be answered from something
            // other than what it named, and never learn its setting was ignored.
            self::$reason = $overrideReason;

            return null;
        }

        $binary = self::consoleBinary($root);
        if ($binary === null) {
            // Naming every probed path rather than the two defaults: a console
            // that sits somewhere else is the likely case here, and a reason
            // that lists where this looked is one the caller can act on.
            self::$reason = sprintf(
                '%s has no TYPO3 console — none of %s exists%s',
                $root,
                implode(', ', self::consoleCandidates($root)),
                self::unreachableBinDirectory($root)
            );

            return null;
        }

        [$invocation, $ddevReason] = self::viaDdev($root, $binary);
        if ($invocation !== null) {
            return self::$resolved = $invocation;
        }

        [$invocation, $phpReason] = self::viaPhp($root, $binary);

        // A project that ships a DDEV setup is meant to run there, so its
        // reason wins over the direct one: "start the project" is something the
        // caller can act on, "install another PHP" is not.
        if ($invocation === null) {
            self::$reason = $ddevReason !== '' ? $ddevReason : $phpReason;

            return null;
        }

        // An interpreter on this machine reaches the console of a project that
        // is meant to run elsewhere. That is not "unreachable", and it is not
        // "reachable" either — but what it costs is not the list of tools this
        // sentence used to name. Driven against `.environments/e-site-14.3`
        // with its DDEV project stopped on 2026-08-04, all seven
        // installation-backed tools answered, byte for byte what the same calls
        // answered through `ddev exec` with the project up, on a host PHP 8.3
        // that had no database driver compiled in at all. Booting TYPO3 is not
        // what a stopped project takes away; the runtime the project declares
        // is, and which answer meets that limit is a property of the
        // installation rather than of the tool asked.
        // Naming the way out is half of it. The caller that acts on it holds an
        // answer given from the weaker source, and nothing revises that answer
        // where it stands — so the step that ends the state is the second call,
        // and a caveat that names only the first leaves the session working
        // from what it was told to stop trusting. `R-DIS-010`.
        self::$caveat = $ddevReason === '' ? '' : $ddevReason
            . '. Until then the console runs on an interpreter of this machine rather than in the runtime the '
            . 'project declares. What TYPO3 assembles from its own files answers as it would there; what needs '
            . 'the services that runtime brings, its database first of all, may not. An answer that needs one '
            . 'says so rather than coming back thin. Ask again once it is up: this answer stands as it was '
            . 'given, and the runtime the project declares reaches only the calls that come after the start';

        // A caveated resolution is the weaker of two and the stronger one
        // arrives during the session, so it is not remembered — `R-DIS-009`,
        // one state over from the negative it was written for. Measured in one
        // process against `.environments/e-site-13.4` stopped on 2026-08-04:
        // host PHP 8.3 satisfies the 8.2.0 that installation pins, so the
        // resolution succeeded through it, was remembered, and `ddev start`
        // changed nothing until the process ended. What it costs is one
        // `ddev describe -j` per call while the project is stopped, 0.25s
        // there, which is what a failing resolution already pays on that path.
        if (self::$caveat !== '') {
            return $invocation;
        }

        return self::$resolved = $invocation;
    }

    public static function isAvailable(): bool
    {
        return self::resolve() !== null;
    }

    /**
     * Drops the memoized resolution. The installation is discovered once per
     * process in normal use, so this exists for tests that move between
     * installations within one.
     */
    public static function forget(): void
    {
        self::$resolved = false;
        self::$reason = '';
        self::$caveat = '';
    }

    /** Why the console cannot be invoked. Empty once it can. */
    public static function reason(): string
    {
        self::resolve();

        return self::$reason;
    }

    /**
     * What limits the console that was found. Empty when nothing does.
     *
     * Reachable is not one state: a project whose containers are stopped is
     * answered by an interpreter of this machine, which is not the runtime the
     * project declares. Reporting that as reachable is what let five tools be
     * presented as usable without a word about where the answer came from.
     *
     * What it says is what the boot cannot reach, never which lookups are lost.
     * The list this used to carry was measured wrong: with
     * `.environments/e-site-14.3` stopped on 2026-08-04 every installation-backed
     * tool answered, and only `typo3_schema_lookup` asks the connection for
     * anything at all — the platform, which `pdo_sqlite` supplies with no
     * server and `pdo_mysql` fetches by connecting unless `serverVersion` is
     * configured (`D-DIS-012`). So which answer a stopped project costs depends
     * on the installation, and a sentence naming tools is wrong somewhere.
     */
    public static function caveat(): string
    {
        self::resolve();

        return self::$caveat;
    }

    /**
     * What a failure means, where the message alone does not say it.
     *
     * A console that starts and then fails on a missing table has a specific
     * remedy, and the caller cannot see it in the stack trace: the code is
     * installed, the database behind it is not. Empty for everything else —
     * guessing at the rest would bury the one diagnosis worth having.
     */
    public static function diagnose(string $error): string
    {
        $needles = ['doesn\'t exist', 'base table or view not found', '42s02', 'no such table'];
        $haystack = mb_strtolower($error);
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return 'The console runs, so the code is installed — the database it connects to has no TYPO3 '
                    . 'schema yet. Import the dump, or run the setup, before asking again.';
            }
        }

        return '';
    }

    /**
     * Runs a console command and returns what it printed.
     *
     * @param array<int, string> $arguments
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public static function run(array $arguments): array
    {
        $invocation = self::resolve();
        if ($invocation === null) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => self::reason()];
        }

        // Non-interactive and unstyled, so the output is the data and not a
        // terminal painting of it.
        $arguments = array_merge($arguments, ['--no-interaction', '--no-ansi']);

        return self::execute(
            array_merge($invocation['command'], self::pastTheShell($invocation['via'], $arguments)),
            Instance::root() ?? getcwd() ?: '.',
        );
    }

    /**
     * Arguments that survive the one shell on the way to the console.
     *
     * `proc_open` is given an array and runs no shell, so nothing here would
     * need quoting — except that `ddev exec` joins its arguments back into a
     * line and hands that to bash inside the container. An argument carrying a
     * character bash acts on is then bash's rather than the console's, and the
     * command dies before TYPO3 is reached. Measured against DDEV v1.25.1 on
     * 2026-08-02: `--regex=/(save)/i` — the argument `typo3_label_lookup`
     * builds for `language:domain:search` — comes back exit 2 with "syntax
     * error near unexpected token `('", every time, in every DDEV project. So
     * that tool never once answered from a booted installation there; it fell
     * back to the package files and said `answeredBy: "packages"`, which reads
     * as a console that could not be reached rather than as a quoting fault.
     *
     * Only the DDEV transport is quoted. The direct one reaches the console
     * through `proc_open` with no shell between, where a quoted argument would
     * arrive with its quotes. A stated one is left alone for a reason rather
     * than an oversight: what `TYPO3_MCP_CONSOLE` names may put a shell in the
     * way or may not — `docker compose exec` does not, another wrapper might —
     * and this cannot tell. Quoting it would break every stated command that
     * needs no quoting, to fix the ones that do; the caller who names a
     * transport is the one who can quote for it.
     *
     * @param array<int, string> $arguments
     * @return array<int, string>
     */
    private static function pastTheShell(string $via, array $arguments): array
    {
        return $via === self::VIA_DDEV ? array_map(escapeshellarg(...), $arguments) : $arguments;
    }

    /**
     * Runs PHP inside the installation and returns what it printed.
     *
     * The console answers what a command exists for. Everything else an
     * installation knows — its icon registry, its TCA — has no command, and
     * reading it means booting TYPO3 and asking the container. That happens
     * here, in a subprocess, for the same reason every console call does: the
     * installation's autoloader, its PHP version and its extensions stay on the
     * other side of a process boundary.
     *
     * The code is delivered base64-encoded inside an `eval`, and that is not
     * decoration. `ddev exec` joins its arguments and hands the line to bash,
     * so a payload travels through one shell whose quoting nobody controls
     * from here; an encoded one carries no character that shell could act on.
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public static function php(string $code): array
    {
        $invocation = self::resolve();
        if ($invocation === null) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => self::reason()];
        }

        $interpreter = self::interpreter($invocation);
        if ($interpreter === null) {
            return [
                'ok' => false,
                'exitCode' => -1,
                'output' => '',
                'error' => sprintf(
                    '%s names "%s", and no interpreter can be derived from it — only what a console command '
                        . 'answers is available here',
                    self::CONSOLE_VARIABLE,
                    implode(' ', $invocation['command'])
                ),
            ];
        }

        $command = array_merge($interpreter, ['-r', sprintf('eval(base64_decode("%s"));', base64_encode($code))]);

        return self::execute($command, Instance::root() ?? getcwd() ?: '.');
    }

    /**
     * The same way in, pointed at PHP itself rather than at the console.
     *
     * A stated console is a transport plus a binary — `ddev exec .build/bin/typo3`,
     * `docker compose exec web bin/typo3` — and the transport is what this
     * server could never have worked out on its own. So the transport is kept
     * and the binary is exchanged, which is the opposite of answering from
     * somewhere the caller did not name: it is the same machine, the same
     * container, one program along.
     *
     * @param array{command: array<int, string>, via: string, php: string} $invocation
     * @return array<int, string>|null
     */
    private static function interpreter(array $invocation): ?array
    {
        if ($invocation['via'] === self::VIA_PHP) {
            return [$invocation['command'][0]];
        }
        if ($invocation['via'] === self::VIA_DDEV) {
            return ['ddev', 'exec', '--', 'php'];
        }

        $command = $invocation['command'];
        // Already an interpreter in front: the console argument simply goes.
        if (str_starts_with(basename($command[0]), 'php')) {
            return [$command[0]];
        }
        $last = array_key_last($command);
        if ($last === 0 || !str_contains(basename((string) $command[$last]), 'typo3')) {
            return null;
        }
        $command[$last] = 'php';

        return $command;
    }

    /**
     * Runs a command that speaks JSON and returns what it decoded.
     *
     * Some commands print a SymfonyStyle title before the payload and some
     * answer with a bare scalar, so the payload is looked for rather than
     * assumed to start at the first byte or at a brace.
     *
     * What was printed is handed back whether or not it decoded, because the
     * exit code cannot tell a caller which of two things happened when no
     * payload arrived. A console that exits 0 without one may have said "there
     * is nothing" in its own words, or may have said nothing at all — and only
     * the caller knows what its command prints in the first case.
     *
     * @param array<int, string> $arguments
     * @return array{ok: bool, data: mixed, error: string, exitCode: int, output: string}
     */
    public static function json(array $arguments): array
    {
        $result = self::run($arguments);
        $decoded = self::decode($result['output']);

        if ($decoded === null) {
            $error = $result['error'] !== '' ? $result['error'] : trim($result['output']);

            return [
                'ok' => false,
                'data' => null,
                'error' => $error !== '' ? $error : 'the console answered with something other than JSON',
                'exitCode' => $result['exitCode'],
                'output' => $result['output'],
            ];
        }

        return [
            'ok' => $result['ok'],
            'data' => $decoded,
            'error' => $result['error'],
            'exitCode' => $result['exitCode'],
            'output' => $result['output'],
        ];
    }

    /** Returns the decoded payload, or null when the output carries none. */
    private static function decode(string $output): mixed
    {
        $output = trim($output);
        if ($output === '') {
            return null;
        }

        foreach ([$output, self::fromFirstStructure($output)] as $candidate) {
            if ($candidate === null) {
                continue;
            }
            $decoded = json_decode($candidate, true);
            if ($decoded !== null || $candidate === 'null') {
                return $decoded;
            }
        }

        return null;
    }

    private static function fromFirstStructure(string $output): ?string
    {
        $offsets = array_filter([strpos($output, '{'), strpos($output, '[')], static fn($o): bool => $o !== false);

        return $offsets === [] ? null : substr($output, min($offsets));
    }

    /**
     * The command the caller stated, before anything is worked out.
     *
     * Autodiscovery is a chain — a binary at a known path, then DDEV, then an
     * interpreter that satisfies the platform — and every link is something
     * about a machine this server does not control. When one breaks there is
     * nothing left to try, and five tools go quiet over a layout its owner
     * could have described in a sentence: TYPO3_MCP_CONSOLE="ddev exec
     * .build/bin/typo3". Lando, a compose stack, a container this server has
     * never heard of are then all one setting rather than a feature request.
     *
     * The command is run as given, never through a shell, so quoting a
     * multi-word argument is all the syntax there is.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaOverride(): array
    {
        $configured = getenv(self::CONSOLE_VARIABLE);
        if (!is_string($configured) || trim($configured) === '') {
            return [null, ''];
        }

        $command = self::tokenize($configured);
        if ($command === []) {
            return [null, sprintf('%s is set but empty', self::CONSOLE_VARIABLE)];
        }

        // Only that the program exists is checked here, not that it answers:
        // running it to find out would put a subprocess in front of every
        // lookup, and a wrong argument surfaces as the failing call it is.
        $program = $command[0];
        $found = str_contains($program, '/') ? is_file($program) : self::locateBinary($program) !== null;
        if (!$found) {
            return [null, sprintf(
                '%s starts with "%s", which is not a program on this machine',
                self::CONSOLE_VARIABLE,
                $program
            )];
        }

        return [['command' => $command, 'via' => self::VIA_OVERRIDE, 'php' => ''], ''];
    }

    /**
     * Splits a command line into arguments, honouring quotes. proc_open is
     * given an array and runs no shell, so nothing else in a shell's syntax
     * would do anything here.
     *
     * @return array<int, string>
     */
    private static function tokenize(string $commandLine): array
    {
        preg_match_all('/"([^"]*)"|\'([^\']*)\'|(\S+)/', trim($commandLine), $matches, PREG_SET_ORDER);

        $arguments = [];
        foreach ($matches as $match) {
            // Whichever alternative matched; the groups after it are not set.
            $bare = $match[3] ?? '';
            $single = $match[2] ?? '';
            $arguments[] = $bare !== '' ? $bare : ($single !== '' ? $single : ($match[1] ?? ''));
        }

        return $arguments;
    }

    /**
     * DDEV first: the project declares the PHP version it needs and DDEV has
     * it, while this machine may not. A paused or stopped project is reported
     * rather than started — an agent asking about a label must not bring
     * containers up as a side effect.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaDdev(string $root, string $binary): array
    {
        if (!is_file($root . '/.ddev/config.yaml')) {
            return [null, ''];
        }
        // The server may itself have been started with `ddev exec`. There is
        // deliberately no nested DDEV binary in the web container; the direct
        // PHP process is already the project's declared runtime and reaches its
        // services. Treating the missing host-side binary as a stopped project
        // adds a false database caveat to an otherwise fully ready console.
        if (filter_var(getenv('IS_DDEV_PROJECT'), FILTER_VALIDATE_BOOL)) {
            return [null, ''];
        }
        if (self::locateBinary('ddev') === null) {
            return [null, 'the installation is a DDEV project but ddev is not installed on this machine'];
        }

        $described = self::execute(['ddev', 'describe', '-j'], $root);
        $status = '';
        $php = '';
        if ($described['ok']) {
            $decoded = json_decode($described['output'], true);
            $raw = is_array($decoded) ? ($decoded['raw'] ?? []) : [];
            $status = is_array($raw) ? (string) ($raw['status'] ?? '') : '';
            $php = is_array($raw) ? (string) ($raw['php_version'] ?? '') : '';
        }

        if ($status !== 'running') {
            return [null, sprintf(
                'the DDEV project is %s — start it with "ddev start" in %s to answer from the installation',
                $status === '' ? 'not reachable' : $status,
                $root
            )];
        }

        return [[
            'command' => ['ddev', 'exec', '--', self::DDEV_APPROOT . '/' . $binary],
            'via' => self::VIA_DDEV,
            'php' => $php,
        ], ''];
    }

    /**
     * Directly, with an interpreter that satisfies what the installation
     * declares. Composer pins the platform, so running the console with a PHP
     * below it aborts in platform_check.php before TYPO3 is even reached.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaPhp(string $root, string $binary): array
    {
        $required = self::requiredPhpVersion($root);

        foreach (self::interpreterCandidates($required) as $interpreter) {
            $version = self::interpreterVersion($interpreter);
            if ($version === null) {
                continue;
            }
            if ($required !== null && version_compare($version, $required, '<')) {
                continue;
            }

            return [['command' => [$interpreter, $root . '/' . $binary], 'via' => self::VIA_PHP, 'php' => $version], ''];
        }

        return [null, $required === null
            ? 'no PHP interpreter on this machine could run the console'
            : sprintf(
                'the installation requires PHP %s and no interpreter on this machine provides it (running %s)',
                $required,
                PHP_VERSION
            )];
    }

    private static function consoleBinary(string $root): ?string
    {
        foreach (self::consoleCandidates($root) as $candidate) {
            if (is_file($root . '/' . $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Where the console may sit, what the installation declares first.
     *
     * Composer's default bin-dir is vendor/bin, so an installation that
     * declares nothing is probed exactly as before. One that declares
     * `"bin-dir": ".build/bin"` — the layout the TYPO3 extension testing setup
     * produces, and with it a large share of the published extensions — has its
     * console there and nowhere else, and probing only the default meant every
     * question the installation alone can answer came back unanswered in a
     * checkout whose console was one directory away.
     *
     * @return array<int, string>
     */
    private static function consoleCandidates(string $root): array
    {
        $candidates = [];
        $declared = self::binDirectory($root);
        if ($declared !== null) {
            $candidates[] = $declared . '/typo3';
        }

        return array_values(array_unique(array_merge($candidates, ['vendor/bin/typo3', 'bin/typo3'])));
    }

    /**
     * The bin directory the installation declares, relative to its root.
     *
     * Composer expands `$vendor-dir` inside bin-dir, so that spelling is
     * expanded here too. It also accepts an absolute bin-dir and puts the
     * binaries there — verified with Composer 2.9.5 against a project
     * declaring one — so an absolute declaration below the root is expressed
     * relative to it rather than dropped: it is the same directory the
     * relative spelling names, and the console is reached the same way. One
     * outside the root has no relative form, and the invocation has to have
     * one — inside a DDEV container the host path would not exist anyway.
     *
     * `Installer` asks the same question about a different binary: where this
     * server's own entrypoint sits once a project has required it. One rule,
     * because a project that moved its bin directory moved both.
     */
    public static function binDirectory(string $root): ?string
    {
        $binDir = self::declaredBinDirectory($root);

        return $binDir === null || !str_starts_with($binDir, '/')
            ? $binDir
            : self::belowRoot($root, $binDir);
    }

    /**
     * What to add to "no console was found" when the installation declares a
     * bin directory outside its root. Without it the caller reads a list of
     * two default paths while its own composer.json names a third, and nothing
     * says the declaration was read at all.
     */
    private static function unreachableBinDirectory(string $root): string
    {
        $declared = self::declaredBinDirectory($root);
        if ($declared === null || !str_starts_with($declared, '/') || self::binDirectory($root) !== null) {
            return '';
        }

        return sprintf(
            '. The declared bin-dir %s is outside the installation, so a console there cannot be invoked from it — set %s to the command that reaches it',
            $declared,
            self::CONSOLE_VARIABLE
        );
    }

    /**
     * The bin directory as the manifest spells it, absolute declaration and
     * all, so a console that cannot be reached can still be named.
     */
    private static function declaredBinDirectory(string $root): ?string
    {
        $config = self::manifest($root)['config'] ?? [];
        if (!is_array($config)) {
            return null;
        }

        $binDir = $config['bin-dir'] ?? null;
        if (!is_string($binDir) || trim($binDir) === '') {
            return null;
        }

        $vendorDir = $config['vendor-dir'] ?? null;
        $binDir = str_replace(
            '$vendor-dir',
            is_string($vendorDir) && trim($vendorDir) !== '' ? trim($vendorDir) : 'vendor',
            trim($binDir)
        );
        $binDir = rtrim($binDir, '/');

        return $binDir === '' ? null : $binDir;
    }

    /**
     * The installation's autoloader, relative to its root.
     *
     * Relative because the two sides of DDEV do not share absolute paths: the
     * subprocess starts in the root, and inside the container that same root is
     * /var/www/html. An absolute vendor directory is treated like an absolute
     * bin directory: expressed relative to the root where it is below it, and
     * left at the default where no relative form exists.
     */
    public static function autoloader(string $root): string
    {
        $config = self::manifest($root)['config'] ?? [];
        $vendorDir = is_array($config) ? ($config['vendor-dir'] ?? null) : null;
        $vendorDir = is_string($vendorDir) ? rtrim(trim($vendorDir), '/') : '';
        if (str_starts_with($vendorDir, '/')) {
            $vendorDir = (string) self::belowRoot($root, $vendorDir);
        }

        return ($vendorDir === '' ? 'vendor' : $vendorDir) . '/autoload.php';
    }

    /**
     * An absolute path as the root sees it, or null when it is not below the
     * root at all. Both sides are resolved, because a declaration is written by
     * hand while the root has been through realpath().
     */
    private static function belowRoot(string $root, string $path): ?string
    {
        $root = realpath($root) ?: rtrim($root, '/');
        $absolute = realpath($path) ?: rtrim($path, '/');

        return str_starts_with($absolute, $root . '/') ? substr($absolute, strlen($root) + 1) : null;
    }

    /**
     * The lowest PHP the installation accepts: the pinned Composer platform
     * first, then what the install itself was resolved to, then the root's own
     * requirement.
     *
     * The manifest alone was not enough, and the base distribution is why. Its
     * `composer.json` carries `"platform": {}` and no `require.php` at all —
     * the PHP bound lives in the packages it pulls in, not in the file that
     * pulls them — so this read `null`, every interpreter satisfied it, and
     * host PHP 8.3 was accepted for an installation whose dependencies require
     * 8.5.0. `typo3_server_scope` then reported that console reachable while
     * every boot through it aborted in Composer's own platform check.
     *
     * That check is where the bound actually is. `composer install` writes the
     * lower bound over every non-dev package it installed into
     * `vendor/composer/platform_check.php`, and the autoloader includes it, so
     * it is not a second opinion about whether the console runs — it is the
     * code that stops it. Read against Composer 2.9.5 on 2026-08-04, both in
     * its own `AutoloadGenerator::getPlatformCheck()` and in the four
     * installations under `.environments/`: the expression is
     * `PHP_VERSION_ID >= 80500`, an integer of major*10000 + minor*100 + patch,
     * with `>` instead of `>=` where the bound is exclusive — which is why an
     * exclusive one is read as the next id up rather than the same one.
     *
     * It goes above `require.php` and below the pin, which is what each of the
     * three knows. `config.platform.php` is what Composer resolves against and
     * will resolve against again, and the generated check cannot be higher than
     * it in an install that check came out of. `require.php` is the root
     * package's own bound and says nothing about the twenty-five packages it
     * requires, so it is the weakest of the three and only answers where
     * nothing has been installed to read.
     */
    private static function requiredPhpVersion(string $root): ?string
    {
        $decoded = self::manifest($root);

        $platform = $decoded['config']['platform']['php'] ?? null;
        if (is_string($platform) && $platform !== '') {
            return $platform;
        }

        $installed = self::installedPhpBound($root);
        if ($installed !== null) {
            return $installed;
        }

        $required = $decoded['require']['php'] ?? null;
        if (!is_string($required) || preg_match('/(\d+)\.(\d+)/', $required, $matches) !== 1) {
            return null;
        }

        return $matches[1] . '.' . $matches[2] . '.0';
    }

    /**
     * The bound Composer wrote into the install, or null where there is none to
     * read.
     *
     * Absent is a real answer and not a failure: Composer leaves the file out
     * when nothing requires a PHP version, and deletes it where `platform-check`
     * is off or the platform requirements were ignored. In all of those the
     * console will not abort on a version, so nothing here should say it might.
     *
     * The vendor directory is asked for the same way the autoloader is, because
     * an installation that moved one moved the other — the file sits beside it.
     */
    private static function installedPhpBound(string $root): ?string
    {
        $path = $root . '/' . dirname(self::autoloader($root)) . '/composer/platform_check.php';
        if (!is_file($path)) {
            return null;
        }

        $generated = (string) file_get_contents($path);
        if (preg_match('/PHP_VERSION_ID\s*(>=|>)\s*(\d+)/', $generated, $matches) !== 1) {
            return null;
        }

        // PHP_VERSION_ID is an integer, so "greater than 80500" is "at least
        // 80501" exactly rather than approximately, and the operator does not
        // have to be carried any further than this line.
        $id = (int) $matches[2] + ($matches[1] === '>' ? 1 : 0);

        return $id <= 0 ? null : sprintf('%d.%d.%d', intdiv($id, 10000), intdiv($id % 10000, 100), $id % 100);
    }

    /**
     * What the installation declares about itself. Its manifest answers both
     * where the console is and which PHP may run it, so it is read in one place.
     *
     * @return array<string, mixed>
     */
    private static function manifest(string $root): array
    {
        $path = $root . '/composer.json';
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<int, string> */
    private static function interpreterCandidates(?string $required): array
    {
        $candidates = [PHP_BINARY];

        // A machine that carries several interpreters names them by version.
        if ($required !== null && preg_match('/^(\d+)\.(\d+)/', $required, $matches) === 1) {
            $major = (int) $matches[1];
            for ($minor = (int) $matches[2]; $minor <= (int) $matches[2] + 4; ++$minor) {
                $named = self::locateBinary(sprintf('php%d.%d', $major, $minor));
                if ($named !== null) {
                    $candidates[] = $named;
                }
            }
        }

        return array_values(array_unique($candidates));
    }

    private static function interpreterVersion(string $interpreter): ?string
    {
        $result = self::execute([$interpreter, '-r', 'echo PHP_VERSION;'], getcwd() ?: '.');

        return $result['ok'] && $result['output'] !== '' ? trim($result['output']) : null;
    }

    /** Where an executable of this name is, asked of the same seam as a run. */
    private static function locateBinary(string $name): ?string
    {
        return self::runner()->locate($name);
    }

    /** The real one, unless a test put something else in its place. */
    private static function runner(): CommandRunner
    {
        return self::$runner ?? new SystemRunner();
    }

    /**
     * @param array<int, string> $command
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    private static function execute(array $command, string $workingDirectory): array
    {
        $result = self::runner()->run($command, $workingDirectory, self::TIMEOUT_SECONDS);

        // The error is trimmed here rather than in the runner: what a caller of
        // this class does with it is put into an answer a client reads, and a
        // trailing newline in one of those is a blank line in a tool result.
        $result['error'] = trim($result['error']);

        return $result;
    }
}
