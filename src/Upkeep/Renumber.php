<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;

/**
 * Moving an entry's file — to another number, or under the title it now has —
 * and naming what nobody here can move.
 *
 * The renumbering is the dangerous part rather than the collision: twice the
 * files naming the old number did not all mean the same entry — `R-PRJ-008`
 * rested on the `D-ANS-013` that kept its number while five other files meant
 * the one that became `D-ANS-015` — so a search and replace over the id is
 * silently wrong there. A reference is therefore moved only where the file
 * itself says which entry is meant, which is a link path, and every other one is
 * reported: moved or named, never silently left — `D-DOC-015`.
 */
final class Renumber
{
    /** A file larger than this is not prose somebody wrote an id into. */
    private const LARGEST = 2_000_000;

    /**
     * What one call did: the entry it moved, the references it settled from a
     * link path, and the ones only a person can.
     *
     * @return array{from: string, to: string, file: string, moved: list<array{file: string, line: int, text: string}>, named: list<array{file: string, line: int, text: string}>}
     */
    public static function decision(string $root, string $file, string $to): array
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException($file . ' is no decision in ' . $root . '/decisions/');
        }

        // Both sides of every path comparison below, in one spelling. The entry
        // is recognised among the documents by being the same path, and a
        // caller naming it relatively would leave its own id line unmoved.
        $root = (string) (realpath($root) ?: $root);
        $file = (string) realpath($file);

        // The entry the caller named rather than the id it carries: two files
        // claim one id after a rebase, which is the only state this exists for,
        // and the caller is the one who knows which of them moves.
        $from = Decisions::read($file)['id'];
        if ($from === $to) {
            throw new \InvalidArgumentException($from . ' already has that number');
        }
        if (substr($from, 0, 6) !== substr($to, 0, 6)) {
            // The prefix names the group directory, so this would be a
            // re-filing rather than a renumbering, and what the entry is about
            // moves with it.
            throw new \InvalidArgumentException($from . ' and ' . $to . ' are not in one group');
        }
        $taken = self::taken($root, $to);
        if ($taken !== '') {
            throw new \InvalidArgumentException($to . ' is already ' . self::relative($root, $taken));
        }

        $old = basename($file);
        $new = self::name($old, $to);
        $entry = dirname($file) . '/' . $new;
        rename($file, $entry);

        $moved = [];
        $named = [];
        foreach (self::documents($root) as $path) {
            $contents = (string) file_get_contents($path);
            if (!str_contains($contents, $from) && !str_contains($contents, $old)) {
                continue;
            }

            $rewritten = self::rewrite(
                $contents,
                $from,
                $to,
                $old,
                $new,
                $path === $entry,
                self::relative($root, $path),
                $moved,
                $named,
            );
            if ($rewritten !== $contents) {
                file_put_contents($path, $rewritten);
            }
        }

        return [
            'from' => $from,
            'to' => $to,
            'file' => self::relative($root, $entry),
            'moved' => $moved,
            'named' => $named,
        ];
    }

    /**
     * One entry, filed under the name its title says — `D-DOC-047`.
     *
     * A number never moves here, so nothing has to be told apart: an id is
     * ambiguous across two entries and a file name is not, which is why this
     * rewrites every reference to the name rather than reporting the ones it
     * cannot settle. Where the file is already where it belongs it is left
     * alone and nothing is rewritten.
     *
     * @return array{from: string, to: string, references: int}
     */
    public static function refile(string $root, string $file, string $id, string $title): array
    {
        $root = (string) (realpath($root) ?: $root);
        $file = (string) realpath($file);

        $old = basename($file);
        $new = Entry::fileName($id, $title);
        if ($old === $new) {
            return ['from' => $old, 'to' => $old, 'references' => 0];
        }

        rename($file, dirname($file) . '/' . $new);

        $references = 0;
        foreach (self::documents($root) as $path) {
            $contents = (string) file_get_contents($path);
            if (!str_contains($contents, $old)) {
                continue;
            }

            $references += substr_count($contents, $old);
            file_put_contents($path, str_replace($old, $new, $contents));
        }

        return ['from' => $old, 'to' => $new, 'references' => $references];
    }

    /**
     * One document, line by line: what the line settles is rewritten, what it
     * does not is recorded where it stands.
     *
     * A line naming the entry's own file settles the id on it, which is every
     * markdown link and the reference definition a generated listing ends with.
     * A reference-style link is the one form whose path sits on another line, so
     * a file carrying that definition settles its own usages of it too. The
     * entry's own front matter and heading are the id rather than a reference to
     * it.
     *
     * @param list<array{file: string, line: int, text: string}> $moved
     * @param list<array{file: string, line: int, text: string}> $named
     */
    private static function rewrite(
        string $contents,
        string $from,
        string $to,
        string $old,
        string $new,
        bool $isTheEntry,
        string $relative,
        array &$moved,
        array &$named,
    ): string {
        // The lookahead is the letter suffix: `R-ANS-008b` is the entry split
        // off `R-ANS-008` and never a spelling of it — D-DOC-005.
        $id = '/\b' . preg_quote($from, '/') . '(?![0-9a-z])/';
        $defines = preg_match('/^\[' . preg_quote($from, '/') . '\]:\s*\S*' . preg_quote($old, '/') . '\s*$/m', $contents) === 1;
        $usage = '/\[`?' . preg_quote($from, '/') . '`?\]\[' . preg_quote($from, '/') . '\]/';

        $lines = explode("\n", $contents);
        foreach ($lines as $number => $line) {
            if (preg_match($id, $line) !== 1 && !str_contains($line, $old)) {
                continue;
            }

            $settled = str_contains($line, $old)
                || ($defines && preg_match($usage, $line) === 1)
                || ($isTheEntry && preg_match('/^(?:id:\s|# )/', $line) === 1);

            if ($settled) {
                $lines[$number] = (string) preg_replace($id, $to, str_replace($old, $new, $line));
                $moved[] = ['file' => $relative, 'line' => $number + 1, 'text' => trim($lines[$number])];
                continue;
            }

            $named[] = ['file' => $relative, 'line' => $number + 1, 'text' => trim($line)];
        }

        return implode("\n", $lines);
    }

    /**
     * Every decision file a caller's argument names: a path names itself, an id
     * names each file carrying it, and a bare file name names the one called
     * that.
     *
     * More than one comes back only in the state this class exists for — two
     * branches cut from one `main` handed out one id — and which of them moves
     * is the caller's to say, because the entry already on `main` keeps its
     * number.
     *
     * @return list<string>
     */
    public static function files(string $root, string $decision): array
    {
        if (is_file($decision)) {
            return [$decision];
        }

        $isId = preg_match('/^D-[A-Z]{3}-\d{3}[a-z]?$/', $decision) === 1;
        $directory = $root . '/decisions/' . ($isId ? Decisions::GROUPS[substr($decision, 2, 3)] ?? '' : '');
        if (!is_dir($directory)) {
            return [];
        }

        $name = $isId ? strtolower(substr($decision, 2)) . '-*.md' : basename($decision);
        $depth = $isId ? 0 : 1;

        $paths = [];
        foreach (Finder::create()->files()->in($directory)->depth('<= ' . $depth)->name($name)->sortByName() as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /** The file the number is already taken by, or '' where it is free. */
    private static function taken(string $root, string $id): string
    {
        return self::files($root, $id)[0] ?? '';
    }

    /**
     * The next number free in a group, which is one past the highest rather
     * than the first gap. An id is never reused, and nothing reads a gap:
     * `decisions:check` reads the width, the group, the heading, the date, the
     * status, the field order and the duplicates, and never one number against
     * the next.
     */
    public static function next(string $root, string $prefix): string
    {
        $directory = $root . '/decisions/' . (Decisions::GROUPS[$prefix] ?? '');
        $highest = 0;
        if (is_dir($directory)) {
            foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->notName('readme.md') as $file) {
                preg_match('/^[a-z]+-(\d+)/', $file->getBasename(), $number);
                $highest = max($highest, (int) ($number[1] ?? 0));
            }
        }

        if ($highest >= 999) {
            throw new \RuntimeException($prefix . ' has reached 999, and a number is three digits wide — D-DOC-005');
        }

        return sprintf('D-%s-%03d', $prefix, $highest + 1);
    }

    /** The file name the entry takes under its new id, its slug untouched. */
    private static function name(string $old, string $to): string
    {
        return (string) preg_replace(
            '/^[a-z]+-\d+[a-z]?-/',
            strtolower(substr($to, 2, 3)) . '-' . substr($to, 6) . '-',
            $old,
        );
    }

    /**
     * Everything this repository writes an id into, which is every file it
     * keeps. `vendor/` is somebody else's, and `.checkouts/` and the worktrees
     * are other checkouts that a dot in the name already excludes.
     *
     * @return list<string>
     */
    private static function documents(string $root): array
    {
        $paths = [];
        foreach (Finder::create()->files()->in($root)->exclude(['vendor', 'node_modules'])->sortByName() as $file) {
            if ($file->getSize() <= self::LARGEST) {
                $paths[] = $file->getPathname();
            }
        }

        return $paths;
    }

    private static function relative(string $root, string $path): string
    {
        return str_starts_with($path, $root . '/') ? substr($path, strlen($root) + 1) : $path;
    }
}
