---
id: D-DOC-002
title: The prose rule is measured, and only the lead fails on it
date: 2026-08-01
status: open
---

# D-DOC-002 — The prose rule is measured, and only the lead fails on it

**Thirty words is where a sentence in this repository is reported as two, and
the only place it fails is the bold opening of a requirement or a decision.**

Everything else `bin/cli prose:check` prints is a count.

Every other rule in AGENTS.md is held by something: the names by
`ToolNamingTest`, the file shapes by `bin/cli requirements:check`, the scope by
`ScopeTest`. "One point per sentence" was held by whoever happened to reread the
paragraph, and prose is the one thing this repository produces that nothing
downstream can tell apart from prose that was thought through.

## Evidence

- 47 of 169 leads ran past 30 words, the longest at 96, and every one of them
  came apart into a rule and the enumeration behind it without losing a word.
  Across the whole corpus 805 of 3944 sentences are over, concentrated in six
  files that carry a fifth of them.

## Decided

- 30 words, because that is where the leads stopped being one point. It is not a
  style ceiling read off a manual, and it is the same number in the check and in
  the rule it holds.
- The body is reported and never fails. A long sentence can be the right one,
  and a rewrite made to satisfy a counter produces two short sentences saying
  what one said. What the count is for is the file with twenty of them, which is
  a file nobody has reread since it was written.
- The lead fails, because that sentence has a job the rest of the file does not
  — a reader who stops after it knows what was settled, and nobody stops after
  96 words.
- `feedback/` is not measured. A feedback is a session's report written in
  somebody else's agent, and holding it to this rule would report on the wrong
  author.

## Assumed

- ~~That the counts move down. The report is a number nobody is obliged to act
  on, which is the same shape as the three states `bin/cli unresolved:list`
  names, and those sat unread until something printed them.~~ Read on
  2026-08-22: the share held while the corpus grew sevenfold, so what the report
  moved is nothing.

## Wrong if

- The corpus total sits where it is for a month while the files are edited —
  then reporting is not enough and the measure needs a place in the work rather
  than a line in a check. Or a lead genuinely needs more than 30 words and the
  split makes it worse, in which case the number is measuring the wrong thing
  and the exception has to say so where it is taken.

## Since then

The half that fails works and the half that reports did not move. Read on
2026-08-22: every requirement and decision opens within the measure, so no lead
has run past 30 words since the check was written, and the corpus stands at 5777
sentences over of 27046 against the 805 of 3944 this entry recorded — 21.4%
against 20.4%, three weeks and about seven times the prose later.

What the numbers also show is that the concentration went. Six files carried a
fifth of the long sentences on 2026-08-01; the ten worst carry 454 of 5777
today, which is 8%. So the file nobody has reread since it was written is no
longer where they are, and the count reads as a property of how this repository
writes rather than as a backlog.

The first **Wrong if** is not due until 2026-09-01 and this is what it will be
read against. What would answer it either way is a month with the number moving,
and nothing here can make that happen while judging.
