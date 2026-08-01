---
date: 2026-07-28T14:56:18+00:00
category: wrong-answer
status: closed
closed: 2026-07-28
commit: 4e41aed
subject: "Separate what a task says from what it is about"
tool: typo3_core_task_brieftypo3_core_run_tests_help
---

# For a Fluid/XLIFF-only patch, typo3_core_task_brief classified the domains as frontend, xliff, ph...

## Observation

For a Fluid/XLIFF-only patch, typo3_core_task_brief classified the domains as frontend, xliff, php and recommended unrelated PHP unit/functional, TypeScript, Sass, build and JavaScript checks. Its checkoutDiscovery text also points to typo3_test_run_guide, which was not advertised in that MCP session (the advertised name was typo3_core_run_tests_help). typo3_core_run_tests_help returned many unrelated suites such as lintServicesYaml, lintPhp, build, unitJavascript, checkRst and e2e even though the query specifically requested XLIFF integrity and normalization. A restarted server exposed renamed tools, making the session/version inconsistency itself worth checking.

## Query

Task: Replace empty accessible names in typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html and add one label to locallang_layout.xlf; test query: XLIFF integrity and normalization after changing one backend Fluid template and XLF file

## Suggestion

Infer domains from the concrete paths and return only relevant checks by default. For this task, recommend checkIntegrityXliff and normalizeXliff -n; optionally mention broader checks separately. Keep all next-tool references synchronized with tools/list and ensure the configured server consistently loads one tool API version.
