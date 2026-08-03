---
date: 2026-08-02T14:43:26+00:00
category: wrong-answer
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403), working inside a git w...

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403), working inside a git worktree created under .claude/worktrees/.

typo3_task_guide returned "CI=true ./Build/Scripts/runTests.sh -s cglGit" among the checks for the fluid-viewhelpers hint. Run from a git worktree it produces:

  fatal: not a git repository: (null)
  cglFixMyCommit.sh: No PHP files to check, all is well.
  ...
  Result of cglGit ... SUCCESS

That is a false green, and the worst possible failure mode for a check: it reports SUCCESS while having inspected zero files, because the git call it depends on failed and the script treats "no files found" as "nothing to fix". A session that trusted it would report a coding-standards check as passed having run nothing. I only noticed because the "not a git repository" line was visible in the output, and switched to "-s cgl -n", which does work in a worktree and reported "Found 0 of 6277 files that can be fixed".

The cause is that a worktree's .git is a file pointing at the common dir rather than a directory, and cglFixMyCommit.sh does not resolve it. The reason it matters here is that this server's own core workflow encourages worktrees — the environment I was given had already placed me in one.

## Query

task="Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource", changeType=bugfix, area=fluid, paths=["typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php","typo3/sysext/extbase/Classes/Service/ImageService.php"], targetVersion=15.0 — then running the returned check: CI=true ./Build/Scripts/runTests.sh -s cglGit

## Suggestion

Carry the constraint with the recommendation: cglGit only inspects files when run from a normal checkout, and silently passes from a git worktree. Either qualify it ("cglGit inspects the files of the current commit; from a git worktree it finds none and still reports SUCCESS — use cgl -n there") or recommend "cgl -n" as the default for a change-scoped standards check and keep cglGit as the faster variant with its precondition named. The same caveat probably applies to any other suite whose script shells out to git for the file list — worth checking checkExtensionScannerRst and friends against a worktree too.
