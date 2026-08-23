---
id: D-DOC-054
title: A held decision is read when its behaviour moves
date: 2026-08-23
status: open
restsOn: [D-DOC-044, D-DOC-048, D-DOC-053]
coveredBy:
  - UnresolvedTest::anOpenDecisionATestHoldsIsNotWaitingForAReader
---

# D-DOC-054 — A held decision is read when its behaviour moves

**`bin/cli unresolved:list` names the oldest open decision that no test holds,
because one a test declares is read by whoever makes it fail.**

Going back to an entry was a scheduled task while nothing pointed at it. The
attribute is the pointer, and the failure is the appointment.

## Evidence

- Read on 2026-08-23. 336 of 454 decisions are open and 155 have no **Since
  then**, which is the reading this listed until today. 120 of those 155 are
  declared by a test, and 35 are not.
- What a declared entry gets is better than a scheduled reading and arrives at
  the right moment: `Tests\Support\HeldEntries` prints the id, the title and the
  path of every entry a failing test was holding, so the session standing in the
  changed behaviour is the one who reads it — `D-DOC-044`.
- A scheduled reading arrives at no moment in particular. It was 155 entries
  deep this morning and the oldest was three weeks old, which is what a queue
  nobody is owed looks like.

## Decided

- The listing counts all three — open, never revisited, held by nothing — and
  names the oldest of the last kind. The other two numbers stay, because a
  reader who sees only the third cannot tell whether the corpus shrank or the
  coupling grew.
- The recurring todo keeps its job and loses the pile. What it asks for is the
  same judgement; what it is pointed at is the 35 rather than the 155.
- Nothing changes for a requirement. `not guarded` already means no test holds
  it, so its listing was never about the ones that do.

## Assumed

- That a test declaring an entry fails when the entry's own claim moves, rather
  than only when something near it does. It is `D-DOC-048`'s assumption, and
  this entry spends it: an entry held by a test that watches something else is
  now also an entry nobody is scheduled to read.
- That behaviour a decision describes gets touched eventually. An entry about
  code nobody edits for a year is read by nobody under this rule, and it was
  read by the listing before it.

## Wrong if

- An entry held by a test goes stale anyway, and what showed it was somebody
  reading the corpus rather than a failure. Then the coupling is thinner than
  this rests on, and the pile it removed was doing something.
- The 35 stay 35. The point of naming the oldest is that somebody answers it; a
  number that does not move means the reading was dropped rather than narrowed.
