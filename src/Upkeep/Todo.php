<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Symfony\Component\Finder\Finder;
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
 * `recurring/` is what comes round and is never deleted. `progress/` is what a
 * session has in hand: it is offered to nobody else, and it says on which
 * branch the work is and since when. `waiting/` is what no session can start,
 * because it is blocked on an answer this repository cannot produce, and it
 * carries that answer's question in a `**Waiting on:**` line. `reference/` is
 * none of the four and is there so a session does not rediscover it and mistake
 * it for work.
 *
 * `Serves:` is what makes a todo work rather than an idea. `Every:` is the
 * cadence of a recurring one, and `Run:` is the command the step starts from,
 * which `bin/cli todo:next` runs where this repository owns it. `Branch:` and
 * `Claimed:` belong to a todo in hand: the first is where the work is, the
 * second is what tells a claim somebody is working from one nobody came back
 * to.
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
 * @phpstan-type Section array{title: string, kind: string, position: string, path: string, every: string, checked: string, waitingOn: string, branch: string, claimed: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}
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
     * How several todos are worked at once, handed over with every claim.
     *
     * `bin/cli todo:claim` moves files and names branches, which is the half of
     * the arrangement this repository owns. The other half — the worktree the
     * branch is checked out in, what a session does with a question it cannot
     * settle, and who merges — is not something a command can carry out, and a
     * claim taken without it is a lock nobody knows how to release.
     */
    public const PARALLEL = 'documentation/feedback/working-todos-in-parallel.md';

    /**
     * What a session started from a command line has to be given, handed over
     * with the message that starts one. It is the same launch a forward run
     * uses, which is why it is not on the page about claims.
     */
    public const LAUNCH = 'documentation/driving-a-session.md';

    /**
     * What one of several sessions is started with, and the whole of it.
     *
     * It carries no worktree path, no branch and no todo, which is the point:
     * a message with a blank in it is a message somebody fills in, and the one
     * run that went wrong here went wrong that way — the template was sent as
     * it stands, placeholders and all, and the session had no way to tell that
     * from an instruction. Nothing in this one is per-session, so every session
     * gets the same characters and there is nothing to get wrong.
     *
     * What it therefore cannot say is which todo is being worked, and that is
     * the half the repository answers rather than the prompt:
     * `bin/cli todo:next --worktree` reads it off the branch the session is
     * standing on, and refuses where that is not a worktree carrying a claim.
     * A path handed to a session is one it has no reason to doubt; a claim read
     * out of the checkout it is standing in cannot be the wrong one.
     */
    public const BRIEFING = <<<'TEXT'
        You work in the git worktree you were started in, and only there. Check with
        `git rev-parse --show-toplevel` that you are standing in one: the main checkout
        is worked by somebody else at the same time, and nothing in it is yours to
        change. Use paths below your own directory, or change into it first.

        Your work is not in this message. Fetch it:

            bin/cli todo:next --worktree

        That names the one todo that is yours, the branch you commit it on, and what
        that branch may not carry. Asked anywhere else it refuses, and a refusal ends
        the session: report it rather than looking for something to do.

        If you hit a question this repository cannot answer and that would change what
        you build, do not ask and do not wait. Write it into a `**Waiting on:**` line
        on your claim, commit what you have, and end.

        `composer ci` before every commit. Report at the end what you read, what you
        changed, whether it is green, and what state your claim is in.
        TEXT;

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
     * The branch a todo is worked on, derived rather than chosen.
     *
     * Two sessions that name their own branches produce two names for one piece
     * of work, and the claim in `progress/` is then the only thing that says
     * which is which — while the whole point of the field is that somebody who
     * finds a stale claim can go and look at the work. Deriving it means the
     * branch can be found from the todo and the todo from the branch.
     *
     * It is the name the work wants, not necessarily the one it gets. A todo
     * claimed, released and claimed again derives the same branch as the run
     * that left one standing, and two pieces of work under one name are worse
     * than a name that has to be looked up — so the claim is given a free one
     * and carries it in `**Branch:**`, which is where every reader was already
     * looking.
     *
     * @param Section $todo
     */
    public static function branch(array $todo): string
    {
        return 'todo/' . preg_replace('/^\d+-/', '', basename($todo['path'], '.md'));
    }

    /**
     * Taking a queued todo on: out of the queue, into `progress/`, carrying the
     * branch the work will be on and the day it was taken.
     *
     * The number goes with the move. It is the place in the queue and nothing
     * else, and a todo in hand has no place in an order nothing is coming for
     * it from — the same reason one in `waiting/` drops its number. What puts
     * it back gives it a new one at the end.
     *
     * @param Section     $todo
     * @param string|null $branch the free name the work was given, where the
     *                            derived one was taken by an earlier claim
     *
     * @return string the path it has from now on
     */
    public static function claim(array $todo, ?string $today = null, ?string $branch = null): string
    {
        $to = 'todo/progress/' . preg_replace('/^\d+-/', '', basename($todo['path']));
        $head = $todo['head'] . "\n**Branch:** " . ($branch ?? self::branch($todo))
            . "\n**Claimed:** " . ($today ?? date('Y-m-d'));

        return self::move($todo, $to, $head);
    }

    /**
     * Putting one back: out of `progress/`, to whichever of the two states the
     * claim itself says it is in.
     *
     * A claim carrying a question goes to `waiting/`, because that is what it
     * is: blocked on an answer nothing here can produce. The rest go to the end
     * of the queue, which is what this repository already does with a todo
     * somebody could not finish — honest about the priority, and its own timer,
     * coming round again as the queue drains.
     *
     * Reading it off the file rather than asking is what keeps the two apart
     * without a second command. The question was written by the session that
     * hit it, and a todo whose remaining step is "wait for somebody to answer"
     * parked at the end of the queue reads as the lowest priority in the
     * repository while it is actually waiting on a person.
     *
     * What it was claimed for is dropped either way. A branch nobody is on and
     * a date nobody is counting from are worse than no fields at all, because
     * the next reader cannot tell them from a live claim — and after the work
     * is merged, the branch they name is one somebody is about to delete.
     *
     * @param Section $todo
     *
     * @return string the path it has from now on
     */
    public static function release(array $todo): string
    {
        $head = implode("\n", array_filter(
            preg_split('/\R/', $todo['head']) ?: [],
            static fn(string $line): bool => !str_starts_with($line, '**Branch:**') && !str_starts_with($line, '**Claimed:**'),
        ));

        if ($todo['waitingOn'] !== '') {
            return self::move($todo, 'todo/waiting/' . basename($todo['path']), $head);
        }

        $positions = array_map(intval(...), array_column(self::items(), 'position'));
        $next = sprintf('%03d', ($positions === [] ? 0 : max($positions)) + 10);

        return self::move($todo, 'todo/' . $next . '-' . basename($todo['path']), $head);
    }

    /**
     * One todo, rewritten where it now belongs and removed from where it was.
     *
     * Written from what was read rather than patched in place: the head is the
     * one part a move changes, and rebuilding the file from its title, its head
     * and its step is what keeps the result readable by the same parse that
     * produced it.
     *
     * @param Section $todo
     */
    private static function move(array $todo, string $to, string $head): string
    {
        $directory = dirname(Paths::root() . '/' . $to);
        if (!is_dir($directory) && !mkdir($directory) && !is_dir($directory)) {
            throw new \RuntimeException($directory . ' is not there and cannot be made');
        }

        file_put_contents(
            Paths::root() . '/' . $to,
            '# ' . $todo['title'] . "\n\n" . trim($head) . "\n\n" . $todo['body'] . "\n",
        );
        unlink(Paths::root() . '/' . $todo['path']);

        return $to;
    }

    /**
     * Every todo there is: the queue in its order, then what recurs, then what
     * a session has in hand, then what waits, then what is none of the four.
     *
     * @return array<int, Section>
     */
    public static function all(): array
    {
        return array_merge(self::items(), self::recurring(), self::progress(), self::waiting(), self::references());
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
     * What a session has in hand, and is therefore offered to no other.
     *
     * The queue is an order, not an assignment, and `bin/cli todo:next` reads
     * the same first item for everybody who asks. That is right while one
     * session works at a time and wrong the moment two do: both are handed the
     * same todo, and the second finds out by writing a change somebody else has
     * already written. So taking one on is a move, the way finishing one is a
     * deletion — the claim is a file in a directory rather than a field nobody
     * can see from outside the checkout it was set in.
     *
     * It carries the two things a claim nobody came back to cannot be told from
     * a live one without: `**Branch:**`, where the work is, because a claim
     * whose half-finished diff cannot be found is worth less than no claim; and
     * `**Claimed:**`, the date, because a state that locks everybody else out
     * has to be readable as stale.
     *
     * @return array<int, Section>
     */
    public static function progress(): array
    {
        return self::read('progress', 'progress');
    }

    /**
     * The claim this checkout is standing on, or null where it is on none.
     *
     * `bin/cli todo:next` is where a session starts, and that sentence has to
     * keep being true in a worktree. A session working one of several claims
     * would otherwise be the one session here that starts differently — told
     * its file name by whoever set it up, and handed the front of the queue by
     * the command everything else points it at. Getting that wrong is silent:
     * it reads a todo, it is a real todo, and it is somebody else's.
     *
     * The branch is what answers, because the branch is what the claim named.
     * A checkout on `main` is on no claim and gets the queue, which is every
     * session this repository had before there were two.
     *
     * @return Section|null
     */
    public static function claimed(): ?array
    {
        $branch = self::standing();
        if ($branch === '') {
            return null;
        }

        foreach (self::progress() as $todo) {
            if ($todo['branch'] === $branch) {
                return $todo;
            }
        }

        return null;
    }

    /**
     * The branch this checkout is standing on, or the empty string where git
     * cannot say.
     *
     * A detached head answers with `HEAD`, which is no branch and matches no
     * claim — the same as not knowing, and it needs no case of its own.
     *
     * The root is a parameter so that a test can ask about a checkout other
     * than the one it is running in. Nothing else passes it: a session asks
     * about where it stands, and where it stands is where this file is.
     */
    public static function standing(?string $root = null): string
    {
        [$exitCode, $branch] = Checkouts::run(['git', '-C', $root ?? Paths::root(), 'rev-parse', '--abbrev-ref', 'HEAD']);

        return $exitCode === 0 ? trim($branch) : '';
    }

    /**
     * Whether this checkout is a worktree of another one rather than the
     * checkout itself.
     *
     * It is the one question that tells a session set up for a claim apart
     * from one that was always going to read the queue, and it cannot be asked
     * of the branch: a worktree on the wrong branch and the main checkout on
     * `main` both stand on no claim, and only one of them is a mistake.
     *
     * git answers it with two directories. A linked worktree keeps its own
     * under the checkout they share, so `--git-dir` is that one and
     * `--git-common-dir` the shared one; in the checkout itself they are the
     * same directory. Both are asked for as absolute paths, because git answers
     * the shared one relatively often enough that comparing what it prints
     * would call every main checkout a worktree.
     */
    public static function linked(?string $root = null): bool
    {
        $root ??= Paths::root();
        [$own, $ownDir] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--absolute-git-dir']);
        [$shared, $sharedDir] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--path-format=absolute', '--git-common-dir']);
        if ($own !== 0 || $shared !== 0) {
            return false;
        }

        return rtrim(trim($ownDir), '/') !== rtrim(trim($sharedDir), '/');
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
     * Read from the queue, from what a session has in hand and from what waits,
     * which are the three states of work somebody has taken on. What is kept in
     * `reference/` names ids too, and one of those pages is the list of what is
     * deliberately *not* queued — the opposite of somebody having it in hand. A
     * recurring todo is not a taking-on either: it is owed every session.
     *
     * @return array<int, string>
     */
    public static function serves(): array
    {
        $served = [];
        foreach (array_merge(self::items(), self::progress(), self::waiting()) as $item) {
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
        $directory = self::directory() . ($group === '' ? '' : '/' . $group);
        if (!is_dir($directory)) {
            return [];
        }

        $todos = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.md')->notName('readme.md')->sortByName() as $file) {
            $todos[] = self::parse($file->getPathname(), $kind);
        }

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
        $branch = '';
        $claimed = '';
        $serves = [];
        $run = [];
        $strays = [];
        foreach (self::fields((string) $head, $strays) as [$label, $value]) {
            match ($label) {
                'Serves' => $serves = array_values(array_filter(array_map(trim(...), explode(',', $value)))),
                'Every' => $every = $value,
                'Checked' => $checked = $value,
                'Waiting on' => $waitingOn = $value,
                'Branch' => $branch = $value,
                'Claimed' => $claimed = $value,
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
            'branch' => $branch,
            'claimed' => $claimed,
            'serves' => $serves,
            'run' => $run,
            'head' => trim((string) $head),
            'strays' => $strays,
            'body' => trim((string) $body),
        ];
    }
}
