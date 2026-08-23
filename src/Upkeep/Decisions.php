<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

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
    /**
     * The heading the revoked entries stand under, wherever a listing is
     * written. They keep a run of their own rather than a marker in the one
     * above, which is what the listing's own comment says why.
     */
    public const REVOKED = 'Revoked, and kept as the record';

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
     * The tests an entry is held by are `coveredBy` in its front matter, because
     * nothing runs over them. Where a test would catch the **Wrong if**, naming
     * it is what turns the promise into something the suite keeps: a renamed
     * test then fails a check instead of quietly orphaning the claim.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['Evidence', 'Decided', 'Assumed', 'Wrong if'];

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

    /**
     * The entries a test holds, which is what a failing one prints.
     *
     * Read from `coveredBy`, which the test's own `#[Decision]` attributes are
     * what wrote — so this answers with the entries that test declared, and a
     * session standing in a red test is sent to them.
     *
     * @return list<array{id: string, title: string, file: string}>
     */
    public static function restingOn(string $class, string $method): array
    {
        $held = [];
        foreach (self::all() as $decision) {
            if (!in_array($class . '::' . $method, $decision['coveredBy'], true)) {
                continue;
            }
            $held[] = [
                'id' => $decision['id'],
                'title' => $decision['title'],
                'file' => 'decisions/' . $decision['group'] . '/' . $decision['file'],
            ];
        }

        return $held;
    }

    /**
     * Entries a reader pays more for the history of than for the decision,
     * longest history first.
     *
     * Not a defect and nothing fails on it: `Confirmed on` is what a reading
     * that held leaves, and an entry stating a rule the repository applies
     * often collects one per application. It is a reading cost, and it is
     * concentrated — 85% of entries carry no dated section or one, and the
     * ones measured here held 30% of every later-reading line in the corpus on
     * 2026-08-22.
     *
     * @return array<int, array{id: string, entry: int, later: int, dated: int}>
     */
    public static function outgrown(): array
    {
        $outgrown = [];
        foreach (self::all() as $decision) {
            $lines = file(self::directory() . '/' . $decision['group'] . '/' . $decision['file']) ?: [];
            $foot = count($lines);
            foreach ($lines as $index => $line) {
                if (preg_match('/^## (' . implode('|', self::laterFields()) . ')\b/', $line) === 1) {
                    $foot = $index;
                    break;
                }
            }
            $later = count($lines) - $foot;
            if ($later <= $foot) {
                continue;
            }
            $outgrown[] = [
                'id' => $decision['id'],
                'entry' => $foot,
                'later' => $later,
                'dated' => count(array_filter(
                    self::fields(implode('', $lines)),
                    static fn(string $field): bool => in_array($field, self::laterFields(), true),
                )),
            ];
        }

        usort($outgrown, static fn(array $a, array $b): int => $b['later'] <=> $a['later']);

        return $outgrown;
    }

    /**
     * Entries pointing at this repository's code that no test holds, most
     * references first.
     *
     * Not a defect and nothing fails on it. Most entries here are about process
     * and nothing runs over them, so an entry may name `Scope::of()` in its
     * evidence while deciding something no test could keep — and a check that
     * demanded a `coveredBy` would be answered with a test name chosen to
     * satisfy it.
     *
     * What it reports is the one coupling that actually holds an entry to the
     * code: a test named in `coveredBy` fails when the behaviour moves,
     * and `DecisionsTest::everyTestADecisionNamesExists` fails when the test
     * goes with it. Read on 2026-08-22, the three entries found stale that day
     * carried no such name and the two whose code had moved under them carried
     * one and were right.
     *
     * @return array<int, array{id: string, names: int, status: string}>
     */
    public static function uncovered(): array
    {
        $classes = Sources::classes();

        $uncovered = [];
        foreach (self::all() as $decision) {
            if ($decision['coveredBy'] !== []) {
                continue;
            }
            $body = (string) file_get_contents(self::directory() . '/' . $decision['group'] . '/' . $decision['file']);
            preg_match_all('/`(\w+)::\w+/', $body, $matches);
            $named = array_filter(array_unique($matches[1]), static fn(string $class): bool => isset($classes[$class]));
            if ($named === []) {
                continue;
            }
            $uncovered[] = [
                'id' => $decision['id'],
                'names' => count($named),
                'status' => $decision['status'],
            ];
        }

        usort($uncovered, static fn(array $a, array $b): int => [$b['names'], $a['id']] <=> [$a['names'], $b['id']]);

        return $uncovered;
    }

    /**
     * One entry's file as `coveredBy` generated from the tests would write it.
     *
     * The `#[Decision]` attributes are the source and this is the copy, which
     * is why the whole file comes back rather than the list: `decisions:cover`
     * writes what this returns and `decisions:check` compares against it, so
     * one implementation decides what the front matter says.
     *
     * An entry no test names keeps a `coveredBy: []` it was written with —
     * that empty list is a statement that nothing can hold the entry, and the
     * absence of the key is that nobody has asked.
     *
     * @param list<string> $tests
     */
    public static function covered(string $contents, array $tests): string
    {
        $written = $tests === []
            ? "coveredBy: []\n"
            : "coveredBy:\n" . implode('', array_map(static fn(string $test): string => '  - ' . $test . "\n", $tests));

        // A callback rather than a replacement string: a `$` in one would be
        // read as a group reference, and what is written here is a name.
        $carries = '/^coveredBy:(?: \[])?\R(?:  - .*\R)*/m';
        if (preg_match($carries, $contents) === 1) {
            return (string) preg_replace_callback($carries, static fn(): string => $written, $contents, 1);
        }
        if ($tests === []) {
            return $contents;
        }

        // The key is new, and goes at the foot of the front matter: the first
        // `---` on a line of its own, the opening one standing at the very
        // start of the file.
        return (string) preg_replace_callback('/\R---\R/', static fn(): string => "\n" . $written . "---\n", $contents, 1);
    }

    /**
     * A later label opening a line in bold, which is a section written as a
     * paragraph.
     *
     * Derived from `laterFields()` rather than spelled out, so a fourth label
     * is covered by being added there. Anchored to the start of a line: a
     * **Since then** named inside a sentence is a reference to a section and
     * not one.
     */
    public static function labelAsAParagraph(): string
    {
        return '/^(- )?\*\*(' . implode('|', array_map(preg_quote(...), self::laterFields())) . ')\*\*/m';
    }

    public static function directory(): string
    {
        return Paths::root() . '/decisions';
    }

    /**
     * Every decision, keyed by id and newest first — which is the order the
     * file this replaces claimed to be in.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, coveredBy: list<string>, revisited: bool, statement: string, fields: array<int, string>}>
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
     * The ids more than one file claims, each with the files claiming it,
     * relative to the root. `all()` is keyed by id and keeps whichever of the
     * two it read last, so the collision is only visible from the files.
     *
     * @return array<string, array<int, string>>
     */
    public static function duplicates(): array
    {
        $claims = [];
        foreach (self::files() as $path) {
            $decision = self::read($path);
            $claims[$decision['id']][] = basename(self::directory())
                . '/' . $decision['group'] . '/' . $decision['file'];
        }

        return array_filter($claims, static fn(array $paths): bool => count($paths) > 1);
    }

    /**
     * What the failure says, which is all a reader of it gets: `todo:home`
     * prints the tail of a red `composer ci` and adds nothing to it. So the id,
     * both files and the command that repairs it stand in the message rather
     * than on the page the reader would have to know to open — `D-FBK-046`.
     *
     * @param array<string, array<int, string>> $duplicates
     */
    public static function collision(array $duplicates): string
    {
        if ($duplicates === []) {
            return '';
        }

        $message = "two decision files claim the same id\n";
        foreach ($duplicates as $id => $paths) {
            $message .= "\n    " . $id . "\n";
            foreach ($paths as $path) {
                $message .= '        ' . $path . "\n";
            }
            $message .= '    bin/cli decisions:renumber <the file this branch added>' . "\n";
        }

        return $message . "\nName the file rather than the id: both carry it, so the id says which number\n"
            . 'is meant and not which entry moves. The command names what it cannot move.';
    }

    /**
     * The decisions of one group, or every one of them where no group is named.
     *
     * @return array<string, array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, coveredBy: list<string>, revisited: bool, statement: string, fields: array<int, string>}>
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
        // The whole of it is read by group rather than as one run of every
        // entry: an id names its group in the prefix, which is what a commit
        // and a requirement's `restsOn` arrive with. Inside a group the order
        // is unchanged and newest first, and what was decided lately across all
        // of them is `bin/cli decisions:list`.
        $sections = $group === '' ? array_fill_keys(array_values(self::GROUPS), []) : ['' => []];
        $sections[self::REVOKED] = [];

        foreach (self::group($group) as $decision) {
            $status = DecisionStatus::tryFrom($decision['status']);
            $entry = [
                'ref' => $decision['id'],
                'path' => ($group === '' ? $decision['group'] . '/' : '') . $decision['file'],
                'says' => sprintf('%s · %s', $decision['title'], $decision['date'])
                    . ($status === DecisionStatus::Confirmed ? ' · confirmed' : '')
                    . ($decision['revokedBy'] === '' ? '' : ' → ' . $decision['revokedBy']),
            ];
            $sections[$status?->stillHolds() === false ? self::REVOKED : ($group === '' ? $decision['group'] : '')][] = $entry;
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
     * @return array{id: string, group: string, file: string, heading: string, written: string, title: string, date: string, status: string, revokedBy: string, coveredBy: list<string>, revisited: bool, statement: string, fields: array<int, string>}
     */
    public static function read(string $path): array
    {
        $contents = (string) file_get_contents($path);
        $head = Entry::head($contents);
        $matter = $head['matter'];

        return [
            'id' => Entry::value($matter, 'id'),
            'group' => basename(dirname($path)),
            'file' => basename($path),
            'heading' => $head['heading'],
            'written' => $head['written'],
            'title' => Entry::value($matter, 'title'),
            'date' => Entry::value($matter, 'date'),
            'status' => Entry::value($matter, 'status'),
            // What replaced it, where a revoked entry has a successor. A reader
            // who reaches a dead entry needs somewhere to go next, and prose
            // said it on four of them and nowhere a listing could see.
            'revokedBy' => Entry::value($matter, 'revokedBy'),
            'coveredBy' => Entry::names($matter, 'coveredBy'),
            'revisited' => self::revisited($contents),
            'statement' => $head['statement'],
            'fields' => self::fields($contents),
        ];
    }

    /**
     * Whether somebody has been back to this entry since it was written.
     *
     * `status` cannot answer it. `confirmed` and `revoked` are the two readings
     * that settle a **Wrong if**, and a reading that settles neither leaves the
     * entry `open` — indistinguishable from one nobody has opened. What tells
     * the two apart is a **Since then**, which the format already carries for
     * what followed without a date of its own.
     *
     * One form, since the 51 labels still written as a bold paragraph were
     * converted and `bin/cli decisions:check` began failing on that spelling.
     * A **Since then** named inside a sentence is a reference to one and not
     * one, which is why the heading is what this matches.
     */
    private static function revisited(string $contents): bool
    {
        return preg_match('/^## Since then\b/m', $contents) === 1;
    }

    /**
     * The sections an entry carries, in the order it carries them, with the
     * date of a later addition folded away — `Revoked on 2026-07-31` is the
     * section `Revoked on`, and the date belongs to the entry rather than to
     * the shape.
     *
     * They were bullets carrying a bold label, and the label repeated once an
     * entry made more than one decision. A section says it once — `D-DOC-003`.
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

}
