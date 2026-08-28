---
id: D-SCO-005
title: The installation is evidence about the task, and the weakest kind
date: 2026-07-29
status: confirmed
coveredBy:
  - ScopeTest::inASiteInstallationTheWorkIsOutsideTheCore
  - ScopeTest::namingAnInstallationToReadDoesNotMoveWhereTheWorkIs
  - ScopeTest::theNamedInstallationIsTheEvidenceWhereNothingElseIs
---

# D-SCO-005 — The installation is evidence about the task, and the weakest kind

**The signals for work outside the core are ordered by how specific they are,
and the kind of installation the session sits in is the last of them.**

`outsideCore` was decided by phrases. "bootstrap_package" as the area matched
none of them, so a third-party extension got the core's changelog rules until
the caller wrote "not TYPO3 core" into the prose. Three structural signals are
now consulted, and the installation this server was started in is one of them.

## Decided

- An ordering rather than a vote. Core work named outright wins, then an
  outside-core marker, then an area the installation knows as somebody's
  extension, then a path in extension layout, and last the kind of installation.
  Each step is more specific than the one below it, so the general signal never
  overrules a statement about the task.

## Assumed

- In a Composer project, work is not core contribution unless something says it
  is. That is what the checkout is: core patches are written in a core monorepo,
  and a site installation that vendors `typo3/cms-*` is not one.
- A path starting with `Classes/`, `Configuration/` or `Resources/` is inside a
  package. From the core root nothing is named that way — `typo3/sysext/<key>/`
  or `Build/` comes first.

## Wrong if

- A core contributor runs their client from a site installation that has the
  core checked out somewhere else, or passes paths relative to the system
  extension directory they are standing in. Both then read as extension work,
  and the way out is to say `typo3/sysext/` once.
- `TYPO3_DEV_COMPANION_ROOT` points at a site installation for the label and
  icon lookups while the questions are about the core. The variable now moves
  the boundary too, which it was not introduced to do.

## Since then

The second **Wrong if** was happening: one value answered two questions, which
installation to read and which repository the work is in, so a core path came
back `project`. They are separated — `Instance::startedIn()` walks up from where
the server was started and the variable keeps the reading alone. Its back half
is settled too: inside a core checkout a `Classes/` path is one a contributor
typed from the system extension directory, so the package layout counts only
where the session is not standing in the core, which is the mirror of the gate
`Build/Sources/` already had. No order changed.

## Confirmed on 2026-08-22

The statement was read against `Scope::of()` and the ordering is as written.
`Instance::startedIn()` is the closing `match`, below every marker, below what
the installation knows the path as, and below both layout gates. Both **Wrong
if** have been gone back to in the two sections above, and the three tests this
entry names still stand in `ScopeTest`.
