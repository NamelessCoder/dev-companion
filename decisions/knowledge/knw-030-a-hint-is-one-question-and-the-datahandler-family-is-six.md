---
id: D-KNW-030
date: 2026-08-03
status: open
---

# D-KNW-030 — A hint is one question, and the DataHandler family is six of them

**A subject is split along the questions a caller arrives with rather than along
the subsystem: DataHandler becomes six hints, and reading a record becomes
`persistence-reading`.**

`datahandler-persistence` was one hint over eight statements — the datamap,
relation resolution, record placement, the backend user, and three sentences of
patch-review obligation. Which of them a caller got depended on how much of its
329 words their phrasing happened to cover.

## Evidence

- The subject was already split and nobody had said so: DataHandler statements
  sat in `datahandler-persistence`, in `sitepackage-initial-content` (the boot,
  the backend user, the datamap ordering) and in `core-tests` (the test base
  class). The axis was the file somebody had open, not the question.
- The gap that axis hid is `R-KNW-045`: the vocabulary for reading records was
  on a hint with no statement about it, and the corpus held one sentence naming
  any reading API.
- After the split, `hints:coverage` reports a mean body of 278 against the
  300-word ceiling, up from 3 words of headroom to 22, with no statement
  deleted. The General share falls from 63% to 59% for the same reason: six
  reachable PHP hints displace what the always-on bucket used to supply.
- `hints:probe "how do I read records without the hidden and starttime
  restrictions"` answers `persistence-reading` first, and `"seed a page tree
  with content programmatically"` answers `datahandler-seeding`. Neither query
  reached anything about its subject before.

## Decided

- Six hints, one question each: what DataHandler is and why not an INSERT
  (`datahandler-basics`), the datamap (`-writing`), what it does to a relation
  field (`-relations`), where a record lands (`-placement`), building content
  that exists nowhere yet (`-seeding`), covering any of it (`-testing`).
- Reading is not DataHandler and gets its own file. The write path is one API
  and the read path is a QueryBuilder with restrictions on it plus two overlays
  applied afterwards; filing them together is what produced the gap.
- impexp stays with the shipping subject, and the seeding hint says why: a page
  tree that has to be established again is what the export is for, and a seeding
  script is for content that exists nowhere yet (`R-KNW-046`). The corpus had it
  the other way round — the script was the product and the export its residue.
- The entry hint names the other five. A family that a caller lands in the
  middle of has to say what else there is, or the split trades one unfindable
  hint for five.
- The bare `DataHandler` pattern is on the entry hint and the testing one only.
  On all six it made them tie on the curated score and crowd the answer: a core
  test question came back with four datamap hints and lost `core-tests` off the
  end of the limit. A pattern shared by every hint of a family discriminates
  nothing.

## Assumed

- Six is the granularity, not a step towards more. Each of the six is a question
  somebody has asked in a feedback or a scenario; a seventh would have to name
  the question it answers.
- The statements moved out of `sitepackage-initial-content` and `core-tests` are
  not missed there. Both keep their own subject whole, and the seeding hint is
  reached by the words a seeding question is asked in.

## Wrong if

- A DataHandler question comes back with three of the six and the one it needed
  is not among them. That is the family crowding itself, and the fix is the
  vocabulary rather than a merge.
- `datahandler-basics` becomes the answer to every DataHandler question, which
  would mean the five specific ones are unreachable and the split is cosmetic.
- Somebody asks how to read a record and gets `persistence-reading` plus four
  write-path hints, because `persistence` is a word both halves carry.

## Covered by

- `HintsTest::readingRecordsIsAnsweredAsWellAsWritingThem`
- `HintsTest::theSeedingAnswerNamesImpexpAsTheWayATreeIsEstablishedAgain`
- `HintsTest::aRelationInADatamapSaysWhatTheParentColumnEndsUpHolding`
- `HintsTest::theSeedingAdviceCarriesTheStepsItAsksFor`
