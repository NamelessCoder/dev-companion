# Working several todos at once

One session works one todo, and everything about that is
[working-a-todo.md](working-a-todo.md) — unchanged here. This page is the part
around it: how several sessions get different work, where each of them writes,
and how what they wrote comes back. Nothing on it replaces the reading, the
research or the question a todo is owed.

It is worth doing where the queue holds work that does not overlap, which is
most of what accumulates here: entries that name a decision each and are read
against different parts of the checkout. It is not worth doing for two todos
about one file, and it is not worth doing for one.

## What is everybody's and what is one session's

**`main` carries the state, the branch carries the work.**

That is the whole arrangement, and every rule below follows from it. Who has
what in hand is on `main`, where all of them read it. The half-finished diff is
on a branch, where nobody else has to look at it. A question that stopped a
session comes back to `main` on its own — the branch it was asked on stays where
it is.

The reason is that `todo/` is the one thing every session writes. One todo is
one file, and that was already true before anybody worked two at once; it is
what makes this possible at all. Each session touches its own file and no other.

## Taking them on

    bin/cli todo:claim 3

Three todos come out of the queue into `todo/progress/`, each with the branch it
will be worked on and today's date. From then on `bin/cli todo:next` offers
them to nobody: the fourth session is handed the item behind them.

Commit that before anything else. A claim that is not on `main` is a claim
nobody can read, the next `todo:claim` hands out the same todos again, and the
worktree — cut from `main` after it — carries no file saying what it was made
for. `bin/cli todo:next` refuses there rather than reading the queue, which is
how the last of those is found in the first minute instead of the third hour.

What it prints besides the branches is an overlap: two claims answering for one
entry. Nothing here knows which files a step will touch, so it is a warning to
read before the worktrees exist rather than a refusal. Two todos on one entry
are two sessions editing one file, and taking one of them is cheaper than
merging both.

`bin/cli todo:release <name>` is the way back out, for a claim nobody is
working — a branch that came home with a question left over, one that was
abandoned, a session that never started. Where it goes is read off the claim,
and the branch is left alone either way.

## The worktree

    git worktree add .worktrees/<name> -b todo/<name>
    cd .worktrees/<name> && composer install
    ln -s ../../.checkouts .checkouts

**Run `composer install` in the worktree. Never symlink `vendor/`.**

That one costs an afternoon to find. `Paths::root()` is the directory above
`src/`, and Composer's autoload map resolves `src/` from wherever the autoloader
physically sits — so a symlinked `vendor/` points every path in this repository
back at the main checkout. `bin/cli` then reads and writes the todos,
requirements and decisions of the checkout the session is not in, and nothing
about the output looks wrong.

`.checkouts/` is the opposite case and is symlinked on purpose. It is 861 MB,
it is gitignored, and a session working a todo only ever reads it. Only
`bin/cli checkouts:update` writes there, and that is not a claim's work.

## Starting the sessions

One session per worktree, and what it has to be told is short, because the rest
it reads here itself. Both sessions of the first run did.

```text
You work in a git worktree and only there:

    <absolute path to the worktree>

Every command and every file operation happens in that directory. The main
checkout is worked by another session at the same time — change nothing in it.
Start bash calls by changing into your worktree, or use absolute paths below it.
Check with `git rev-parse --show-toplevel` where you are; it has to be your
worktree.

Start the way every session in this repository starts:

    bin/cli todo:next

That hands you one todo — your claim, not the front of the queue. In a worktree
it hands over nothing else: where it names a todo, that todo is yours, and where
the setup is wrong it says so instead of reading the queue. Work it by
documentation/feedback/working-a-todo.md, which the command names itself. What
parallel work adds is in documentation/feedback/working-todos-in-parallel.md.
The three rules everything hangs on:

- Commit on your branch `<branch>`, never on main — the todo file included.
- Leave the group listings at the foot of a requirements/ or decisions/ group
  readme alone, by hand and by command.
- Merge nothing and do not remove your worktree.

If you hit a question this repository cannot answer and that would change what
you build: do not ask and do not wait. Write it into a `**Waiting on:**` line on
your claim, commit what you have, and end.

`composer ci` before every commit. Report at the end what you read, what you
changed, whether it is green, and what state your claim is in.
```

The absolute path is the load-bearing part. Where each session is its own
process in its own directory, it is redundant. Where they are agents spawned
from one session, they share a file system and nothing else keeps them in the
worktree — and a session that works the main checkout by mistake produces a diff
that looks exactly right.

What the prompt cannot make the session check is which todo is its own. Every
line above is answered against what the session was told, and a path it was
handed is one it has no reason to doubt: a worktree on a branch the claim never
named — or one cut from a `main` that did not carry the claim yet — passes each
of them and then reads the queue, where it finds real work that belongs to
somebody else. That half is answered where it can be, by the repository rather
than the prompt. `bin/cli todo:next` asks git whether it is standing in a
worktree, and a worktree standing on no claim is refused instead of served.

## What the session does with it

    bin/cli todo:next

The same command as always, and in a worktree standing on a claim it hands over
that claim — standing on none, it says so and stops. Nothing else about the
session is special: it reads what the todo serves,
settles what the step turns on, and leaves the file true — all of
[working-a-todo.md](working-a-todo.md), which the command names as usual.

Two things are different, and both are consequences of `main` being elsewhere:

- **Commit on the branch, never on `main`.** That includes the todo file itself.
  A finished claim is a deletion in the branch, and the merge is what carries it.
- **Leave the group listings alone** — the block at the foot of a
  `requirements/<group>/readme.md` or `decisions/<group>/readme.md`. It is
  generated from every file in the group, and a worktree can only see its own
  new entry. That means the command, `bin/cli requirements:index` or
  `bin/cli decisions:index`, and it means the line: two sessions editing one
  listing by hand conflict where two sessions leaving it alone do not. The
  session that merges runs both commands once, and `requirements:check` says so
  if it forgets.

## A question that arrives mid-work

A todo that turns out to need an answer nobody here can give is the normal case,
not the exception, and a session working alone asks and waits. One of several
cannot: waiting blocks a worktree on a person who is answering three others.

So it does not ask, it records. The question goes into a `**Waiting on:**` line
on the claim in `progress/`, in the words it would have been asked in, together
with what the reading already established. Then the session commits what it has
and ends. The branch keeps the half that is done.

What happens next depends on whether the work behind the question stands on its
own. Most of the time it does — the session settled one half of its todo and the
question is about the other — and then the branch merges like any other and the
trimmed claim comes with it. Where it does not, only the claim comes back:

    git checkout <branch> -- todo/progress/<name>.md

That keeps `main` free of a half-finished change while still saying, in one
place, what is open and where the work behind it is.

**A claim whose branch is gone does not stay in `progress/`.** That state is for
as long as a branch is live. Once the work is merged and the branch deleted,
`**Branch:**` names something nobody can look at, and the todo is blocked on a
person — which is what `waiting/` is. `bin/cli todo:release` reads that off the
claim: one carrying a `**Waiting on:**` goes to `waiting/`, one carrying none
goes to the end of the queue. Neither touches the branch.

A claim in `progress/` with no `**Waiting on:**` and an old `**Claimed:**` is
the other thing to look for. Nobody is working it, and nothing will notice on
its own — `bin/cli todo:list` prints the date for exactly that reading.

## Bringing the branches home

**One at a time, and `composer ci` after each.**

    git merge --no-ff todo/<name>
    composer ci

Not because merging is dangerous, but because a suite that fails after three
merges says nothing about which one broke it. The claims themselves never
conflict — each session touched one file in `todo/` and it was its own.

Where one branch fixes something the others are also failing on, that one goes
first. Otherwise every merge behind it is checked against a suite that was
already red, which is the one thing this order exists to avoid.

Then, once, on `main`:

    bin/cli requirements:index && bin/cli decisions:index
    bin/cli repository:check

Both halves are things a per-branch run cannot see. The listing at the foot of a
group readme is generated from every file in that group, so entries merged from
two branches leave it short by one — `bin/cli requirements:check` says so and
names the command, and the index commands above are that command run before it
has to. And two sessions that each queued new work will have picked the same
number for it, because both read the same last number; `bin/cli todo:check`
reports the collision, and renumbering one of them is the fix.

Delete the worktree and the branch when the merge is in. A branch named for a
todo that no longer exists is a claim nobody can release.
