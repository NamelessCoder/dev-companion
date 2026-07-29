---
date: 2026-07-29T16:25:35+00:00
category: missing-knowledge
status: open
tool: typo3_task_guide, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Nothing covers the setup surface of a new site

## Observation

Trimmed: the first half of this note — camino is a layout reference and never a
dependency — is answered by the `sitepackage-layout` hint, which now says so in
its own text, names the Composer package next to the sysext path, and states
that the theme is announced to leave the core.

What is still open is the gap the query actually hit: nothing here covers the
setup and installation surface of a new site. All of the following was read out
of the core source by hand in that session:

- `typo3 setup` and `typo3 extension:setup` are two separate steps; the second
  is what fires package initialization and imports the shipped content.
- A sitepackage that ships `Initialisation/data.xml` (or `data.t3d`) IS a
  distribution in the core's sense — `SetupService::getAvailableDistributions()`
  detects packages purely by the presence of that file.
- Consequence: when such a sitepackage is already active, setup skips its own
  site creation entirely, and `--distribution` / `--create-site` are then
  ignored with a warning.
- `--distribution` and `--create-site` may not be used together at all.
- A distribution needs `typo3/cms-impexp` active or it is silently unavailable.
- `Initialisation/Site/<identifier>/config.yaml` is used only when
  `config/sites/<identifier>/` does not exist yet; the core remaps `rootPageId`
  to the uid the imported root page actually gets. Passing `--create-site` on
  top of that produces a second root page and the shipped site configuration is
  skipped.

## Query

typo3_task_guide task="Set up a new TYPO3 v14 website in Composer mode with an
own sitepackage extension: run the setup command, create the site configuration
and site set, ship templates and initial content", changeType=feature,
targetVersion=14.3 — returned only the site-sets and frontend-page-rendering
hints, nothing about the installation/setup surface.

## Suggestion

An architecture hint for the setup surface, next to `sitepackage-initial-content`:
the two setup steps and what each one does, what makes a package a distribution,
which options exclude each other, and what an already-active sitepackage does to
the site creation.
