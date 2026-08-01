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
nobody can read, and the next `todo:claim` hands out the same todos again.

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

## What the session does with it

    bin/cli todo:next

The same command as always, and in a worktree standing on a claim it hands over
that claim. Nothing about the session is special: it reads what the todo serves,
settles what the step turns on, and leaves the file true — all of
[working-a-todo.md](working-a-todo.md), which the command names as usual.

Two things are different, and both are consequences of `main` being elsewhere:

- **Commit on the branch, never on `main`.** That includes the todo file itself.
  A finished claim is a deletion in the branch, and the merge is what carries it.
- **Do not regenerate an index.** `bin/cli requirements:index` and
  `bin/cli decisions:index` rewrite listings from every file in a group, and a
  worktree can only see its own new entry. Two sessions doing it produce two
  listings that each drop the other's. The session that merges runs them once.

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
