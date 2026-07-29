---
date: 2026-07-29T16:11:04+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup, typo3_task_guide, typo3_server_scope
directory: /home/benji/projects/site-new
---

# THE SERVER DOES NOT KNOW ABOUT typo3/theme-camino, AND THAT IS THE BIGGEST GAP FOUND IN THIS SESS...

## Observation

THE SERVER DOES NOT KNOW ABOUT typo3/theme-camino, AND THAT IS THE BIGGEST GAP FOUND IN THIS SESSION.

theme_camino is a system extension in the core repository (typo3/sysext/theme_camino, Feature-108539-Default-Theme-Camino.rst, v14.1). It is the core's own default theme and therefore the canonical reference for how a v14 sitepackage is laid out. Nothing in this server points at it. I built a working product section with a directory layout I invented, the user rejected it with "die ordner passen nicht zu den best practices, bitte prüfe camino", and I had to read the extension to find out what the convention is. Every one of those minutes was avoidable.

The convention camino establishes, none of which is in the knowledge base:

  Resources/Private/Templates/          <- the single PAGEVIEW paths.10 root
      Pages/                            page templates
      Content/                          content element templates
      ContentPreviews/                  backend previews of content elements
      Layouts/Pages/                    page layouts
      Layouts/Content/                  content element layouts
      Partials/<Group>/                 partials, grouped (Pages/, Header/, Icons/, Atoms/, Components/, Wrapper/)
  Configuration/BackendLayouts/<Name>.tsconfig   one file per page layout,
      pulled in by page.tsconfig with @import '...*.tsconfig'
  Configuration/Sets/<set>/TypoScript/*.typoscript, imported by setup.typoscript
  Resources/Private/Language/{messages,backend_fields,backend_layouts,backend_previews}.xlf

Two consequences that are load-bearing rather than cosmetic:

1. Why Layouts/ has the Pages/ and Content/ subdirectories at all. PAGEVIEW resolves Layouts/ next to Pages/, fluid_styled_content ships Layouts/Default.fluid.html (the content element frame), and a sitepackage's page frame is also called Default. Put both at the root of one Layouts/ directory and every content element renders the page header and footer inside itself. The subdirectories are the fix: page templates say <f:layout name="Pages/Default"/>, content templates say <f:layout name="Content/Default"/>. I had independently hit this collision and worked around it with a whole separate Resources/Private/ContentElements/ tree — a worse answer to a problem the core already answers.

2. The PAGE object belongs in setup.typoscript AFTER the @import, not next to lib.fluidPage. "page.10 =< lib.fluidPage" is a copy, so any file that adds data processors to lib.fluidPage has to have been read first. Camino gets this right; splitting TypoScript into a TypoScript/ directory without knowing it produces a page that silently ignores half its data processors.

Also worth carrying: camino derives the content element template name from the CType with templateName.ifEmpty.cObject + case = uppercamelcase, so CType camino_textmedia_teaser renders Content/CaminoTextmediaTeaser.fluid.html. That makes snake_case CType naming a requirement rather than a preference — printworks_productteaser would have become PrintworksProductteaser.

Note on scope: typo3_server_scope lists "Frontend theming: the CSS and the JavaScript a website renders" as not covered, and refers to docs.typo3.org. That exclusion should not swallow camino. Camino is not third-party theming, it is a system extension of the core whose whole purpose is to demonstrate the conventions — the same status as fluid_styled_content, which the server does answer about.

## Query

typo3_architecture_lookup task="frontend rendering with PAGEVIEW, custom content elements and DataProcessors in a sitepackage, Fluid templates and TypoScript site sets", targetVersion=14.3 — hints "frontend-page-rendering", "fluid-templates", "site-sets"

## Suggestion

Add a "sitepackage-structure" architecture hint built from typo3/sysext/theme_camino, carrying the directory tree above, the Layouts/Pages vs Layouts/Content rationale, the setup.typoscript ordering rule, the per-layout Configuration/BackendLayouts/*.tsconfig split, the split language files with their translation domains, and the uppercamelcase CType-to-template mapping. Reference it from typo3_task_guide for anything that reads as sitepackage or theme work, and from the frontend-page-rendering and fluid-templates hints. Also reconsider the "frontend theming is out of scope" wording in typo3_server_scope so it excludes a project's CSS but not the core's own reference theme.
