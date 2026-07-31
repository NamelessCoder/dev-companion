---
date: 2026-07-31T19:30:50+00:00
category: idea
status: open
model: nemotron-3-ultra-free
tool: typo3_project_scope, typo3_extension_scope, typo3_architecture_lookup, typo3_changelog_lookup, typo3_label_lookup, typo3_icon_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. What the server saved me from:

## Observation

Task: TYPO3 extension conformance audit. What the server saved me from:
- typo3_project_scope: instantly gave TYPO3 14.3.5, PHP ^8.4 (actual 8.3.23), project extension printworks_sitepackage, site config (main at site-new.ddev.site, set bk2k/printworks), and declared commands (composer test:unit/function, npm test:e2e). Without this I would have had to parse composer.json, package.json, and site config manually.
- typo3_extension_scope: returned complete registration picture — 2 custom tables (tx_printworks_hero_slide, tx_printworks_product), 7 content elements (5 with templates, 2 missing templateName), 3 icons, Fluid roots, 6 classes (Controller, Domain, EventListener, Service), requires, and 3 German-only XLF files. This would have required reading 10+ registration files manually.
- typo3_architecture_lookup (2 calls): provided version-specific conventions for content elements with inline children (TCA, FormEngine, Site Sets, Registering Content Element, Records Without Extbase) and Extbase plugin registration (Extbase Plugins, Routing, Caches, Site Sets). The worked examples (fluid_styled_content, blog_example) pointed to reference implementations. This replaces hours of core code reading.
- typo3_changelog_lookup: filtered v14 deprecations to 30 relevant entries with FullyScanned/PartiallyScanned tags — the Extension Scanner coverage info is critical for upgrade work.
- typo3_label_lookup + typo3_icon_lookup: confirmed all labels/icons exist and are registered, with source files.

Concrete wrong path avoided: I would have assumed the Catalogue/Teaser plugins used lib.contentElement (they don't — finding #3) without the architecture lookup clarifying that plugins are CTypes and need explicit template registration.

## Query

what the server saved me from in extension audit

## Suggestion

These lookups are the core value — keep them fast and version-accurate.
