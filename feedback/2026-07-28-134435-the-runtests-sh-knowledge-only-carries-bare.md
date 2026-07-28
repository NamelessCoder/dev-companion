---
date: 2026-07-28T13:44:35+00:00
category: missing-knowledge
status: open
tool: typo3_core_run_tests_help
---

# The runTests.sh knowledge only carries bare suite commands ("./Build/Scripts/runTests.sh -s unit"...

## Observation

The runTests.sh knowledge only carries bare suite commands ("./Build/Scripts/runTests.sh -s unit"), which is almost never what a core patch actually needs. Two things are missing. First: in a non-interactive environment runTests.sh tries to allocate a TTY and fails — it needs a "CI=true" prefix. Grepping the whole knowledge/ directory for "CI=true" returns zero hits in all 15 files. For an agent, this is the single highest-value line in the entire knowledge base, because without it the very first test command fails. Second: there is no targeted invocation. Iterating on a patch means running one file or one method, not the whole suite, which costs minutes each round.

## Query

query="functional" — returned only the plain suite command with no options

## Suggestion

Add to test-suite-hints.json, and surface it in typo3_core_run_tests_help: the CI=true prefix for non-interactive use; single file via "-- path/to/FooTest.php"; single method via "-- --filter methodName path/to/FooTest.php"; database selection "-d mariadb|postgres|sqlite" for functional; PHP version "-p 8.4"; and for the npm suite the passthrough form "-s npm -- run build". Example: CI=true ./Build/Scripts/runTests.sh -s unit -- --filter canResolveDomain typo3/sysext/core/Tests/Unit/Localization/TranslationDomainMapperTest.php
