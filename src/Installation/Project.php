<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

use Symfony\Component\Yaml\Yaml;

/**
 * What the project around the discovered installation consists of, read from
 * its files.
 *
 * The knowledge base describes TYPO3; this describes the repository the caller
 * is standing in — which extensions are its own, which sites it configures,
 * which commands it declares. None of that could be bundled, and all of it is
 * what an answer has to be right about before it can recommend anything: a
 * check that does not exist here is worse than no check.
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
            'extensions' => self::extensions($root),
            'sites' => self::sites($root),
            'commands' => self::commands($root, $manifest),
            'patches' => self::patches($manifest),
        ];
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
        $sites = [];
        foreach (glob($root . '/config/sites/*/config.yaml') ?: [] as $file) {
            $configuration = self::yaml($file);
            $languages = [];
            foreach ($configuration['languages'] ?? [] as $language) {
                if (is_array($language)) {
                    $languages[] = (string) ($language['title'] ?? $language['locale'] ?? '');
                }
            }

            $sites[] = [
                'identifier' => basename(dirname($file)),
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
     * runs; the core's own runTests.sh suites are not there, which is the whole
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
