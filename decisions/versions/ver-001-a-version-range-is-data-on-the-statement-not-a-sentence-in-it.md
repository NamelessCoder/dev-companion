---
id: D-VER-001
title: A version range is data on the statement, not a sentence in it
date: 2026-07-29
status: confirmed
---

# D-VER-001 — A version range is data on the statement, not a sentence in it

**A statement is bound by `since` and `until` fields on itself rather than by
version words in its sentence, and a bare string holds on every covered
version.**

The knowledge base covers four TYPO3 lines — 12.4, 13.4, 14.3 and main — and
until now every statement was written to hold on all of them. That was the rule
in `AGENTS.md`: no version numbers, and where a fact is branch-specific, write
the rule that holds everywhere. It bought branch-neutrality by leaving out
everything that is not.

## Decided

- Bind the statement, not the hint, and bind it with `since` and `until` fields
  rather than words in the sentence. A subsystem does not change wholesale — one
  sentence in it does, and the other six are unaffected — so per-hint versions
  would duplicate what did not change.
- A bare string stays a valid statement and means "holds on every covered
  version". The two hundred existing bullets are exactly that, so nothing had to
  be rewritten to introduce the model.

## Assumed

- Majors are granularity enough. `13.4` and `13.3` do differ, but the covered
  lines are one release line per major, and a range that cannot express a minor
  is better than a range nobody maintains.
- Filtering is better than qualifying once a version is known. A statement that
  does not hold is left out rather than shown with a warning, because an answer
  the caller has to filter is an answer they will not filter.

## Wrong if

- A statement is true on 12.4 and 14 but not on 13 — the range cannot say that,
  and it would have to become two statements.

## Confirmed on 2026-08-02

The **Wrong if** has not happened: every bound statement was read against the
four checkouts and each truth set is contiguous, which `bin/cli catalog:check`
says mechanically for the derived half and no check can say for the judged one.
What the reading found is the failure a range does express rather than a hole —
`extension-files` bound a fallback with no upper bound while the statement
beside it said it was gone, and that one gained its `until`.
