---
id: R-ANS-029
title: 'An answer that names a record says enough of it to judge whether to open it'
status: held
restsOn: [D-ANS-064, D-ANS-069]
heldBy:
  - ForgeTest::aQueryUrlNamesNoChangeAndIsNotReportedAsOne
  - ForgeTest::aRelationCarriesEnoughOfTheOtherIssueToJudgeWhetherToReadIt
  - ForgeTest::aRelationTheFillCouldNotReachIsStillTheRelationThatWasFiled
  - ForgeTest::aReviewChangeIsLiftedOutOfTheProseThatCarriesIt
  - ForgeTest::aRowSaysWhetherTheReviewServerHoldsAChangeThatNamesIt
  - ForgeTest::theRelationsOfAWholePageAreFilledInOneCall
  - GerritTest::aChangeCarriesTheIssuesItsCommitMessageNames
---

# R-ANS-029 — An answer that names a record says enough of it to judge whether to open it

**Where an answer names another record this server can read, it carries what a
caller needs to decide whether to read it — not the identifier alone.**

An identifier with nothing beside it costs one call to evaluate, so a caller
holding several of them evaluates none. The record that mattered is then skipped
for the same reason as the ones that did not, and the answer looks complete
while the caller acts on less than it was handed.

## From

`feedback/2026-08-07-231225`, 2026-08-07. `typo3_forge_lookup` answered issue
15984 with four relations as `{issue, relation}` pairs. The session spent no
reads on them and told the user nothing about them; one was `#32756`, "Massive
Memory Leak in 4.5.8+ / 4.6", marked `precedes` — the issue the 2012 revert was
filed under and the record that answers what a fix would cost. It surfaced
afterwards out of a git commit message.

`feedback/2026-08-07-231146` is the same shape one field over: the Gerrit change
references an issue's journal names are prose inside a note, so
`typo3_gerrit_lookup` was never called and never even had its schema loaded.

**Built on 2026-08-08.** A relation carries the subject, tracker and status a
search hit already carries, filled for the whole set in one bulk read of
`issues.json?issue_id=…&status_id=*`; the change references are parsed out of
the journal into a `reviews` field naming `typo3_gerrit_lookup`, with the change
number, the Change-Id and the patch set a note gave them. Nothing is claimed
about a change's state: a note says what was true the day it was written.

**Widened on 2026-08-09** to the enumeration, where the same two records are
what a backlog row is chosen on. A row carries its relations filled by the one
bulk read the issue answer already made, and the changes on the review server
whose commit message names it — asked of Gerrit in one query per twelve rows,
because the journal that carries a change reference is not in the index answer
at all (`D-ANS-069`).

## Held by

What no test reaches is the general form — every answer that names a record this
server can read. The fields the feedback named are held; a further one that
arrives is held when somebody writes it.
