<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;

/**
 * Reads requirements/, where every requirement is one file.
 *
 * The file used to be one document, and five ids had been handed out twice
 * before anybody noticed — which is what a list nobody can index looks like
 * from the inside. An id now decides the group directory and the file name, so
 * the check below is a comparison rather than a search, and the group listing
 * is generated from the files instead of maintained beside them.
 */
final class Requirements
{
    /**
     * The prefix of an id says what the requirement is about, and that is the
     * directory it lives in. A new prefix is a new group and belongs here in
     * the same commit that writes the first entry under it.
     *
     * @var array<string, string>
     */
    public const GROUPS = [
        'AUD' => 'audience',
        'DIS' => 'discovery',
        'ANS' => 'answers',
        'DOC' => 'documentation',
        'SKL' => 'task-skills',
        'PRJ' => 'project',
        'SCO' => 'scope',
        'GUI' => 'guides',
        'FBK' => 'feedback',
        'KNW' => 'knowledge',
        'COD' => 'code',
    ];

    public static function directory(): string
    {
        return Paths::root() . '/requirements';
    }

    /**
     * Every requirement, keyed and sorted by id.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, status: string, statement: string, heldBy: string, tests: array<int, string>}>
     */
    public static function all(): array
    {
        $requirements = [];
        foreach (self::files() as $path) {
            $requirement = self::read($path);
            $requirements[$requirement['id']] = $requirement;
        }

        uksort($requirements, static fn(string $a, string $b): int => strnatcmp($a, $b));

        return $requirements;
    }

    /**
     * The requirements of one group, in the order its listing shows them.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, status: string, statement: string, heldBy: string, tests: array<int, string>}>
     */
    public static function group(string $group): array
    {
        return array_filter(self::all(), static fn(array $r): bool => $r['group'] === $group);
    }

    /**
     * The table under a group readme: what must hold, and what state it is in.
     * Generated rather than written, because a listing maintained by hand is a
     * second copy of the directory that only says what was true once.
     */
    public static function listing(string $group): string
    {
        $entries = [];
        foreach (self::group($group) as $requirement) {
            $state = self::state($requirement);
            $entries[] = [
                'ref' => $requirement['id'],
                'path' => $requirement['file'],
                'says' => sprintf(
                    '%s · %s',
                    $requirement['title'],
                    $state === 'open' ? '**open**' : $state,
                ),
            ];
        }

        return Listing::render($entries);
    }

    /**
     * `open` is the backlog. Everything else is held by the tests it names, or
     * says outright that nothing holds it — the third state is the one worth
     * seeing in a listing, because it looks exactly like the first from afar.
     *
     * The three words are the state itself, and a reader that wants one of them
     * emphasised says so where it renders. Bolding `open` in here is what made
     * every caller strip the asterisks back off.
     *
     * @param array{status: string, heldBy: string, tests: array<int, string>} $requirement
     */
    public static function state(array $requirement): string
    {
        if ($requirement['status'] === 'open') {
            return 'open';
        }

        return $requirement['tests'] === [] ? 'not guarded' : 'held';
    }

    /**
     * Every requirement file, group readmes excluded.
     *
     * @return array<int, string>
     */
    public static function files(): array
    {
        $directories = array_values(array_filter(
            array_map(static fn(string $group): string => self::directory() . '/' . $group, self::GROUPS),
            is_dir(...),
        ));
        if ($directories === []) {
            return [];
        }

        $paths = [];
        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md')->notName('readme.md')->sortByName() as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /**
     * One file. Read on its own rather than through all(), which is keyed by
     * id and would hide the second file claiming one.
     *
     * @return array{id: string, group: string, file: string, heading: string, title: string, status: string, statement: string, heldBy: string, tests: array<int, string>}
     */
    public static function read(string $path): array
    {
        $contents = (string) file_get_contents($path);

        preg_match('/^---\R(.*?)\R---\R/s', $contents, $matches);
        $frontMatter = $matches[1] ?? '';

        preg_match('/^# (\S+) — (.*)$/m', $contents, $heading);

        // The first paragraph after the heading, which is the sentence that has
        // to hold. Everything below it is why it is one.
        $body = (string) preg_replace('/^---\n.*?\n---\n\n# [^\n]*\n\n/s', '', $contents);
        $statement = (string) preg_split('/\R\R/', $body, 2)[0];

        // A test method where one holds it, a whole test class where the class
        // is the answer — `VersionsTest` in full is a claim about every method
        // in it, and naming them one at a time would go stale on the next one
        // written.
        $heldBy = self::field($contents, 'Held by');
        preg_match_all('/`(\w+Test(?:::\w+)?)`/', $heldBy, $tests);

        return [
            'id' => self::frontMatterValue($frontMatter, 'id'),
            'group' => basename(dirname($path)),
            'file' => basename($path),
            'heading' => $heading[1] ?? '',
            'title' => $heading[2] ?? '',
            'status' => self::frontMatterValue($frontMatter, 'status'),
            'statement' => trim(str_replace('**', '', $statement)),
            'heldBy' => $heldBy,
            'tests' => $tests[1],
        ];
    }

    private static function frontMatterValue(string $frontMatter, string $key): string
    {
        return preg_match('/^' . $key . ':\s*(.*)$/m', $frontMatter, $matches) === 1 ? trim($matches[1]) : '';
    }

    /** The paragraph under a bold label, wrapped lines folded back together. */
    private static function field(string $contents, string $label): string
    {
        $start = strpos($contents, '**' . $label . ':**');
        if ($start === false) {
            return '';
        }

        $marker = '**' . $label . ':**';
        $paragraph = (string) preg_split('/\R\R/', substr($contents, $start + strlen($marker)), 2)[0];

        return trim((string) preg_replace('/\s+/', ' ', $paragraph));
    }
}
