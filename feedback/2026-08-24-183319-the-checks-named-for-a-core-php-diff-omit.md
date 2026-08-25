---
date: 2026-08-24T18:33:19+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# the checks named for a core PHP diff omit checkIntegrityPhp, and the two tools that name suites d...

Trimmed on 2026-08-25. The half about `checkIntegrityPhp` and the exception code
is answered — `D-ANS-108`, which also records that the suggestion about
`listExceptionCodes` does not hold, since `-p` makes that suite exit
successfully whatever it finds. The working-tree paragraph is withdrawn by
`feedback/2026-08-24-183711`, which carries its own card. What is left is below.

## Observation

Task: review Gerrit change 91127 against a 15.0.0-dev core checkout, and write a functional test proving it.

Two tools name suites for the same paths and return different lists:

- typo3_task_guide (changeType "audit", paths PageContentErrorHandler.php and PageRenderer.php) returned `checks: ["CI=true ./Build/Scripts/runTests.sh -s unit", "... -s functional", "... -s cgl -n"]`. Its longer `testSuites` array separately contained cglGit, checkIntegrityPhp, e2e and functional, unmarked as required.
- typo3_test_run_guide with the same paths plus the test file returned exactly one suite: functional.

## Query

typo3_task_guide(task: "Review an open Gerrit core patch that resets PageRenderer before a subrequest in PageContentErrorHandler, test it locally and check whether a functional test is possible", changeType: "audit", paths: ["typo3/sysext/core/Classes/Error/PageErrorHandler/PageContentErrorHandler.php", "typo3/sysext/core/Classes/Page/PageRenderer.php"], targetVersion: "15.0") — read the `checks` array.

typo3_test_run_guide(paths: ["typo3/sysext/core/Classes/Error/PageErrorHandler/PageContentErrorHandler.php", "typo3/sysext/core/Classes/Page/PageRenderer.php", "typo3/sysext/frontend/Tests/Functional/SiteHandling/SiteRequestTest.php"], query: "functional") — read the `suites` array.

## Suggestion

Make the two tools agree, or say why they differ. Right now typo3_task_guide's `checks` is the shorter list and the one labelled as checks, while its own `testSuites` is longer and typo3_test_run_guide's is shorter still. A caller has no way to know which is authoritative, and the safe reading (run everything named anywhere) is not what "checks" suggests.
