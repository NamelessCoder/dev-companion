---
date: 2026-07-29T10:05:51+00:00
category: tool-gap
status: open
tool: typo3_test_run_guide
---

# No scope guard: given paths that are plainly not core paths (packages/... in a composer site proj...

## Observation

No scope guard: given paths that are plainly not core paths (packages/... in a composer site project, no typo3/sysext/ prefix), the tool answers with `CI=true ./Build/Scripts/runTests.sh -s cglGit|e2e|functional|unit` and the instruction to run them "from the TYPO3 core root". Build/Scripts/runTests.sh does not exist in a site project, so every command handed back is unrunnable. typo3_task_guide handles the identical situation well -- it prefixes "This reads as work outside the TYPO3 core ... use https://docs.typo3.org/" -- but that guard is not applied by typo3_test_run_guide, nor by typo3_architecture_lookup, which appends "Relevant checks: CI=true ./Build/Scripts/runTests.sh -s functional" to hints it returned for the same non-core paths. Confidently actionable and wrong is worse here than declining, because the commands look copy-pasteable.

## Query

{"query":"how do I test my sitepackage","paths":["packages/franks_ballerbude/Classes/Command/SeedCommand.php","packages/franks_ballerbude/Resources/Public/Css/styles.css"]}

## Suggestion

Lift the out-of-core detection that typo3_task_guide already performs into a shared check and apply it in typo3_test_run_guide and typo3_architecture_lookup. When the given paths do not look like core paths, prefix the same disclaimer and suppress the runTests.sh check lines rather than emitting commands that cannot run. The architecture hints themselves are worth keeping in that case -- they transfer -- it is only the check commands that do not.
