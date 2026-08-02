# Decide what stops two sessions claiming one id

**Serves:** decisions/
**Priority:** normal
**Branch:** todo/decide-what-stops-two-sessions-claiming-one-id
**Claimed:** 2026-08-02

Four runs of ten todos produced seven duplicate ids — one in the second run,
three in the third, three in the fourth. Every session reads the same last
number out of a `main` that does not move while they work, so the rate rises
with the share of todos that end in a judgement, and judging is what the queue
increasingly holds. `DecisionsTest` catches each one on the rebase and the fix
is mechanical, so the collision itself costs a few minutes.

The renumbering is the part that is not safe. Twice the files naming the old
number did not all mean the same entry — `R-PRJ-008` rested on the `D-ANS-013`
that kept its number while five other files meant the one that became
`D-ANS-015` — and a search and replace over the id is silently wrong there,
with no check failing afterwards because the entry it now points at exists.
`git diff main -- <file>` settles it, and that is a step somebody has to know to
take. `working-todos-in-parallel.md` now says so, which is the cheap half of
this and not the answer.

**What this asks for is which of three, and it is a decision rather than a
step.** Each is a different trade and none of them is obviously right:

- **A command that does the renumbering.** `bin/cli decisions:renumber <file>`
  renames, rewrites the id and the heading, moves the references it can settle
  from the link target, and prints the bare `D-XXX-000` mentions it cannot — the
  ambiguous ones — for a person. Smallest change, keeps the numbers, and leaves
  a human in the one place that needs judgement.
- **Reserve at claim time.** `todo:claim` writes to `main` before the worktrees
  are cut, which is the only moment anything here is atomic across the sessions,
  so it could hand each claim a distinct starting offset per group. It removes
  the collision instead of easing it, and it buys that with gaps in the
  numbering, which `decisions:check` currently has an opinion about.
- **Take the number out, as the queue already did.** `todo/` had exactly this
  problem and answered it by having no number at all — "two todos are both
  `normal` and the older one is older". The same argument holds for a decision:
  the date carries the order and the file name carries the subject. It costs the
  most, because `D-ANS-011` is a citation handle in prose across the whole
  repository, and every one of those would have to become something else.

Read `decisions/readme.md` first — the three-digit width is there for the
sort order of a directory listing, which is the thing the third option gives up
and the first two keep. `bin/cli todo:claim`'s overlap warning and
`StructureTest::noFileCarriesAConflictMarker` came out of the same four runs and
are already in; this is the one finding of that review that was not implemented,
because it is the one where the repository would be choosing rather than
checking.

The recommendation from the review was the first, with the third as the honest
long answer. Ask before building either.
