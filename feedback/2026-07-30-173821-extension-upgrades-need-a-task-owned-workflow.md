---
date: 2026-07-30T17:38:21+02:00
category: tool-gap
status: open
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup, typo3_changelog_lookup
---

# Extension upgrades need a task-owned workflow

## Observation

**Trimmed 2026-07-31**, against a recorded `REVIEW-02` run in the environment
this note had none for: `/home/benji/projects/news` on `13.x`, declaring
`^12.4.37 || ^13.4.15` against an installed 13.4.33 — an extension a major
behind the world. Two of the three things the note claimed now have forward
evidence, and one of them turned out to be wrong.

Wrong: that the broad conformance skill cannot make the multi-major decisions.
It can, when a review walks past them. The run produced a *Correct trade-offs —
not findings* section that argues `event.listener` and `console.command` YAML
registration is **required** while 12.4 is supported because the attributes are
v13-only, and reads `(new Typo3Version())->getMajorVersion() < 13` as the
branching the declared range demands rather than as debt. Shared-versus-
version-specific is not the gap.

Standing, and now demonstrated: what is missing is the **order**, and the
evidence step that only an ordered workflow makes unconditional. Given project
facts, changelog entries and subsystem conventions, the run swept what its
checklist enumerates and never swept the installed core's deprecations against
the extension's own call sites — so it reported the frontend code as carrying no
superglobal access at all, across 24 `$GLOBALS['TSFE']` sites in 11 files, with
`@deprecated since TYPO3 v13, will vanish during v14 development` sitting in the
installed core. It found `renderStatic()` only because a ViewHelper finding
walked it there. The extension scanner was never reached at all, on a checkout
that has one available. That failure was filed on the review side and answered
there: the sweep is step 5 of `skills/base.md` since 2026-07-31, which is the
step this skill starts from rather than restates
([`R-SKL-5`](../requirements/task-skills/skl-5-the-order-a-task-starts-in-is-written-once.md)).
This note keeps the upgrade side, where the same step decides whether "go through
the extension and find what breaks" has an answer or a guess.

The environment half is closed: `E-EXT` is played by a checkout with a major in
front of it, named in `todo.md`, and `scenarios/runs/REVIEW-02.json` is the run.

## Query

Our extension supports TYPO3 12 and 13. The next major is out and I want to add
support for it without dropping 13. Go through the extension, find what breaks,
and fix it.

## Suggestion

A thin `typo3-extension-upgrade` skill that owns the ordered work and nothing
else: the evidence step first — installed changelog queried by
`type: deprecation` per declared major from the surface `typo3_extension_scope`
reports, the extension scanner, the deprecation annotations, with the
`FullyScanned` / `PartiallyScanned` tag carried into the answer because it says
whether the scanner can be trusted for the rest — then the Composer and PHP
constraint resolution, the implementation boundary, and the supported matrix as
proof. Version facts stay in installation-backed results and official
documentation. Add the acceptance result to the forward scenario, and guard the
routing and ownership boundary in `SkillTest`.
