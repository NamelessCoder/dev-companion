---
name: typo3-content-element-development
description: Build or refactor TYPO3 content elements on both sides — what an editor fills in and sees in the page module, and what a visitor gets rendered. Use for CType registration, TCA, inline child records, a custom backend preview, TypoScript, Fluid, assets, labels and tests.
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Content Element Development

Design the editor workflow before choosing fields or rendering. Build the
smallest domain model that owns its content and follows the active project's
file organization. Keep this skill as routing and design method; obtain
versioned TYPO3 facts from the MCP tools.

## Establish evidence

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable.

Then, for this workflow:

- `typo3_documentation_lookup` for version-specific TCA, DataHandler, Fluid or
  AssetCollector APIs.
- `typo3_label_lookup` and `typo3_icon_lookup` before adding a label or choosing
  an icon identifier.
- Read the nearby content elements, TCA files, TypoScript imports, templates,
  assets, schema and tests — the project's file organization is the thing a new
  element has to fit, and only the checkout has it.

## Choose the content model first

Describe how an editor creates, orders, translates, hides and deletes the
content before implementing it.

- Use fields on `tt_content` for one bounded element.
- Use a dedicated child table with `type=inline` when an element owns a
  repeatable ordered collection such as slides, tabs, accordions or cards.
- Use references to existing records only when reuse is an explicit requirement
  and the lifecycle, visibility, localization and duplicate rendering behavior
  are understood.
- Use a container of arbitrary `tt_content` only when arbitrary nested content
  is a deliberate requirement. Do not substitute the generic `records` field for
  an owned repeatable model.

Read [references/checklist.md](references/checklist.md) before creating or
changing a content element.

## Keep each element cohesive

- Put shared CType groups or truly cross-element changes in the generic
  `tt_content` override.
- Put one element's fields and registration in a named sibling override.
- Put a custom record table in its own TCA file.
- Put one element's rendering in a dedicated TypoScript file below the project's
  established content-element directory.
- Keep the Fluid template under the project's content-element template root and
  follow the CType-to-template naming convention.
- Load element-only CSS and JavaScript from the template through the Fluid
  AssetCollector. Use global page inclusion only for assets required site-wide.

## Implement the full lifecycle

- Configure sorting, workspaces, localization, enable fields and cascading
  behavior for owned child records.
- Use domain label files for backend fields and frontend message files for
  visitor-facing text. Do not hard-code JavaScript state labels.
- Add a useful backend preview for a custom CType.
- Use Core data processors where they express the query; add a custom processor
  only for behavior the Core processors cannot represent.
- Keep raw Fluid output limited to markup already rendered by a trusted TYPO3
  rendering API.

## Verify at the right layers

- Validate PHP, YAML, Fluid, XLIFF and TypoScript through commands the project
  actually declares.
- Add unit tests only for isolated logic.
- Add functional coverage for TCA, schema, inline persistence, localization and
  rendered output.
- Treat a functional frontend subrequest as proof of server-side HTML rendering
  and AssetCollector registration only. It does not execute JavaScript, apply
  CSS, measure layout or prove interaction. Report that boundary explicitly.
- Add browser coverage when JavaScript interaction, editor workflow or
  accessibility is part of the feature.
- Re-run `typo3_extension_describe` after the change and report parser blind
  spots separately from implementation defects.

## Commit the element

`typo3_commit_message_guide` with `workflow="project"` drafts the message and
checks it. The element lands in an extension or a sitepackage, which is the
workflow that argument names; the default is the core's.

This skill owns content-element architecture and implementation. Activate
`typo3-extension-testing` for test infrastructure,
`typo3-extension-documentation` for manuals, and `typo3-extension-conformance`
for a broader extension audit — stop before editing that owner's files, and
carry across the extension key, the target version and the behaviour already
verified.
