---
id: D-DOC-044
title: A failing test names the decisions it was holding
date: 2026-08-23
status: open
restsOn: [D-DOC-043]
coveredBy:
  - DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd
---

# D-DOC-044 — A failing test names the decisions it was holding

**A test that fails prints the entries whose **Covered by** names it, with the
path to each.**

The session that made it red is sent to the decision rather than left to make
the test green again.

`D-DOC-043` coupled the two directions and stopped there: an entry names its
test, and since 2026-08-22 the test names the entry back. Both namings live in a
file somebody has to open. A failure prints neither.

## Evidence

- Read on 2026-08-23 over the readings of the two days before it. 44 entries
  carry a **Since then** or a **Confirmed on** dated 2026-08-22 or 2026-08-23,
  and 22 of them name no test at all. A reading holds the reading; it holds
  nothing about the entry.
- The word that promises most holds least. 22 of the 79 `confirmed` entries
  point at this repository's code and name no test — and `confirmed` is the
  status that reads as "somebody checked", where `open` at least says nobody has
  been back.
- The guard mostly exists and is filed wrongly. Of those 22, 18 name a test
  somewhere in their prose and only in prose: `D-COD-001` names
  `StructureTest::everyFileDeclaresOneClass` in **Decided**, `D-COD-003` its
  finder test in a dated section, `D-DOC-001` two `ProseTest` methods written
  the same day. `Decisions::uncovered()` reads **Covered by** and sees none of
  them.
- What a failure prints today is the assertion message and the file and line of
  the assertion. Nothing in it says which entry rested on the behaviour that
  moved.

## Decided

- A PHPUnit extension, `Tests\Support\HeldDecisions`, registered in
  `phpunit.xml.dist`. It collects the failed and errored tests, and at
  `ExecutionFinished` prints each entry whose **Covered by** names one, with its
  id, its title and its path. A run where nothing fails prints nothing.
- `Decisions::restingOn()` is the reading, and it is the other direction of
  `unnamedByItsTests()`: which entries a test was holding rather than what a
  test says about its entry. A **Covered by** naming a whole class holds every
  method in it, which the format already allows.
- The extension is thin and the reading is tested.
  `DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd` asserts it over
  the whole corpus — every entry that names a test is reachable from that test —
  because a mapping that misses an entry is a decision that quietly stops being
  pointed at.
- No test is edited for it. The coupling it prints is the one **Covered by**
  already declares, so a decision joins the mechanism by being named there and
  by nothing else.

## Assumed

- That the session reading a failure reads to the end of the run. The report
  prints once, after the failures and before the summary, rather than beside
  each one — a failure list of thirty would otherwise carry the same entry
  thirty times.
- That naming the entry is enough to send somebody to it. What the line carries
  is the id, the title and the path; whether that is read as an instruction is
  not something this can hold.

## Wrong if

- A session reports making a test green while the entry it named said the
  behaviour was decided the other way. Then the pointer arrived and was not
  read, and what is missing is in the entry rather than in the run.
- The report grows long enough to be scrolled past. It is one line pair per
  entry per failing test, and a suite where fifty tests fail is a run nobody
  reads that far into anyway.
- `Covered by` starts collecting names chosen to make this print something. That
  is `D-DOC-043`'s second **Wrong if** with a new incentive behind it, and the
  count falling while entries go on going stale is what would show it.
