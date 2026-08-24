---
date: 2026-08-24T11:09:08+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# installed:true does not notice a vendor directory older than composer.lock; cost a phantom red suite

## Observation

Task: review a local core patch in EXT:impexp before pushing, which included running the suites the project declares.

typo3_project_describe reported installed:true, typo3Version 15.0.0-dev, corePhpConstraint ^8.5, installedPhpBound 8.5.0 — all correct and all read from files. What it does not compare is composer.lock against what is actually in vendor/. In this checkout they had drifted: I had reset main onto origin/main, which brought c657b53904d "[BUGFIX] Restore expanded YAML dump in SiteWriter" and with it a changed composer.json/composer.lock, while vendor/symfony/yaml on disk was from 22 July.

The consequence was a full unit run reporting 3 failures in SiteWriterTest — expanded versus inline YAML sequences — in the middle of a review of an impexp patch. I had to isolate the failing class, confirm my commit touches no core file, check which of the four new commits changed composer.lock, and finally run -s composerInstall and the suite again, at which point everything was green. Four extra calls and two full suite runs to establish "not my patch, stale vendor".

typo3_test_run_guide names composerInstall in its preconditions, but only for the case it can see from the file system: a fresh clone or a git worktree with no vendor/ at all. A vendor/ that exists and predates the lock is the case that produces a red suite rather than a missing binary, and neither tool warns about it.

## Query

typo3_project_describe (no arguments) in a core checkout whose composer.lock had just been updated by a git reset onto origin/main while vendor/ was a month old; then typo3_test_run_guide with the changed impexp paths

## Suggestion

In typo3_project_describe, compare the mtime of composer.lock against vendor/composer/installed.json (or the lock hash the latter records) and report a third state beside installed true/false — installed but stale, with the one command that fixes it, CI=true ./Build/Scripts/runTests.sh -s composerInstall. Both files are on disk, so it stays a file-only answer. The value is not the convenience: a stale vendor produces failures in classes the patch never touched, which is exactly the evidence a review is least able to attribute correctly.
