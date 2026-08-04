---
name: typo3-extension-upgrade
description: Carry a TYPO3 extension, sitepackage, or project package from the TYPO3 versions it supports today to another set — adding support for a new TYPO3 major without dropping the old one, dropping one that is no longer maintained, or replacing what a major deprecated or removed. Use when the request is to make a package run on a newer TYPO3 or PHP version, to find and fix what breaks in it, to widen or narrow its supported version range, to resolve version conflicts that block an update, or to prove that a package still works on every version it claims.
---

# TYPO3 Extension Upgrade

Cross a package from the range it declares to the range it is meant to declare,
in an order where each step decides what the next one is worth. Keep this skill
as routing and workflow; never retain version-specific APIs, constraints,
replacements, or the contents of a changelog — every one of those is a property
of the installation being read and of the target being aimed at.

## The order

1. Work through [references/base.md](references/base.md). It fixes what this
   package is, what it ships, and — as its last step before the checkout — the
   sweep of the installed core's deprecations over that surface. This workflow
   starts from the result of that sweep rather than restating it.
2. Widen the sweep, below, into the work list.
3. Resolve the range the package may declare, below. Not before: what breaks is
   what decides whether a range is reachable at all.
4. Change what the list justifies, and nothing else.
5. Prove it against every combination the package declares.

## Widen the sweep into a work list

The base sweeps one source. An upgrade needs three, because each reaches call
sites the others cannot:

- **The changelog**, as the base sweeps it, and `typo3_changelog_lookup` again
  with `type: breaking` — same tags, same majors, still no query. A review asks
  what will stop working; an upgrade also asks what already has.
- **The Extension Scanner**, in the installation's own Upgrade module — it needs
  a reachable backend and an administrator, and it reads the extension's
  installed files. It finds the call sites of what its matchers cover, and the
  `FullyScanned` / `PartiallyScanned` tag the base carries out of the changelog
  is what says whether its silence on an entry means anything. A clean scan for
  a partially scanned entry is not a result; those call sites are yours to find.
- **The deprecation annotations on what this package actually calls**, in the
  installed core and in the packages it depends on. A changelog entry is per
  release and is reached by the tags of the system extension it sits in; an
  annotation sits on the class, method or property itself, so a symbol whose
  entry falls outside the tags the sweep named — or carries no `ext:` tag at all
  — is reached only this way, and a class deprecated as a whole takes every call
  site of it with it.

Both the changelog and the scanner answer from the **core that is installed**,
which is the boundary this whole order rests on: they say what this package owes
the majors it already runs on, and they do not know what the target major
changed until the installation is on it. Until then the target's changes come
from official documentation for that version, never from memory — a list of
"what the new major changed" written from recall reads exactly like one that was
looked up. Once the resolution below puts the installation on the target, run
the sweep again there; that second pass is what says the work is done.

Write the result down before changing a file: one entry per call site, with the
identifier, the path and line in this package, which declared major deprecates
or removes it, and which of the three established it. That list is the work, and
it is what the result closes on — including the entries that came back empty,
with the majors they covered.

## Resolve the range, rather than assert it

- The declared range is in the Composer manifest and in `ext_emconf.php`, and
  the two either say the same thing or the difference is itself a finding: for a
  non-Composer installation `ext_emconf.php` is the only constraint that
  governs.
- The PHP range is the intersection of what every declared TYPO3 major supports,
  never the PHP the current machine happens to run.
- Where the package requires a system extension, establish that the target still
  ships it: `typo3_system_extension_lookup` answers by key and package name and
  does not need it installed. One that stopped being part of the core is a
  requirement that cannot resolve, and the replacement is a decision, not a
  rename.
- Let the dependency solver answer, and quote what it printed. A constraint that
  should work and one the solver accepts are different claims, and the solver
  reports a third-party dependency without a release for the target as a
  conflict rather than as advice — that dependency then decides the schedule.
- Dropping a major is the user's decision, never one taken to make the code
  simpler. Widening to a new one keeps every version currently declared unless
  the request says otherwise.

## The boundary of what may change

The **lowest declared major decides every shape in the package**. A registration
form, attribute or API introduced later cannot replace one that still has to
work there, so a runtime branch on the major and a registration written the
older way are what the declared range requires — not debt to clean up. Say so
where the code already does it; the alternative is an upgrade that breaks the
version it was told to keep.

Where a replacement belongs to a subsystem the base did not have in scope, ask
its conventions before writing it: `typo3_hint_lookup` with the concrete paths,
and `typo3_documentation_lookup` with the target version where the official API
decides the shape. Where nothing in the declared range replaces a removal, that
is the answer — one package version cannot serve both, and choosing silently is
how a supported version stops working without anyone noticing.

Change what the work list justifies. An upgrade is not a modernization, a
cleanup or a rewrite: anything the list did not produce goes to the workflow
that owns it — `typo3-extension-conformance` for what else is wrong with the
package, `typo3-extension-testing` for coverage the upgrade wants but does not
have, `typo3-extension-documentation` for the manual that now describes a
different range.

## Prove it on every version it claims

1. Build the matrix from the declaration, not from convenience: every TYPO3
   major the package declares against the PHP versions that major supports.
2. Resolve each cell before running it, and treat a cell that will not resolve
   as a result — it is the finding, and skipping it is what lets a package
   declare a version nobody has ever installed it on.
3. Run the repository's own commands per cell, the checks first. A step that
   runs in only one cell leaves the others unproven, and that includes the ones
   the repository's CI declares.
4. Report the work list with every entry closed or explicitly left open, the
   resolutions with what the solver printed, what changed and what deliberately
   did not, and the matrix cell by cell. A cell nobody ran is named as unrun
   rather than left out — the matrix is the claim the package makes about
   itself, and an unrun cell is the part of that claim nothing stands behind.
5. Draft the message with `typo3_commit_message_guide` and `workflow="project"`.
   The crossing lands in the package's own repository, and the range it now
   declares is what the message is about.

This skill owns crossing a package from one supported range to another: the
sweep that says what breaks, the constraints that say what may be declared, the
changes those two justify, and the proof per declared combination. It does not
own deciding whether the package is otherwise sound, establishing a harness that
is missing, or rewriting the documentation — each of those is named above with
the workflow it belongs to, and the crossing to it is explicit: state the
verified point the upgrade reached, stop before editing that owner's files, and
carry across the range and the call sites already established.
