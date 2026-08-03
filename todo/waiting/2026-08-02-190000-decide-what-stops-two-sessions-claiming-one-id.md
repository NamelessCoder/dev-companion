# Decide what stops two sessions claiming one id

**Serves:** decisions/
**Priority:** normal
**Waiting on:** which of the three, given that both stated costs moved when they
    were measured? Reserving at claim time was priced here as gaps the checks
    object to, and nothing objects to a gap — but it spends the three-digit
    width instead, which is the harder cost and the one D-DOC-005 assumed away.
    Dropping the number is 601 hand-written mentions rather than a rename. The
    recommendation is still the renumber command, for a reason this file did not
    give: every mis-pointing on record was a bare reference, so naming the five
    is the fix and moving them was never the part that failed. Putting this back
    unanswered is one of the answers, and so is taking only the command.

Four runs of ten todos produced seven duplicate ids — one in the second run,
three in the third, three in the fourth. Every session reads the same last
number out of a `main` that does not move while they work, so the rate rises
with the share of todos that end in a judgement, and judging is what the queue
increasingly holds. `DecisionsTest` catches each one on the rebase and the fix
is mechanical, so the collision itself costs a few minutes.

The renumbering is the part that is not safe. Twice the files naming the old
number did not all mean the same entry — `R-PRJ-008` rested on the `D-ANS-013`
that kept its number while five other files meant the one that became
`D-ANS-015` — and a search and replace over the id is silently wrong there, with
no check failing afterwards because the entry it now points at exists.
`git diff main -- <file>` settles it, and that is a step somebody has to know to
take. `working-todos-in-parallel.md` now says so, which is the cheap half of
this and not the answer.

## What was measured, and what it moved

The three options were costed against the checkout rather than recalled. Two of
the three costs this file stated turned out to be wrong, and the reason to
prefer the first one changed.

- **A gap in the numbering costs nothing today.** `decisions:check` reads the
  width, the group, the heading, the date, the status, the field order and the
  duplicate ids, and it never compares one number to the next. Neither does
  `DecisionsTest`. Renaming `D-GUI-002` to `D-GUI-004` and reindexing left both
  green — 101 decisions and 0 problems, then 19 tests and 4834 assertions. The
  sentence saying `decisions:check` has an opinion about gaps was wrong, and the
  experiment was reverted.
- **A prose citation is not checked at all.** The same experiment left four
  files naming an entry that no longer existed, plus one markdown link pointing
  at a deleted path, and nothing failed. What is checked is narrower than it
  looks: `restsOn:` in requirement front matter, which fires on a missing
  decision — `R-PRJ-008 rests on D-ANS-777, which no decision has` — and `R-`
  citations inside decision files, at `DecisionCheck` line 138. Bare `D-` ids in
  prose, in PHP, and in link paths are unchecked in both directions.
- **Both mis-pointings on record were bare references.** `R-PRJ-008` carried
  `restsOn: [D-ANS-013]`, where existence is checked and correctness is not, and
  `ans-006` carried `` `D-ANS-016` `` in a sentence, where neither is. No linked
  citation has yet gone wrong, which is what the third measurement predicts.
- **A renumber command could move 11% of the references by itself.** There are
  1207 mentions across 222 files. 606 sit in the listings `decisions:index`
  regenerates and cost nothing. Of the 601 written by hand, 66 carry a link
  whose path says which entry is meant, and 535 are bare, across 207 files. One
  renumbering touches one entry, so the typical event hands a person about five
  bare mentions to read.

That last number is what to read the first option against, and it lowers the
claim this file made for it. Its value is naming the five rather than moving
them, because the reference that goes wrong is bare in every recorded case. That
is still the fix: not knowing to look was the failure, and being handed the list
ends it.

**What this asks for is which of three, and it is a decision rather than a
step.** Each is a different trade and none of them is obviously right:

- **A command that does the renumbering.** `bin/cli decisions:renumber <file>`
  renames, rewrites the id and the heading, moves the 66-in-601 it can settle
  from the link target, and prints the bare `D-XXX-000` mentions it cannot — the
  ambiguous ones — for a person. Smallest change, keeps the numbers, and leaves
  a human in the one place that needs judgement, which is most of them.
  `Decisions` already has `files()`, `read()`, `all()` and `listing()`, so this
  is roughly `TodoRelease`'s 67 lines plus a reference scan and a test.
- **Reserve at claim time.** `todo:claim` writes to `main` before the worktrees
  are cut, which is the only moment anything here is atomic across the sessions,
  so it could hand each claim a distinct starting offset per group. It removes
  the collision instead of easing it. The gaps it buys that with are free —
  measured above — but the width is not. A claim cannot know which group it will
  write into, so a block has to be reserved in each, and it has to fit the worst
  case: 15 decisions landed in `answers/` on 2026-08-02 alone. Ten claims
  reserving ten apiece spend 100 numbers per group per run, and three digits
  then lasts about ten runs. Four have happened in three days. `D-DOC-005`
  assumed no group reaches 1000, on the evidence that `requirements/knowledge/`
  took a year to reach 39; this is the option that breaks that assumption rather
  than
  `decisions:check`.
- **Take the number out, as the queue already did.** `todo/` had exactly this
  problem and answered it by having no number at all — "two todos are both
  `normal` and the older one is older". It costs 601 hand-written mentions in
  222 files, and unlike `D-DOC-005`'s 1393 it is not mechanical: a number can be
  repadded, but a citation handle has to become a name somebody chose. The
  precedent is also weaker than it reads. `D-FBK-008`'s **Since then** says the
  todo number was a *rank*, and a rank is what only one session can hand out; a
  decision number is an identity, and the date and file name replace a rank more
  cleanly than they replace a handle.

An option none of the three covers, found while measuring and not recommended:
**check the citations instead of moving them.** Requiring the link form for
cross-entry citations would let a check compare the label against the target,
which is the one thing that would catch a mis-pointing rather than report it. It
converts 352 bare ids in 120 files inside `decisions/` and `requirements/`
alone, so it is close to the third option in cost and does not remove the
collision. It composes with the first, and it is the only one of the four that
would have caught the two failures on record.

Read `documentation/decisions/writing-a-decision.md` first — the three-digit
width is there for the sort
order of a directory listing, which is the thing the third option gives up and
the first two keep. `bin/cli todo:claim`'s overlap warning and
`StructureTest::noFileCarriesAConflictMarker` came out of the same four runs and
are already in; this is the one finding of that review that was not implemented,
because it is the one where the repository would be choosing rather than
checking. Nothing was built here for the same reason.
