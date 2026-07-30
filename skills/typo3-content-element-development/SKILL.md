---
name: typo3-content-element-development
description: Build or refactor TYPO3 frontend content elements in extensions and sitepackages. Use for CType registration, editor-facing data models, inline child records, TCA, generated schema, TypoScript rendering, Fluid templates, AssetCollector CSS or JavaScript, previews, localization, and content-element tests.
---

# TYPO3 Content Element Development

Design the editor workflow before choosing fields or rendering. Build the
smallest domain model that owns its content and follows the active project's
file organization. Keep this skill as routing and design method; obtain
versioned TYPO3 facts from the MCP tools.

## Establish evidence

1. Call `typo3_project_scope` and `typo3_extension_scope`.
2. Inspect nearby content elements, TCA files, TypoScript imports, templates,
   assets, labels, schema and tests in the checkout.
3. Call `typo3_task_guide`.
4. Call subsystem-specific `typo3_architecture_lookup` queries with concrete
   paths.
5. Call `typo3_documentation_lookup` for version-specific TCA, DataHandler,
   Fluid or AssetCollector APIs.
6. Search labels and icons through `typo3_label_lookup` and
   `typo3_icon_lookup` before adding or choosing them.

The MCP does not read the working tree. Treat its answers as versioned
conventions and verify every concrete path and project pattern locally.

## Choose the content model first

Describe how an editor creates, orders, translates, hides and deletes the
content before implementing it.

- Use fields on `tt_content` for one bounded element.
- Use a dedicated child table with `type=inline` when an element owns a
  repeatable ordered collection such as slides, tabs, accordions or cards.
- Use references to existing records only when reuse is an explicit
  requirement and the lifecycle, visibility, localization and duplicate
  rendering behavior are understood.
- Use a container of arbitrary `tt_content` only when arbitrary nested content
  is a deliberate requirement. Do not substitute the generic `records` field
  for an owned repeatable model.

Read [references/checklist.md](references/checklist.md) before creating or
changing a content element.

## Keep each element cohesive

- Put shared CType groups or truly cross-element changes in the generic
  `tt_content` override.
- Put one element's fields and registration in a named sibling override.
- Put a custom record table in its own TCA file.
- Put one element's rendering in a dedicated TypoScript file below the
  project's established content-element directory.
- Keep the Fluid template under the project's content-element template root
  and follow the CType-to-template naming convention.
- Load element-only CSS and JavaScript from the template through the Fluid
  AssetCollector. Use global page inclusion only for assets required site-wide.

## Implement the full lifecycle

- Configure sorting, workspaces, localization, enable fields and cascading
  behavior for owned child records.
- Use domain label files for backend fields and frontend message files for
  visitor-facing text. Do not hard-code JavaScript state labels.
- Add a useful backend preview for a custom CType.
- Use Core data processors where they express the query; add a custom
  processor only for behavior the Core processors cannot represent.
- Keep raw Fluid output limited to markup already rendered by a trusted TYPO3
  rendering API.

## Verify at the right layers

- Validate PHP, YAML, Fluid, XLIFF and TypoScript through commands the project
  actually declares.
- Add unit tests only for isolated logic.
- Add functional coverage for TCA, schema, inline persistence, localization
  and rendered output.
- Add browser coverage when JavaScript interaction, editor workflow or
  accessibility is part of the feature.
- Re-run `typo3_extension_scope` after the change and report parser blind spots
  separately from implementation defects.

This skill owns content-element architecture and implementation. Use the
testing skill for test infrastructure, the documentation skill for manuals,
and the conformance skill for a broader extension audit.
