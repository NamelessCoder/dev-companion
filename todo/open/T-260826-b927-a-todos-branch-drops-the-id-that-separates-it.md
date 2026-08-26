# A todo's branch drops the id that separates it

**Serves:** D-DOC-061
**Priority:** normal

`Todo::branch()` strips the `T-<yymmdd>-<hash>-` stamp off the file name, so two
todos whose slug matches derive one branch. The id is generated to separate
exactly those two, and the derivation throws away the half that does it:
`TodoClaim::worktrees()` then reads the second todo's branch as standing and
passes it over, so a todo nobody has in hand is never offered again.

The branch is the id and nothing else — `todo/T-260824-5867`. Decided with the
maintainer on 2026-08-27, against carrying the slug behind it: a listing that
names the work reads well, but the branch would then change under a retitle and
orphan the worktree standing on it. What a worktree is for is saying a todo is
in hand, and the id is what every command already takes.

- `Todo::branch()` returns `'todo/' . Todo::identifier($todo)`, and the `STAMP`
  reference leaves the method.
- `Todo::worktreeNamed()` resolves a whole file name and a path by way of the
  identifier, since neither is the branch any more.
- A test declaring `D-DOC-061` that two todos sharing a slug derive two
  branches, which is the case that has no coverage today.
- The docblocks on `Todo::branch()` and `TodoClaim` describe the derivation and
  both say the branch is derived from the todo; the second also owes the reader
  what the worktree directory is named.
- A **Since then** on `D-DOC-061`: its own **Wrong if** predicted the slug would
  be what people read, and what happened is the opposite — the id is what every
  command takes and it was missing from `git worktree list`.

Nothing may change the derivation while a worktree stands on the old one.
`TodoNext` and `TodoClaim` decide a todo is in hand by deriving its branch and
looking for it among the standing worktrees, so a derivation that moves under
them offers work already in flight a second time.
