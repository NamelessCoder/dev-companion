---
id: R-SKL-013
title: 'A surface reported as assessed names what was read'
status: held
restsOn: [D-SKL-007]
heldBy:
  - SkillTest::aSurfaceReportedAsAssessedNamesWhatWasRead
---

# R-SKL-013 — A surface reported as assessed names what was read

**A surface reported as assessed names what was read to assess it — the file
that was opened, the call site that was followed, the command that was run.**

A clean verdict carries its evidence the way a finding carries its rule. It is
the third of the three ways a review disposes of a thing, and it was the one
that certified itself. Reporting and dropping both cost a reading somebody can
check; assessed costs one word, and a surface somebody glanced at reads exactly
like one somebody worked through. Where the reading did not happen the word is
unassessed, which costs the same line and says something a reader can act on.

## From

The second recorded `REVIEW-03` run met all five criteria and reported the
security surface as assessed. Its own notes file the gap: the strongest finding
of the run before it is absent, because four fields are written out of
`$be_user_data = $beUsersSubmission` without consulting the `$fieldList` guard
the generic loop applies, which is what makes the patch's new docblock overstate
what it achieves. The judgement names the shape — "a surface asserted clean
rather than left unassessed, which costs more than a gap because the coverage
table beside it is what a reader uses to decide what still needs looking at" —
and records it as the shape `REVIEW-02` was partial for and `REVIEW-01` produced
three times.

What this requirement buys is the honest word, not the reading. The same note
says neither the criteria nor the checklist asks for the deletions-and-writes
reading that would have caught the finding, and naming what was read does not
make that reading happen. It turns a wrong "assessed" into an "unassessed" a
reader can act on, which is what the judgement itself argues is the cheaper of
the two failures.
