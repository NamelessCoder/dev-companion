---
id: D-DOC-048
title: A test declares the decision it holds
date: 2026-08-23
status: open
restsOn: [D-DOC-043, D-DOC-045]
coveredBy:
  - DecisionsTest::everyEntrySaysWhatTheTestsHoldingItDeclare
  - EntriesTest::aGeneratedListReplacesTheValueTheKeyAlreadyCarried
  - EntriesTest::anEntryNothingHoldsKeepsTheWordsItSaidSoIn
---

# D-DOC-048 — A test declares the decision it holds

**A test carries `#[Decision('D-XXX-000')]`, and `bin/cli decisions:cover`
writes each entry's `coveredBy` from those attributes.**

The coupling has to be readable from both ends, and written by hand at both ends
it drifted: the entry named the test and the test said nothing back.

## Evidence

- Read on 2026-08-22 over the 555 test names the entries carried: 150 said
  something about the entry resting on them and 405 did not. Naming them back
  took three passes and a scripted rewrite, which is what one hand-kept copy of
  a coupling costs every time either end moves.
- The prose reading that measured it had to be corrected twice — first for
  docblock-only, then for where the run of comments above a declaration begins —
  and each correction moved the count. A reading of prose is a reading nobody
  can reproduce.
- 240 entries name 564 tests in 53 classes on 2026-08-23, and every one of them
  is a method. Nothing names a whole class, so the attribute takes a method and
  the reading no longer carries the class case.
- Generating `coveredBy` from the attributes reproduced what 447 of the 448
  entries already said. The one that differed named the same test twice.

## Decided

- `#[Decision]` in `tests/Support/`, repeatable, on a method. It is the source
  and `coveredBy` is the copy, so a test renamed or moved rewrites the entry
  rather than orphaning a name in it.
- `bin/cli decisions:cover` writes the copy and `bin/cli decisions:check` fails
  where the file says anything else, naming the command. A test declaring an id
  no entry has fails the same check.
- The prose naming is gone with the reading that measured it, which was 60 lines
  of `Upkeep\Sources` and a report in `decisions:check`. A comment saying what
  the attribute above it says is the second copy `AGENTS.md` sends to the id
  instead.
- Read from the text rather than through reflection, like the rest of
  `Upkeep\Sources`: loading every test class to ask about it is a cost every
  check run would pay.

## Assumed

- That a test declaring an entry holds that entry's claim. Nothing measures
  that, and it is `D-DOC-043`'s first **Assumed** in the place where it is now
  written.
- That an attribute is harder to leave behind than a sentence, because a renamed
  method carries it and a moved one does too.

## Wrong if

- An attribute is added to leave a report rather than because the test holds the
  entry. `coveredBy` would grow while nothing more is held, which is the same
  failure `D-DOC-043` names and the same one nothing can see.
- A decision needs a clause per test the way a requirement's **Held by** does.
  The attribute carries an id and nothing else, so the clause would have to go
  somewhere, and that somewhere is the prose this replaced.
- The generated `coveredBy` is edited by hand and the edit is lost. It fails the
  check, so what would show it is a commit that never ran one.
