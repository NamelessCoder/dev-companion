---
id: D-DOC-013
title: A commit here is three keywords and a condensed subject
date: 2026-08-03
status: confirmed
---

# D-DOC-013 — A commit here is three keywords and a condensed subject

**A commit message in this repository is `[TASK]`, `[BUGFIX]` or `[FEATURE]`, a
subject under 52 characters, and a body wrapped at 72.**

`AGENTS.md` stated the shape of a body and named not one keyword, so the set was
learned by reading `git log` — where four keywords and no keyword at all are all
attested.

The two measures could not both be right. This repository's own
`typo3_commit_message_guide` describes this case in its own words for
`workflow="project"`, while the history is written wider than what that answers.

## Evidence

- Over the 926 commits on `main` on 2026-08-03: 671 `[TASK]`, 85 `[FEATURE]`, 26
  `[BUGFIX]`, 5 `[DOCS]`, no `[!!!]`, and 139 carrying no keyword.
- 708 of those 926 subjects run past 52 characters, at a median of 61 and a
  maximum of 97.
- 8587 of 15289 body lines run past 72, at a median of 74 and a ninetieth
  percentile of 79 — the column `bin/cli prose:format` wraps the markdown corpus
  at.
- `Knowledge\CommitMessage` warns above 52 characters of subject, fails above
  72, and wraps a body at 72, over the enum `BUGFIX`, `FEATURE`, `TASK`, `DOCS`,
  `SECURITY`.

## Decided

- Three keywords, not five. `[DOCS]` and `[SECURITY]` belong to the core's
  process — the security team owns one and the changelog the other — and this
  checkout runs neither, so a documentation change here is a `[TASK]`.
- The tool's widths, not the history's. The measure this repository states for
  other repositories is the one it can be held to, and the alternative was a
  second number that exists only in this checkout.
- The 80 that `prose:format` wraps at stays what it is: the markdown corpus's
  column. It reached commit bodies because both were written by the same hand,
  not because anything said it applied.
- The history is not the rule. 708 subjects stay past 52 and nothing goes back
  over them; the rule holds from here.
- Nothing checks it. The `commit-msg` hook the card offered would have been the
  first thing in this checkout to refuse a commit, where `.githooks/pre-commit`
  repairs and never refuses, and a hook that only warns prints what the reread
  before `git commit` already has to catch.

## Assumed

- That a condensed subject fits 52 characters. The history's median is 61, so
  this is a bet that the extra nine are the sentence rather than the subject.
- That a reread holds it, which is the bet the prose rule already makes and the
  one `D-DOC-002` records.

## Wrong if

- Subjects keep landing past 52 after this date. Then either 52 is the wrong
  number for a repository whose subjects say what a change did rather than which
  ticket it closes, or a reread is not enough and the `commit-msg` hook comes
  back.
- A commit here genuinely needs `[DOCS]` or `[SECURITY]` — a security advisory
  against this server would be the case, and that keyword is then not the core's
  alone.

## Confirmed on 2026-08-23

Measured over the 779 commits on `main` since 2026-08-04. The keyword half held
without an exception: 668 `[TASK]`, 70 `[FEATURE]`, 41 `[BUGFIX]`, no `[DOCS]`,
no `[SECURITY]`, no `[!!!]`, and not one subject without a keyword — against 139
of 926 carrying none when this was written. The second **Wrong if** has not
fired either; no change here has needed one of the core's two.

The first one has. 397 of the 779 subjects are 52 characters or longer, at a
median of 58 and a ninetieth percentile of 62, and the three weeks run 56%, 42%
and 55%, so nothing is converging. What did move is the other number: exactly
one subject is past 72, where 708 of 926 used to be past 52 at a median of 61.

So the practice follows `Knowledge\CommitMessage`'s two severities rather than
one demand — it warns above 52 with "Under 52 characters is preferred" and fails
above 72 — while `AGENTS.md` stated both numbers as one rule. Put to the
maintainer on 2026-08-23 with the measurement, and the answer was that the
wording follows the tool: the widths stand as this entry decided them, and the
line now says 52 is where a subject goes if it can and 72 is where it may not.

The other two were declined. Raising 52 would move what
`typo3_commit_message_guide` tells every other project, which is the second
number this entry refused to invent; the `commit-msg` hook stays refused for
what **Decided** already says, that a hook which only warns prints what the
reread has to catch anyway. What holds the widths is still the reread, and the
number to measure the next reading against is above.
