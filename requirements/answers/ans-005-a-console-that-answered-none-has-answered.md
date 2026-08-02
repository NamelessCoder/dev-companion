---
id: R-ANS-005
status: held
---

# R-ANS-005 — A console that answered "none" has answered

**A console that ran and answered "none" is an empty answer.**

The `unsupported` answer is reserved for a console that could not be reached or
that failed — this is
[R-ANS-001](ans-001-could-not-ask-never-looks-like-does-not-exist.md) in the other
direction, and a zero-hit answer dressed as a breakage sends the caller to fix
an installation instead of narrowing a query.

## From

The same feedback; the console's zero-match warning was read as an unreachable
installation (2026-07-29).

## Held by

- `LabelSearchTest::aConsoleThatFoundNothingIsAnAnswerRatherThanAFailure`
- `LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered`
