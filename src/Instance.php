<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * The TYPO3 installation the calling agent is working in, if there is one.
 *
 * Most of this server answers from bundled knowledge. This class supplies the
 * exception for questions whose answer belongs to an installation: registered
 * icons and labels, and the backend CSS, JavaScript, and styleguide files that
 * define its component contract. A snapshot of one core revision cannot be the
 * primary answer for those.
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

    /** Found by walking up from the directory the server was started in. */
    public const VIA_DISCOVERY = 'discovery';

    /** Named outright by the caller, and then not searched for at all. */
    public const VIA_ENVIRONMENT = 'environment';

    /** Names the installation to read, whatever the working directory says. */
    public const ROOT_VARIABLE = 'TYPO3_MCP_ROOT';

    /** How far up from the starting directory to look before giving up. */
    private const MAX_DEPTH = 12;

    private static ?string $startingDirectory = null;

    /** @var array{root: string, kind: string, startedFrom: string, via: string}|null|false false = not resolved yet */
    private static array|null|false $resolved = false;

    private static string $misconfiguration = '';

    /** @var array<int, string> The directories the last search walked. */
    private static array $searched = [];

    /**
     * Hands the working directory the server was started in to the discovery.
     * Called by the stdio entrypoint and by nothing else.
     */
    public static function discoverFrom(?string $directory): void
    {
        self::$startingDirectory = $directory === null || $directory === '' ? null : $directory;
        self::$resolved = false;
    }

    /**
     * The working directory the server was started in, when an entrypoint
     * handed one in, and null otherwise.
     *
     * Unlike root() this says nothing about an installation — it is only where
     * the calling session was, which is worth knowing even when nothing was
     * found there. The same restriction applies as everywhere else in this
     * class: it is the agent's directory only because the stdio entrypoint says
     * so, so an endpoint that never calls discoverFrom() gets null rather than
     * its own document root.
     */
    public static function startedFrom(): ?string
    {
        return self::$startingDirectory;
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
     * What was found, how, and where the search started, so a caller can tell
     * whether the server is reading the installation it means. A silently wrong
     * instance would be worse than none at all.
     *
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    public static function describe(): ?array
    {
        // Only a success is remembered. A failure is retried on the next call,
        // because an installation appearing during a session is the ordinary
        // case, not the exotic one: the agent runs composer install, or starts
        // the containers, and does it precisely because the answer said there
        // was nothing to read. Remembering "nothing" outlives the reason for
        // it, and the caller has no way to tell — the session would have to be
        // restarted to get an answer that is already true.
        if (is_array(self::$resolved)) {
            return self::$resolved;
        }

        self::$searched = [];
        [$configured, self::$misconfiguration] = self::fromEnvironment();
        if ($configured !== null || self::$misconfiguration !== '') {
            // A configured root that cannot be used is not quietly replaced by
            // a discovered one: the caller stated which installation it means,
            // and answering about a different one is the failure this whole
            // class exists to avoid.
            return self::$resolved = $configured;
        }

        $located = self::$startingDirectory === null
            ? null
            : self::locate(self::$startingDirectory);

        return $located === null ? null : self::$resolved = $located;
    }

    /**
     * What is wrong with the configuration, if anything. Empty otherwise.
     *
     * A variable that was set and could not be used has to be said out loud.
     * Silence would look exactly like not having set it, which is the one thing
     * the caller knows it did.
     */
    public static function misconfiguration(): string
    {
        self::describe();

        return self::$misconfiguration;
    }

    /**
     * The directories the search walked, so a failure can be told apart from a
     * server started in the wrong place.
     *
     * "No installation was found" is two very different situations wearing one
     * sentence: a layout this cannot read, and a client that launched the
     * server somewhere else entirely. Which one it is follows from where it
     * looked, and from nothing else the caller has access to.
     *
     * @return array<int, string>
     */
    public static function searched(): array
    {
        self::describe();

        return self::$searched;
    }

    /**
     * The installation named outright, for the layouts discovery cannot reach:
     * an installation in a subdirectory, a checkout the client starts the
     * server beside rather than inside, a stack this server has never heard of.
     *
     * Unlike the working directory this is not derived from anything — it is a
     * decision someone made — so it holds for every entrypoint, the HTTP one
     * included, where it is the only way to name an installation at all.
     *
     * @return array{0: array{root: string, kind: string, startedFrom: string, via: string}|null, 1: string}
     */
    private static function fromEnvironment(): array
    {
        $configured = getenv(self::ROOT_VARIABLE);
        if (!is_string($configured) || trim($configured) === '') {
            return [null, ''];
        }

        $configured = trim($configured);
        $root = realpath($configured);
        if ($root === false || !is_dir($root)) {
            return [null, sprintf(
                '%s is set to "%s", which is not a directory on this machine',
                self::ROOT_VARIABLE,
                $configured
            )];
        }

        return [[
            'root' => $root,
            'kind' => (self::readJson($root . '/composer.json')['type'] ?? '') === 'typo3-cms-core'
                ? self::KIND_CORE_CHECKOUT
                : self::KIND_COMPOSER_PROJECT,
            'startedFrom' => $configured,
            'via' => self::VIA_ENVIRONMENT,
        ], ''];
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
     * The TYPO3 version of this installation, or null when there is none to
     * read.
     *
     * Read from the core package's Typo3Version class rather than asked of the
     * console: the version decides whether an answer holds, so it has to be
     * available exactly when the console is not — an installation whose
     * database has no schema still has a version, and knowing it is what keeps
     * a v15 answer from being handed to a v13 caller.
     */
    public static function typo3Version(): ?string
    {
        $core = self::packages()['core'] ?? null;
        if ($core === null) {
            return null;
        }

        $file = $core . '/Classes/Information/Typo3Version.php';
        if (!is_file($file)) {
            return null;
        }

        if (preg_match('/const\s+VERSION\s*=\s*[\'"]([^\'"]+)[\'"]/', (string) file_get_contents($file), $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    /** The major of that version as a number, for comparing it with another. */
    public static function typo3Major(): ?int
    {
        $version = self::typo3Version();

        return $version === null ? null : (int) $version;
    }

    /**
     * Whether an extension key is a system extension of this installation, or
     * null when the installation does not have it — or when there is none.
     *
     * Where a package lives is what decides it, because the key alone cannot:
     * a system extension is below typo3/sysext/ in a core checkout and comes
     * from typo3/cms-* in a Composer project, and everything else is somebody's
     * extension.
     */
    public static function isSystemExtension(string $key): ?bool
    {
        $path = self::packages()[$key] ?? null;
        if ($path === null) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);

        return str_contains($normalized, '/typo3/sysext/') || str_contains($normalized, '/typo3/cms-');
    }

    /**
     * Both the kind of installation and the packages in it are declared by the
     * installation itself, so neither is guessed here: the monorepo root
     * declares "type": "typo3-cms-core", and a Composer installation lists
     * every TYPO3 package in composer/installed.json below the vendor directory
     * it declares — the same source TYPO3's own PackageArtifactBuilder reads.
     *
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    private static function locate(string $startingDirectory): ?array
    {
        $directory = realpath($startingDirectory);
        if ($directory === false) {
            return null;
        }
        $startedFrom = $directory;

        for ($depth = 0; $depth < self::MAX_DEPTH; ++$depth) {
            self::$searched[] = $directory;
            if ((self::readJson($directory . '/composer.json')['type'] ?? '') === 'typo3-cms-core') {
                return [
                    'root' => $directory,
                    'kind' => self::KIND_CORE_CHECKOUT,
                    'startedFrom' => $startedFrom,
                    'via' => self::VIA_DISCOVERY,
                ];
            }
            if (self::composerPackages($directory) !== []) {
                return [
                    'root' => $directory,
                    'kind' => self::KIND_COMPOSER_PROJECT,
                    'startedFrom' => $startedFrom,
                    'via' => self::VIA_DISCOVERY,
                ];
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
