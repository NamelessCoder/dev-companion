---
name: typo3-backend-module-development
description: Build or change a TYPO3 backend module in a core checkout, extension, site package, or Composer project. Use for module registration, controllers, routes, backend templates, buttons, status markers, labels, icons, and the rest of a module's own backend UI, where the implementation must match the active installation and TYPO3 version. The backend preview of a content element in the page module is not a module and belongs to content-element work.
---

# TYPO3 Backend Module Development

Establish the task's scope and query the sources that own each fact before
writing code. Keep this skill as routing only; never retain markup, identifiers,
labels, API signatures, or version facts here.

## Gather the evidence

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable.

Then, for this workflow:

- `typo3_server_scope` for the knowledge depth available and any tool the caller
  excluded.
- Decide whether this is a core patch, extension, or site task from the task and
  the affected paths. If the signals disagree, state the uncertainty; do not
  attach core-only checks to project work.
- `typo3_backend_module_lookup` before choosing the module identifier, parent,
  position, route, or registration shape.
- `typo3_icon_lookup` for every proposed module or action icon. Do not invent an
  identifier.
- `typo3_label_lookup` with words from recurring backend wording before adding a
  label, and `typo3_translation_domain_lookup` for the extension's own XLF path.
- `typo3_component_lookup` with the target TYPO3 version before writing buttons,
  status markers, cards, tables, or other backend markup.
- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version for module registration, controller, routing, security, and
  other official API details.

If an installation-backed lookup is unavailable, report that gap and its
diagnosis. Do not turn it into an empty registry or replace it with memory. If
live documentation is unavailable, keep the failure distinct from no match.

## Implement and verify

- Read the existing extension and nearby working modules before editing. Tool
  answers describe conventions and registrations; they do not inspect the
  caller's changed files.
- Reuse the module API, backend components, labels, and registered icons. Avoid
  custom CSS that recreates the TYPO3 backend.
- Keep project paths and commands in the project. Use core-only checks,
  changelogs, Gerrit rules, and `Build/Scripts/runTests.sh` only for an actual
  core patch.
- Run the repository's own relevant checks. Use `typo3_test_run_guide` with the
  changed paths only for an actual core patch: it answers `Build/Scripts/runTests.sh`,
  which exists in the core repository alone. Never present it as a project
  command, whatever the task turns out to be — what decides is the work, not
  whether the tool is in the list.
- Re-run the lookups when the target version, extension, or implementation
  choice changes; do not treat an earlier result as universal.

This skill owns backend module registration, controllers, routes, and backend UI
implementation.

When implementation is verified and only documentation remains, stop this workflow.
Activate `typo3-extension-documentation` before editing documentation.
Carry forward the extension key, target version, and verified public behavior;
let that skill select the documentation surface.
Documentation for functionality encapsulated in an extension belongs to that
extension, not to the project around it.

Stop and activate the testing skill before changing test infrastructure, the
conformance skill before broadening into an audit, and the content-element skill
before implementing a content element or its backend preview.
