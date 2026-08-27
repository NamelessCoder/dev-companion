---
date: 2026-08-27T14:55:07+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# The bugfix checklist does not scale down; I skipped the deprecation sweep it demanded

## Observation

Task: "please search for 1 workspace bug in forge and fix it". The resulting patch was three added statements in one method plus a parameter, a test and a CSV fixture.

typo3_task_guide was genuinely useful and most of the checklist earned its place — "Reproduce the bug first, ideally with a failing test that the fix turns green" is exactly what I did and what the repo's AGENTS.md demands. The `checks` array (unit, functional, checkIntegrityPhp) and the `testSuites` entries on cglGit/cglHeaderGit were accurate, and the cglGit note about git worktrees silently reporting SUCCESS having read nothing is the kind of thing I would never have discovered until it bit me.

But one checklist item did not fit the size of the work and I silently skipped it: "Sweep the deprecations before writing: typo3_changelog_lookup with type 'deprecation' and the query omitted, at TYPO3 v15. One call per tag: the ext: tag of each system extension this package calls into, and TCA, Fluid, Backend or Frontend for the kinds of file it ships."

For a diff that adds a language constraint to an existing QueryBuilder chain, using only APIs already imported and used twice in the same file, that is several calls that cannot change the outcome. I did not make them and I do not regret it. The problem is that the checklist gave me no basis for that call — it reads as unconditional, so skipping it was me overriding the guide on my own judgement, which is precisely the situation a guide exists to prevent. Next session I would skip it again, for the same unexamined reason, and one of those times it will be a diff where the sweep mattered.

Same shape, smaller: "Check whether the bug also affects maintained older release branches." I could not act on this from the checkout — I only have `main` — and I resolved the branch question from typo3_commit_message_guide instead, which told me a BUGFIX goes to main and 14.3 and that naming 13.4 claims a severity I would have to justify. That was the better answer, and the checklist item pointed at work I had no way to do.

## Query

typo3_task_guide(task: "Fix workspaces module language filter not applying to moved records in WorkspaceService::getMovedRecordsFromPages", changeType: "bugfix", paths: ["typo3/sysext/workspaces/Classes/Service/WorkspaceService.php", "typo3/sysext/workspaces/Tests/Functional/Service/WorkspaceServiceTest.php"])

## Suggestion

Condition the checklist on the shape of the diff, or say what the condition is so I can apply it.

For the deprecation sweep: "Sweep the deprecations before writing where the change introduces a call, a TCA key, a ViewHelper or a configuration option you have not used on v15 — a change confined to APIs already called in the file you are editing does not need it." That gives me a test I can run against my own diff in one thought, instead of an unconditional instruction I quietly drop.

For the release-branch item: point it at typo3_commit_message_guide rather than at the checkout. That tool already answers which lines take a BUGFIX and which claim a severity; the checklist item as written implies I should go look at branches I do not have.

More generally — typo3_task_guide takes `changeType` but nothing about magnitude. A one-method fix and a subsystem rework are both "bugfix" and get the same checklist. If the answer carried the checklist in two tiers, or if the items each said what makes them apply, I would follow more of it rather than picking.
