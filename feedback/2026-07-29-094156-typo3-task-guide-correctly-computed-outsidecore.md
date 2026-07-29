---
date: 2026-07-29T09:41:56+00:00
category: wrong-answer
status: open
tool: typo3_task_guide
---

# typo3_task_guide correctly computed outsideCore:true for a task in a project sitepackage, and the...

## Observation

typo3_task_guide correctly computed outsideCore:true for a task in a project sitepackage, and then ignored its own verdict: the rest of the answer was pure core-contribution advice presented without any caveat. It returned checks "CI=true ./Build/Scripts/runTests.sh -s functional" and "-s checkIntegrityXliff" — this project has no Build/Scripts/runTests.sh, its checks are the composer scripts t3g:cgl, t3g:phpstan and t3g:rector, which is also what .github/workflows/ci.yml runs. The checklist told me to "Confirm the target TYPO3 core branch and issue context" and to "Add a changelog feature file under typo3/sysext/core/Documentation/Changelog/", a path that cannot exist in a site project. testSuites offered checkExtensionScannerRst and checkComposer, both meaningless here. The checkoutDiscovery block instructed me to look for tests mirroring typo3/sysext/<ext>/Tests/. Because the flag is already computed, this is not a detection problem but an output problem: the one field that knows the answer is wrong changes nothing about the answer. An agent that does not read the flag — and nothing in the payload draws attention to it — follows the instructions and produces commands that fail and a changelog file in a directory that does not exist.

## Query

typo3_task_guide{task:"Add a new content element with TCA fields to the project sitepackage extension", area:"events_sitepackage", changeType:"feature"}

## Suggestion

Let outsideCore:true actually govern the response. Suppress the core-only checks, testSuites and changelog checklist entries rather than emitting them, and lead the payload with a prominent notice, for example {"outsideCore":true,"notice":"This task is outside typo3/sysext/. The core contribution checks, changelog requirements and runTests.sh suites below do not apply; determine this project's own checks from its composer.json scripts and CI configuration.","seeInstead":"https://docs.typo3.org/"}. Keep emitting the subsystem architecture hints, which do transfer — the TCA/FormEngine, ViewHelper, site set and icon hints were the most useful thing this server gave me all session. The distinction worth encoding is that conventions transfer to extension code while the process scaffolding does not.
