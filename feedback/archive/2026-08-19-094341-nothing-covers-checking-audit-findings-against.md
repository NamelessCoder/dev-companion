---
date: 2026-08-19T09:43:41+00:00
category: missing-knowledge
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3-extension-health, typo3_task_guide
directory: /home/benji/projects/blog
---

# Nothing covers checking audit findings against work already in flight, and I got it wrong

## Observation

Task: full audit of the blog extension before its v14 release. After I delivered the report the user said: "gehe die punkte nach der reihe durch und schau dir offene prs an ob diese in die stellen greifen" — walk the items in order and check whether open PRs reach into those places.

The typo3-extension-health skill covers this half-way and stops. It has step 5-7 (write the list, agree it, keep it in the session) and steps 8-12 (work it off, one owner at a time, commit per item). What it never says is that a finding may already be fixed on a branch nobody merged, and that the list must be checked against the repository's open work before anything is started.

I got it wrong on the first pass. I queried open pull requests (gh pr list --json files) and mapped 23 of them against my 17 items. I reported "Item 2 — frei, kein PR berührt SetupService.php". Then the user asked "hast du den branch gesehen bugfix/synthehtic?" and a git branch -a turned up 13 pushed branches with no PR at all, one of which — bugfix/synthetic-tt-content-row — contained my Item 2 already fixed, with the same diagnosis I had reached independently (MariaDB does not detect the functional dependency on the primary key, so the redundant GROUP BY fails under ONLY_FULL_GROUP_BY) and with the missing test. It also carried two v14 defects my audit had not found at all.

So the finding is: the surface is "work in flight", and it is larger than "open PRs". In this repository it was open PRs, pushed branches without a PR, and release branches. And the naive tools mislead:
- git cherry compares patch-ids, so a squash-merged branch reports as fully outstanding. It told me feature/schema-author-profile had 10 commits open when the work was long since in master.
- a two-dot diff against master is dominated by how far behind the branch is, not by what it adds.
What actually settled it was, per branch, a two-dot diff restricted to the files the branch touches: git diff --stat master origin/<branch> -- <its files>. Empty means landed. That is the step I would work out again next session, and it took me four attempts to get to.

Nothing in the skill or in typo3_task_guide's audit brief points at any of this. The brief's checklist has "Report what the review did not reach" and "Hand each finding over with its consequence and what would remediate it" — both good — but not "establish what is already in flight against this finding before you propose work on it". For a repository with 23 open PRs and 13 stale branches, that omission cost a wrong statement to the maintainer and would have cost duplicated work.

## Query

Skill typo3-extension-health, steps 5-9 (the agreed list and working it off). Task text that exposed it: "gehe die punkte nach der reihe durch und schau dir offene prs an ob diese in die stellen greifen", followed by "hast du den branch gesehen bugfix/synthehtic?"

## Suggestion

Add a step to typo3-extension-health between "write the list" and "agree it": check each item against the work already in flight, and say per item whether it is untouched, already fixed somewhere unmerged, or colliding with a change in review.

State the surface explicitly, because the obvious reading is too narrow: open pull requests, branches pushed without a pull request, and the maintained release branches. A branch with no pull request is the one that gets missed, and it is where a maintainer's own unfinished work lives.

State the method too, because the obvious tools are wrong here: git cherry compares patch-ids and reports squash-merged branches as outstanding; a plain two-dot diff is dominated by how far behind the branch is. What answers it is a two-dot diff restricted to the files the branch touches — empty means the content is already in the base.

This is not TYPO3 knowledge, which may be why it is absent. But the skill already owns "the list is agreed before anything changes", and a list that proposes work already done is not one a maintainer can agree to.
