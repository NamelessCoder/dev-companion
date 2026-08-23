---
id: D-DOC-053
title: What no test holds is unheld for a reason in the entry
date: 2026-08-23
status: open
restsOn: [D-DOC-043, D-DOC-048, D-DOC-052]
coveredBy:
  - DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOut
---

# D-DOC-053 — What no test holds is unheld for a reason in the entry

**The corpus was swept once and what stayed uncovered stayed for a reason the
entry itself carries, so the number the check prints is a state rather than a
backlog.**

An attribute is added because a test would catch the entry's **Wrong if**, never
because a report would be shorter.

## Evidence

- Swept on 2026-08-23 over 453 decisions. 87 entries pointed at this
  repository's code and were held by nothing; 23 are left, 16 of them `open` and
  7 `confirmed`.
- 49 gained an attribute. 43 of those were on a test written for the entry that
  said so in its docblock and nowhere a listing could read — the naming had run
  one way until `D-DOC-048`.
- Two were new tests, and both were entries about a boundary rather than about
  an answer: `D-ANS-003`, where an embedding library would arrive as a
  dependency and a database tool as a connection, and `D-KNW-058`, where a
  document id is a scope, a topic and a name.
- The 23 left fall in three kinds and none of them is neglect. Seven `confirmed`
  are process — how a feedback is judged, how evidence is produced — and nothing
  runs over them. Nine record a gap or a defect nobody has closed, so there is
  no behaviour to hold. The rest decide that a domain earns a skill or that a
  fact belongs in a document, whose **Wrong if** only a session can settle.
- The four that were declined are the measure of the criterion. A test asserting
  a skill exists does not hold "this domain earns a skill", and a test asserting
  no skill carries a fetch line watches for something nobody has written.

## Decided

- The count stops moving on its own, and a session that wants to move it reads
  the entries rather than the number. What it may not do is attach a test that
  comes near the entry: that is `D-DOC-043`'s first **Assumed** turned into a
  habit, and it makes the report say the opposite of what it measures.
- `Decisions::uncovered()` leaves revoked entries out. `D-DOC-052` forbids a
  test declaring one, so counting them as missing a test would report as absent
  what the checks refuse.
- The report says how many are `open` and how many `confirmed`, and names this
  entry. A reader who meets the number needs to know it was read once, and
  where.

## Assumed

- That the reasons are readable in the entries. Nothing is written into the 23
  saying "no test can hold this", because a sentence in each is a sentence to
  keep true — the kinds are named here instead and the entry says the rest.
- That a swept corpus stays swept. Every entry written from now on is written
  under `D-DOC-048`, where the test declares the id, so a new entry arrives held
  or arrives knowing it is not.

## Wrong if

- The number climbs while nobody notices, which would mean new entries are being
  written the old way. `bin/cli decisions:check` prints it on every run and
  `bin/cli repository:check` closes with it.
- One of the 23 goes stale — the code moves under an entry nothing was watching.
  That is the case `D-DOC-043` exists for, and it would show as an entry whose
  statement describes a call that no longer exists.
- A session shortens the report by attaching tests that do not hold what they
  declare. The count would fall and nothing more would be held, which nothing
  here can see.

## Since then

The reading that wrote this entry read the **Decided** and stopped there, and
eleven of the 23 were closed further down. `D-ANS-022` ends its **Since then**
with the sentence "`HintsTest::aCompoundIsFoundWhicheverWayTheCallerJoinsIt` is
what would catch this coming back" — a declaration written in prose, four months
of readings after the entry said it was queued. `D-ANS-030`, `D-ANS-046`,
`D-ANS-014`, `D-ANS-021`, `D-ANS-024`, `D-FBK-002`, `D-KNW-061`, `D-KNW-069`,
`D-SKL-012` and `D-SKL-033` are the rest, and each named its test in the same
place.

So "nine record a gap nobody has closed" was wrong on the same day it was
written, and what was wrong with it is the reading rather than the count: an
entry is what it says now, not what its **Decided** said when it was queued. 12
are left, six `open` and six `confirmed`, and the two kinds named above hold for
them — the process entries nothing runs over, and the ones whose **Wrong if**
only a session can settle.

What the entry keeps is the criterion. `D-KNW-016` was declined here again: the
gap it records is closed, and the test that reaches the hint carrying the
statement would pass with the statement deleted, so it does not catch the gap
reopening.

