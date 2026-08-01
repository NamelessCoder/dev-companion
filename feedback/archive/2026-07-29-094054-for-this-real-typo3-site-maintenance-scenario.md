---
date: 2026-07-29T09:40:54+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: e83e65e
subject: "[FEATURE] Name the content elements an extension adds"
tool: typo3_extension_scope
---

# The content elements an extension adds are still not named

## Observation

Most of this note is answered. `typo3_extension_scope` reads what an extension
registers from its own files — the tables its TCA defines and the ones it
extends, its backend modules and routes, its icons, its site sets, its service
tags, its middlewares, its Fluid roots and namespaces, the shape of its
`Classes/` — and `typo3_project_scope` reports the Composer patches.

What is left is the thing a sitepackage question is most often actually about:
which content elements the extension adds. The answer says that it extends
`tt_content`, which is where they are registered, and stops there. The
identifiers themselves sit in the third argument of
`ExtensionManagementUtility::addTcaSelectItem()` — a nested array whose shape
changed inside the covered range, positional on the older line and keyed by
`value` after it — and in the TypoScript of the set that renders them, where
only some of them appear as `tt_content.<identifier>`.

## Query

Evaluate whether this MCP helps maintain and further develop a TYPO3 site: 19 site configurations, local sitepackage and extension, Fluid, TypoScript, TCA, frontend SCSS and DDEV

## Suggestion

Read the identifiers out of the `addTcaSelectItem()` calls, both shapes, and
list them as the content elements the extension adds — with the template each
one renders through where the set says so. A list that is nearly complete is
worth more here than none, as long as the answer says how it was derived.
