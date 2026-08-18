---
date: 2026-08-18T11:34:41+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/bootstrap_package
---

# a project package's release and backport policy had to come from the human after work had already...

## Observation

Task: review PR #1627 against the bootstrap_package sitepackage, then merge it, fix a separate frontend build failure and carry both to the maintenance branch.

Halfway through, the user stopped me to state the repository's rules: every released version branches off into a maintenance branch, pull requests are always squash-merged, fixes always start on master and are cherry-picked down, and only the branch of the latest released version is actively supported, with older lines touched only in severe cases. None of that was derivable with confidence from the checkout before being told. I could see BP_6-2 through BP_16_0 in the remote refs, and `git log --merges` showed the last merge commit came from PR #463 with everything since squashed — but "which of these branches is still alive", "master first", and "never fix directly on a maintenance branch" are policy, not history.

The cost was concrete and came after the fact. By the time I learned the rules I had already opened a pull request and pushed a branch. I then had to survey five maintenance branches to work out which were affected by the second bug, and I nearly proposed cherry-picking into BP_15_0 and below, which are not supported at all — that was averted only because the Node version pinned in each branch's CI workflow happened to make them technically unaffected as well, not because I knew the support policy.

typo3_project_describe is described as answering what the installation is, which extensions are the project's own, and which commands the repository declares. Whether it reaches release policy I do not know; I did not call it, for the reason filed separately about name-only tool lists.

## Query

Release, merge and backport policy for the benjaminkott/bootstrap_package project package — supplied by the user mid-session in four separate messages, never asked of the server. typo3_project_describe not called.

## Suggestion

Where a project package declares its supported branches and its merge and backport policy anywhere machine-readable — a CONTRIBUTING file, a .github config, a documented branch scheme, or simply an inferable pattern of `BP_*`-style branches paired with tags — typo3_project_describe reporting it would let an agent get the workflow right before it pushes rather than after. Where none of it is written down, the useful answer is that it is not written down, so the agent knows to ask the human before opening a pull request instead of guessing from branch names. The distinction between "the latest release branch is maintained" and "all these branches exist" is the one that mattered here and the one a branch listing cannot carry.
