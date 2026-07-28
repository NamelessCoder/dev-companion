---
date: 2026-07-28T13:44:43+00:00
category: missing-knowledge
status: open
tool: typo3_script_help
---

# The Gerrit workflow is absent, although it is how every core patch is actually submitted. Greppin...

## Observation

The Gerrit workflow is absent, although it is how every core patch is actually submitted. Grepping knowledge/ for "refs/for" returns zero hits; "Change-Id" appears only in typo3-commit-messages.md, and only as the rule "do not create or change it manually". typo3-contribution-sources.md does not contain the workflow either — it only says "Use this guide for contribution setup, account setup, Git and Gerrit workflow, ...", i.e. it is a pointer to a document that is not bundled. So the knowledge base explains how to write a commit message but not how to get it into review, how to amend an existing patch set, or how to push a backport to a release branch.

## Query

task="push a patch to gerrit for review"

## Suggestion

Add a short Gerrit section: push for review with "git push origin HEAD:refs/for/main" (and the equivalent for release branches), amend a patch set with "git commit --amend" while keeping the Change-Id, where the commit-msg hook comes from and that it must be installed once, and how a cherry-picked backport keeps its own Change-Id. If bundling the full contribution guide is out of scope, at least return the canonical URL instead of a summary line that only describes what the missing document would cover.
