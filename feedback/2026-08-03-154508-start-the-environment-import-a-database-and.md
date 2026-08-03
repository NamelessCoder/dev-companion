---
date: 2026-08-03T15:45:08+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/site-demo-typo3-org
---

# Task: boot an existing TYPO3 Composer project locally from a fresh clone, changing no code — star...

## Observation

Task: boot an existing TYPO3 Composer project locally from a fresh clone, changing no code — start the environment, import a database and files, build frontend assets, create a backend user, verify the site responds.

Trimmed to what is left of this report: the hints. Of the four that came back, the asset-build one was on topic, while datahandler-basics, fal-basics and public-assets were not. `php` is the only domain a task of this shape detects as, and the corpus holds nothing about bringing an installation up.

The change-type half landed on 2026-08-03 (`D-GUI-008`): `operations` is on the enum, the `installation-operations` intent is what this call reaches, and the checklist it gets is no longer a patch's. That did not move the hints, and cannot — `CHANGE_TYPE_TERMS` reaches the domains the brief reports, while `Hints::find()` detects the domains it selects by from the paths and the task text.

## Query

typo3_task_guide with task="Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, start the local environment, import the demo database and fileadmin, build frontend assets, create a backend user, verify the site responds", changeType="unknown", no area and no paths.

## Suggestion

Carry what booting an installation needs as hints of its own — the environment a repository declares, the import that seeds it, what the asset build produces — so that a brief for this task answers from the corpus instead of from the four PHP hints a domain fallback selects.
