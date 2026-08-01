<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads decisions/, where every decision is one file.
 *
 * It was one document of thirty entries, newest first, and by the end neither
 * half of that was true: two entries had arrived at the foot of the file, and
 * the labels a reader navigates by had drifted into thirteen spellings of four
 * things. An id now decides the group directory and the file name, the listings
 * are generated from the files, and the fields are a fixed set — so finding the
 * decision about versions is a directory rather than a search through prose.
 */
final class Decisions
{
    /**
     * The prefix of an id says what the decision is about, and that is the
     * directory it lives in. A new prefix is a new group and belongs here in
     * the same commit that writes the first entry under it.
     *
     * @var array<string, string>
     */
    public const GROUPS = [
        'AUD' => 'audience',
        'DIS' => 'discovery',
        'ANS' => 'answers',
        'KNW' => 'knowledge',
        'VER' => 'versions',
        'CAT' => 'catalog',
        'SCO' => 'scope',
        'GUI' => 'guides',
        'EVI' => 'evidence',
        'SKL' => 'task-skills',
        'FBK' => 'feedback',
        'DOC' => 'documentation',
        'COD' => 'code',
    ];

    /**
     * What an entry may be labelled with, in the order the labels have to
     * appear. The evidence that was available comes before what was decided on
     * it, the assumptions it rests on come after, and what would show it to be
     * wrong closes the entry — everything below that line arrived later than
     * the entry did.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['Evidence', 'Decided', 'Assumed', 'Wrong if'];

    /**
     * The labels a later session adds. `Tested on` and `Corrected on` carry the
     * date they were added on, and the status says which of them is there.
     *
     * @var array<int, string>
     */
    public const LATER_FIELDS = ['Tested on', 'Corrected on', 'Since then'];

    /** @var array<int, string> */
    public const STATUSES = ['standing', 'tested', 'corrected'];

    public static function directory(): string
    {
        return Paths::root() . '/decisions';
    }

    /**
     * Every decision, keyed by id and newest first — which is the order the
     * file this replaces claimed to be in.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, statement: string, fields: array<int, string>}>
     */
    public static function all(): array
    {
        $decisions = [];
        foreach (self::files() as $path) {
            $decision = self::read($path);
            $decisions[$decision['id']] = $decision;
        }

        uasort($decisions, static fn(array $a, array $b): int => [$b['date'], $a['id']] <=> [$a['date'], $b['id']]);

        return $decisions;
    }

    /**
     * The decisions of one group, or every one of them where no group is named.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, statement: string, fields: array<int, string>}>
     */
    public static function group(string $group): array
    {
        if ($group === '') {
            return self::all();
        }

        return array_filter(self::all(), static fn(array $d): bool => $d['group'] === $group);
    }

    /**
     * The table under a readme. Generated rather than written, because a
     * listing maintained by hand is a second copy of the directory that only
     * says what was true once. The root listing carries the group as well, and
     * is the one place every decision is in one order.
     */
    public static function listing(string $group): string
    {
        $root = $group === '';

        $entries = [];
        foreach (self::group($group) as $decision) {
            $entries[] = [
                'ref' => $decision['id'],
                'path' => ($root ? $decision['group'] . '/' : '') . $decision['file'],
                'says' => sprintf(
                    '%s · %s · %s',
                    $decision['title'],
                    $decision['date'],
                    $decision['status'],
                ),
            ];
        }

        return Listing::render($entries);
    }

    /**
     * Every decision file, readmes excluded.
     *
     * @return array<int, string>
     */
    public static function files(): array
    {
        $paths = [];
        foreach (self::GROUPS as $group) {
            foreach (glob(self::directory() . '/' . $group . '/*.md') ?: [] as $path) {
                if (basename($path) !== 'readme.md') {
                    $paths[] = $path;
                }
            }
        }
        sort($paths);

        return $paths;
    }

    /**
     * One file. Read on its own rather than through all(), which is keyed by
     * id and would hide the second file claiming one.
     *
     * @return array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, statement: string, fields: array<int, string>}
     */
    public static function read(string $path): array
    {
        $contents = (string) file_get_contents($path);

        preg_match('/^---\R(.*?)\R---\R/s', $contents, $matches);
        $frontMatter = $matches[1] ?? '';

        preg_match('/^# (\S+) — (.*)$/m', $contents, $heading);

        // The first paragraph after the heading, which is what was decided.
        // Everything below it is the evidence it was decided on.
        $body = (string) preg_replace('/^---\n.*?\n---\n\n# [^\n]*\n\n/s', '', $contents);
        $statement = (string) preg_split('/\R\R/', $body, 2)[0];

        return [
            'id' => self::frontMatterValue($frontMatter, 'id'),
            'group' => basename(dirname($path)),
            'file' => basename($path),
            'heading' => $heading[1] ?? '',
            'title' => $heading[2] ?? '',
            'date' => self::frontMatterValue($frontMatter, 'date'),
            'status' => self::frontMatterValue($frontMatter, 'status'),
            'statement' => trim(str_replace('**', '', $statement)),
            'fields' => self::fields($contents),
        ];
    }

    /**
     * The labels an entry carries, in the order it carries them, with the date
     * of a later addition folded away — `Tested on 2026-07-31` is the field
     * `Tested on`, and the date belongs to the entry rather than to the shape.
     *
     * @return array<int, string>
     */
    public static function fields(string $contents): array
    {
        preg_match_all('/^- \*\*([^*]+?):\*\*/m', $contents, $matches);

        return array_map(static function (string $label): string {
            foreach (self::LATER_FIELDS as $later) {
                if (str_starts_with($label, $later . ' ')) {
                    return $later;
                }
            }

            return $label;
        }, $matches[1]);
    }

    /**
     * Where a field sits in the order an entry is written in. Everything a
     * later session added ranks last and behind all of them, whichever of the
     * three it is.
     */
    public static function rank(string $field): int
    {
        $rank = array_search($field, self::FIELDS, true);

        return $rank === false ? count(self::FIELDS) : $rank;
    }

    /**
     * What a status promises the reader is a line further down: `tested` says a
     * later run confirmed the decision, `corrected` that one did not. The field
     * that carries it has to be there, or the status is a claim about nothing.
     */
    public static function fieldFor(string $status): string
    {
        return match ($status) {
            'tested' => 'Tested on',
            'corrected' => 'Corrected on',
            default => '',
        };
    }

    private static function frontMatterValue(string $frontMatter, string $key): string
    {
        return preg_match('/^' . $key . ':\s*(.*)$/m', $frontMatter, $matches) === 1 ? trim($matches[1]) : '';
    }
}
