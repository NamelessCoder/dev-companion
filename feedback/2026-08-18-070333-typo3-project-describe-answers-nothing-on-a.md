---
date: 2026-08-18T07:03:33+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/blog
---

# typo3_project_describe answers nothing on a fresh clone although its description promises it does

## Observation

Task: "bitte starte mir dieses projekt" — boot the local DDEV development installation of the t3g/blog extension repository from a clean checkout.

The tool's own description states: "Read from files only, no console and no database, so it answers on a fresh clone." On the fresh clone it did not answer. The first call returned:

{"unsupported":{"cause":"no-installation","reason":"no TYPO3 installation was found to describe","searched":[...5 paths up to /],"misconfiguration":null,"settings":{...}}}

and nothing else — no typo3Version, no phpConstraint, no coreConstraint, no environment block, no extensions, no sites, no commands, no guides. Every one of those is derivable from files that were present at that moment: composer.json (require typo3/cms-core ^13.4.15 || ^14.3 || 15.*.*@dev, php ^8.2, the seven composer scripts, the two npm scripts), .ddev/config.yaml (project blog, docroot .build/public, php_version 8.2, mariadb 10.4, hooks: none), package.json. The identical call after the install returned all of it, including the environment block read from .ddev/config.yaml — a file that had not changed between the two calls.

So the file-only half of the answer is gated behind an installation existing. The consequence is not academic: the typo3-development-installation skill routes on exactly this answer, and its base.md tells the caller to treat "no installation to describe" as the entry condition rather than a failure. That is the state in which the caller most needs to know what the repository declares — which environment, which docroot, which PHP, which commands — and it is the one state where none of it comes back. I read all of it out of the checkout by hand instead: four Bash calls over .ddev/config.yaml, composer.json, package.json, README.rst, Build/, .github/, .gitignore.

Secondary consequence, same call: the "guides" list only rides on the successful answer, so in the no-installation state a session also never learns the guides exist. Filed separately.

## Query

typo3_project_describe (no arguments) called twice in one session: once on a fresh clone of github.com/TYPO3GmbH/blog with no .build/, no config/, no vendor — and once after ddev composer install + typo3 setup had run.

## Suggestion

Split the answer at its own seam. Where no installation is reachable, still return everything answeredBy "packages" — root kind, coreConstraint, phpConstraint, the environment block from .ddev/config.yaml with its hooks and pull providers, the extensions detectable from composer.json, the declared commands, and the guides — and report the absence as a null typo3Version plus the existing "unsupported" block for the installation-derived keys (sites, installed version, the extension list that needs the booted registry). A fresh clone is the state the description advertises and the state the installation workflow starts in; it should be the best-covered path, not the empty one. If the current all-or-nothing shape is deliberate, the description sentence "so it answers on a fresh clone" is the thing to change, because it is what made me call it first and trust it.
