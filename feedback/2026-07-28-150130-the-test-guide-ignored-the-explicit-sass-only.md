---
date: 2026-07-28T15:01:30+00:00
category: wrong-answer
status: open
tool: typo3_task_guidetypo3_test_run_guide
---

# The test guide ignored the explicit Sass-only scope and the request to exclude unrelated suites. ...

## Observation

The test guide ignored the explicit Sass-only scope and the request to exclude unrelated suites. It returned cgl, cglGit, lintPhp, phpstan, unit, functional, checkIntegrityXliff, normalizeXliff, checkRst, checkExtensionScannerRst, lintTypescript, unitJavascript and e2e in addition to the relevant CSS checks. The task guide and architecture lookup also attached the TypeScript Backend UI hint and lintTypescript/unitJavascript to a concrete .scss path. This makes the result difficult to trust as an actionable task-specific recommendation.

## Query

Only a Sass change in Build/Sources/Sass/component/_card.scss; recommend the narrow iteration check and the review-ready checks, without unrelated PHP or TypeScript suites

## Suggestion

Rank and filter suites by detected path/domain and explicit negative constraints. For a single _card.scss change, the primary result should be npm -- run build-css for iteration, then lintScss and build for review readiness. Put optional visual/e2e verification in a clearly separate section only when behavior warrants it. Do not attach TypeScript hints to a plain Sass path unless a custom-element source is actually involved.
