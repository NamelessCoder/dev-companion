---
id: D-SCO-005
date: 2026-07-29
status: open
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
- `TYPO3_MCP_ROOT` points at a site installation for the label and icon lookups
  while the questions are about the core. The variable now moves the boundary
  too, which it was not introduced to do.

## Covered by

- `ScopeTest::namingAnInstallationToReadDoesNotMoveWhereTheWorkIs`
- `ScopeTest::whereNothingElsePlacesTheSessionTheNamedInstallationIsTheEvidence`
- `ScopeTest::inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise`

## Since then

The second **Wrong if** was read on 2026-08-02 and it was happening. With
`TYPO3_MCP_ROOT` set to a site installation and the server started inside
`.checkouts/14.3`,
`Scope::of('', 'Add a content element with a backend preview')` came back
`project`, and so did `Build/Sources/Sass/theme.scss` — a path shape that exists
nowhere but the core root. One value was answering two questions: which
installation to read, and which repository the work is in.

They are separated now. `Instance::startedIn()` is the second of them and walks
up from the directory the server was started in; the variable keeps the first
and moves nothing else. Where the walk-up reaches no installation the named one
is the only evidence there is, so it still answers — which is the case
`D-DIS-006` leaves it for, a client that starts this server away from the
session's own directory.

What that costs is one escape that worked by accident. A contributor in the
first **Wrong if** — client run from a site installation, core checked out
elsewhere — could name the core checkout in the variable and have the scope
follow. Now only the reading follows, and the way out is the one this entry
already states: say `typo3/sysext/` once.

The first **Wrong if** was not settled. Its front half behaves as written and is
pinned; its back half, paths passed relative to the system extension directory
the contributor is standing in, is untried — a `Classes/` path is read as
extension work by its shape before the checkout is consulted at all, and whether
that is the wrong order here is a separate question.

## Since then

The back half is settled, and the answer is that the shape stops being evidence
there. `Classes/`, `Configuration/` and `Resources/` are what a package is laid
out as, and from the core root nothing is named that way — this entry's own
second **Assumed** says so — so inside a core checkout such a path is one a
contributor typed from the system extension directory they were standing in. The
gate is the mirror of the one `Build/Sources/` already has: the core layout
counts only where the session could be standing in the core, the package layout
only where it is not standing in it, and where there is no installation at all
both stand, being the only evidence in the call.

It changes no order. `R-SCO-001` still reads the path before anything said about
the call, and every marker above the shape — `typo3/sysext/`, `packages/`,
`vendor/`, an area the installation knows — decides before it as before. What
changed is that one signal is weighed by the checkout it is being read in, which
is what the neighbouring rung already did.

