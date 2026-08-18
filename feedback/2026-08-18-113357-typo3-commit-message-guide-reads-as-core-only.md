---
date: 2026-08-18T11:33:57+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/bootstrap_package
---

# typo3_commit_message_guide reads as core-only from its name and was passed over on the project re...

## Observation

Task: review PR #1627 against the bootstrap_package sitepackage, then merge it, fix a second unrelated CI failure, and backport both to the maintenance branch.

I wrote six commit messages during this session — four commits and two squash-merge subjects — for a sitepackage repository, and derived their convention by hand: `git log --oneline -15` to see that the repo writes `[BUGFIX] Subject (#PR)`, plus a check that CONTRIBUTING.md does not exist and Documentation/Contribution says nothing about it. I never called typo3_commit_message_guide.

My assumption, from the bare name in the deferred list, was that a "TYPO3 commit message guide" meant the core's Gerrit convention — Resolves:, Releases:, Change-Id — which would have been actively wrong for this repository and worse than no answer. Reading the schema during this debrief, the assumption does not hold. The tool takes `workflow` and defaults it to "project", described as "any repository of your own — the keyword, the 52/72 character limits and the wrapping are checked, no trailer is added or demanded". It also wraps bodies at 72 characters and names lines it could not bring under the width, which I did by eye across six messages of five to fifteen body lines each.

It would have fitted every one of them. This is a name-versus-content finding rather than a knowledge gap: the tool was there, it was right, its default was already the case I was in, and its name kept it shut for the entire session.

## Query

Six commit messages and squash-merge subjects written for benjaminkott/bootstrap_package, a non-core repository. typo3_commit_message_guide never called; convention derived from `git log --oneline -15` instead. Assumed core-Gerrit-only from the tool name alone.

## Suggestion

Nothing about the tool's behaviour needs changing — its default is already the right one. Its discoverability does. The name says "TYPO3 commit message", and under schema deferral that is all a model sees, so "TYPO3" reads as "core". One line in the server instructions saying that this tool covers your own repositories by default and the core workflow on request would remove the assumption I made. Alternatively the name itself could carry it.
