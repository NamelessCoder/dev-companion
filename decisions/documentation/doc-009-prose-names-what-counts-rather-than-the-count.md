---
id: D-DOC-009
date: 2026-08-03
status: open
---

# D-DOC-009 — Prose names what counts rather than the count

**A count of something that grows does not go into prose; the thing and the
command that counts it do.**

`readme.md` was about to say "34 files holding 120 hints". Both numbers were
true while they were typed and wrong at the next commit, and nothing in this
repository fails when they turn.

## Evidence

- The corpus went from 66 hints to 120 in one day, across four commits. Any
  count written into the readme during that day would have been stale before it
  was read.
- Nothing checks a number in prose. `prose:check` measures sentence length,
  `links:check` resolves paths, and neither can tell that a figure has drifted —
  so a reader believes it and a maintainer never sees it.

## Decided

- Name the thing and where the live number is: "one file per subject, which
  `bin/cli hints:coverage` counts". Where even that is more than the sentence
  needs, "and many more" is the whole of it.
- A number belongs where it was measured and dated. A decision records what a
  sweep found on the day it ran, which is why the counts in `D-KNW-032` and
  `D-KNW-033` stay: they are evidence rather than a description.
- A report prints what is true when it runs. That is what `hints:coverage`,
  `todo:list` and `unresolved:list` are for, and why none of them is transcribed.

## Assumed

- A reader who wants the number will run the command. The alternative is a
  generated listing in the readme, which is what `decisions:index` does for the
  entries below it — worth building where the listing is the point, and not
  worth it for one clause.

## Wrong if

- A count turns up in `readme.md`, `AGENTS.md` or a tool description again and
  survives a review, which would mean the rule is not where an author reads it.
- Somebody strips the numbers out of a decision to satisfy this, which is the
  opposite: an undated, unsourced claim replacing dated evidence.
