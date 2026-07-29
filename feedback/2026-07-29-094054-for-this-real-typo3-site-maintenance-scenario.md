---
date: 2026-07-29T09:40:54+00:00
category: tool-gap
status: open
tool: typo3_server_scope
---

# Read-only project discovery is the capability this scenario asks for

## Observation

The wrong answers this note reported are gone: frontend SCSS no longer receives
the backend's CSS conventions, `typo3_test_run_guide` no longer hands over
runTests.sh commands to a repository without it, the labels are readable without
a booted console, and every covered topic now says whether it is core-only,
transferable, or read from the installation.

The capability the scenario asks for is untouched. There is no project mode:
nothing enumerates `config/sites` and the site sets a site depends on, maps the
local extensions with their TCA, services, routes and Fluid roots, reads the
Composer scripts a repository declares, or reports its TYPO3 and PHP
constraints. `config/sites/*/config.yaml` is still classified as an ordinary
YAML path with no hint behind it.

## Query

Evaluate whether this MCP helps maintain and further develop a TYPO3 site: 19 site configurations, local sitepackage and extension, Fluid, TypoScript, TCA, frontend SCSS and DDEV

## Suggestion

Read-only project discovery, as its own tool rather than as an extension of the
core surface: parse composer.json, package.json and the CI configuration;
enumerate the sites and their set dependencies; map the local extensions; and
recommend only commands that exist in that repository. Tag every recommendation
by where it came from, which the scope now does per topic and would have to do
per answer.

The note about the v14 maintenance scenario asks for the same capability from
the other end. Both are a decision about what this server is, not a defect in
what it does.
