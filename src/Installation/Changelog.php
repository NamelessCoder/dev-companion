<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Installation;

use Symfony\Component\Finder\Finder;

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
        foreach (Finder::create()->directories()->in($directory)->depth(0) as $path) {
            $versions[] = $path->getFilename();
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
            foreach (Finder::create()->files()->in($directory . '/' . $inVersion)->depth(0)->name('*.rst')->sortByName() as $file) {
                $name = $file->getBasename('.rst');
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
                    'file' => $file->getPathname(),
                ];
            }
        }

        return $entries;
    }

    /**
     * The title, the index tags and the stated removal a matched entry carries,
     * read from the file.
     *
     * @param array{file: string, version: string, type: string} $entry
     * @return array{title: string, tags: array<int, string>, removal: string}
     */
    public static function read(array $entry): array
    {
        $contents = (string) file_get_contents($entry['file']);
        $title = '';
        $tags = [];
        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
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

        return ['title' => $title, 'tags' => $tags, 'removal' => self::removal($contents, $entry)];
    }

    /**
     * The version this entry states its subject stops working in, empty where
     * it states none.
     *
     * There is no field to read: the removal is a clause in the Description,
     * written freely — "will be removed in TYPO3 v15.0", "will be removed with
     * v15", "marked for removal in v15" are all in `.checkouts/14.3`, and the
     * sentence wraps, so the whole text is matched rather than a line. 44 of
     * the 75 deprecations of 14 state one that way and 31 state none, which is
     * why an empty answer here is the ordinary case rather than a failure.
     *
     * Only a Deprecation states one about itself. Ten entries of the other
     * three types carry the same clause about something else: `14.2`
     * Feature-109412 announces the replacement and says the mechanism it
     * replaces will be removed in v15.0, which is the deprecation's removal
     * and not the feature's.
     *
     * What a number has to survive is being later than the version the entry
     * was released in. Two things in the corpus are not this entry's removal
     * and read like one: a 13.3 deprecation says its subject "will be removed
     * with v5", which is Fluid standalone, and an entry recounting what an
     * earlier release already removed carries that release's number.
     *
     * @param array{version: string, type: string} $entry
     */
    private static function removal(string $contents, array $entry): string
    {
        if ($entry['type'] !== 'Deprecation') {
            return '';
        }

        preg_match_all(
            '/\b(?:will\s+be\s+)?(?:removed|removal)\s+(?:in|with|for|from)\s+(?:TYPO3\s+)?v?(\d+(?:\.\d+)?)\b/i',
            $contents,
            $matches,
        );
        foreach ($matches[1] as $stated) {
            if (version_compare($stated, $entry['version'], '>')) {
                return $stated;
            }
        }

        return '';
    }

    /** "ExperimentalBackendViewHelpers" as the words it is made of. */
    private static function words(string $camelCase): string
    {
        $spaced = preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $camelCase);

        return str_replace('-', ' ', $spaced ?? $camelCase);
    }
}
