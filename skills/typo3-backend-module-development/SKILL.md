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

## Settle the module's shape before the first file

Where the module hangs, what it is called, who may open it and what it keeps per
user are one decision rather than four, because each of them constrains the next
— and `typo3_task_guide` returns what each costs to get wrong. Take them from
what this installation already registers rather than from the extension being
copied.

What neither answers is the user's: which parent an editor should find this
under, and whom it is for. Ask before registering rather than after, because a
module is placed for as long as it exists and a rename is carried by the
registration afterwards rather than undone.

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
  changed paths only for an actual core patch: it answers
  `Build/Scripts/runTests.sh`, which exists in the core repository alone. Never
  present it as a project command, whatever the task turns out to be — what
  decides is the work, not whether the tool is in the list.
- Re-run the lookups when the target version, extension, or implementation
  choice changes; do not treat an earlier result as universal.

## Prove it by opening it

The work is done when the module has been opened, not when its files are
written. Five things fail there and nowhere earlier: the entry appears where it
was placed, the route answers, the icon renders, the labels resolve instead of
showing their keys, and the access declared lets in whom it claims to. A green
suite reports none of the five, and a registration file has no schema behind it
to fail against.

Opening it needs a backend that runs. Where this repository has none, that is a
prerequisite of the task rather than a part of it, and it has an owner:
`typo3-development-installation` brings one into existence or boots the one the
repository declares. Cross to it at the verified point — the module is written
and registered, and nothing has been opened — and come back to these five. Where
that is not wanted, report the module as written and unproven and say which of
the five is unestablished. Correcting a mistake from a test afterwards is what
the lookups above are there to make unnecessary.

## Commit it

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it. The scope settled before the first file is what picks that argument:
a module in an extension, a sitepackage or a site project is committed in that
repository, and only an actual core patch leaves the argument at its default.

This skill owns backend module registration, controllers, routes, and backend UI
implementation, up to the module being opened.

When implementation is verified and only documentation remains, stop this
workflow. Activate `typo3-extension-documentation` before editing documentation.
Carry forward the extension key, target version, and verified public behavior;
let that skill select the documentation surface. Documentation for functionality
encapsulated in an extension belongs to that extension, not to the project
around it.

Stop and activate `typo3-extension-testing` before changing test infrastructure,
`typo3-extension-conformance` before broadening into an audit, and
`typo3-content-element-development` before implementing a content element or its
backend preview.
