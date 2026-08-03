---
id: D-SKL-007
date: 2026-08-03
status: open
---

# D-SKL-007 — Every disposition a review makes carries its evidence

**A review disposes of a thing in three ways — it reports it, it drops it, or it
declares it clean — and all three carry what backs them.**

Only the first of the three did. A dismissal nobody records reads like a surface
nobody opened, and a surface reported as assessed with nothing under it is the
cheapest sentence in a review to write.

## Evidence

- A published multi-pass review pipeline for a large C project, read on
  2026-08-03, carries two lists through every pass rather than one: what the
  pass raised, and what it raised and disproved. Its consolidation pass is told
  that both lists are untrusted claims and that neither side is assumed correct;
  its verification pass may discard a candidate only against concrete proof, and
  where no such proof is found the candidate is reported.
- The same pipeline marks every finding as introduced or pre-existing, requires
  the reported wording to state it, and keeps pre-existing findings out of the
  report below its top two severities. It runs a benchmark of its own for that
  one property.
- Its severity guidance forbids lowering a rank for a path believed to be
  unreachable, on the grounds that reachability is the thing a diff establishes
  worst, while raising a rank for reachable input stays allowed.
- The conformance checklist here already states the asymmetric bar for one
  subject — a security verdict "has to be disproved before it can be dismissed"
  — and `SkillTest::aSecurityFindingIsNotEstablishedUntilItsSinkIs` holds it.
  The REVIEW-02 run that earned it did not drop a candidate; it reported one
  whose sink escapes. The bar is written for the subject and the reason under it
  is not about the subject: what makes a dismissal expensive is that its cost
  falls on the reader rather than on the review.

- Every verification step in that pipeline's own guide ends in an emission
  rather than in a judgement — "Output: quote the call chain with locations",
  "Output: subsystem invariants checked, or none", "Output: production code, or
  test code with the severity adjusted". A step that produces nothing was not
  performed, and the shape of the instruction is what makes that readable.

## Decided

- The three dispositions are named as three in the checklist's opening, and the
  demand is stated once for all of them rather than per section.
- A surface reported as assessed names what was read to assess it. Where the
  reading did not happen the word is unassessed, which costs the same line and
  says something a reader can act on.
- The checklist gains **What a dropped candidate owes**: each candidate let go
  is named with the line that let it go, a candidate is dropped only against
  something that concretely disproves it, and one that can be neither
  established nor disproved is reported as open with the missing reading named.
- Two dismissals are called out by name, because they are the ones that go
  wrong: dropping on the strength of a comment, a docblock or an annotation
  without reading the implementation it describes, and dropping because a path
  looks unlikely rather than because it is impossible.
- The author's case is made against a finding before it is reported, and what
  survives is reported together with what it survived.
- A finding carries a fifth obligation — whether this patch introduced it. What
  it did not stays in the review where it blocks submission on its own and goes
  to the issue tracker otherwise.
- Reachability may raise a rank and never lower one.
- A patch that is one of a set is read against the state at the end of the set,
  by opening the later patch rather than by believing a message about it.
- The eleven-pass pipeline itself was rejected. What that shape buys is one lens
  at a time against a fixed schema, and what it costs is eleven readings of one
  diff; the checklist already enumerates the surfaces whole before the diff is
  read a second time, and the failure the surfaces were written for was a
  surface going unreported rather than two lenses interfering.

## Assumed

- That a reviewer who writes a dismissal down also holds it to the bar, rather
  than filling the section with candidates nobody seriously entertained.
- That the pre-existing obligation is used to attribute a finding rather than to
  move an inconvenient one out of the report.
- That a core patch set is small enough that reading the later patches to settle
  a finding is affordable. The pipeline this came from reviews series of a size
  that made it worth automating.

## Wrong if

- A recorded review's dropped-candidate section carries dismissals with no line
  of code under them. That is the section becoming a place to look thorough, and
  it is visible in the report itself.
- A review reports fewer real findings after this than before, because the
  author's-case step is where a correct finding gets talked away. The forward
  runs in `scenarios/runs/` are where it would show.
- The pre-existing obligation appears on findings that the diff plainly
  introduced, which would mean it is being read as a way to soften a report
  rather than as a question about the diff.
- Surfaces start being reported as unassessed at a rate that says the word is
  being used to avoid naming a reading rather than to report a missing one. The
  demand would then have bought a cheaper evasion than the one it closed.

## Covered by

- `SkillTest::aReviewReportsWhatItDroppedAndWhatDroppedIt`
- `SkillTest::aFindingSaysWhetherThePatchIntroducedIt`
- `SkillTest::aSurfaceReportedAsAssessedNamesWhatWasRead`
