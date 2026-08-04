---
date: 2026-07-29T09:34:46+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 4894123
subject: "Read 'not TYPO3 core' as the opposite of TYPO3 core"
tool: typo3_task_guide
---

# The answer correctly reported "outsideCore": true and then ignored it. It still returned four run...

## Observation

The answer correctly reported "outsideCore": true and then ignored it. It still returned four runTests.sh test suites (checkComposer, checkExtensionScannerRst, checkIntegrityPhp, composer), a checklist opening with "Confirm the target TYPO3 core branch and issue context", the full core checkoutDiscovery block ("git status in the core checkout", "Core tests mirror the class path below typo3/sysext/<ext>/Tests/"), and nextTools pointing at typo3_test_run_guide "for the targeted runTests.sh invocation". None of it applies: this repository has no Build/Scripts/runTests.sh, it runs `composer test:php:unit` (phpunit -c Build/phpunit-unit.xml), `composer cgl`, `composer phpstan`. The flag is computed but changes nothing in the payload, which is worse than not computing it — the caller is told the server knows it is outside core and then handed core-only commands.

## Query

typo3_task_guide {"task":"Raise the compatibility of the third-party extension bootstrap_package (not TYPO3 core, a composer package under vendor bk2k) to TYPO3 v14 and PHP 8.5","area":"third-party extension bootstrap_package","changeType":"cleanup"}

## Suggestion

When outsideCore is true, drop checks, testSuites, conditionalChecks and checkoutDiscovery from the payload, or replace them with an explicit note that the conventions still apply while the commands do not. A middle ground that keeps the value: tag each architecture hint transferable vs core-only, so hints like "ViewHelper: final class, initializeArguments(), escapeOutput" survive while "add a changelog under typo3/sysext/core/Documentation/Changelog/" is dropped.
