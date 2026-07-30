---
date: 2026-07-30T17:38:21+02:00
category: tool-gap
status: open
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup, typo3_changelog_lookup
---

# Extension upgrades need a task-owned workflow

## Observation

An extension author adding support for a new TYPO3 major while retaining the
previous major receives useful project facts, changelog entries and subsystem
conventions, but no task-owned workflow that carries the change from discovery
through implementation and proof. The broad conformance skill can assess
compatibility; it does not own the ordered work of resolving Composer and PHP
constraints, finding scanner and deprecation evidence, deciding which code can
remain shared across both majors, adapting registrations and configuration, and
proving the supported matrix. Scenario `EXT-01` remains partial for this reason.

## Query

Our extension supports TYPO3 12 and 13. The next major is out and I want to add
support for it without dropping 13. Go through the extension, find what breaks,
and fix it.

## Suggestion

Re-run `EXT-01` against the current server, then add a thin
`typo3-extension-upgrade` skill around the gaps the run demonstrates. Keep
version facts in installation-backed changelog results, official documentation
and the checkout; let the skill own evidence order, multi-major decisions,
implementation boundaries and the test matrix. Add the resulting acceptance
criteria to the forward scenario and guard the routing and ownership boundary in
`SkillTest`.
