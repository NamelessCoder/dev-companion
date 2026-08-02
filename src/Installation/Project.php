<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * What the project around the discovered installation consists of, read from
 * its files.
 *
 * The knowledge base describes TYPO3; this describes the repository the caller
 * is standing in — which extensions are its own, which sites it configures,
 * which commands it declares, and which environment it declares them to run in.
 * None of that could be bundled, and all of it is what an answer has to be
 * right about before it can recommend anything: a check that does not exist
 * here is worse than no check.
 *
 * Files only. No console, no database, nothing started — the same rule the rest
 * of this server follows, and the reason this works on a fresh clone.
 */
final class Project
{
    /** The extension is the repository's own, or one of its path repositories. */
    public const ORIGIN_PROJECT = 'project';

    /** Installed as a dependency, and not a TYPO3 system extension. */
    public const ORIGIN_THIRD_PARTY = 'third-party';

    /**
     * Shipped by the repository's test setup, not by the repository.
     *
     * An extension repository routinely installs a package of its own from
     * below Tests/ — a fixture the functional suite loads, a demo package a
     * scenario needs. Composer lists it like any other path repository, and
     * calling it the project's own says "this is what is being worked on"
     * about something that exists to be loaded and thrown away. Reported as
     * its own thing rather than dropped: a fixture the answer omits is one
     * nobody can account for when it shows up in an installed package list.
     */
    public const ORIGIN_FIXTURE = 'fixture';

    /** A declared command that reports on the sources and leaves them as they are. */
    public const RUNS_AS_CHECK = 'check';

    /** One that rewrites something. */
    public const RUNS_AS_CHANGE = 'change';

    /** One whose declaration does not say which of the two it is. */
    public const RUNS_UNDECLARED = 'unknown';

    /**
     * @return array{
     *     root: string,
     *     kind: string,
     *     typo3Version: ?string,
     *     phpConstraint: ?string,
     *     coreConstraint: ?string,
     *     environment: array{via: string, php: ?string, source: string, entered: bool}|null,
     *     extensions: array<int, array{key: string, path: string, origin: string}>,
     *     sites: array<int, array{identifier: string, base: string, rootPageId: ?int, sets: array<int, string>, languages: array<int, string>}>,
     *     commands: array<int, array{command: string, source: string, declares: string, runs: string}>,
     *     patches: array<int, array{package: string, description: string, file: string}>
     * }|null
     */
    public static function describe(): ?array
    {
        $instance = Instance::describe();
        if ($instance === null) {
            return null;
        }

        $root = $instance['root'];
        $manifest = self::json($root . '/composer.json');

        return [
            'root' => $root,
            'kind' => $instance['kind'],
            'typo3Version' => Instance::typo3Version(),
            'phpConstraint' => self::requirement($manifest, 'php'),
            'coreConstraint' => self::requirement($manifest, 'typo3/cms-core'),
            'environment' => self::environment($root),
            'extensions' => self::extensions($root),
            'sites' => self::sites($root),
            'commands' => self::commands($root, $manifest),
            'patches' => self::patches($manifest),
        ];
    }

    /**
     * The environment this repository configures to run itself in, where it
     * configures one at all.
     *
     * A containerised project has two PHP versions — the one its manifest
     * constrains and the one its container runs — and an answer that states
     * only the first invites the review that reported "PHP version mismatch
     * blocks all tests" over a host at 8.3.23 against a declared ^8.4, while
     * the suite it meant had been running on 8.4 in the container all along
     * (`feedback/2026-07-31-193611`). The commands below make that worse rather
     * than better: they are the ones a task is sent to run, and a declared
     * `composer test:unit` put on the caller's own shell is a different machine
     * from the one the project is built for.
     *
     * Read from the environment's own files, so `R-PRJ-001` still holds and the
     * answer arrives on a fresh clone. `ddev describe` would answer this too,
     * and starting anything to find out is the whole of what `R-DIS-006`
     * forbids — so a stopped project reads exactly like a running one here.
     *
     * @return array{via: string, php: ?string, source: string, entered: bool}|null
     */
    private static function environment(string $root): ?array
    {
        if (is_file($root . '/.ddev/config.yaml')) {
            [$php, $source] = self::ddevPhp($root);

            return [
                'via' => Typo3Cli::VIA_DDEV,
                'php' => $php,
                'source' => $source,
                // DDEV sets this inside the web container, and there the shell
                // the caller has is the environment. Telling it to put `ddev`
                // in front of a command would name a binary that is not there.
                'entered' => filter_var(getenv('IS_DDEV_PROJECT'), FILTER_VALIDATE_BOOL),
            ];
        }

        // Nothing in the files says DDEV, and one thing outside them may still
        // say there is an environment: the console command the caller stated
        // for `Typo3Cli`. A `docker compose exec web bin/typo3` is a machine
        // this server cannot read a PHP version from — and reporting no
        // environment there says "these run in your shell", which is the claim
        // this whole field exists to stop being made by silence.
        $stated = getenv(Typo3Cli::CONSOLE_VARIABLE);
        $program = is_string($stated) ? (self::firstToken($stated) ?? '') : '';
        if ($program !== '' && !str_starts_with(basename($program), 'php')) {
            return [
                'via' => Typo3Cli::VIA_OVERRIDE,
                'php' => null,
                'source' => Typo3Cli::CONSOLE_VARIABLE,
                'entered' => false,
            ];
        }

        // An interpreter on this machine is not another environment: a stated
        // `php /some/where/typo3` reaches the console from the same shell every
        // declared command would run in, and there is one PHP.
        return null;
    }

    /** The program a stated command line starts with, quoted or not. */
    private static function firstToken(string $commandLine): ?string
    {
        $tokens = preg_split('/\s+/', trim($commandLine)) ?: [];

        return ($tokens[0] ?? '') === '' ? null : trim($tokens[0], '"\'');
    }

    /**
     * The PHP a DDEV project runs, and the file it is stated in.
     *
     * `.ddev/config.yaml` is read first, then every `.ddev/config.*.yaml` and
     * `.ddev/config.*.yml` beside it in filename order, and the last statement
     * of the version is the one that holds. That is DDEV's own merge — measured
     * against v1.25.1 on 2026-08-02, where a base `8.1` beside `config.aaa.yaml`
     * saying `8.2` and `config.zzz.yaml` saying `8.0` came out of `ddev debug
     * configyaml` as 8.0, and where `config.override.yaml` took no special last
     * place but sorted with the rest. Reading the base file alone would report
     * a version the container does not run in every project that keeps its
     * local settings in the `config.local.yaml` DDEV gitignores for that.
     *
     * Null where nothing states one: DDEV then uses the default of the DDEV
     * that is installed, which is not in these files and is not the same number
     * from one release to the next.
     *
     * @return array{0: ?string, 1: string}
     */
    private static function ddevPhp(string $root): array
    {
        $files = ['.ddev/config.yaml'];
        foreach (Finder::create()->files()->in($root . '/.ddev')->depth(0)
            ->name('config.*.yaml')->name('config.*.yml')->sortByName() as $file) {
            $files[] = '.ddev/' . $file->getFilename();
        }

        $php = null;
        $source = '.ddev/config.yaml';
        foreach ($files as $file) {
            $version = self::phpVersion(self::yaml($root . '/' . $file)['php_version'] ?? null);
            if ($version !== null) {
                $php = $version;
                $source = $file;
            }
        }

        return [$php, $source];
    }

    /**
     * A `php_version` as the file spells it.
     *
     * Quoting it is optional in DDEV and the difference reaches here: unquoted,
     * `8.0` is a YAML float, and casting that to a string gives "8" — a PHP
     * version that exists nowhere. Both digits are kept instead, which is the
     * whole of the spelling: DDEV v1.25.1 takes major.minor from a list it
     * ships (5.6 through 8.5) and refuses "8.4.3" and "8" by name, so there is
     * no patch level and no bare major to carry.
     */
    private static function phpVersion(mixed $stated): ?string
    {
        return match (true) {
            is_float($stated) => sprintf('%.1F', $stated),
            is_int($stated) => (string) $stated,
            is_string($stated) && trim($stated) !== '' => trim($stated),
            default => null,
        };
    }

    /**
     * The patches this project applies to its dependencies.
     *
     * A patched package is a package whose behaviour is not what its version
     * says, and the next composer update either reapplies the patch or fails on
     * it. Nothing else in an answer about this project matters more to an
     * upgrade, and it is one entry in composer.json.
     *
     * @param array<string, mixed> $manifest
     * @return array<int, array{package: string, description: string, file: string}>
     */
    private static function patches(array $manifest): array
    {
        $declared = $manifest['extra']['patches'] ?? null;
        if (!is_array($declared)) {
            return [];
        }

        $patches = [];
        foreach ($declared as $package => $entries) {
            foreach (is_array($entries) ? $entries : [] as $description => $file) {
                $patches[] = [
                    'package' => (string) $package,
                    // The list form carries no description, only the file.
                    'description' => is_string($description) ? $description : '',
                    'file' => (string) $file,
                ];
            }
        }

        return $patches;
    }

    /**
     * The extensions that are not TYPO3's own, with where they come from.
     *
     * A system extension is TYPO3; everything else is what this project brought
     * with it, and the ones inside the repository are the ones it is actually
     * working on.
     *
     * @return array<int, array{key: string, path: string, origin: string}>
     */
    private static function extensions(string $root): array
    {
        $extensions = [];
        foreach (Instance::packages() as $key => $path) {
            if (Instance::isSystemExtension($key) === true) {
                continue;
            }
            $extensions[] = [
                'key' => $key,
                'path' => self::relative($root, $path),
                'origin' => self::origin($path),
            ];
        }

        return $extensions;
    }

    /**
     * Where an extension in this installation comes from, read off its path.
     *
     * Below the vendor directory it was installed as a dependency. Below a
     * Tests/ directory it belongs to the test setup, whatever Composer's
     * install path says — a package repository under Tests/Packages/ resolves
     * to a real directory in the repository, and nothing else distinguishes it
     * from the extension being developed.
     */
    public static function origin(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        if (str_contains($path, '/vendor/')) {
            return self::ORIGIN_THIRD_PARTY;
        }

        return preg_match('#(^|/)[Tt]ests?/#', $path) === 1 ? self::ORIGIN_FIXTURE : self::ORIGIN_PROJECT;
    }

    /**
     * The sites this project configures, with the sets each of them depends on.
     *
     * The dependencies are where a site says which TypoScript it gets, so they
     * are the first thing to look at when a template renders nothing.
     *
     * @return array<int, array{identifier: string, base: string, rootPageId: ?int, sets: array<int, string>, languages: array<int, string>}>
     */
    private static function sites(string $root): array
    {
        $directory = $root . '/config/sites';
        if (!is_dir($directory)) {
            return [];
        }

        $sites = [];
        foreach (Finder::create()->files()->in($directory)->depth(1)->name('config.yaml')->sortByName() as $file) {
            $configuration = self::yaml($file->getPathname());
            $languages = [];
            foreach ($configuration['languages'] ?? [] as $language) {
                if (is_array($language)) {
                    $languages[] = (string) ($language['title'] ?? $language['locale'] ?? '');
                }
            }

            $sites[] = [
                'identifier' => $file->getRelativePath(),
                'base' => (string) ($configuration['base'] ?? ''),
                'rootPageId' => isset($configuration['rootPageId']) ? (int) $configuration['rootPageId'] : null,
                'sets' => array_map('strval', $configuration['dependencies'] ?? []),
                'languages' => $languages,
            ];
        }

        return $sites;
    }

    /**
     * The commands this repository declares, which are the only ones worth
     * recommending in it.
     *
     * Composer scripts and npm scripts are where a project writes down what it
     * runs; the core's own testing suites are not there, which is the whole
     * point of asking.
     *
     * @param array<string, mixed> $manifest
     * @return array<int, array{command: string, source: string, declares: string, runs: string}>
     */
    private static function commands(string $root, array $manifest): array
    {
        $commands = [];
        $scripts = is_array($manifest['scripts'] ?? null) ? $manifest['scripts'] : [];
        foreach ($scripts as $name => $declaration) {
            $commands[] = [
                'command' => 'composer ' . $name,
                'source' => 'composer.json',
                'declares' => self::declaration($declaration),
                'runs' => self::runs($declaration, $scripts),
            ];
        }

        $packageScripts = self::json($root . '/package.json')['scripts'] ?? [];
        foreach (is_array($packageScripts) ? $packageScripts : [] as $name => $declaration) {
            $commands[] = [
                'command' => 'npm run ' . $name,
                'source' => 'package.json',
                'declares' => self::declaration($declaration),
                'runs' => self::runs($declaration, []),
            ];
        }

        return $commands;
    }

    /**
     * What running a declared command does to the sources, read off what it
     * declares.
     *
     * A task told not to change files still wants the checks, and no script
     * name carries the difference: `cgl` and `cgl:ci` are one `--dry-run` apart
     * and are the same tool. So it is read out of the body — the tool that is
     * invoked, and the flags that decide which way that tool runs.
     *
     * Three answers rather than two, because a `no` covering everything
     * unrecognised makes the undecided look decided. A test suite is the
     * ordinary undeclared case: it runs the project's own code, and nothing in
     * a composer.json says what that code writes.
     *
     * "The sources", not "nothing": a checker may still write a cache of its
     * own — `php-cs-fixer --dry-run` writes `.php-cs-fixer.cache` unless told
     * not to — and this answers whether the code it was pointed at comes back
     * different, which is what a review is asked about.
     *
     * @param array<int, mixed>|string $declaration one composer or npm script, as declared
     * @param array<string, mixed> $scripts the declaring manifest's scripts, for `@name` references
     * @param array<int, string> $seen the references already followed, so a cycle ends
     */
    public static function runs(array|string $declaration, array $scripts = [], array $seen = []): string
    {
        $lines = array_filter(is_array($declaration) ? $declaration : [$declaration], is_string(...));
        if ($lines === []) {
            return self::RUNS_UNDECLARED;
        }

        $answers = [];
        foreach ($lines as $line) {
            $answers[] = self::runsLine($line, $scripts, $seen);
        }

        // The strongest claim any line makes is the claim about all of them: a
        // script that lints and then fixes changes the sources, and one that
        // lints and then runs a suite is as undeclared as the suite is.
        return match (true) {
            in_array(self::RUNS_AS_CHANGE, $answers, true) => self::RUNS_AS_CHANGE,
            in_array(self::RUNS_UNDECLARED, $answers, true) => self::RUNS_UNDECLARED,
            default => self::RUNS_AS_CHECK,
        };
    }

    /**
     * @param array<string, mixed> $scripts
     * @param array<int, string> $seen
     */
    private static function runsLine(string $line, array $scripts, array $seen): string
    {
        $line = trim($line);

        // One line is one command only where nothing chains another onto it.
        // Read as the tool in front, `phpstan analyse && php-cs-fixer fix` is
        // the analyser and the rewriter is never reached — and an npm script
        // chains by convention, so the shape is the ordinary one there.
        $chained = self::chained($line);
        if (count($chained) > 1) {
            return self::runs($chained, $scripts, $seen);
        }

        // Composer's own prefixes come before any tool: @php picks the PHP the
        // project runs on, @putenv sets a variable for the lines after it, and
        // a bare @name is another script of the same manifest.
        while ($line !== '' && $line[0] === '@') {
            [$prefix, $rest] = array_pad(preg_split('/\s+/', $line, 2) ?: [], 2, '');
            if ($prefix === '@php' || $prefix === '@php_binary' || $prefix === '@composer') {
                $line = $prefix === '@composer' ? 'composer ' . $rest : $rest;
                continue;
            }
            if ($prefix === '@putenv') {
                return self::RUNS_AS_CHECK;
            }

            $name = substr($prefix, 1);

            return isset($scripts[$name]) && !in_array($name, $seen, true)
                && (is_array($scripts[$name]) || is_string($scripts[$name]))
                ? self::runs($scripts[$name], $scripts, [...$seen, $name])
                : self::RUNS_UNDECLARED;
        }

        // A leading `NAME=value` is the environment a command is given, not the
        // command: `PHP_CS_FIXER_IGNORE_ENV=1 php-cs-fixer fix --dry-run` is the
        // fixer reporting, and reading the assignment as the tool loses every
        // flag behind it — so the reporter and the rewriter come back the same.
        $line = (string) preg_replace('/^(?:[A-Za-z_][A-Za-z0-9_]*=(?:"[^"]*"|\'[^\']*\'|\S*)\s+)+/', '', $line);

        $tokens = array_values(array_filter(preg_split('/\s+/', $line) ?: []));
        if ($tokens === []) {
            return self::RUNS_UNDECLARED;
        }

        $tool = self::tool(array_shift($tokens));
        // `php vendor/bin/phpstan` is phpstan; `php -l` is the linter itself.
        if ($tool === 'php' && $tokens !== [] && !str_starts_with($tokens[0], '-')) {
            $tool = self::tool((string) array_shift($tokens));
        }

        $carries = static function (string ...$flags) use ($tokens): bool {
            foreach ($tokens as $token) {
                foreach ($flags as $flag) {
                    if (strcasecmp($token, $flag) === 0 || stripos($token, $flag . '=') === 0) {
                        return true;
                    }
                }
            }

            return false;
        };
        $first = strtolower($tokens[0] ?? '');

        return match ($tool) {
            // Linters and analysers: they read, they report, and their exit
            // code is the whole of what they do to the checkout.
            'phplint', 'parallel-lint', 'typoscript-lint', 'phpcs', 'phpmd', 'phpcpd',
            'composer-require-checker', 'composer-unused', 'composer-dependency-analyser' => self::RUNS_AS_CHECK,
            'php' => $carries('-l') ? self::RUNS_AS_CHECK : self::RUNS_UNDECLARED,
            'phpstan' => $carries('--generate-baseline') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'psalm' => $carries('--set-baseline', '--alter') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            // Both directions of one tool, and the flag is the only difference.
            'php-cs-fixer' => $first === 'check' || $carries('--dry-run') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'ecs' => $carries('--fix') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'rector' => $carries('--dry-run') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'eslint', 'stylelint' => $carries('--fix') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'tsc' => $carries('--noEmit') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'phpcbf' => self::RUNS_AS_CHANGE,
            // Build steps exist to produce files, and the composer commands
            // that write are the ones that touch the tree the review is about.
            'vite', 'webpack', 'rollup', 'esbuild', 'sass', 'postcss', 'gulp', 'grunt',
            'git', 'rm', 'cp', 'mv', 'mkdir', 'touch' => self::RUNS_AS_CHANGE,
            'composer' => match ($first) {
                'validate', 'audit', 'show', 'outdated', 'licenses', 'diagnose', 'check-platform-reqs' => self::RUNS_AS_CHECK,
                'install', 'update', 'require', 'remove', 'dump-autoload', 'dumpautoload' => self::RUNS_AS_CHANGE,
                default => self::RUNS_UNDECLARED,
            },
            'npm', 'yarn', 'pnpm' => match ($first) {
                'install', 'ci', 'update', 'add' => self::RUNS_AS_CHANGE,
                default => self::RUNS_UNDECLARED,
            },
            // Its two writing subcommands are what TYPO3 extensions declare it
            // for; the rest of it is not read here.
            'extension-helper' => in_array($first, ['version:set', 'changelog:create'], true)
                ? self::RUNS_AS_CHANGE
                : self::RUNS_UNDECLARED,
            // A suite runs the project's own code, and `bin/typo3` runs whatever
            // command it is handed. Neither is readable from the declaration.
            default => self::RUNS_UNDECLARED,
        };
    }

    /**
     * The commands one declared line puts on the shell.
     *
     * `&&`, `||`, `;`, `|` and a trailing `&` each start another one, and every
     * one of them runs. A quoted operator does not, because a filter pattern
     * carries `|` and a message carries `;`.
     *
     * @return array<int, string>
     */
    private static function chained(string $line): array
    {
        $commands = [];
        $command = '';
        $quote = '';
        foreach (str_split($line) as $character) {
            if ($quote !== '') {
                $quote = $character === $quote ? '' : $quote;
            } elseif ($character === '"' || $character === "'") {
                $quote = $character;
            } elseif ($character === '&' || $character === '|' || $character === ';') {
                $commands[] = $command;
                $command = '';

                continue;
            }
            $command .= $character;
        }
        $commands[] = $command;

        return array_values(array_filter(array_map(trim(...), $commands), static fn(string $command): bool => $command !== ''));
    }

    /** The tool a declared line invokes, without the path, the extension, or the runner in front of it. */
    private static function tool(string $token): string
    {
        $token = basename(str_replace('\\', '/', $token));
        $token = (string) preg_replace('/\.(phar|bat|cmd|sh)$/i', '', $token);

        return strtolower($token);
    }

    /** @param array<int, mixed>|string $declaration */
    private static function declaration(array|string $declaration): string
    {
        $lines = array_filter(is_array($declaration) ? $declaration : [$declaration], is_string(...));

        return implode(' && ', array_map(trim(...), $lines));
    }

    /** @param array<string, mixed> $manifest */
    private static function requirement(array $manifest, string $package): ?string
    {
        $constraint = $manifest['require'][$package] ?? null;

        return is_string($constraint) ? $constraint : null;
    }

    /**
     * What this repository declares it needs of the core, straight from its
     * root manifest.
     *
     * `describe()` returns it too, but everything else there costs file reads
     * this has no use for, and the version an answer is composed for is decided
     * before any of it.
     */
    public static function coreConstraint(): ?string
    {
        $instance = Instance::describe();
        if ($instance === null) {
            return null;
        }

        return self::requirement(self::json($instance['root'] . '/composer.json'), 'typo3/cms-core');
    }

    /** @return array<string, mixed> */
    private static function json(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $decoded = json_decode((string) file_get_contents($file), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * A site configuration, or an empty one when it cannot be read.
     *
     * A broken config.yaml is a state a project is genuinely in — mid-edit, or
     * with an environment placeholder a parser rejects — and the answer is the
     * other sites rather than an exception.
     *
     * @return array<string, mixed>
     */
    private static function yaml(string $file): array
    {
        try {
            $parsed = Yaml::parseFile($file);
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsed) ? $parsed : [];
    }

    private static function relative(string $root, string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $root = str_replace('\\', '/', $root) . '/';

        return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
    }
}
