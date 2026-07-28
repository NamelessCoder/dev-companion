<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

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

    /** A TYPO3 bootstrap can hang on a broken configuration; an MCP session must not. */
    private const TIMEOUT_SECONDS = 90;

    /** @var array{command: array<int, string>, via: string, php: string}|null|false */
    private static array|null|false $resolved = false;

    private static string $reason = '';

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

        $instance = Instance::describe();
        if ($instance === null) {
            self::$reason = 'no TYPO3 installation was found to run it in';

            return null;
        }

        $root = $instance['root'];
        $binary = self::consoleBinary($root);
        if ($binary === null) {
            self::$reason = sprintf('%s has no bin/typo3 or vendor/bin/typo3', $root);

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
        self::$reason = $ddevReason !== '' ? $ddevReason : $phpReason;

        return $invocation;
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
    }

    /** Why the console cannot be invoked. Empty once it can. */
    public static function reason(): string
    {
        self::resolve();

        return self::$reason;
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
     * Runs a command that speaks JSON and returns what it decoded.
     *
     * The console prints a SymfonyStyle title before the payload, so the JSON
     * starts at the first brace rather than at the first byte.
     *
     * @param array<int, string> $arguments
     * @return array{ok: bool, data: array<string, mixed>, error: string}
     */
    public static function json(array $arguments): array
    {
        $result = self::run($arguments);
        if (!$result['ok']) {
            $error = $result['error'] !== '' ? $result['error'] : trim($result['output']);

            return ['ok' => false, 'data' => [], 'error' => $error];
        }

        $brace = strpos($result['output'], '{');
        $decoded = $brace === false ? null : json_decode(substr($result['output'], $brace), true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'data' => [], 'error' => 'the console answered with something other than JSON'];
        }

        return ['ok' => true, 'data' => $decoded, 'error' => ''];
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
            'command' => ['ddev', 'exec', '--', $binary],
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
        foreach (['vendor/bin/typo3', 'bin/typo3'] as $candidate) {
            if (is_file($root . '/' . $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * The lowest PHP the installation accepts, taken from what it declares:
     * the pinned Composer platform first, then the requirement itself.
     */
    private static function requiredPhpVersion(string $root): ?string
    {
        $manifest = $root . '/composer.json';
        if (!is_file($manifest)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($manifest), true);
        if (!is_array($decoded)) {
            return null;
        }

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
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open($command, $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => 'could not start ' . $command[0]];
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $error = '';
        $deadline = time() + self::TIMEOUT_SECONDS;
        while (true) {
            $output .= (string) stream_get_contents($pipes[1]);
            $error .= (string) stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if (!$status['running']) {
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
        $exitCode = proc_close($process);

        return [
            'ok' => $exitCode === 0,
            'exitCode' => $exitCode,
            'output' => $output,
            'error' => trim($error),
        ];
    }
}
