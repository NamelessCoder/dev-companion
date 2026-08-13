---
date: 2026-08-13T22:43:11+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: AGENTSmd, How, a, session, reads, bincli, todoclaim
directory: /home/benji/projects/typo3-cms-mcp
---

# eight worktree sessions sent 369 calls and batched none of them, which is the rule AGENTS.md stat...

## Observation

The task was to work eight todos in parallel. Afterwards I read the eight transcripts rather than the reports, and counted what the sessions actually did. Every one of them had AGENTS.md inlined through CLAUDE.md, so all eight were handed the "How a session reads" section before their first call.

369 tool calls in 369 tool turns. Not one turn sent two calls. The section's first bullet — send the calls that do not depend on each other together — changed nothing in eight of eight sessions, and it is the bullet written from the 5414-call measurement in D-FBK-020.

Per session: 37, 41, 43, 44, 47, 48, 50, 59 calls, no batching in any of them.

The second bullet fared little better. Bash was 223 of the 369 calls (60%), and 79 of those 223 opened with ls (32), grep (23), sed (16), cat (4), head (3) or wc (1) — a file or search tool spelling itself out in the shell. 19 were sed -n windows into a file. Read was 96 calls, Grep 18, Glob none.

The third bullet held: no session read the same file twice. That is the one of the three that names a concrete waste a session can notice in itself, and it is also the only one that survived.

So the section is not being read as instructions to follow — or it is read and does not reach the moment a call is issued. Its own evidence is a measurement of exactly this shape, taken on 82 sessions, and the measurement repeats unchanged one release later.

What worked and must not be broken: the judging itself was good under the same conditions. Eight sessions each read the card against the repository rather than trusting it, each settled its question from a source (two re-ran measurements against review.typo3.org and Forge, one read .checkouts/main for the tokenizer, one re-ran TaskGuide::answer() and found a routing defect nobody had asked about), each declined at least one of its feedback's own suggestions with a reason, and each left composer ci green. Three of them wrote a Since then onto an existing decision instead of a new entry. One stopped and asked the maintainer with three answers priced rather than guessing. None of that is what the call count would predict.

## Query

bin/cli todo:claim 8; the eight sessions started from .session-command, each judging one feedback; transcripts read afterwards from ~/.claude/projects/-home-benji-projects-typo3-cms-mcp--worktrees-<worktree>/*.jsonl

## Suggestion

Two readings, and the choice between them is the point: either the bullets are advice a session may weigh, in which case eight of eight weighing it away is the answer and the section should say what it actually buys rather than how a session should behave — or they are a rule, in which case something has to hold them, and nothing does. Nothing here can fail on a call pattern the way prose:check fails on a sentence.

The cheap half is the second bullet, which is mechanical: a session reaching for ls, cat, sed -n or grep in Bash is asking for Glob, Read or Grep. That is a sentence in .session-command's message, where it is read at the moment the session starts rather than in a document loaded before the task, and .session-command already carries the message all eight were sent. The first bullet is harder and probably not worth policing: batching is a property of a turn, and a session that reads it as advice will still issue one call at a time.

Worth measuring before either: whether the sessions that judged well are the ones that spent the calls. 37 to 59 is a narrow band for eight tasks of visibly different depth.
