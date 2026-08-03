---
date: 2026-08-02T14:43:57+00:00
category: tool-gap
status: closed
closed: 2026-08-02
model: claude-opus-5[1m]
tool: typo3-backend-module-development, typo3-content-element-development, typo3-extension-conformance, typo3-extension-documentation, typo3-extension-release, typo3-extension-testing, typo3-extension-upgrade
directory: /home/benji/projects/typo3-cms
---

# Task: evaluate Forge #105403, find related issues and existing patches, then write and push a fix...

## Observation

Task: evaluate Forge #105403, find related issues and existing patches, then write and push a fix — a bugfix in TYPO3 core itself (typo3/sysext/fluid, typo3/sysext/extbase, typo3/sysext/core), in a core checkout at 15.0.0-dev.

No skill activated, and none should have: all seven typo3-* skills are scoped to extensions, sitepackages or projects. Their descriptions say so explicitly — "in a core checkout, extension, site package, or Composer project" for the module skill, "TYPO3 project, sitepackage, or extension" for conformance, "a TYPO3 extension, sitepackage or project package" for release and upgrade. Core development is the one workflow with no skill, even though this server ships in a core checkout and typo3_task_guide has a dedicated scope:"core" branch with core-only checks and a core commit workflow.

The work this task actually needed was recognisably a single repeatable shape, and I assembled it by hand: locate the subsystem, check whether the bug still reproduces on main, find the functional test that covers it, write a failing case, fix, update every expectation the behaviour change invalidates across sysexts, decide whether the change needs a changelog entry and of which type, run the right suites in the right order, produce a message with the right trailers, keep one commit, push to refs/for/main. That sequence is not discoverable from the extension skills, and the parts of it that are core-specific — Changelog/<version>/ file naming and the Breaking/Deprecation/Feature/Important choice, one-commit-with-preserved-Change-Id, refs/for/<branch> — are exactly where a session without prior TYPO3 core experience would go wrong.

## Suggestion

Add a core-development skill covering the core patch workflow end to end: reproduce against the branch, functional-test-first for ViewHelpers and similar public API, the changelog decision tree (which of Breaking/Deprecation/Feature/Important, the file naming convention, when no entry is needed), the suite order to run and which of them false-green in a worktree, the single-commit/amend/Change-Id rule, and the push refspec including the %private variant. typo3_task_guide already computes scope:"core" and returns core-only checks, so the routing signal exists — the skill is what is missing at the other end of it.
