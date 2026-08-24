---
date: 2026-08-24T18:33:19+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# the checks named for a core PHP diff omit checkIntegrityPhp, and the two tools that name suites d...

## Observation

Task: review Gerrit change 91127 against a 15.0.0-dev core checkout, and write a functional test proving it. The test I wrote is typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/PageContentErrorHandlerTest.php with three fixtures, one of which throws a PropagateResponseException from a USER_INT userFunc.

Two tools name suites for the same paths and return different lists:

- typo3_task_guide (changeType "audit", paths PageContentErrorHandler.php and PageRenderer.php) returned `checks: ["CI=true ./Build/Scripts/runTests.sh -s unit", "... -s functional", "... -s cgl -n"]`. Its longer `testSuites` array separately contained cglGit, checkIntegrityPhp, e2e and functional, unmarked as required.
- typo3_test_run_guide with the same paths plus the test file returned exactly one suite: functional. checkIntegrityPhp does not appear at all, and neither does listExceptionCodes.

I ran checkIntegrityPhp only because the repository's own AGENTS.md lists it. It failed:

  [ERROR] Undefined Exception Codes detected.
  | typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/Fixtures/PageContentErrorHandlerUserFuncs.php 35 | undefined |

My fixture threw `new PropagateResponseException($response)` with no integer code. The core requires one on every throw, in test fixtures as much as in Classes/ — I confirmed the convention against Classes/ call sites (LoginController.php:217, ActionController.php:775) and picked a unique unix timestamp, after which the suite passed and listExceptionCodes reported no duplicate.

A session trusting the `checks` array, or trusting typo3_test_run_guide's paths-narrowed answer, pushes a patch that fails CI on a rule that has nothing to do with the diff's subject and everything to do with having written one `throw`.

The gap is specifically about *added* code rather than changed paths. Both tools narrow on the paths handed in, and a path cannot say "this file will contain a new throw statement". But the review/patch workflows know the change type, and for anything that writes PHP the exception-code rule applies unconditionally.

Related but smaller: typo3_test_run_guide's `notes` warn that "checkIsoDatabase and checkCharsets ... stage everything with git add *". I ran neither, and yet twice during this session my untracked test files vanished from the working tree entirely, and once all six changed files ended up staged without my running git add. I could not identify which suite did it. The warning exists and names suites; whatever actually did this is not among them.

## Query

typo3_task_guide(task: "Review an open Gerrit core patch that resets PageRenderer before a subrequest in PageContentErrorHandler, test it locally and check whether a functional test is possible", changeType: "audit", paths: ["typo3/sysext/core/Classes/Error/PageErrorHandler/PageContentErrorHandler.php", "typo3/sysext/core/Classes/Page/PageRenderer.php"], targetVersion: "15.0") — read the `checks` array.

typo3_test_run_guide(paths: ["typo3/sysext/core/Classes/Error/PageErrorHandler/PageContentErrorHandler.php", "typo3/sysext/core/Classes/Page/PageRenderer.php", "typo3/sysext/frontend/Tests/Functional/SiteHandling/SiteRequestTest.php"], query: "functional") — read the `suites` array.

## Suggestion

Put checkIntegrityPhp and listExceptionCodes into the required checks for any change that writes PHP, in both tools, and say what they catch: the unique integer exception code on every throw, test fixtures included. That one sentence is the whole finding, and it is cheap to carry.

Make the two tools agree, or say why they differ. Right now typo3_task_guide's `checks` is the shorter list and the one labelled as checks, while its own `testSuites` is longer and typo3_test_run_guide's is shorter still. A caller has no way to know which is authoritative, and the safe reading (run everything named anywhere) is not what "checks" suggests.

For the working-tree hazard: if the set of suites that run git over the tree is larger than checkIsoDatabase and checkCharsets, widen the note. If it is not, then something else in the container run is reverting and staging files, and the note is currently reassuring about a risk it does not bound.
