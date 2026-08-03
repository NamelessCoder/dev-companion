---
date: 2026-08-03T15:45:01+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_project_scope
directory: /home/benji/projects/site-demo-typo3-org
---

# Task: boot an existing TYPO3 Composer project locally from a fresh clone, changing no code — inst...

## Observation

Task: boot an existing TYPO3 Composer project locally from a fresh clone, changing no code — install dependencies, start the local environment, import a database and files, build frontend assets, create a backend user, verify the site responds.

What the tool got right should not be lost: version, PHP constraint, project-owned versus third-party extensions, sites with their sets, and the correct identification of DDEV as the declared environment with the container PHP read from .ddev/config.yaml. All of that matched the checkout.

What it did not carry is the part this task needed. The `commands` array held only the composer.json script, and the `environment` object reduced .ddev/config.yaml to `php`, `source` and `entered`. For a project of this shape the bootstrap procedure lives in the parts of that same file that were not reported — the hooks per stage (post-start, post-import-db, pre-pull, post-pull) and the pull providers under .ddev/providers/ — plus the README. Those hooks are what actually install dependencies, update the schema, run extension setup and create the backend user; the provider is what makes the data import reproducible. Every step needed to boot came from reading those files directly. `commands` is where a caller looks for "what can I run here", and it named none of them, so the tool's answer read as complete while the executable part of the environment was absent.

## Query

typo3_project_scope (no arguments), called on a Composer TYPO3 project whose local environment is DDEV with lifecycle hooks and a custom pull provider.

## Suggestion

Report the environment's declared lifecycle next to its interpreter, not just the interpreter. For a DDEV project that means the hooks that exist, each as the stage it fires at and the command it runs, and the pull providers that are configured — marked check or change the way the composer commands already are. Then "how does this repository start itself, and what runs without my asking" is answerable from the server rather than from the caller re-reading .ddev/config.yaml. The same applies to any environment the repository declares that carries lifecycle commands.
