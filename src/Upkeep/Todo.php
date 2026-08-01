<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Typo3CmsMcp\Paths;

/**
 * Reads todo/, where one todo is one file and the order is in the names.
 *
 * It was one document, the way requirements/ and decisions/ each were before
 * they became directories, and it failed in the same way twice over. Finishing
 * a todo meant loading 30 kB to delete a paragraph, at the end of a run where
 * there is least room for exactly that. And every session that added, moved or
 * dropped work wrote the same file, so two of them could not do it at once.
 *
 * Where a file sits is what it is, and the head it opens with says the rest:
 *
 *     todo/030-give-d-cat-1-a-digest-to-notice-markup-by.md
 *
 *     # Give `D-CAT-1` a digest to notice markup by
 *
 *     **Serves:** decisions/
 *     **Run:** bin/cli catalog:check
 *
 *     One paragraph: the next concrete step.
 *
 * The number is the todo's place in the queue and nothing else — a move is a
 * rename, a finish is a deletion, and a new todo is a new file no other session
 * is writing. They run in tens so something can be put between two of them.
 * `recurring/` is what comes round and is never deleted. `waiting/` is what no
 * session can start, because it is blocked on an answer this repository cannot
 * produce, and it carries that answer's question in a `**Waiting on:**` line.
 * `reference/` is none of the three and is there so a session does not
 * rediscover it and mistake it for work.
 *
 * `Serves:` is what makes a todo work rather than an idea. `Every:` is the
 * cadence of a recurring one, and `Run:` is the command the step starts from,
 * which `bin/cli todo:next` runs where this repository owns it.
 *
 * A cadence is `session` or a number of days, and the days are why the pair
 * exists: five sessions in an afternoon owe the feedback five readings and the
 * release check none. What is measured in days carries `**Checked:** <date>`,
 * the session that ran it writes the date, and a todo that is not due is not
 * printed. Forgetting the date costs one repeat rather than a stale answer.
 *
 * The paragraph under the head is one step, because it is printed whole and a
 * session that has to read three of them to find where to start is reading
 * instead of working. Two steps are two todos, and the order between them is
 * the order their numbers are in.
 *
 * @phpstan-type Section array{title: string, kind: string, position: string, path: string, every: string, checked: string, waitingOn: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}
 */
final class Todo
{
    /** What a cadence can say, for the check that has to name it. */
    public const CADENCE = 'session, or a number of days';

    /**
     * How a todo is worked, handed over with every todo that is handed over.
     *
     * A todo prints as an instruction, and the shortest way to act on one is to
     * start editing. What that skips is the half nothing here can see: that the
     * step was read against what the repository does today, and that a question
     * it turns on was settled from a source rather than remembered. Both are
     * invisible afterwards — the diff is identical — so the pointer travels
     * with the todo instead of waiting on the page for whoever thinks to look.
     */
    public const PROCEDURE = 'documentation/feedback/working-a-todo.md';

    /**
     * The readings `bin/cli todo:next` exists to perform. Exactly one recurring
     * todo has to name each: none and the command silently stops doing half its
     * job, two and it does it twice.
     */
    public const READINGS = ['bin/cli feedback:next', 'bin/cli backlog:list', 'bin/cli todo:waiting'];

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

    public static function directory(): string
    {
        return Paths::root() . '/todo';
    }

    /**
     * Every todo there is: the queue in its order, then what recurs, then what
     * waits, then what is none of the three.
     *
     * @return array<int, Section>
     */
    public static function all(): array
    {
        return array_merge(self::items(), self::recurring(), self::waiting(), self::references());
    }

    /**
     * The queue: what is to be worked on once, in the order it is to be worked
     * on, which is the order of the numbers it is named by.
     *
     * @return array<int, Section>
     */
    public static function items(): array
    {
        return self::read('', 'queue');
    }

    /**
     * What comes round and is never deleted.
     *
     * @return array<int, Section>
     */
    public static function recurring(): array
    {
        return self::read('recurring', 'recurring');
    }

    /**
     * What is blocked on an answer nothing here can produce, and is therefore
     * offered to no session.
     *
     * A todo that cannot be started used to go to the end of the queue, where
     * `next` would hand it to every session after the ones ahead of it were
     * done, and where it read as the lowest priority in the repository while it
     * was actually waiting on somebody. Here it says what it waits on, and the
     * answer is what moves it back into the queue.
     *
     * @return array<int, Section>
     */
    public static function waiting(): array
    {
        return self::read('waiting', 'waiting');
    }

    /**
     * What recurs on a clock: an appointment, whose day either has come or has
     * not, and which is therefore asked before the queue. Missing it is missing
     * the day, not losing a place in an order.
     *
     * @return array<int, Section>
     */
    public static function appointments(): array
    {
        return array_values(array_filter(self::recurring(), static fn(array $s): bool => $s['every'] !== 'session'));
    }

    /**
     * What recurs every session: sighting what arrived from outside — the feedback
     * and the backlog — and deciding what of it becomes work.
     *
     * Asked last, once the queue is empty, because that decision is what puts
     * entries into the queue. While the queue still has any, sighting more is
     * deciding twice and doing nothing, and it is the group that would win
     * every session forever if it were asked first: feedback arrive from every
     * session everywhere, and one session judges a handful.
     *
     * @return array<int, Section>
     */
    public static function sightings(): array
    {
        return array_values(array_filter(self::recurring(), static fn(array $s): bool => $s['every'] === 'session'));
    }

    /**
     * What a session would otherwise rediscover and mistake for work: the
     * environment table, the answers that are standing rather than pending.
     *
     * @return array<int, Section>
     */
    public static function references(): array
    {
        return self::read('reference', 'reference');
    }

    /**
     * Everything the queue answers for, which is what turns an entry in
     * requirements/ or a feedback in feedback/ into work somebody has taken on.
     *
     * Read from the queue and from what waits, which are the two states of
     * work somebody has taken on. What is kept in `reference/` names ids too,
     * and one of those pages is the list of what is deliberately *not* queued —
     * the opposite of somebody having it in hand. A recurring todo is not a
     * taking-on either: it is owed every session.
     *
     * @return array<int, string>
     */
    public static function serves(): array
    {
        $served = [];
        foreach (array_merge(self::items(), self::waiting()) as $item) {
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
     * that owns it rather than against a list kept here. A feedback is the one worth
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
            // A feedback in the archive was answered, whether the todo names it
            // where it was or where it now is.
            return is_file(Paths::root() . '/' . $what) && !str_starts_with($what, 'feedback/archive/')
                ? null
                : 'and that feedback is closed — the todo is done, or trims to the part that is left';
        }

        return 'which is none of a requirement, a scenario, a feedback, or a directory of this repository';
    }

    /**
     * One directory of todos, in the order its file names have them, which for
     * the queue is the order of the work.
     *
     * The readme is the only file here that is not a todo, and it is what the
     * directory says about itself rather than something to do.
     *
     * @return array<int, Section>
     */
    private static function read(string $group, string $kind): array
    {
        $todos = [];
        foreach (glob(self::directory() . '/' . ($group === '' ? '' : $group . '/') . '*.md') ?: [] as $path) {
            if (basename($path) !== 'readme.md') {
                $todos[] = self::parse($path, $kind);
            }
        }
        usort($todos, static fn(array $a, array $b): int => strcmp($a['path'], $b['path']));

        return $todos;
    }

    /**
     * The labelled lines of a head, as label and value.
     *
     * An indented line belongs to the field above it, the way it does under a
     * bold label in `requirements/`: a question asked in somebody's own words
     * does not fit in what is left of one line, and wrapping it is what every
     * other file here does.
     *
     * @param array<int, string> $strays what no label was found on, appended to
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private static function fields(string $head, array &$strays): array
    {
        $fields = [];
        foreach (preg_split('/\R/', trim($head)) ?: [] as $line) {
            if (trim($line) === '') {
                continue;
            }
            if (preg_match('/^\s/', $line) === 1 && $fields !== []) {
                $fields[count($fields) - 1][1] .= ' ' . trim($line);
                continue;
            }
            if (preg_match('/^\*\*([A-Z][a-z]+(?: [a-z]+)?):\*\*\s*(.*)$/', trim($line), $matches) !== 1) {
                $strays[] = trim($line);
                continue;
            }

            $fields[] = [$matches[1], trim($matches[2])];
        }

        return $fields;
    }

    /**
     * One file: what it is called, what it declares, and the step itself.
     *
     * @return Section
     */
    private static function parse(string $path, string $kind): array
    {
        $contents = (string) file_get_contents($path);

        preg_match('/^# (.*)$/m', $contents, $heading);
        $rest = ltrim(array_pad(preg_split('/\R\R/', $contents, 2) ?: [], 2, '')[1]);

        // A head is a head where it opens with one of its own labels. What is
        // kept in reference/ has none, and reading its first paragraph as a
        // broken one would report every line of it as a field nobody knows.
        [$head, $body] = preg_match('/^\*\*[A-Z]/', $rest) === 1
            ? array_pad(preg_split('/\R\R/', $rest, 2) ?: [], 2, '')
            : ['', $rest];

        $every = '';
        $checked = '';
        $waitingOn = '';
        $serves = [];
        $run = [];
        $strays = [];
        foreach (self::fields((string) $head, $strays) as [$label, $value]) {
            match ($label) {
                'Serves' => $serves = array_values(array_filter(array_map(trim(...), explode(',', $value)))),
                'Every' => $every = $value,
                'Checked' => $checked = $value,
                'Waiting on' => $waitingOn = $value,
                'Run' => $run[] = $value,
                default => $strays[] = '**' . $label . ':** ' . $value,
            };
        }

        preg_match('/^(\d+)-/', basename($path), $position);

        return [
            'title' => trim($heading[1] ?? ''),
            'kind' => $kind,
            'position' => $position[1] ?? '',
            'path' => 'todo/' . ($kind === 'queue' ? '' : basename(dirname($path)) . '/') . basename($path),
            'every' => $every,
            'checked' => $checked,
            'waitingOn' => $waitingOn,
            'serves' => $serves,
            'run' => $run,
            'head' => trim((string) $head),
            'strays' => $strays,
            'body' => trim((string) $body),
        ];
    }
}
