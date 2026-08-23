---
id: D-DOC-015
title: A renumber moves what a link path settles and names the rest
date: 2026-08-04
status: open
coveredBy:
  - RenumberTest::aLetterSuffixIsAnotherEntryAndStaysWhereItIs
  - RenumberTest::aReferenceNoLineSettlesIsNamedRatherThanMoved
  - RenumberTest::everyMentionIsEitherMovedOrNamed
  - RenumberTest::noPathIsLeftPointingAtTheOldFile
---

# D-DOC-015 — A renumber moves what a link path settles and names the rest

**`bin/cli decisions:renumber` rewrites every reference whose own line names the
entry's file, and prints every reference that does not.**

Ten sessions read one `main` and hand out one id twice. The collision is caught
on the rebase and costs minutes; the renumbering that follows it is what has
gone wrong, twice, silently.

## Evidence

- Four runs of ten todos produced seven duplicate ids. Twice the files naming
  the old number did not all mean one entry: `R-PRJ-008` rested on the
  `D-ANS-013` that kept its number while five other files meant the one that
  became `D-ANS-015`, and `ans-006` named the `D-ANS-016` that stayed while a
  requirement and a todo named the one that became `D-ANS-019`.
- Both mis-pointings were **bare** references — an id in a `restsOn:` and an id
  in a sentence. No citation carrying a link path has yet gone wrong.
- What is checked is narrower than it looks. `requirements:check` fires on a
  `restsOn:` naming no decision, and `decisions:check` on an `R-` citation
  naming no requirement. A bare `D-` id in prose, in PHP or in a link path is
  checked in neither direction, and a renumbering that leaves one behind is
  silent because the entry it now points at exists.
- A sweep of everything git tracks on 2026-08-04, at 221 decisions, found 3026
  mentions of a decision id. 1356 sit in the listings `decisions:index`
  regenerates and 438 are an entry's own front matter and heading. Of the 1232
  written by hand, 260 carry a link path that says which entry is meant, 86 are
  a requirement's `restsOn:`, and 886 are bare — a mean of 3.97 per entry, a
  median of 3, 22 on the worst, and none at all on 35 of them.
- The linked share is rising. The same sweep on 2026-08-02, at 101 decisions,
  found 66 of 601 hand-written mentions carrying a link — 11%, against 21% now.
- Renumbering `D-GUI-002` to `D-GUI-010` in a copy of this checkout moved 7
  mentions across 4 files and named 8 across 7. `decisions:check` and
  `links:check` were green immediately afterwards; `requirements:check` failed
  on `R-SKL-017 rests on D-GUI-002, which no decision has`, which is one of the
  8 and the only one anything would have caught.

## Decided

- A command that renumbers, rather than reserving a block of numbers at
  `todo:claim` or taking the number out altogether. Reserving removes the
  collision and buys it with gaps, which cost nothing — but a claim cannot know
  which group it will write into, so it reserves in every one, and ten claims of
  ten apiece spend 100 numbers per group per run. Three digits then lasts about
  ten runs, and four happened in three days: it is the option that breaks
  `D-DOC-005`'s assumption rather than a check. Taking the number out is 601
  hand-written mentions that are not mechanical, because a citation handle has
  to become a name somebody chose.
- The command's value is naming, not moving. A reference is rewritten only where
  its own line says which entry is meant — a link path, and the reference
  definition a generated listing ends with — and every other one is printed with
  its file, its line and its text, for a person to read against
  `git diff main -- <file>`.
- A `restsOn:` is named rather than moved, although the id in it is the one
  thing a check reads. It is the reference that went wrong the first time, and
  it says nothing about which entry it means. What existence-checking buys is
  that leaving it behind fails loudly once the old number is free — and in the
  collision case it is not free, which is why it is printed as well.
- Every mention is accounted for. A line naming the old id is rewritten or
  reported and never neither, which is the property that makes the printed list
  worth reading: a person handed it has been handed all of it.
- The generated listings are put back in order, but only where they already
  carried the entry. The id is what a group sorts on, so an in-place rewrite can
  leave a listing `decisions:check` disagrees with. Where the listing never
  carried the entry — a branch that added it and left the block alone, which is
  what a worktree is told to do — nothing writes one either.
- Decisions only. Requirement ids collide by the same mechanism, and no recorded
  collision has been one; the second corpus is speculation until it happens.

## Assumed

- That a link path names the entry that was meant. The file is what decides, and
  a link written to the wrong file was wrong before any renumber touched it.
- That a link's label and its path stay on one line. `bin/cli prose:format`
  never breaks inside a link, so the label a reference-style link uses is
  settled by the definition in the same file.
- That renumbering never changes the group. The prefix names the directory, so
  moving it is a re-filing and what the entry is about moves with it.

## Wrong if

- A renumber leaves a reference that was neither rewritten nor printed. That is
  the failure this exists to prevent, and it would be silent.
- Somebody rewrites the printed list without opening `git diff main -- <file>`.
  The list makes the wrong move as cheap as the right one, and nothing here can
  tell which was made.
- The linked share keeps rising until the naming half is what costs. Requiring
  the link form for a cross-entry citation would then be the cheaper fix: it is
  the only option measured that would have caught both mis-pointings rather than
  reported them, and it composes with this one.
- A requirement id collides and there is no `requirements:renumber` to move it.
- A branch's listing does carry the entry, the regeneration fires in a worktree,
  and the merge conflicts on the one block a branch is told not to touch.

## Since then

Reserving the numbers at `todo:claim` was asked for again on 2026-08-13, by a
session that had just worked three rounds of eight claims: five of its 24
branches collided on an id, and every one of them was the newest number in its
group plus one. It stays rejected, and the first **Decided** can now be priced
rather than estimated. That night wrote 21 decisions, one per commit, into five
of the thirteen groups — eight in `knowledge/`, seven in `task-skills/`, two
each in `answers/` and `feedback/`, one in `documentation/`. A claim still
cannot know which group it will write into, so even the cheapest reservation —
one number per group per claim, which is what the feedback asks for — spends 24
in every group, 312 for the 21 that were written, and leaves `knowledge/` at 094
rather than at 078. The number would then count claims rather than entries,
which is `D-DOC-005`'s assumption rather than a check.

It would also bind only the sessions that read it. `bin/cli todo:next` in the
main checkout computes the newest number plus one as before, and a session
writing a second entry in one group is past its reservation by the same
arithmetic. What the collisions cost on that run is the other side: 10
`decisions:renumber` calls and 30 `todo:home` invocations for 24 branches, each
renumber followed by a hand edit of the mentions the command names and does not
move — the split the reporting session says it would keep. The refusal is what
made the repair cheap: `todo:home` merges nothing where the rebased branch is
red and leaves the worktree standing, so every collision was repaired where it
happened rather than reverted off `main`. `D-FBK-046` is where the same
feedback's fallback waits, as that entry's first **Wrong if** — the renumber
moves into `todo:home` if naming the id, both files and this command in the
failure turns out not to be enough.
