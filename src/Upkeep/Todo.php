<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Feedback\Card;
use TYPO3\DevCompanion\Paths;

/**
 * Reads todo/, where one todo is one file and the order is in the names.
 *
 * It was one document, the way requirements/ and decisions/ each were before
 * they became directories, and it failed in the same way twice over. Finishing
 * a todo meant loading 30 kB to delete a paragraph, and every session that
 * added, moved or dropped work wrote the same file.
 *
 * Where a file sits is what it is and the head it opens with says the rest.
 * `todo/readme.md` is that form — the stages, the labelled lines, the one
 * paragraph under them — and this reads it rather than restating it.
 *
 * @phpstan-type Section array{title: string, kind: string, priority: string, path: non-empty-string, every: string, checked: string, waitingOn: string, serves: array<int, string>, run: array<int, string>, head: string, strays: array<int, string>, body: string}
 */
final class Todo
{
    /** What a cadence can say, for the check that has to name it. */
    public const CADENCE = 'session, or a number of days';

    /**
     * What a priority can say, highest first, and the whole of it.
     *
     * A closed list rather than a number, which is the difference between this
     * and the queue positions it replaced: two sessions queueing work at once
     * both read the same last number and both took it, while two todos that are
     * `normal` are simply both normal. Nothing has to be renamed to put one
     * between two others, because there is no between.
     *
     * Every todo in a stage carries one, and that is what makes it checkable: a
     * priority somebody forgot and one deliberately left off are the same file,
     * and while absence meant something no check could tell them apart. What a
     * card is for is readable without it — a judging card is the one that
     * serves a `feedback/` file — so `low` says what absence used to, and
     * `bin/cli todo:check` says when nothing does.
     */
    public const PRIORITIES = ['high', 'normal', 'low'];

    /**
     * What a todo in a stage is named: its id, and nothing beside it.
     *
     * The day is what a listing sorts by — within a priority the older one comes
     * first, and that is the whole of the order below the three words. The digest
     * beside it is what two writers in one second cannot both produce, which the
     * day and the time on their own could.
     *
     * A slug behind the id would be a second name for the same todo, read by
     * nothing and shareable by two of them — `D-DOC-061`. What the work is is
     * the title inside the file, which every listing prints.
     */
    public const NAME = '/^T-\d{6}-[0-9a-f]{4}$/';

    /**
     * The id a todo is cited by, which is also what its file name opens with.
     *
     * Derived from what the todo is named after rather than counted off what
     * exists — `D-DOC-061`. Two branches cut from one `main` each allocated
     * `D-ANS-114` by counting, and todos are claimed in batches, so the same
     * count would collide more often. The seed is the feedback a card serves, or
     * the title and the instant where somebody wrote one by hand: the same todo
     * derives the same id wherever it is derived.
     */
    public static function id(string $seed, ?string $day = null): string
    {
        return 'T-' . ($day ?? date('ymd')) . '-' . substr(sha1($seed), 0, 4);
    }

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
    public const PROCEDURE = 'documentation/records/working-a-todo.rst';

    /**
     * How several todos are worked at once, handed over with every claim.
     *
     * `bin/cli todo:claim` moves files and names branches, which is the half of
     * the arrangement this repository owns. The other half — the worktree the
     * branch is checked out in, what a session does with a question it cannot
     * settle, and who merges — is not something a command can carry out, and a
     * claim taken without it is a lock nobody knows how to release.
     */
    public const PARALLEL = 'documentation/records/working-todos-in-parallel.rst';

    /**
     * What a session started from a command line has to be given, handed over
     * with the message that starts one. It is the same launch a forward run
     * uses, which is why it is not on the page about claims.
     */
    public const LAUNCH = 'documentation/contributing/driving-a-session.rst';

    /**
     * What one of several sessions is started with, and the whole of it.
     *
     * Nothing in it is per-session, so there is no blank for anybody to fill in
     * and no template to send as it stands; which todo is being worked is read
     * out of the checkout instead, by `bin/cli todo:next --worktree`. How to
     * read is duplicated from `AGENTS.md` on purpose — of the 82 sessions of
     * 2026-08-02 every one opened the procedure page and 13 opened `AGENTS.md`
     * (`D-FBK-020`).
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

        You are charged one context per call and not one per token, so read the same
        in fewer of them: send the calls that do not depend on each other together,
        reach for a file with your own file and search tools rather than through
        `cat`, `sed`, `grep` and `ls`, and open a file once rather than in windows.

        `composer ci` before every commit. Report at the end what you read, what you
        changed, whether it is green, and what state your claim is in.
        TEXT;

    /**
     * The readings `bin/cli todo:next` exists to perform. Exactly one recurring
     * todo has to name each: none and the command silently stops doing half its
     * job, two and it does it twice.
     */
    public const READINGS = ['bin/cli unresolved:list', 'bin/cli todo:waiting'];

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

    /**
     * A queue somewhere other than this checkout's, which only a test asks
     * for.
     *
     * `R-COD-003`: a unit test writes into no directory this repository keeps.
     * The cases that hold claiming and releasing have to write a todo to have
     * one, and they used to write it into the real `todo/` — a fixture in the
     * queue a session reads, removed afterwards by a marker in its name, and
     * left behind by any run that died in between.
     */
    private static ?string $directory = null;

    /**
     * Where the queue is read and written, for as long as a test says so.
     *
     * The directory has to be named `todo` below a root of its own, because a
     * todo's `path` is relative to that root — `todo/open/....md` — and moving
     * one resolves the two against each other.
     */
    public static function useDirectory(?string $directory): void
    {
        self::$directory = $directory;
    }

    public static function directory(): string
    {
        return self::$directory ?? Paths::root() . '/todo';
    }

    /** The checkout the queue belongs to, which a relative path is resolved against. */
    private static function root(): string
    {
        return self::$directory === null ? Paths::root() : dirname(self::$directory);
    }

    /**
     * The branch a todo is worked on, which is the id and nothing else.
     *
     * Two sessions that name their own branches produce two names for one piece
     * of work, and nothing then says which is which. Deriving it means the
     * branch can be found from the todo and the todo from the branch, which is
     * what lets the worktree standing on it say the todo is in hand —
     * `D-DOC-060`.
     *
     * The id is the whole of the name a todo carries, so the branch is that name
     * under `todo/` — `D-DOC-061`. Nothing has to be stripped off it, and a
     * retitle moves neither the branch nor the worktree standing on it.
     *
     * @param Section $todo
     */
    public static function branch(array $todo): string
    {
        return 'todo/' . self::identifier($todo);
    }

    /**
     * A todo whose remaining step is an answer nobody here can give, moved to
     * where it is offered to no session.
     *
     * The one move left. Taking a todo on is cutting a worktree and finishing it
     * is deleting the file, so neither writes anything for the other to undo —
     * a todo nobody is working is in `open/` because the worktree is gone,
     * rather than because a command put it back.
     *
     * The question is read off the file rather than asked for. It was written by
     * the session that hit it, and a todo whose remaining step is "wait for
     * somebody to answer" parked among the workable ones reads as ordinary work
     * while it is actually waiting on a person.
     *
     * @param Section $todo
     *
     * @return string the path it has from now on
     */
    public static function park(array $todo): string
    {
        return self::move($todo, 'todo/waiting/' . basename($todo['path']), $todo['head']);
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
        $directory = dirname(self::root() . '/' . $to);
        if (!is_dir($directory) && !mkdir($directory) && !is_dir($directory)) {
            throw new \RuntimeException($directory . ' is not there and cannot be made');
        }

        file_put_contents(
            self::root() . '/' . $to,
            '# ' . $todo['title'] . "\n\n" . trim($head) . "\n\n" . $todo['body'] . "\n",
        );
        unlink(self::root() . '/' . $todo['path']);

        return $to;
    }

    /**
     * Every todo there is: the queue in its order, then what recurs, then what
     * waits, then what is none of the three.
     *
     * What a session has in hand is not a stage of its own. It is a todo in the
     * queue with a worktree standing on its branch, which is what `inHand()`
     * answers — `D-DOC-060`.
     *
     * @return array<int, Section>
     */
    public static function all(): array
    {
        return array_merge(self::items(), self::recurring(), self::waiting(), self::references());
    }

    /**
     * The queue: what is to be worked on once, in the order it is to be worked
     * on — by priority, and within one by age.
     *
     * Both halves are read off the file rather than kept anywhere: the word in
     * the head, and the stamp in the name that `read()` has already sorted by.
     * PHP's sort holds equal elements in the order they came in, which is what
     * makes the second half free.
     *
     * @return array<int, Section>
     */
    public static function items(): array
    {
        $items = self::read('open', 'queue');
        usort($items, static fn(array $left, array $right): int => self::rank($left) <=> self::rank($right));

        return $items;
    }

    /**
     * Where a todo's priority puts it. One carrying none is a file
     * `bin/cli todo:check` is already reporting, and it goes last rather than
     * first, so a defect cannot promote itself.
     *
     * @param Section $todo
     */
    private static function rank(array $todo): int
    {
        $at = array_search($todo['priority'], self::PRIORITIES, true);

        return $at === false ? count(self::PRIORITIES) : $at;
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
     * What a session has in hand, as the branch each worktree stands on and the
     * directory it stands in.
     *
     * The queue is an order, not an assignment, and `bin/cli todo:next` reads
     * the same first item for everybody who asks. That is right while one
     * session works at a time and wrong the moment two do: both are handed the
     * same todo, and the second finds out by writing a change somebody else has
     * already written.
     *
     * What says a todo is taken is the worktree cut for it — `D-DOC-060`. It was
     * a file in `todo/progress/` as well, which is a third copy of what the
     * branch name and the worktree already carry, and the copy was the one that
     * could go stale: a claim outlived the branch it named often enough to need
     * a check of its own.
     *
     * Read as one call rather than one per worktree, because every caller here
     * wants the whole set.
     *
     * @return array<string, string> the branch of each, by the directory it is checked out in
     */
    public static function inHand(?string $root = null): array
    {
        return array_filter(self::worktrees($root));
    }

    /**
     * Every worktree below `.worktrees/`, and the branch it stands on.
     *
     * Read off git rather than off the directory: a `.worktrees/` entry git has
     * forgotten is not one anything here can merge, and the logs `todo:claim`
     * writes live in there beside the real ones.
     *
     * A detached one answers with the empty string and holds no todo, which is
     * the difference between this and `inHand()`. It is still a worktree
     * somebody has to be able to name, so it is not left out here.
     *
     * @return array<string, string> the branch of each, by the directory it is checked out in
     */
    public static function worktrees(?string $root = null): array
    {
        $root ??= Paths::root();
        [$listed, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'list', '--porcelain']);
        if ($listed !== 0) {
            return [];
        }

        $standing = [];
        $name = '';
        foreach (preg_split('/\R/', trim($said)) ?: [] as $line) {
            if (str_starts_with($line, 'worktree ')) {
                $path = substr($line, strlen('worktree '));
                $name = str_starts_with($path, $root . '/.worktrees/') ? basename($path) : '';
                if ($name !== '') {
                    $standing[$name] = '';
                }
                continue;
            }
            // A detached worktree says `detached` here instead, and stands on no
            // branch a todo could be found from.
            if ($name !== '' && str_starts_with($line, 'branch refs/heads/')) {
                $standing[$name] = substr($line, strlen('branch refs/heads/'));
            }
        }

        return $standing;
    }

    /**
     * The id a todo is cited by, which is the whole of its file name.
     *
     * @param Section $todo
     */
    public static function identifier(array $todo): string
    {
        $name = basename($todo['path'], '.md');

        return preg_match(self::NAME, $name) === 1 ? $name : '';
    }

    /**
     * The worktree a caller named, or null where nothing here is that.
     *
     * A todo is named by its id and a worktree is an implementation of having
     * one in hand, so both are accepted and the id is the one written down.
     * Anything a caller pastes resolves: the id, the path whose file name it is,
     * or the worktree's own directory.
     */
    public static function worktreeNamed(string $reference, ?string $root = null): ?string
    {
        $named = basename(rtrim($reference, '/'), '.md');
        $standing = self::worktrees($root);
        if (array_key_exists($named, $standing)) {
            return $named;
        }

        foreach (self::items() as $todo) {
            if (self::identifier($todo) !== $named) {
                continue;
            }

            $branch = self::branch($todo);
            $at = array_search($branch, $standing, true);

            return $at === false ? null : (string) $at;
        }

        return null;
    }

    /**
     * The todos somebody has in hand, by the branch each is being worked on.
     *
     * @return array<string, Section>
     */
    public static function held(?string $root = null): array
    {
        $branches = array_flip(self::inHand($root));

        $held = [];
        foreach (self::items() as $todo) {
            $branch = self::branch($todo);
            if (isset($branches[$branch])) {
                $held[$branch] = $todo;
            }
        }

        return $held;
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
     * The branch is what answers, because the branch is derived from the todo
     * and from nothing else. A checkout on `main` is on no claim and gets the
     * queue, which is every session this repository had before there were two.
     *
     * @return Section|null
     */
    public static function claimed(): ?array
    {
        $branch = self::standing();
        if ($branch === '') {
            return null;
        }

        foreach (self::items() as $todo) {
            if (self::branch($todo) === $branch) {
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
     * What recurs every session: sighting what arrived from outside — the
     * feedback and what nothing answers for — and deciding what of it becomes
     * work.
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
     * requirements/ or decisions/, or a feedback in feedback/, into work
     * somebody has taken on.
     *
     * Read from the queue and from what waits, which are the two states of work
     * somebody has taken on — one in hand is in the queue with a worktree on it.
     * What is kept in `reference/` names ids too, and one of those pages is the
     * list of what is deliberately *not* queued — the opposite of somebody
     * having it in hand. A recurring todo is not a taking-on either: it is owed
     * every session.
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
     * The cards a judgement folded into another todo and nothing took away.
     *
     * A feedback arrives with one card and is never given a second, so a session
     * judging a cluster writes one todo carrying two feedback while the card the
     * first already had stays in the queue — `D-FBK-040`. The constant step is
     * what tells them apart, and repairing it is a deletion, so this reports.
     *
     * @return array<int, array{card: string, feedback: string, judged: array<int, string>}>
     */
    public static function folded(): array
    {
        $todos = array_merge(self::items(), self::waiting());

        $serving = [];
        foreach ($todos as $todo) {
            foreach ($todo['serves'] as $what) {
                $serving[$what][] = $todo['path'];
            }
        }

        $folded = [];
        foreach ($todos as $todo) {
            if ($todo['body'] !== Card::STEP) {
                continue;
            }
            foreach ($todo['serves'] as $what) {
                $judged = array_values(array_diff($serving[$what], [$todo['path']]));
                if ($judged === []) {
                    continue;
                }

                $folded[] = ['card' => $todo['path'], 'feedback' => $what, 'judged' => $judged];
            }
        }

        return $folded;
    }

    /**
     * Why what a todo names cannot be read, or null where it can.
     *
     * Five things are legitimate to serve, and each is checked against the place
     * that owns it rather than against a list kept here. A feedback is the one worth
     * catching: it is the reason the todo is in the queue, and the commit that
     * closes it deletes the file — so a todo still naming one is either
     * finished or has a part left that nobody has trimmed it down to.
     *
     * A decision is named by its id rather than by the directory it sits in,
     * because the work such a todo carries is one entry's **Wrong if** gone back
     * to, and `decisions/` says only that somebody is sorting the pile.
     */
    public static function unreadable(string $what): ?string
    {
        if (preg_match('/^R-[A-Z]{3}-\d+[a-z]?$/', $what) === 1) {
            return isset(Requirements::all()[$what]) ? null : 'which no requirement has';
        }

        if (preg_match('/^D-[A-Z]{3}-\d+[a-z]?$/', $what) === 1) {
            return isset(Decisions::all()[$what]) ? null : 'which no decision has';
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

        return 'which is none of a requirement, a decision, a scenario, a feedback, or a directory of this repository';
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
                // Put back rather than appended to, so the pair stays a pair:
                // an append through an offset is what loses the shape.
                [$label, $value] = array_pop($fields);
                $fields[] = [$label, $value . ' ' . trim($line)];
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
        $priority = '';
        $serves = [];
        $run = [];
        $strays = [];
        foreach (self::fields((string) $head, $strays) as [$label, $value]) {
            match ($label) {
                'Serves' => $serves = array_values(array_filter(array_map(trim(...), explode(',', $value)))),
                'Priority' => $priority = $value,
                'Every' => $every = $value,
                'Checked' => $checked = $value,
                'Waiting on' => $waitingOn = $value,
                'Run' => $run[] = $value,
                default => $strays[] = '**' . $label . ':** ' . $value,
            };
        }

        return [
            'title' => trim($heading[1] ?? ''),
            'kind' => $kind,
            'priority' => $priority,
            'path' => 'todo/' . basename(dirname($path)) . '/' . basename($path),
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
