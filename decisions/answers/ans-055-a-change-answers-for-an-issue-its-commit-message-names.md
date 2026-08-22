---
id: D-ANS-055
title: 'A change answers for an issue its commit message names'
date: 2026-08-05
status: open
coveredBy:
  - GerritTest::aChangeMatchedByItsNumberAndNotItsMessageIsNotAnswered
  - GerritTest::theNumberInAReviewUrlIsNotTheIssueBeingNamed
  - GerritTest::anAnswerOfNothingButFalsePositivesIsEmpty
  - GerritTest::theCommitMessageIsAskedForWhereTheAnswerIsHeldAgainstIt
  - GerritTest::aChangeWhoseMessageDidNotComeBackIsJudgedByItsNumberAlone
---

# D-ANS-055 — A change answers for an issue its commit message names

**A change is handed back for an issue only where its commit message names that
issue, and what the review server matched some other way is held back.**

`message:<number>` is the right question and not the whole answer: Gerrit
indexes the change number under the same operator, so the search returns the
change that carries the number as its own whatever it is about.

## Evidence

- `feedback/2026-08-05-033826`: five of seven calls in one session came back
  with a MERGED core change whose number equalled the issue number and whose
  subject had nothing to do with it. The truthful answer for all seven was
  nothing.
- Re-measured against `review.typo3.org` on 2026-08-05. `message:81676`,
  `message:87400`, `message:88690` and `message:93409` each answer with the
  change of that number and with nothing else; `message:88556` answers with
  change 88556 and with change 95108, which resolves the issue.
- Change 88556's commit message names issue 106318 and carries `88556` in its
  `Reviewed-on:` trailer alone — the trailer a merged change gains, which ends
  in the change's own number.
- A narrower query does not settle it. `message:"#88556"` still answers with
  both, and `message:"Resolves: #88556"` answers with the right one and would
  miss every `Related:` and every issue named in a sentence.
- What the false positive costs is written into both skills that call this.
  `typo3-core-issue-triage` calls its cheapest outcome the one that ends the
  work, and `typo3-core-patch-development` the one that cancels it. The reported
  session was two clicks from abandoning a patch that did not exist.

## Decided

- The filter is on this side. `o=CURRENT_COMMIT` comes back with the search, and
  a change whose message does not carry the number outside a URL is dropped.
- URLs and the `Change-Id:` line are removed before the message is read, because
  the trailer that carries the number without meaning it is a URL ending in it.
- Everything else in a reference position stands: `Resolves: #88556`,
  `Related: #88556`, an issue named in prose. What is dropped is the match no
  human wrote.
- A change whose message did not come back is judged by the one rule that needs
  none: it is the false positive where its own number is the number asked for.
- Nothing is filtered on a `change:` lookup. A caller naming a change has named
  it.
- What was held back is said in the answer, with the reason. The `query` field
  exists so the question can be asked by hand, and a hand-run query answers with
  more than this one.

## Assumed

- A change that resolves an issue names it in its commit message. That is what
  the core's own process requires, and it is what `message:` was reaching for
  before the index widened it.
- The number is not carried in a further place that means nothing. A version
  string like `12.4.88556` would read as a reference; no such message was found
  in the five measured.

## Wrong if

- A session reports a change this dropped that really was about the issue —
  visible as a `dropped` count beside an answer a caller then finds by hand.
- The core stops writing the issue into the message, and the filter empties the
  answer for a whole class of change.
- `o=CURRENT_COMMIT` stops being served anonymously, which would leave every
  answer decided by the number rule alone.
