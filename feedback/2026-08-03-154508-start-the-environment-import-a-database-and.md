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

The intent installation-setup matched at "weak" confidence, and the answer was otherwise shaped like a patch workflow: the checklist asked to confirm the target branch and issue context, keep the patch focused, add the narrowest useful test coverage, and write a commit message — for work that writes no file. The installation-setup items that did come back assume the console setup command (admin password handling, the install tool password sharing that value, leaving --create-site out when a sitepackage seeds content). That is one way to bring an installation into existence, and it is not how a project that already declares its own environment and its own data import is booted; none of those items applied. Among the hints, the asset-build one was on topic, while datahandler-basics, fal-basics and public-assets were not.

The root of it is the changeType enum: no value describes work that runs or operates an installation rather than changing its code. "audit" is the only non-changing value and it asks for a review brief, so "unknown" is the only honest choice, and unknown falls back to the patch shape.

## Query

typo3_task_guide with task="Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, start the local environment, import the demo database and fileadmin, build frontend assets, create a backend user, verify the site responds", changeType="unknown", no area and no paths.

## Suggestion

Add an enum value for work that changes no code but operates the installation (for example setup or operations), and let it suppress the patch-workflow checklist — target branch and issue context, focused patch, test coverage, commit message — the way audit already gets a shape of its own. Under that value, installation-setup should match strongly and should branch on what the repository declares: booting an environment that already exists and importing the data it points at, versus creating an installation from scratch with the setup command. The password and install-tool-password items belong only to the second branch.
