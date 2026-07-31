<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads todo.md as the order of the work rather than as prose.
 *
 * The file always held that order, and only a person could see it. Sections sit
 * in it that look identical from the outside: the ones that come round every
 * session, the ones that are the queue, and the ones that are neither — the
 * environment table, the things deliberately not queued. A session that had
 * read nothing else could not tell which heading was the next piece of work.
 *
 * So each section opens with a head of labelled lines, in the same bold-label
 * shape requirements/ and decisions/ use for the fields they are read by:
 *
 *     ## Add the static-quality branch to `typo3-extension-testing`
 *
 *     **Serves:** R-SKL-2, feedback/2026-07-30-174423-….md
 *     **Every:** session
 *     **Run:** bin/cli backlog list
 *
 *     One paragraph: the next concrete step.
 *
 * `Serves:` is what makes it work rather than an idea. `Every:` is what makes
 * it recurring — without that line it is the queue, and the queue is an order.
 * `Run:` is the command the step starts from, and `bin/cli next` runs the ones
 * this repository owns rather than naming them.
 *
 * A cadence is `session` or a number of days, and the days are why the pair
 * exists: five sessions in an afternoon owe the notes five readings and the
 * release check none. What is measured in days carries `**Checked:** <date>`,
 * the session that ran it writes the date, and a todo that is not due is not
 * printed. Forgetting the date costs one repeat rather than a stale answer.
 *
 * The paragraph under the head is one step, because it is printed whole and a
 * session that has to read three of them to find where to start is reading
 * instead of working. Two steps are two todos, and the order between them is
 * the order they are in.
 */
final class Todo
{
    /** What a cadence can say, for the check that has to name it. */
    public const CADENCE = 'session, or a number of days';

    /**
     * The readings `bin/cli next` exists to perform. Exactly one recurring todo
     * has to name each: none and the command silently stops doing half its job,
     * two and it does it twice.
     */
    public const READINGS = ['bin/cli feedback list', 'bin/cli backlog list'];

    /**
     * Whether the clock has come round for a recurring todo. Nothing here
     * knows whether there is anything to do — that is the `Run:` command's
     * answer, and it costs a process to ask.
     *
     * An unreadable cadence is due: a todo nobody can date is one that gets
     * looked at, and the check below says so out loud in the same run.
     */
    public static function due(string $every, string $checked, ?string $today = null): bool
    {
        if (preg_match('/^(\d+) days?$/', $every, $matches) !== 1 || $checked === '') {
            return true;
        }

        $next = strtotime($checked . ' +' . $matches[1] . ' days');
        $now = strtotime($today ?? 'today');

        return $next === false || $now === false || $next <= $now;
    }

    public static function file(): string
    {
        return Paths::root() . '/todo.md';
    }

    /**
     * Every section, in the order the file has them, which is the order of the
     * work.
     *
     * @return array<int, array{title: string, kind: string, every: string, checked: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}>
     */
    public static function sections(): array
    {
        $parts = preg_split('/^## /m', (string) file_get_contents(self::file())) ?: [];

        return array_map(self::parse(...), array_slice($parts, 1));
    }

    /**
     * What comes round every session, in the order the file has it, and is
     * never deleted.
     *
     * @return array<int, array{title: string, kind: string, every: string, checked: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}>
     */
    public static function recurring(): array
    {
        return array_values(array_filter(
            self::sections(),
            static fn(array $s): bool => $s['kind'] === 'todo' && $s['every'] !== '',
        ));
    }

    /**
     * The queue: what is to be worked on once, in the order it is to be worked
     * on.
     *
     * @return array<int, array{title: string, kind: string, every: string, checked: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}>
     */
    public static function items(): array
    {
        return array_values(array_filter(
            self::sections(),
            static fn(array $s): bool => $s['kind'] === 'todo' && $s['every'] === '',
        ));
    }

    /**
     * What a session would otherwise rediscover and mistake for work: the
     * environment table, the answers that are standing rather than pending.
     *
     * @return array<int, array{title: string, kind: string, every: string, checked: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}>
     */
    public static function references(): array
    {
        return array_values(array_filter(self::sections(), static fn(array $s): bool => $s['kind'] === 'reference'));
    }

    /**
     * Everything the queue answers for, which is what turns an entry in
     * requirements/ or a note in feedback/ into work somebody has taken on.
     *
     * Read from the queue alone rather than from the file as a whole: the
     * section that lists what is deliberately *not* queued names ids too, and a
     * search over the text counts those as taken on. A recurring todo is not a
     * taking-on either — it is owed every session, which is the opposite of
     * somebody having it in hand.
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
     * Why what a todo names cannot be read, or null where it can.
     *
     * Four things are legitimate to serve, and each is checked against the place
     * that owns it rather than against a list kept here. A note is the one worth
     * catching: it is the reason the todo is in the queue, and the commit that
     * closes it deletes the file — so a todo still naming one is either
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
                : 'and that note is closed — the todo is done, or trims to the part that is left';
        }

        return 'which is none of a requirement, a scenario, a note, or a directory of this repository';
    }

    /**
     * @return array{title: string, kind: string, every: string, checked: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}
     */
    private static function parse(string $part): array
    {
        [$title, $rest] = array_pad(preg_split('/\R/', $part, 2) ?: [], 2, '');
        [$head, $body] = array_pad(preg_split('/\R\R/', ltrim((string) $rest), 2) ?: [], 2, '');

        // The horizontal rules between the sections belong to the section above
        // them the way a page break belongs to the page.
        $body = trim((string) preg_replace('/\R-{3,}\s*$/', '', (string) $body));

        $kind = '';
        $every = '';
        $checked = '';
        $serves = [];
        $run = [];
        $strays = [];
        foreach (preg_split('/\R/', trim((string) $head)) ?: [] as $line) {
            $line = trim($line);
            if ($line === '**Not an item.**') {
                $kind = 'reference';
                continue;
            }
            if (preg_match('/^\*\*([A-Z][a-z]+):\*\*\s*(.*)$/', $line, $matches) !== 1) {
                $strays[] = $line;
                continue;
            }

            match ($matches[1]) {
                'Serves' => $serves = array_values(array_filter(array_map(trim(...), explode(',', $matches[2])))),
                'Every' => $every = trim($matches[2]),
                'Checked' => $checked = trim($matches[2]),
                'Run' => $run[] = trim($matches[2]),
                default => $strays[] = $line,
            };
        }

        if ($kind === '' && $serves !== []) {
            $kind = 'todo';
        }

        return [
            'title' => trim((string) $title),
            'kind' => $kind,
            'every' => $every,
            'checked' => $checked,
            'serves' => $serves,
            'run' => $run,
            'head' => trim((string) $head),
            'strays' => $strays,
            'body' => $body,
        ];
    }
}
