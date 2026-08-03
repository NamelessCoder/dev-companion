---
id: R-SKL-012
status: held
restsOn: [D-SKL-007]
---

# R-SKL-012 — A finding is attributed to the change under review

**Every finding says whether this patch introduced it, and a patch that is one
of a set is read against the state at the end of the set.**

A finding about a line the diff only moved past sends the author to repair
something they did not change, and nothing in the report says it was not meant
to. What the patch did not introduce stays in the review where it blocks
submission on its own and goes to the issue tracker otherwise. What a later
patch in the same set removes is not a defect of the set, and establishing that
is a reading of that patch rather than of what a message promises about it.

Reachability is the other half of the same question and moves in one direction
only: what a diff shows about who reaches a path may raise a rank and never
lower one, because reachability is what a diff establishes worst.

## From

A published multi-pass review pipeline for a large C project, read on
2026-08-03, flags every finding as introduced or pre-existing, requires the
reported wording to state it, keeps pre-existing findings out of the report
below its top two severities, and runs a benchmark of its own for that one
property. `D-SKL-007` records what was taken from it and what was rejected.

## Held by

- `SkillTest::aFindingSaysWhetherThePatchIntroducedIt`
