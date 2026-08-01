<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

/**
 * The changelog entries of the installation this server was started in.
 *
 * Every TYPO3 package ships the core's own changelog — one RST file per
 * breaking change, deprecation, feature and important note, in the directory of
 * the version it was released in. It is the authoritative record of what
 * changed, it is on disk in every installation, and it is exactly what the
 * question "what did my version deprecate that affects my code" is asking for.
 *
 * Nothing is bundled here. A snapshot would answer for the version it was taken
 * from; the installation's own copy answers for the version the caller runs,
 * and it grows with a Composer update rather than with a release of this
 * server.
 *
 * The scan reads file names, not files: the type, the issue number and the
 * title are all in the name, and only what a query matched is opened.
 */
final class Changelog
{
    /** @var array<int, string> */
    public const TYPES = ['Breaking', 'Deprecation', 'Feature', 'Important'];

    /** Where the installed core keeps them, or null when there is none to read. */
    public static function directory(): ?string
    {
        $core = Instance::packages()['core'] ?? null;
        if ($core === null) {
            return null;
        }

        $directory = $core . '/Documentation/Changelog';

        return is_dir($directory) ? $directory : null;
    }

    /**
     * The versions the installed changelog covers, newest first.
     *
     * @return array<int, string>
     */
    public static function versions(): array
    {
        $directory = self::directory();
        if ($directory === null) {
            return [];
        }

        $versions = [];
        foreach (glob($directory . '/*', GLOB_ONLYDIR) ?: [] as $path) {
            $versions[] = basename($path);
        }
        usort($versions, static fn(string $a, string $b): int => version_compare($b, $a));

        return $versions;
    }

    /**
     * Every entry, as far as its file name says: type, issue, version, and the
     * title the name spells in CamelCase.
     *
     * @return array<int, array{type: string, issue: string, version: string, key: string, source: string, file: string}>
     */
    public static function entries(string $type = '', string $version = ''): array
    {
        $directory = self::directory();
        if ($directory === null) {
            return [];
        }

        $type = ucfirst(strtolower(trim($type)));
        $entries = [];
        foreach (self::versions() as $inVersion) {
            if ($version !== '' && !str_starts_with($inVersion, $version)) {
                continue;
            }
            foreach (glob($directory . '/' . $inVersion . '/*.rst') ?: [] as $file) {
                $name = basename($file, '.rst');
                if (preg_match('/^(Breaking|Deprecation|Feature|Important)-(\d+)-(.+)$/', $name, $matches) !== 1) {
                    continue;
                }
                if ($type !== '' && $matches[1] !== $type) {
                    continue;
                }
                $entries[] = [
                    'type' => $matches[1],
                    'issue' => $matches[2],
                    'version' => $inVersion,
                    // The two fields a label search works on, so the same
                    // "carries every word" rule applies here without a second
                    // matcher: the title as words, and the file name as it is.
                    'key' => $name,
                    'source' => self::words($matches[3]),
                    'file' => $file,
                ];
            }
        }

        return $entries;
    }

    /**
     * The title and the index tags a matched entry carries, read from the file.
     *
     * @param array{file: string} $entry
     * @return array{title: string, tags: array<int, string>}
     */
    public static function read(array $entry): array
    {
        $title = '';
        $tags = [];
        foreach (preg_split('/\R/', (string) file_get_contents($entry['file'])) ?: [] as $line) {
            // "Deprecation: #107208 - <f:debug.render> ViewHelper" — the type
            // and the issue are fields of their own, so the title is what is
            // left of the line.
            if ($title === '' && preg_match('/^(Breaking|Deprecation|Feature|Important):\s*(?:#\d+\s*-\s*)?(.+)$/', trim($line), $matches) === 1) {
                $title = trim($matches[2]);
            }
            // ".. index::" and "..  index::" are both in the wild.
            if (preg_match('/^\.\.\s+index::\s*(.*)$/', trim($line), $matches) === 1) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $matches[1]))));
            }
        }

        return ['title' => $title, 'tags' => $tags];
    }

    /** "ExperimentalBackendViewHelpers" as the words it is made of. */
    private static function words(string $camelCase): string
    {
        $spaced = preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $camelCase);

        return str_replace('-', ' ', $spaced ?? $camelCase);
    }
}
