---
date: 2026-07-30T00:34:18+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: c6142d9
subject: "[FEATURE] Explain project configuration ownership"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# The project-repository-layout hint covers config/sites/<identifier>/config.yaml and var/, but not...

## Observation

The project-repository-layout hint covers config/sites/<identifier>/config.yaml and var/, but not the file in config/ that is contested: config/system/additional.php. In a DDEV project it is generated with a "#ddev-generated" marker, and DDEV rewrites it on its own schedule; removing the marker is DDEV's documented way of handing the file to the repository. What the hint does not say is that this is exactly where a project puts its deployment configuration — the environment-variable reads for the database, the encryption key, the trusted hosts, the mail DSN — because settings.php is written by the install tool and additional.php is the file that survives it. And the failure mode is nasty: when DDEV regenerates the file anyway, it also rewrites config/system/.gitignore back to its own version, which lists /additional.php — so the repository's deployment configuration is reverted *and* removed from version control in the same step, and `git status` shows two innocuous-looking modified files in config/system/. I found it only because I read a staged diff before committing; committed unnoticed, the next deployment would have come up with no environment handling at all and nothing in the tree to show what was lost.

## Query

typo3_architecture_lookup id=project-repository-layout — what it says about config/ in a project repository, versus who owns config/system/additional.php

## Suggestion

Extend the project-repository-layout hint with the two files in config/system/: settings.php as what the install tool owns and writes, additional.php as where the project's own environment handling belongs — and, for a DDEV project, that the "#ddev-generated" marker decides who owns it. State the regression to watch for, because it is silent and reversible only from git history: a regenerated additional.php arrives together with a regenerated config/system/.gitignore that re-adds /additional.php, so the deployment configuration is both reverted and untracked at once. The advice that follows is to diff config/system/ before committing after any ddev command that touches the project configuration, and to keep the readme's environment-variable table as the record of what that file is supposed to contain.
