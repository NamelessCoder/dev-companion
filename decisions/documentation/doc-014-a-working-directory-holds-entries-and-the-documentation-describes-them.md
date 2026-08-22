---
id: D-DOC-014
title: A working directory holds entries, and the documentation describes them
date: 2026-08-03
status: open
---

# D-DOC-014 — A working directory holds entries, and the documentation describes them

**`requirements/` and `decisions/` are working directories: each readme carries
what a command writes into it and a line pointing at the page that describes the
entry.**

The description stood in two places at once. Both readmes opened by explaining
what an entry is, and the page that already existed for writing one explained it
again a directory away.

## Evidence

- The split it replaces was never decided. `documentation/readme.rst` stated it
  — the directories say what a thing is and link away for how it is carried out
  — and `D-DOC-004` applied it once, cutting `requirements/readme.md` from 107
  lines to 62. No entry in `decisions/` carried it.
- The duplication was in the same words. `requirements/readme.md` and
  `writing-a-requirement.md` both said that **open** is the backlog, and that a
  requirement nobody has implemented and one that could silently regress are the
  same kind of thing.
- What was left in the readmes past that opening was the group table, the
  three-digit width and the never-reused rule. All three are properties of an
  entry rather than of the directory holding it: choosing a group is part of
  writing one.

## Decided

- The whole description moves to `documentation/requirements/` and
  `documentation/decisions/`, the group tables with it. What stays is the
  generated listing and a pointer.
- `decisions/readme.md` keeps its `## Every decision, newest first` heading,
  because `bin/cli decisions:index` writes the listing under it.
- `todo/` and `scenarios/` are not changed here. They still describe themselves,
  which is the older convention, and moving them is a separate reading.

## Assumed

- That a reader landing in one of the two directories follows one link rather
  than expecting the description where it used to be. Nothing measures which of
  the two entry points is actually taken.

## Wrong if

- An entry is filed under the wrong group, or with a number of the wrong width,
  by somebody who was writing in the directory and never opened the page. The
  checks catch the width; the group is a judgement nothing fails on.
- A session writing into a record directory this repository does not have yet
  has to guess which of the two conventions applies, because `todo/` and
  `scenarios/` still hold their own description.
- The page stops being read whole, because it now carries where an entry is
  filed and how it is written at once.

## Since then

That last one fired, on 2026-08-04 and one day after this entry was written.
Each kind now has two pages rather than one: what it is, with its states and
what it stands on, beside where an entry goes and how it is written. The
statement above is unaffected — both pages are documentation, and the working
directories still hold only what a command writes into them.
