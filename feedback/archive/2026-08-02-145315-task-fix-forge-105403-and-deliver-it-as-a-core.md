---
date: 2026-08-02T14:53:15+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_commit_message_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix Forge #105403 and deliver it as a core patch. Collecting what producing the patch actua...

## Observation

Task: fix Forge #105403 and deliver it as a core patch. Collecting what producing the patch actually required, as the content a core-development skill would need to carry. Everything below I had to establish from the checkout, from neighbouring files, or from the project's own CLAUDE.md rather than from this server.

Changelog entry. Path is typo3/sysext/core/Documentation/Changelog/<major.minor>/, and the directory is named for the upcoming version — 15.0 here, which I derived from typo3_project_scope reporting typo3Version 15.0.0-dev while the branch is called main. File name is <Type>-<issue>-<CamelCaseSummary>.rst with Type one of Breaking, Deprecation, Feature, Important; choosing between them is the part with no guidance, and I settled on Important by reading neighbours. The required skeleton, copied from an existing entry: "..  include:: /Includes.rst.txt", a "..  _important-<issue>-<timestamp>:" anchor, a title underlined above and below with = of exactly the title's length (I got this wrong first and had to correct the rule length), "See :issue:`N`", a Description section, an Impact section, and a closing "..  index::" line. The RST roles in use are :html:, :php:, :sql: and :issue:.

Tests. ViewHelpers get functional tests under Tests/Functional/ViewHelpers/, never unit tests. Prove the test fails without the fix: I set the source change aside with git stash push of that one file, re-ran, confirmed exactly my new cases failed, and restored — without that step a passing test proves nothing. Fixture hashes are deterministic, so sha1sum on the fixture file yields the exact expectation to assert rather than a regex that would also match a wrong value. Where an expectation is a PCRE whose capture group is reused as a filesystem path, anything appended must sit outside the group.

Suites, all needing the CI=true prefix: unit, functional, cgl -n, phpstan, lintServicesYaml. cglGit reports SUCCESS having checked nothing when run from a git worktree, and a fresh worktree needs composerInstall before any suite runs — both filed separately.

Rebase discipline. The area was under active development: a commit landed mid-session that removed the method my implementation was built on and invalidated all of it. Fetching and rebasing onto origin/main before finalising is not optional in a busy area, and checking git log for the target paths at the start would have shown me the area was moving.

Commit and push invariants, filed separately but part of this: one commit amended forever with the Change-Id preserved, subject as [TYPE] plus summary, body wrapped at 72, the trailers Resolves/Related/Releases, an "Executed commands" block naming what was actually run, and the push as git push origin HEAD:refs/for/<branch>.

One workflow requirement I want stated explicitly for the skill: before pushing, ask the user whether the change should go up public or private. %private creates the change unlisted; the plain refspec publishes it to everyone watching the project, notifies reviewers and is not quietly undone. That is the user's decision every time, not a default a session may assume — I was told "private" explicitly this session and would otherwise have had to guess.

## Query

Whole session: producing a core patch for Forge #105403 on 15.0.0-dev — writing the fix, the functional tests, the changelog entry, the commit message, and pushing to Gerrit. Complements the separate feedback asking for a core-development skill.

## Suggestion

Trimmed on 2026-08-03 by the run that wrote the skill this asked for. What was order rather than fact is in `typo3-core-patch-development`: rebase before finalising in an active area, the prove-it-fails-first step where a layer can hold the test, and the push gate — the skill asks the user which refspec the change goes up on, every time, rather than defaulting.

What stays open is the half a skill may not carry, because a skill is a copy in somebody else's project that no release corrects: the changelog entry as a fact of the core — which type a change owes and by what rule, the directory named for the upcoming version, the file name, the exact skeleton and the roles in use. That belongs in `knowledge/`, where it is versioned and reachable through a lookup. The choice of type is the part with no guidance anywhere: this session settled it by reading neighbouring entries.
