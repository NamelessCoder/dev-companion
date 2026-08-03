---
date: 2026-08-02T14:43:50+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_project_scope
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403), which required running...

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403), which required running unit, functional, cgl and phpstan repeatedly.

typo3_project_scope is described as returning "the commands it declares in composer.json and package.json, each with what running it does to the sources", and the server instructions say "a check you recommend that the repository does not declare is a wrong answer however sensible it sounds". In a core checkout the commands array came back with exactly four entries, all of them git hook installers:

  composer gerrit:setup
  composer gerrit:setup:commitMessageHook:enable
  composer gerrit:setup:preCommitHook:enable
  composer gerrit:setup:preCommitHook:disable

each with runs="unknown". Build/Scripts/runTests.sh was not named. In a core checkout that script is the only way to run anything — every test suite, every linter, every static analysis run, the containerised PHP itself. I ran it roughly thirty times this session and got its invocation syntax from the project's own CLAUDE.md, not from this server. extensions and sites came back empty, which is correct for a core checkout but means the answer as a whole carried almost nothing beyond typo3Version and phpConstraint.

The literal reading of composer.json is defensible, but it makes the tool answer "what can I run here" with the four least important things in the repository while omitting the one that matters.

## Query

typo3_project_scope (no arguments) in a TYPO3 core checkout at /home/benji/projects/typo3-cms, typo3Version 15.0.0-dev

## Suggestion

When kind is "core-checkout", include Build/Scripts/runTests.sh in commands with its invocation shape (CI=true ./Build/Scripts/runTests.sh -s <suite> [-- <args>]) and the note that CI=true is required for non-interactive use, or point explicitly at typo3_test_run_guide from the project_scope result so a session that starts there is routed to the suites rather than left to guess. Reading the -s cases out of the script would also let the suite list reflect the branch rather than a bundled assumption — the acceptance and acceptanceInstall suites no longer exist on main, which is exactly the kind of drift a file-read answer would catch.
