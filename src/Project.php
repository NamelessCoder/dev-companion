<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

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

    /**
     * @return array{
     *     root: string,
     *     kind: string,
     *     typo3Version: ?string,
     *     phpConstraint: ?string,
     *     coreConstraint: ?string,
     *     extensions: array<int, array{key: string, path: string, origin: string}>,
     *     sites: array<int, array{identifier: string, base: string, rootPageId: ?int, sets: array<int, string>, languages: array<int, string>}>,
     *     commands: array<int, array{command: string, source: string}>,
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
     * @return array<int, array{command: string, source: string}>
     */
    private static function commands(string $root, array $manifest): array
    {
        $commands = [];
        foreach (array_keys($manifest['scripts'] ?? []) as $name) {
            $commands[] = ['command' => 'composer ' . $name, 'source' => 'composer.json'];
        }
        foreach (array_keys(self::json($root . '/package.json')['scripts'] ?? []) as $name) {
            $commands[] = ['command' => 'npm run ' . $name, 'source' => 'package.json'];
        }

        return $commands;
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
