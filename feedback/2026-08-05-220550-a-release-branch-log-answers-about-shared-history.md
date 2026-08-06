---
date: 2026-08-05T22:05:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms-mcp
---

# A release branch's log answers about the history from before it was cut

## Observation

Task was the todo "hold the release lines a commit message claims": establish which TYPO3 branches are maintained and have typo3_commit_message_guide hold its releases argument against them.

To settle whether a feature may target a release line I counted subject keywords per branch in a core checkout, and the obvious form of the question is wrong. `git log origin/14.3 --since=2025-01-01` reports 188 [FEATURE] and 96 [!!!] on 14.3, because a release branch's log contains the whole shared history from before the branch was cut. The question only means what it looks like it means when it is asked as `git log origin/main..origin/14.3`, which reports zero of each — and on 13.4, three [FEATURE] against 969 [BUGFIX], all explicit backports.

The two forms differ by one operator and give opposite answers about whether features reach a release line. A session establishing backport policy from the first would write a false statement into knowledge/, verified against a real checkout, with the command in the commit message as evidence. I caught it because 188 features on a branch cut three months earlier was implausible, not because anything warned me.

## Query

typo3_rule_lookup was not called for this — the corpus had nothing on which branches take a change, which is what the todo existed to fix. The two commands were `git log origin/14.3 --since=2025-01-01 --format='%s' | grep -oE '^\[[A-Z!]+\]' | sort | uniq -c` and then `git log origin/main..origin/14.3` with the same pipeline.

## Suggestion

A hint, or a line in the contribution corpus: what a release branch carries since it was cut is `origin/main..origin/<branch>`, and a plain log or a --since window on that branch answers about the shared history instead. It belongs beside the release-target rules, because that is the question it is asked in service of, and it is the kind of measurement a session makes exactly once and writes down permanently.
