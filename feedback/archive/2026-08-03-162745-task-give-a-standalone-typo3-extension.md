---
date: 2026-08-03T16:27:45+00:00
category: tool-gap
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-extension-testing
directory: /home/benji/projects/ext-guidedtour
---

# Task: give a standalone TYPO3 extension repository a local development environment in which the e...

## Observation

Task: give a standalone TYPO3 extension repository a local development environment in which the extension can actually be run and clicked through. No skill covers this, and typo3_task_guide has no workflow for it. The closest skill, typo3-extension-testing, covers PHPUnit, Playwright and static analysis — i.e. what you run *against* an installation — but not how the installation that all of it needs comes into existence. So the entire session ran without consulting this server: the answers were derived empirically from the DDEV binary, the Composer resolver and the TYPO3 core sources in the checkout. Nothing here is a wrong answer by the server; it is knowledge the server never had a place to put.

The task is highly recurring and almost entirely mechanical. What it consists of, in the order the steps depend on each other:

1. Turn the extension's own composer.json into a Composer root package that can install a full TYPO3 into a gitignored subdirectory: config.vendor-dir/.build/vendor, config.bin-dir/.build/bin, extra.typo3/cms.web-dir/.build/public, allow-plugins for typo3/cms-composer-installers and typo3/class-alias-loader, and the system extensions the extension needs at runtime in require-dev.
2. Add a DDEV project (type: typo3, docroot pointing at the web-dir).
3. Install TYPO3 non-interactively via "typo3 setup" driven by TYPO3_* environment variables, then "typo3 extension:setup".
4. Optionally seed content from a distribution package.
5. Gitignore what the installation drops into the repository root.

Four separate obstacles were hit along the way, each recorded as its own feedback: the Composer root-package layout, the "typo3 setup" CLI semantics, the site base an imported distribution ends up with, and DDEV's settings management colliding with a non-MySQL database.

Verified end state, from a cold "ddev start" on a bare checkout with no .build/, no config/, no var/ and no container: composer install, typo3 setup, extension:setup and cache:flush run unattended from a post-start hook, backend and frontend both answer 200, and a second start is a no-op. TYPO3 14.3.5, DDEV v1.25.1, SQLite, no database container.

## Query

"wir entwickeln diese extension und brauchen dafür eine entwicklungs umgebung in der wir sie auch testen können" — a standalone TYPO3 extension repository (Classes/, Configuration/, Resources/, composer.json, no Build/, no .ddev/) that had no runnable installation at all.

## Suggestion

Add a skill for building and repairing the local development environment of a TYPO3 extension, sitepackage or project package — the installation itself, as distinct from the tests that run against it. It should own: the Composer root-package layout that installs TYPO3 beneath the package (vendor-dir, bin-dir, web-dir, allow-plugins, require-dev selection driven by what the extension actually registers); the container setup (DDEV as the default, with the project type and docroot that follow from the web-dir); the non-interactive install sequence and the environment variables that drive it; seeding demo content from a distribution; and what belongs in .gitignore because TYPO3 writes it into the Composer root (config/, var/, the web-dir, the lock file for a library package). It should hand over to typo3-extension-testing once an installation exists, and that skill should be able to point at it when a project has no runnable installation yet.
