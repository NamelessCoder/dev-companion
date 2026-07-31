<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads todo.md as the order of the work rather than as prose.
 *
 * The file always held that order, and only a person could see it. Three kinds
 * of section sit in it and they look identical from the outside: the standing
 * ones that are never deleted, the items that are the queue, and the sections
 * that are neither — the environment table, the list of things deliberately not
 * queued. A session that had read nothing else could not tell which heading was
 * the next piece of work, and `Unresolved` could only ask whether the file
 * contained an id anywhere, which the "not queued, and deliberately so" section
 * answers as loudly as an item does.
 *
 * So each section opens with one line that says which of the three it is, in
 * the same bold-label shape requirements/ and decisions/ use for the fields
 * they are read by. The prose underneath is untouched: it is what a person
 * reads, and the line above it is what tells a reader where to start.
 */
final class Todo
{
    /**
     * What a standing section is answered by. `bin/cli next` runs the first two
     * itself — they are the two readings a session owes before it picks
     * anything up — and prints the commands of the third, which is a question
     * only the network can answer.
     */
    public const STANDING = ['notes', 'backlog', 'by hand'];

    public static function file(): string
    {
        return Paths::root() . '/todo.md';
    }

    /**
     * Every section, in the order the file has them, which is the order of the
     * work.
     *
     * @return array<int, array{title: string, kind: string, runs: string, serves: array<int, string>, marker: string, body: string, commands: array<int, string>}>
     */
    public static function sections(): array
    {
        $parts = preg_split('/^## /m', (string) file_get_contents(self::file())) ?: [];

        return array_map(self::parse(...), array_slice($parts, 1));
    }

    /**
     * The standing sections, which are never deleted and are read before
     * anything else.
     *
     * @return array<int, array{title: string, kind: string, runs: string, serves: array<int, string>, marker: string, body: string, commands: array<int, string>}>
     */
    public static function standing(): array
    {
        return array_values(array_filter(self::sections(), static fn (array $s): bool => $s['kind'] === 'standing'));
    }

    /**
     * The queue: what is to be worked on, in the order it is to be worked on.
     *
     * @return array<int, array{title: string, kind: string, runs: string, serves: array<int, string>, marker: string, body: string, commands: array<int, string>}>
     */
    public static function items(): array
    {
        return array_values(array_filter(self::sections(), static fn (array $s): bool => $s['kind'] === 'item'));
    }

    /**
     * Everything the queue answers for, which is what turns an entry in
     * requirements/ or a note in feedback/ into work somebody has taken on.
     *
     * Read from the `Serves:` lines rather than from the file as a whole: the
     * section that lists what is deliberately *not* queued names ids too, and a
     * search over the text counts those as taken on.
     *
     * @return array<int, string>
     */
    public static function serves(): array
    {
        $served = [];
        foreach (self::items() as $item) {
            foreach ($item['serves'] as $what) {
                $served[$what] = true;
            }
        }

        return array_keys($served);
    }

    /**
     * Why what an item names cannot be read, or null where it can.
     *
     * Four things are legitimate to serve, and each is checked against the place
     * that owns it rather than against a list kept here. A note is the one worth
     * catching: it is the reason the item is in the queue, and the commit that
     * closes it deletes the file — so an item still naming one is either
     * finished or has a part left that nobody has trimmed it down to.
     */
    public static function unreadable(string $what): ?string
    {
        if (preg_match('/^R-[A-Z]{3}-\d+[a-z]?$/', $what) === 1) {
            return isset(Requirements::all()[$what]) ? null : 'which no requirement has';
        }

        if (preg_match('/^[A-Z]+-\d+$/', $what) === 1) {
            $scenarios = Scenarios::load() + Scenarios::contracts();

            return isset($scenarios[$what]) ? null : 'which no scenario has';
        }

        if (str_ends_with($what, '/')) {
            return is_dir(Paths::root() . '/' . $what) ? null : 'which is not a directory of this repository';
        }

        if (str_starts_with($what, 'feedback/')) {
            return is_file(Paths::root() . '/' . $what)
                ? null
                : 'and that note is closed — the item is done, or trims to the part that is left';
        }

        return 'which is none of a requirement, a scenario, a note, or a directory of this repository';
    }

    /**
     * @return array{title: string, kind: string, runs: string, serves: array<int, string>, marker: string, body: string, commands: array<int, string>}
     */
    private static function parse(string $part): array
    {
        [$title, $rest] = array_pad(preg_split('/\R/', $part, 2) ?: [], 2, '');
        [$marker, $body] = array_pad(preg_split('/\R\R/', ltrim((string) $rest), 2) ?: [], 2, '');
        $marker = trim((string) $marker);

        // The horizontal rules between the standing sections belong to the
        // section above them the way a page break belongs to the page.
        $body = trim((string) preg_replace('/\R-{3,}\s*$/', '', (string) $body));

        $kind = '';
        $runs = '';
        $serves = [];
        if (preg_match('/^\*\*Standing:\*\*(.*)$/', $marker, $matches) === 1) {
            $kind = 'standing';
            $runs = trim($matches[1]);
        } elseif (preg_match('/^\*\*Serves:\*\*(.*)$/', $marker, $matches) === 1) {
            $kind = 'item';
            $serves = array_values(array_filter(array_map(trim(...), explode(',', $matches[1]))));
        } elseif ($marker === '**Not an item.**') {
            $kind = 'reference';
        }

        return [
            'title' => trim((string) $title),
            'kind' => $kind,
            'runs' => $runs,
            'serves' => $serves,
            'marker' => $marker,
            'body' => $body,
            'commands' => self::commands($body),
        ];
    }

    /**
     * The indented command lines of a section. A standing section nobody can
     * answer for the reader is worth exactly what it tells them to run.
     *
     * @return array<int, string>
     */
    private static function commands(string $body): array
    {
        $commands = [];
        foreach (preg_split('/\R/', $body) ?: [] as $line) {
            if (preg_match('/^ {4}(\S.*)$/', $line, $matches) === 1) {
                $commands[] = trim($matches[1]);
            }
        }

        return $commands;
    }
}
