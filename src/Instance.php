<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The TYPO3 installation the calling agent is working in, if there is one.
 *
 * Everything else in this server answers from bundled knowledge. This is the
 * one exception, and it exists because some questions have no bundled answer
 * that could be right: which icon identifiers are registered, and which labels
 * exist, are properties of the installation. Both registries are assembled at
 * runtime from every installed package, so a snapshot of one core revision
 * describes an installation nobody works in.
 *
 * Discovery is opt-in per entrypoint and is never derived from getcwd() on its
 * own. Only bin/typo3-cms-mcp hands its working directory in, because only
 * there is that directory the agent's own: the client launches the server as a
 * subprocess of the session it is working in. An HTTP endpoint has no such
 * relationship to its callers — its document root may itself sit inside a TYPO3
 * installation, and a cwd-derived answer would then report that installation to
 * every remote caller. Hence the flag rather than a lookup.
 */
final class Instance
{
    /** A core monorepo checkout: system extensions live in typo3/sysext/. */
    public const KIND_CORE_CHECKOUT = 'core-checkout';

    /** A Composer-installed project: packages live where Composer put them. */
    public const KIND_COMPOSER_PROJECT = 'composer-project';

    /** How far up from the starting directory to look before giving up. */
    private const MAX_DEPTH = 12;

    private static ?string $startingDirectory = null;

    /** @var array{root: string, kind: string, startedFrom: string}|null|false false = not resolved yet */
    private static array|null|false $resolved = false;

    /**
     * Hands the working directory the server was started in to the discovery.
     * Called by the stdio entrypoint and by nothing else.
     */
    public static function discoverFrom(?string $directory): void
    {
        self::$startingDirectory = $directory === null || $directory === '' ? null : $directory;
        self::$resolved = false;
    }

    /** Whether an installation was found to read from. */
    public static function isAvailable(): bool
    {
        return self::describe() !== null;
    }

    /** Absolute path of the installation, or null when there is none to read. */
    public static function root(): ?string
    {
        return self::describe()['root'] ?? null;
    }

    /**
     * What was found and where the search started, so a caller can tell whether
     * the server is reading the installation it means. A silently wrong
     * instance would be worse than none at all.
     *
     * @return array{root: string, kind: string, startedFrom: string}|null
     */
    public static function describe(): ?array
    {
        if (self::$resolved !== false) {
            return self::$resolved;
        }

        self::$resolved = self::$startingDirectory === null
            ? null
            : self::locate(self::$startingDirectory);

        return self::$resolved;
    }

    /**
     * The installed TYPO3 packages, extension key to absolute path.
     *
     * This is what makes the answer instance-correct rather than core-only: a
     * project's own extensions register icons and ship labels exactly like a
     * system extension does.
     *
     * @return array<string, string>
     */
    public static function packages(): array
    {
        $instance = self::describe();
        if ($instance === null) {
            return [];
        }

        $packages = $instance['kind'] === self::KIND_CORE_CHECKOUT
            ? self::systemExtensions($instance['root'])
            : self::composerPackages($instance['root']);

        ksort($packages);

        return $packages;
    }

    /**
     * Both the kind of installation and the packages in it are declared by the
     * installation itself, so neither is guessed here: the monorepo root
     * declares "type": "typo3-cms-core", and a Composer installation lists
     * every TYPO3 package in composer/installed.json below the vendor directory
     * it declares — the same source TYPO3's own PackageArtifactBuilder reads.
     *
     * @return array{root: string, kind: string, startedFrom: string}|null
     */
    private static function locate(string $startingDirectory): ?array
    {
        $directory = realpath($startingDirectory);
        if ($directory === false) {
            return null;
        }
        $startedFrom = $directory;

        for ($depth = 0; $depth < self::MAX_DEPTH; ++$depth) {
            if ((self::readJson($directory . '/composer.json')['type'] ?? '') === 'typo3-cms-core') {
                return ['root' => $directory, 'kind' => self::KIND_CORE_CHECKOUT, 'startedFrom' => $startedFrom];
            }
            if (self::composerPackages($directory) !== []) {
                return ['root' => $directory, 'kind' => self::KIND_COMPOSER_PROJECT, 'startedFrom' => $startedFrom];
            }

            $parent = dirname($directory);
            if ($parent === $directory) {
                break;
            }
            $directory = $parent;
        }

        return null;
    }

    /**
     * The system extensions of a monorepo checkout, each under the extension
     * key it declares rather than under its directory name.
     *
     * @return array<string, string>
     */
    private static function systemExtensions(string $root): array
    {
        $packages = [];
        foreach (glob($root . '/typo3/sysext/*/composer.json') ?: [] as $manifest) {
            $declared = self::readJson($manifest);
            if (($declared['type'] ?? '') !== 'typo3-cms-framework') {
                continue;
            }

            $path = dirname($manifest);
            $key = $declared['extra']['typo3/cms']['extension-key'] ?? basename($path);
            $packages[(string) $key] = $path;
        }

        return $packages;
    }

    /** @return array<string, mixed> */
    private static function readJson(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Where this root keeps its dependencies.
     *
     * Composer's default is vendor/, and a root that declares nothing is
     * resolved exactly as before. But the layout the TYPO3 extension testing
     * setup produces moves it — `"vendor-dir": ".build/vendor"` — and an
     * installation whose metadata is looked for in the wrong place is an
     * installation that does not exist as far as this server is concerned:
     * discovery walks past it and every question only it could answer goes
     * unanswered.
     */
    private static function vendorDirectory(string $root): string
    {
        $configured = self::readJson($root . '/composer.json')['config']['vendor-dir'] ?? null;
        if (!is_string($configured) || trim($configured) === '') {
            return $root . '/vendor';
        }

        $configured = rtrim(trim($configured), '/');

        return str_starts_with($configured, '/') ? $configured : $root . '/' . $configured;
    }

    /**
     * The TYPO3 packages a Composer installation declares, read from the same
     * metadata TYPO3's own PackageArtifactBuilder reads. Only the two TYPO3
     * package types count; every other dependency is irrelevant here.
     *
     * A project's own extensions appear alongside the system extensions, which
     * is the point: they register icons and ship labels in exactly the same way.
     *
     * @return array<string, string>
     */
    private static function composerPackages(string $root): array
    {
        $vendor = self::vendorDirectory($root);
        $decoded = self::readJson($vendor . '/composer/installed.json');
        $entries = $decoded['packages'] ?? $decoded;
        if (!is_array($entries)) {
            return [];
        }

        $packages = [];
        foreach ($entries as $entry) {
            if (!is_array($entry) || !in_array($entry['type'] ?? '', ['typo3-cms-framework', 'typo3-cms-extension'], true)) {
                continue;
            }

            $key = $entry['extra']['typo3/cms']['extension-key'] ?? null;
            if (!is_string($key) || $key === '') {
                // Without a declared key, Composer's TYPO3 installer derives one
                // from the second half of the package name.
                $name = (string) ($entry['name'] ?? '');
                $key = str_replace('-', '_', substr($name, (int) strrpos($name, '/') + 1));
            }

            $relative = (string) ($entry['install-path'] ?? '');
            $path = $relative === '' ? false : realpath($vendor . '/composer/' . $relative);
            if ($key !== '' && $path !== false) {
                $packages[$key] = $path;
            }
        }

        // The extension being worked on is the root package, and Composer lists
        // dependencies rather than the root — so in an extension development
        // checkout the one package the agent is actually editing would be the
        // only one missing from its own answers. It is added only when the
        // installation around it is real: a package list of nothing but the
        // root would report an installation where there is only a repository.
        if ($packages !== []) {
            $rootKey = self::rootPackage($root);
            if ($rootKey !== null) {
                $packages[$rootKey] = $root;
            }
        }

        return $packages;
    }

    /**
     * The extension key the root itself declares, when the root is a TYPO3
     * package rather than a project.
     */
    private static function rootPackage(string $root): ?string
    {
        $manifest = self::readJson($root . '/composer.json');
        if (!in_array($manifest['type'] ?? '', ['typo3-cms-framework', 'typo3-cms-extension'], true)) {
            return null;
        }

        $key = $manifest['extra']['typo3/cms']['extension-key'] ?? null;
        if (is_string($key) && $key !== '') {
            return $key;
        }

        $name = (string) ($manifest['name'] ?? '');

        return $name === '' ? null : str_replace('-', '_', substr($name, (int) strrpos($name, '/') + 1));
    }
}
