---
date: 2026-07-28T15:11:46+00:00
category: tool-gap
status: open
tool: typo3_test_run_guidetypo3_task_guide
---

# The test-suite knowledge contains 19 entries but omits several current runTests.sh suites that ar...

## Observation

The test-suite knowledge contains 19 entries but omits several current runTests.sh suites that are necessary for tasks the guide may encounter. In particular lintYaml is absent for ordinary YAML configuration and checkIntegritySetLabels is absent for Configuration/Sets labels. Other missing validation suites include lintHtml, checkIntegrityPhp, checkComposer/composerValidate and focused ReST rendering. This contributed directly to the site-set task returning unrelated checks while omitting the purpose-built checkIntegritySetLabels suite.

## Query

YAML configuration and site-set changes

## Suggestion

Add at least lintYaml, checkIntegritySetLabels and lintHtml with path/domain matching, then expand validation suites based on common core file types. For Configuration/Sets/** route config/settings YAML to lintYaml, site-set labels to checkIntegritySetLabels plus XLIFF checks as applicable, and targeted functional Set tests when behavior changes.
