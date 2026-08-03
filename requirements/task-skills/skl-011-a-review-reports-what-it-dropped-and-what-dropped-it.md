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

It holds over both reviews this server owns — the core patch review and the
conformance audit — because what makes a dismissal expensive is the reader who
pays for it rather than the surface it was made on. What the audit adds is one
boundary: a subsystem the package does not ship is answered on the coverage list
as not applicable and is not a dropped candidate, so the list holds what was
entertained as a defect and then was not one.

## From

The second recorded `REVIEW-03` run did this once, unprompted, and it is the
only recorded instance: `typo3_commit_message_guide` returned two warnings that
are artifacts of its own rewrite, and the answer named them, said why they do
not hold, and discounted them rather than reporting them as findings. The
judgement calls that "the behaviour the corpus keeps asking for and rarely
records". Nothing in the skill asked for it, so nothing makes the next run do
it, and every dismissal that run made in silence is unreadable either way.

The conformance checklist stated the bar for a security verdict alone — it "has
to be disproved before it can be dismissed" — and the reason under it is about
who pays for a wrong dismissal rather than about security. `D-SKL-007` records
where the general form was read and what was rejected with it.

Whether an audit can carry the same demand was measured on the two recorded
conformance runs rather than argued from the size of the surface (2026-08-03).
Both write dismissals into the answer already, four each, and neither was asked
for one: `REVIEW-01` discounts the Content Security Policy and RTE-sanitizer
settings as core defaults rather than project regressions, places the consent
checkbox outside what this server answers, and clears the dotless translation
domain against `LocalizationUtility:66`; `REVIEW-02` leaves the `ext_tables.sql`
delta-only rule unraised for want of a schema-analyzer comparison, keeps the
`event.listener` and `console.command` YAML tags and the
`getMajorVersion() < 13` branch as required by the declared range, and reads
`provider` and `source` among the returned icon identifiers as an artifact of
the tool. Four is a section a reader sits through, and the first of
`REVIEW-02`'s is already the open case written in the shape this asks for.

## Held by

- `SkillTest::aReviewReportsWhatItDroppedAndWhatDroppedIt`
