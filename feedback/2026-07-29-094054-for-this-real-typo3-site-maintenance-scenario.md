---
date: 2026-07-29T09:40:54+00:00
category: tool-gap
status: open
tool: typo3_server_scope
---

# For this real TYPO3 site-maintenance scenario the server is mostly out of scope. It identifies th...

## Observation

For this real TYPO3 site-maintenance scenario the server is mostly out of scope. It identifies the work as outsideCore, but it has no project/site mode and cannot inspect config/sites, Site Sets, TypoScript includes, local extension registrations, Composer scripts, package.json builds, database schema/content, redirects, scheduler tasks, logs, cache state, reference integrity or upgrade status. Passing concrete project paths does not fix the boundary: config/sites/*/config.yaml is silently unclassified, frontend SCSS receives TYPO3 backend-only light/dark and Bootstrap-removal advice, and typo3_test_run_guide returns only core Build/Scripts/runTests.sh commands that do not exist here instead of the repository-native composer t3g:phpstan, t3g:cgl and extension npm scripts. On a fresh checkout with DDEV stopped and dependencies not installed, all five installation-backed lookups answer unavailable, so the MCP provides almost no site-specific maintenance value beyond translation-domain calculation and a few transferable TCA/Fluid reminders.

## Query

Evaluate whether this MCP helps maintain and further develop /home/benji/projects/site-events: TYPO3 13.4, 19 site configurations, local events_sitepackage and sessionplaner_extended extensions, Fluid, TypoScript, TCA, frontend SCSS and DDEV

## Suggestion

Add an explicit project/extension maintenance mode, separate from core contribution mode. Start with read-only project discovery: parse composer.json/package.json and CI scripts; enumerate config/sites and Site Set/TypoScript dependencies; map local extensions, TCA, services, routes, Fluid roots and content-element registrations; report TYPO3/PHP constraints and patches; and recommend only commands that actually exist in the repository. When a console is reachable, add read-only site/configuration diagnostics for sites, extensions, schema, reference index, redirects, scheduler, reports/logs and cache configuration. Tag every recommendation as core-only, transferable, project-derived or installation-derived, and reject backend component/CSS hints for explicit frontend paths. A useful first deliverable would be typo3_project_scope plus typo3_project_check_guide that clearly distinguishes source-only findings from runtime findings.
