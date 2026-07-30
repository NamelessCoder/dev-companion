---
date: 2026-07-30T18:55:42+02:00
category: idea
status: open
tool: typo3_component_lookup, typo3_catalog_scope
---

# The component catalog is bundled with this server and hand-curated, while every

## Observation

The component catalog is bundled with this server and hand-curated, while every
other installation-dependent answer — registered icons, existing labels,
registered backend modules — is asked of the installation being read. A
component's markup is exactly as installation-dependent as those: it is whatever
the backend of the installed version ships.

The cost showed up in a forward run of EXT-04. The catalog snapshot is cut from
main, the installation was 14.3.5, and the session spent about twenty-five Bash
calls reading vendor/typo3 to establish markup the catalog claims to hold. A
catalog derived from the installation would have had no such gap to distrust.

This reverses a recorded direction — the catalog was deliberately kept static,
with bin/verify-catalog reporting what a core update invalidated. Acting on it
means writing down why that no longer holds.

## Query

backend component markup for the installed TYPO3 version

## Suggestion

Consider deriving the components from the installation like the icons, labels
and modules are: the backend Sass sources and the styleguide templates ship with
it. Keep the bundled snapshot for the case that has no installation to read, and
let the derived answer win where there is one. Weigh it against what static
buys — an answer with no console, and a curation that a derivation cannot
reproduce.
