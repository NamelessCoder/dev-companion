<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Paths;

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
     * it, the assumptions it rests on come after, what would show it to be wrong
     * follows them, and what would catch that happening closes the entry —
     * everything below that line arrived later than the entry did.
     *
     * `Covered by` is optional, because most entries here are about process and
     * nothing runs over them. Where a test would catch the **Wrong if**, naming
     * it is what turns the promise into something the suite keeps: a renamed
     * test then fails a check instead of quietly orphaning the claim.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['Evidence', 'Decided', 'Assumed', 'Wrong if', 'Covered by'];

    /**
     * The labels a later session adds. The dated ones belong to
     * `DecisionStatus`, which is what says whether a reader may still build on
     * the entry; `Since then` carries what followed without a date of its own
     * and says nothing about that.
     *
     * @return array<int, string>
     */
    public static function laterFields(): array
    {
        return [...DecisionStatus::lines(), 'Since then'];
    }

    /**
     * The dated lines an entry may carry, most recent last. The status names
     * the last of them.
     *
     * @param array<int, string> $fields
     * @return array<int, string>
     */
    public static function datedLines(array $fields): array
    {
        return array_values(array_filter(
            $fields,
            static fn(string $field): bool => in_array($field, DecisionStatus::lines(), true),
        ));
    }

    public static function directory(): string
    {
        return Paths::root() . '/decisions';
    }

    /**
     * Every decision, keyed by id and newest first — which is the order the
     * file this replaces claimed to be in.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, revokedBy: string, statement: string, fields: array<int, string>}>
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
     * @return array<string, array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, revokedBy: string, statement: string, fields: array<int, string>}>
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
        // Two lists rather than one, because the first question a reader has of
        // a listing this long is which of it is still true. A revoked entry is
        // kept and read — the wrong assumption is the useful part — but it is
        // not something to build on, and mixed into one list it looked exactly
        // like something to build on.
        $sections = ['' => [], 'Revoked, and kept as the record' => []];
        foreach (self::group($group) as $decision) {
            $status = DecisionStatus::tryFrom($decision['status']);
            $entry = [
                'ref' => $decision['id'],
                'path' => ($group === '' ? $decision['group'] . '/' : '') . $decision['file'],
                'says' => sprintf('%s · %s', $decision['title'], $decision['date'])
                    . ($status === DecisionStatus::Confirmed ? ' · confirmed' : '')
                    . ($decision['revokedBy'] === '' ? '' : ' → ' . $decision['revokedBy']),
            ];
            $sections[$status?->stillHolds() === false ? 'Revoked, and kept as the record' : ''][] = $entry;
        }

        $listing = '';
        foreach ($sections as $heading => $entries) {
            if ($entries === []) {
                continue;
            }
            $listing .= ($heading === '' ? '' : '### ' . $heading . "\n\n") . Listing::render($entries) . "\n";
        }

        return rtrim($listing, "\n") . "\n";
    }

    /**
     * Every decision file, readmes excluded.
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
     * @return array{id: string, group: string, file: string, heading: string, title: string, date: string, status: string, revokedBy: string, statement: string, fields: array<int, string>}
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
            // What replaced it, where a revoked entry has a successor. A reader
            // who reaches a dead entry needs somewhere to go next, and prose
            // said it on four of them and nowhere a listing could see.
            'revokedBy' => self::frontMatterValue($frontMatter, 'revokedBy'),
            'statement' => trim(str_replace('**', '', $statement)),
            'fields' => self::fields($contents),
        ];
    }

    /**
     * The sections an entry carries, in the order it carries them, with the
     * date of a later addition folded away — `Revoked on 2026-07-31` is the
     * section `Revoked on`, and the date belongs to the entry rather than to
     * the shape.
     *
     * They were bullets carrying a bold label until 2026-08-02, and the label
     * repeated: half the entries make more than one decision and a fifth of
     * them rest on more than one assumption, so `- **Decided:**` was written
     * seven times in the worst case. A section says it once. See `D-DOC-3`.
     *
     * @return array<int, string>
     */
    public static function fields(string $contents): array
    {
        preg_match_all('/^## (.+)$/m', $contents, $matches);

        return array_map(static function (string $label): string {
            foreach (self::laterFields() as $later) {
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

    /** The dated line the named status promises, or '' where it names none. */
    public static function fieldFor(string $status): string
    {
        return DecisionStatus::tryFrom($status)?->line() ?? '';
    }

    private static function frontMatterValue(string $frontMatter, string $key): string
    {
        return preg_match('/^' . $key . ':\s*(.*)$/m', $frontMatter, $matches) === 1 ? trim($matches[1]) : '';
    }
}
