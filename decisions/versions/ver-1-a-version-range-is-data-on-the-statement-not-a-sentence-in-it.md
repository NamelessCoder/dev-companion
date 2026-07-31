---
id: D-VER-1
date: 2026-07-29
status: standing
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

- **Decided:** bind the statement, not the hint, and bind it with `since` and
  `until` fields rather than words in the sentence. A subsystem does not change
  wholesale — one sentence in it does, and the other six are unaffected — so
  per-hint versions would duplicate what did not change.
- **Decided:** a bare string stays a valid statement and means "holds on every
  covered version". The two hundred existing bullets are exactly that, so
  nothing had to be rewritten to introduce the model.
- **Assumed:** majors are granularity enough. `13.4` and `13.3` do differ, but
  the covered lines are one release line per major, and a range that cannot
  express a minor is better than a range nobody maintains.
- **Assumed:** filtering is better than qualifying once a version is known. A
  statement that does not hold is left out rather than shown with a warning,
  because an answer the caller has to filter is an answer they will not filter.
- **Wrong if:** a statement is true on 12.4 and 14 but not on 13 — the range
  cannot say that, and it would have to become two statements.
