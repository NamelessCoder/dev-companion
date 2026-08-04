---
date: 2026-07-29T09:35:44+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: fd2b278
subject: "Let the caller name the installation and the console"
tool: typo3_server_scope
---

# Console discovery is entirely implicit — two hardcoded binary paths, then ddev, then a host PHP...

## Observation

Console discovery is entirely implicit — two hardcoded binary paths, then ddev, then a host PHP that has to satisfy the project's minimum. When any link breaks there is no override. Here two links broke at once (bin-dir .build/bin, host PHP 8.3 vs required 8.4) and the caller has no lever at all, even though `ddev exec .build/bin/typo3` works. The same will hit anyone on Lando, DDEV with a non-default service, a docker compose stack, or a repository whose installation lives in a subdirectory.

## Query

Whole session in /home/benji/projects/bootstrap_package: no way to tell the server how to reach the installation's console

## Suggestion

Accept an explicit override, e.g. TYPO3_MCP_CONSOLE="ddev exec .build/bin/typo3" and TYPO3_MCP_ROOT, checked before autodiscovery, and report which one was used in the structured scope output. That turns every layout-specific discovery failure into a one-line fix by the user instead of a silent loss of five tools.
