---
id: R-SKL-011
status: held
restsOn: [D-SKL-007]
---

# R-SKL-011 — A review reports what it dropped and what dropped it

**A candidate the review raised and let go is reported with the evidence that
let it go, and it is let go only against something that concretely disproves
it.**

A dismissal nobody records leaves the same trace as a surface nobody opened, so
the report cannot tell the reader which of the two happened. Raising a candidate
costs a reading; dropping one costs the author a finding and announces nothing,
which is why the two directions are not held to the same bar. A candidate that
can be neither established nor disproved is reported as open, with the reading
that would settle it named beside it.

Two dismissals carry their own line because they are the ones that go wrong: one
made on the strength of a comment, a docblock or an annotation without reading
the implementation it describes, and one made because a path looks unlikely
rather than because it is impossible.

## From

The conformance checklist stated the bar for a security verdict alone — it "has
to be disproved before it can be dismissed" — and the reason under it is about
who pays for a wrong dismissal rather than about security. A published
multi-pass review pipeline for a large C project, read on 2026-08-03, carries
the disproved candidates alongside the raised ones through every pass and lets a
candidate be discarded only against concrete proof; `D-SKL-007` records what was
taken from it and what was rejected.

## Held by

- `SkillTest::aReviewReportsWhatItDroppedAndWhatDroppedIt`
