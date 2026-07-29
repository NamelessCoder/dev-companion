---
date: 2026-07-29T09:40:54+00:00
category: tool-gap
status: open
tool: typo3_server_scope
---

# Read-only project discovery is the capability this scenario asks for

## Observation

Read again against the current server, and most of it is answered.
`typo3_project_scope` enumerates the sites with the sets each depends on, the
extensions separated into the project's own and the ones it pulled in, the TYPO3
and PHP constraints, and the commands the repository declares — from files, so
it works on a fresh clone. `typo3_changelog_lookup` answers what a version
changed. Every covered topic states whether it is core-only, transferable or
read from the installation.

What is left is the depth inside an extension. The scope names an extension and
its path; it does not map its TCA, its services, its routes, its Fluid roots or
its content element registrations, and those are what a maintenance question is
usually actually about. Composer patches are not reported either.

## Query

Evaluate whether this MCP helps maintain and further develop a TYPO3 site: 19 site configurations, local sitepackage and extension, Fluid, TypoScript, TCA, frontend SCSS and DDEV

## Suggestion

Extend `typo3_project_scope`, or give an extension its own lookup: what it
registers is readable from its files the same way the sites are — TCA overrides
below `Configuration/TCA/`, services in `Configuration/Services.yaml`, icons,
Fluid roots, the content elements it adds. Report Composer patches from the
`extra` section while there.
