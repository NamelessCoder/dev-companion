---
date: 2026-08-07T12:59:50+00:00
category: missing-knowledge
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_script_lookup, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# the git pre-commit hook fails on host PHP and reports a false header error

## Observation

Task: verify Forge 109572 and carry it through to a committed core patch on a 15.0.0-dev checkout.

At `git commit` the repository's own .git/hooks/pre-commit died with a PHP fatal in vendor/composer/platform_check.php line 22 — the host PHP does not satisfy the platform requirement the checkout declares — and then printed "ERROR: There was a missing or wrong php file header in one or more of your php files. You must fix this and then commit again (git commit --amend)". The commit was created anyway. The header error is false: the hook never got far enough to read a file. I disregarded it only because of a note stored outside this server, and settled it with CI=true ./Build/Scripts/runTests.sh -s cglGit, which read 8 files and reported 0 to fix.

typo3_project_describe lists composer gerrit:setup:preCommitHook:enable and :disable among the declared commands but says nothing about how the hook behaves. I checked typo3_script_lookup during this debrief with task "git hooks, commit and coding guidelines check before committing": it returns the typo3://guides/core/testing/scripts guide, matched on its CGL section, and that guide contains nothing about the git hooks at all.

Without this, an agent either amends a perfectly good commit chasing a header problem that does not exist, or learns to ignore a hook that on a matching PHP version would be telling the truth.

## Query

typo3_project_describe() — returned composer gerrit:setup:preCommitHook:enable / :disable with runs "unknown"; typo3_script_lookup(task: "git hooks, commit and coding guidelines check before committing", targetVersion: "15") — returned core/testing/scripts with no hook content

## Suggestion

The scripts guide, or a hint on the core contribution workflow, should carry: the pre-commit hook runs on the host PHP rather than in the container, so on a checkout whose declared PHP is newer than the host's it dies in vendor/composer/platform_check.php and then reports a missing or wrong php file header for every commit regardless of content; the commit still succeeds; the check that settles it is runTests.sh -s cglGit against the created commit. Naming the false positive is what makes the hook usable — an agent that cannot tell a real header error from this one has to distrust it always.
