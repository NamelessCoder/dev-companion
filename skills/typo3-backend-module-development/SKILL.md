---
name: typo3-backend-module-development
description: Build or change a TYPO3 backend module in a core checkout, extension, site package, or Composer project. Use for module registration, controllers, routes, backend templates, buttons, status markers, labels, icons, or other TYPO3 backend UI work where the implementation must match the active installation and TYPO3 version.
---

# TYPO3 Backend Module Development

Establish the task's scope and query the sources that own each fact before
writing code. Keep this skill as routing only; never retain markup, identifiers,
labels, API signatures, or version facts here.

## Gather the evidence

1. Call `typo3_project_scope` to identify the project, TYPO3 version, own
   extensions, and commands. If the target extension is known, call
   `typo3_extension_scope` with its key.
2. Call `typo3_server_scope` to establish the active profile and available
   knowledge depth.
3. Decide whether this is a core patch, extension, or site task from the task and
   affected paths. If signals disagree, state the uncertainty; do not attach
   core-only checks to project work.
4. Call `typo3_backend_module_lookup` before choosing the module identifier,
   parent, position, route, or registration shape.
5. Call `typo3_icon_lookup` for every proposed module or action icon. Do not
   invent an identifier.
6. Call `typo3_label_lookup` with words from recurring backend wording before
   adding a label. Use `typo3_translation_domain_lookup` for the extension's own
   XLF path.
7. Call `typo3_component_lookup` with the target TYPO3 version before writing
   buttons, status markers, cards, tables, or other backend markup.
8. Call `typo3_documentation_lookup` with several short English queries and the
   target TYPO3 version for module registration, controller, routing, security,
   and other official API details.
9. Call `typo3_architecture_lookup` with the concrete paths and task for
   conventions that connect these pieces.

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
- Run the repository's own relevant checks. Only for an actual core patch and
  when `typo3_server_scope` reports the all/core contribution profile, use
  `typo3_test_run_guide` with the changed paths. It is unavailable in the
  project profile; never present it as a project command.
- Re-run the lookups when the target version, extension, or implementation
  choice changes; do not treat an earlier result as universal.

This skill owns backend module registration, controllers, routes, and backend UI
implementation. Hand test infrastructure to the testing skill, manuals to the
documentation skill, a broader audit to the conformance skill, and frontend
content elements to the content-element skill.
