---
id: D-VER-1
date: 2026-07-29
status: confirmed
---

# D-VER-1 — A version range is data on the statement, not a sentence in it

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
  rather than words in the sentence. A subsystem does not change wholesale —
  one sentence in it does, and the other six are unaffected — so per-hint
  versions would duplicate what did not change.
- A bare string stays a valid statement and means "holds on every covered
  version". The two hundred existing bullets are exactly that, so nothing had
  to be rewritten to introduce the model.

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

The **Wrong if** has not happened. All 110 bound statements were read against
the four checkouts — 105 hints across 31 topics and 5 test suites — and every
truth set is contiguous. Each predecessor and successor pair partitions the
covered majors, leaving none of them out. The derived half says the same
mechanically: `bin/cli catalog:check` recomputes each catalog range from the
checkouts and names any entry it cannot express, and it reports none across 25
components, 38 system extensions and 7 references. Nothing does that for the
judged half and nothing can, because a hole is visible only to a reader holding
the sentence against both sides — which is what this line stands in for. What
the read did find is the failure a range does express: `extension-files` bound
the `ext_emconf.php` fallback `since: 14` with no upper bound, while the
statement beside it says the fallback is gone `since: 15`. That one gained its
`until` in a commit of its own, which is the difference — a missing bound is
corrected, a hole would have needed a second statement.
