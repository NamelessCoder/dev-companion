---
date: 2026-08-24T18:37:11+00:00
category: bug
status: closed
closed: 2026-08-26
model: claude-opus-5
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# correction: only one working-tree loss is confirmed, and the staging I blamed on a suite was the ...

## Observation

Correction to the feedback filed minutes earlier under "the checks named for a core PHP diff omit checkIntegrityPhp, and the two tools that name suites disagree" (file 2026-08-24-183319). Its closing paragraph overstates, and a feedback is evidence about behaviour, so the overstatement is worth withdrawing explicitly rather than leaving for a maintainer to act on.

Task context: reviewing Gerrit change 91127 in a core checkout at 15.0.0-dev, with four untracked test files under typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/ and edits to two tracked files under typo3/sysext/frontend/Tests/Functional/SiteHandling/.

What I claimed: "twice during this session my untracked test files vanished from the working tree entirely, and once all six changed files ended up staged without my running git add. I could not identify which suite did it."

What I can actually support:

1. One loss, confirmed and observed. After a run of `CI=true ./Build/Scripts/runTests.sh -s unit` (full suite), `ls -la` on typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/ showed only the Fixtures directory, and `ls -la` on Fixtures/ showed it empty. All four untracked files were gone. I restored them from a scratchpad copy and re-verified them by running the test again. That happened and I stand by it. What I cannot say is which suite did it — several ran between the last time I saw the files and the time I found them missing, and I did not check in between.

2. The "second" loss was my own misreading. `git diff` returned empty for the two tracked files and I read that as another disappearance. They were staged, so `git diff --cached` was the correct command; `git status --short` showed `M ` (staged, clean worktree) and I misread the column. Nothing was lost. Withdraw this entirely.

3. The staging was almost certainly not a suite. The session ended with HEAD at 79cde993dca, author Torben Hansen, committer the user, containing all eight files — the user amended the patch set themselves while I was working. A `git add` on their side is the ordinary explanation for the staged state I attributed to a test run, and I have no evidence for the other one. Withdraw this too.

So the surviving report is: one confirmed disappearance of untracked files under typo3/sysext/core/Tests/ during a container test run, suite unidentified. That is thinner than what I filed, and it does not by itself contradict the note in typo3_test_run_guide naming checkIsoDatabase and checkCharsets as the git-touching suites — it only means something removed untracked files once and I cannot say what.

The general point I would still make, at lower confidence: a session that creates untracked test files and then runs several container suites has no signal that anything happened to them. Whatever the cause, it cost me a restore I only survived because I had copied the files to the scratchpad on a hunch.

## Query

Sequence: created four untracked files under typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/, ran (in order) runTests.sh -s functional over several paths, -s unit over Tests/Unit/Page, -s cgl -n, -s phpstan, -s checkIntegrityPhp, -s listExceptionCodes, -s unit (full, twice). Afterwards `ls -la typo3/sysext/core/Tests/Functional/Error/PageErrorHandler/` listed only Fixtures/, and Fixtures/ was empty. Container runtime docker, PHP 8.5, DBMS sqlite, core checkout at 15.0.0-dev.

## Suggestion

Treat the earlier feedback's last paragraph as withdrawn except for point 1 above. If the notes in typo3_test_run_guide are ever extended on this, the useful form is not a longer list of suites but a warning attached to the act: untracked files under a Tests/ directory are not guaranteed to survive a container suite run, so copy anything unsaved outside the checkout first. That is advice a session can act on without knowing which suite is responsible.
