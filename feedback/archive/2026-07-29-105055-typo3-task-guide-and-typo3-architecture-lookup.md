---
date: 2026-07-29T10:50:55+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 2d0c533
subject: "Answer how the mechanism is used, not only what a patch to it must satisfy"
tool: typo3_task_guide
directory: /home/benji/projects/site-new
---

# typo3_task_guide and typo3_architecture_lookup disagree about what to emit for the same outside-c...

## Observation

typo3_task_guide and typo3_architecture_lookup disagree about what to emit for the same outside-core classification, and the task guide is the one that gets it wrong.

Both were asked about building a sitepackage for a project site. Both correctly detected the work as outside the core and printed the same banner. But then:

typo3_architecture_lookup says, and does the right thing:
  'The hints below are conventions that may transfer; the checks that normally come with them are left out, because Build/Scripts/runTests.sh is part of the core repository and does not exist here.'

typo3_task_guide, for the same task, still emitted:
  - 'Relevant TYPO3 core checks:' with four CI=true ./Build/Scripts/runTests.sh invocations (functional, checkExtensionScannerRst, lintHtml, plus lintScss/build as conditional)
  - a checklist item 'Add a changelog feature file under typo3/sysext/core/Documentation/Changelog/ for public API additions'
  - 'Confirm the target TYPO3 core branch and issue context'
  - an 'Establish in your checkout' block about git branch --show-current, 'the patch targets main and the merging core team member handles the backport', and core tests mirroring typo3/sysext/<ext>/Tests/

None of that exists in a project. Build/Scripts/runTests.sh is not there, there is no Changelog directory, there is no backport decision and no Gerrit. The guide states the boundary in its banner and then ignores it for the rest of the output, so the majority of a fairly long answer is noise an integrator has to know enough to discard — which is exactly the knowledge they came to ask for.

The fix looks small: apply the same suppression typo3_architecture_lookup already implements. Suppress the checks block, the changelog item, the branch/backport items and the core-test-location item whenever outsideCore is true.
