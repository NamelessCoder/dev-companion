<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

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
        // Only a success is remembered. A failure is retried on the next call,
        // because the usual reason is a DDEV project that is not running yet —
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
        // is meant to run elsewhere. It answers what can be answered from
        // files, and fails on everything that boots TYPO3 against its database,
        // because the database is in the containers that are not running. That
        // is not "unreachable", and it is not "reachable" either.
        self::$caveat = $ddevReason === '' ? '' : $ddevReason
            . '. Until then only what the console can answer without booting TYPO3 against its database '
            . 'is available — the configuration lookup reads files, the label, module and Fluid namespace '
            . 'lookups need the database';

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
     * Reachable is not one state: a project whose containers are stopped can
     * still be asked what its configuration files say, and cannot be asked
     * anything that boots TYPO3. Reporting that as reachable is what let five
     * tools be presented as usable while four of them were not.
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
        $command = array_merge($invocation['command'], $arguments, ['--no-interaction', '--no-ansi']);

        return self::execute($command, Instance::root() ?? getcwd() ?: '.');
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
     * The lowest PHP the installation accepts, taken from what it declares:
     * the pinned Composer platform first, then the requirement itself.
     */
    private static function requiredPhpVersion(string $root): ?string
    {
        $decoded = self::manifest($root);

        $platform = $decoded['config']['platform']['php'] ?? null;
        if (is_string($platform) && $platform !== '') {
            return $platform;
        }

        $required = $decoded['require']['php'] ?? null;
        if (!is_string($required) || preg_match('/(\d+)\.(\d+)/', $required, $matches) !== 1) {
            return null;
        }

        return $matches[1] . '.' . $matches[2] . '.0';
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

    /**
     * Walks PATH rather than shelling out: "command -v" is a shell builtin and
     * proc_open runs no shell, so asking for it would always come back empty.
     */
    private static function locateBinary(string $name): ?string
    {
        foreach (explode(PATH_SEPARATOR, (string) getenv('PATH')) as $directory) {
            $candidate = rtrim($directory, '/') . '/' . $name;
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $command
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    private static function execute(array $command, string $workingDirectory): array
    {
        // The child gets an stdin of its own, closed immediately so it reads
        // EOF. Left out of the descriptors it would inherit this process's
        // stdin, which on the stdio server is the client's JSON-RPC stream: a
        // request written while the console command runs is then read by the
        // console command, and the client waits forever for an answer to a
        // request the server never saw.
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open($command, $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => 'could not start ' . $command[0]];
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $error = '';
        // Taken from the status rather than from proc_close: before PHP 8.3 the
        // status call reaps the child itself, and proc_close then has nothing
        // left to wait for and answers -1. Every command would read as failed.
        $exitCode = -1;
        $deadline = time() + self::TIMEOUT_SECONDS;
        while (true) {
            $output .= (string) stream_get_contents($pipes[1]);
            $error .= (string) stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if (!$status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }
            if (time() >= $deadline) {
                proc_terminate($process);

                return [
                    'ok' => false,
                    'exitCode' => -1,
                    'output' => $output,
                    'error' => sprintf('timed out after %d seconds', self::TIMEOUT_SECONDS),
                ];
            }
            usleep(20_000);
        }

        $output .= (string) stream_get_contents($pipes[1]);
        $error .= (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $closed = proc_close($process);
        if ($exitCode < 0) {
            $exitCode = $closed;
        }

        return [
            'ok' => $exitCode === 0,
            'exitCode' => $exitCode,
            'output' => $output,
            'error' => trim($error),
        ];
    }
}
