---
id: D-FBK-013
title: An empty queue is a state, not a failure
date: 2026-08-02
status: confirmed
coveredBy:
  - CliTest::theSightingsWaitForAnEmptyQueue
  - CliTest::whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue
  - StructureTest::noTestSkipsItselfInsteadOfHolding
  - TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead
---

# D-FBK-013 — An empty queue is a state, not a failure

**A test that needs a queued todo writes one, and nothing in the suite asserts
that this repository has a queue.**

The queue is the one directory here that a commit is supposed to empty. What the
three cases holding it non-empty produced was a red suite on the session that
finished the last todo, which is the one session that had done nothing wrong.

## Evidence

- The run of 2026-08-02 that finished `todo/540`, the crossing where `R-FBK-007`
  rested on the revoked `D-FBK-005`. Deleting the file left the queue empty and
  `composer test` red on three cases —
  `CliTest::theSightingsWaitForAnEmptyQueue`,
  `CliTest::whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue` and
  `TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead` — each of which
  opened with `assertNotSame([], Todo::items())`. The third said what all three
  meant: "nothing is queued, which is a state this can be in but not silently".
  Two PHP warnings came from the same emptiness, where the queued fixture read a
  position off `$items[count($items) - 1]` and found key -1.
- The commands had the case and the suite did not. `bin/cli todo:next` reaches
  the sightings only with the queue empty, which is the whole of what
  `**Every:** session` means, and closes with "Nothing is due and nothing is
  queued" where even those report no work. `bin/cli todo:list` prints "The queue
  is empty." and `bin/cli repository:check` counts it as `0 queued, 0 problems`.
  So an empty queue was announced by three readings and failed by one.

## Decided

- The three guards go. Two of them are about what happens while items wait, so
  they write the item they need: `tests/Support/QueuedTodo` puts one at the end
  of the queue and takes it away afterwards. The third needed nothing — its loop
  covers `recurring/`, `progress/` and `waiting/` besides, and what is never
  empty is what recurs, because a recurring todo is never deleted.
- A precondition that is a state rather than a property of this checkout is
  made, not asserted. `StructureTest::noTestSkipsItselfInsteadOfHolding` already
  closed the other road out, and for the same reason in reverse: a test may not
  quietly stop holding anything either. Producing the state is the third answer,
  and it is the only one that leaves the case saying what it says.
- The fixture moves out of `TodoTest` into `tests/Support/`, where `Directory`
  and the two installation traits are. It was one copy used by one file and is
  now used by two, which is the shape `Directory` was extracted at — after five
  copies had drifted.
- Nothing new is added to say the queue is empty. The alarm was not missing, it
  was in the one place a session could not act on it: a check a commit cannot
  pass is not a reading, and AGENTS.md already holds that nothing may fail on a
  state that is legitimately unfinished.

## Assumed

- That writing fixtures into the real `todo/` is right. It is what `TodoTest`
  has done since claims existed, and the properties under test — the order
  `todo:next` reads, a claim, a release — are properties of that directory. A
  temporary root is not cheap here: `Paths::root()` is derived from where the
  autoloader physically sits, which is the same thing that made a shared
  `vendor/` impossible under `D-FBK-010`.
- That the queue refills. An empty one hands over a sighting whose whole output
  is new entries, and 67 feedback were unjudged on the day this was written, so
  the mechanism had plenty to work with. Nothing has yet run a session where it
  produced nothing.

## Wrong if

- A fixture outlives its run and is handed over as real work. It carries a
  marker and an `#[After]` removes it, but a fatal error is not an `#[After]`,
  and to everything that reads `todo/` the file is an ordinary queued todo.
- The queue stays empty and nobody notices. Nothing fails on it now by design,
  and what says so instead are three commands a session has to run — which is
  exactly the argument the sighting-before-the-queue order lost under
  `D-FBK-012`, made from the other side.
- A case arrives that needs the queue to be *empty*. Then this fixture is a
  precondition in the way rather than one being met, and the pair has to be able
  to take the queue away as well as add to it.

## Confirmed on 2026-08-22

The state happened again in the session that read this: the queue emptied, the
suite stayed green, and the three commands the entry names said so. The second
**Wrong if** did not fire, and the reason is the order rather than the alarm — a
session that empties the queue is handed the sighting by the same command it was
working from.

The fixture holds where it was put, and nothing has needed the queue to be empty
as a precondition, which is the third **Wrong if**.
