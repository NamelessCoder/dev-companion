---
id: D-DOC-033
title: The derived half of a tool page stays committed
date: 2026-08-14
status: open
---

# D-DOC-033 — The derived half of a tool page stays committed

**The derived half of `documentation/server/tools/` stays in the checkout,
because it is the whole documentation diff of most commits that change a tool.**

Reproducibility was the argument for generating it at render time, and it is not
the question. What decides it is what the lines are read for.

## Evidence

- Measured on 2026-08-14 over the 28 pages: 9,011 derived lines — the head of
  each page, plus the whole page for the eight tools in `ToolCalls::derived()` —
  against 8,815 recorded ones.
- Over the last thirty commits touching `src/Tool/`, 27 carried a page diff and
  every one of those touched the derived half; nine touched the recorded half as
  well. So eighteen commits' entire documentation change was derived, and
  generating it would have left them with none.
- The diff is produced rather than chosen. `ToolSurfaceTest` holds every page to
  the registry, so a commit that rewrites a description and leaves the page
  standing fails `composer ci` — nobody types these lines and nobody can skip
  them.
- It has been read at least once. `3501f48d` is a bugfix whose body states which
  hint ids the answer stopped offering and which 19 it now withholds, and the
  1,063-line diff on `typo3_hint_lookup` is the only place that is visible.
- Whether the other twenty-six were read is not answerable here. This repository
  merges fast-forward and its five merge commits are all from 2026-08-01 and
  2026-08-02, so nothing records what somebody looked at before a branch came
  home, and no feedback, decision or commit message reports a page diff catching
  anything.

## Decided

- The maintainer, asked on 2026-08-14 with three answers priced: the derived
  half stays committed and the card is closed. What was left was a want rather
  than a fact, which is why it was asked rather than settled.
- Generating the whole derived half in `documentation:prepare` is rejected. It
  cuts through a page, which `D-DOC-016` decided against: the eighteen recorded
  pages would open at `Answered` with no heading and no label, and `readme.rst`
  cannot go with them because its head is written by hand above the generated
  listing.
- Generating the eight wholly derived pages alone is rejected too, although it
  keeps every remaining file a whole document. `typo3_hint_lookup` is one of the
  eight, so it is the answer that would have removed the one diff demonstrably
  read.
- The recorded half was never on the table. `tools:record` refuses without
  `.checkouts/`, and this repository is the only place that evidence exists.

## Assumed

- The cost of carrying them is the review diff and nothing else. One command
  rewrites them and `composer ci` catches a session that forgets, so a stale
  page is reported before the commit rather than found by a reader.
- 9,011 lines in a directory nobody edits do not get in the way of reading a
  diff that matters. Nothing measured says they do, and nothing measured says
  they do not.

## Wrong if

- Somebody reports skipping a tool commit's review because the diff was mostly
  generated. Then the lines are in the way of exactly the reading they exist
  for.
- A page diff is never again the only place a behaviour change is visible.
  `3501f48d` is one instance, and one instance is what this rests on.
- The derived half grows enough that `bin/cli tools:index` stops being a cheap
  thing to run before committing — the point at which forgetting it becomes the
  ordinary case rather than the exception `composer ci` catches.
