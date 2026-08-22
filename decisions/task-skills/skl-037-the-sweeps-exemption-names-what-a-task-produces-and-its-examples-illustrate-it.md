---
id: D-SKL-037
title: "The sweep's exemption names what a task produces"
date: 2026-08-14
status: open
coveredBy:
  - SkillTest::theDeprecationSweepIsSkippedOnlyWhereTheChangeTouchesNoTypo3Api
---

# D-SKL-037 — The sweep's exemption names what a task produces

**The deprecation sweep's exemption is stated as the property it rests on — a
task that produces no change — and reviewing a patch is inside it.**

It was written as three examples on 2026-08-08, for the read-only task shape
that had just fallen through the step. The next shape to arrive was not among
the three and fell through it again.

## Evidence

- **The sighting.** `feedback/2026-08-11-055337`: a review of Gerrit change
  94686 in `/home/benji/projects/typo3-cms`, one file in EXT:backend. The sweep
  was not run, and the report did not say so either. The session quotes both
  paragraphs of step 5 and reads them against each other.
- **The file it describes is today's.** Read on 2026-08-14: step 5 is unchanged
  since `4f3510fa` (2026-08-08). `9fef495a` is the only commit to
  `skills/base.md` since, and it rewrote step 3.
- **The same shape three days earlier.** `4f3510fa`'s own message: step 5's
  "skip condition is written for work that changes code", and "a read-only
  triage of one issue matches neither side of that". What it wrote was a triage,
  a reproduction and a review of a report — `D-AUD-009`,
  `feedback/2026-08-07-233512`.
- **What an enumeration does to the shape it does not name.** The third example
  is a review of a report, and this session was reviewing a patch. It read the
  distance between the two as deliberate: "A review of a *patch* is not in that
  list."
- **The two paragraphs disagree on this case.** The first exempts on what the
  task produces. The second says which side a change falls on is read off the
  files it touches and never off the task it started as. A patch review writes
  nothing and reads PHP that calls the core, so it satisfies one test and fails
  the other.
- **The review-shaped question already has an owner.**
  `skills/typo3-core-patch-review/SKILL.md` enumerates what the diff removes or
  renames and asks `typo3_changelog_lookup` for precedent, bounded by `type` and
  `version` with no query. That is the second of the feedback's two suggestions,
  in the skill since before the session ran.
- **What the sweep would have cost instead.** One call per declared major per
  tag, about identifiers a three-hunk diff neither calls nor removes.
- **Nothing asks a report to name a step it did not reach.** `skills/base.md`
  asks it of step 2 alone — a step passed over in silence cannot be told from
  one that was dropped — and the review checklist's surfaces are surfaces of the
  patch rather than steps of the order.
- **`D-SKL-034` was committed 52 minutes after this feedback arrived.** Its
  third **Assumed** says no session has been observed skipping the sweep on a
  change that touches TYPO3 API. The report was on the board at 05:53:37+00:00
  on 2026-08-11 and the commit is 08:45:16+02:00 the same day.

## Decided

- **Step 4 of the ladder, on the exemption's wording.** The rule was delivered,
  read and quoted, and did not take: two readings were defensible, and the
  session took the one written as a property over the one written as a list.
- **The exemption states the property, and the examples stay examples.** What it
  is about is what the task produces, which is what its own reason already says
  — a task that writes nothing is not going to call anything.
- **It ends where the workflow produces a change.** A review asked to change the
  patch invokes `typo3-core-patch-development`, which carries the base again and
  reaches step 5 holding files.
- **A report names the step it did not reach**, which is what step 2 already
  asks of itself and the half of this the session reports against itself.
- **Rejected: taking the condition off the sweep**, which `D-SKL-034`'s third
  **Wrong if** prescribes. That was written for a session with no condition to
  reach for; this one had two and they disagreed. Unconditional, the sweep runs
  on every triage, reproduction and review at one call per declared major per
  tag.
- **Rejected: a review-shaped sweep in `typo3-core-patch-review`.** The skill
  asks that question already, and a second copy of it is one that cannot be
  corrected.
- **The wording is not written here.** `skills/base.md` is published into
  somebody else's project, and `R-SKL-005` and `SkillTest` carry the same
  sentences.

## Assumed

- That a session can answer whether its task produces a change while it is at
  step 5. Both sightings answered it, and both sat in workflows their own skill
  names as read-only.
- That naming the property is what stops the next shape falling through. The
  examples are what failed twice; nothing has measured a property-first wording.
- That the other workflows ending in a report want the same reading.
  `typo3-core-issue-triage` and `typo3-extension-conformance` were not read for
  this.

## Wrong if

- A session whose task does produce a change skips the sweep and cites the
  property. Then the enumeration was holding the boundary and the examples were
  the safer half.
- A review is asked to finish the patch, carries on under the review skill
  rather than handing over, and a deprecation the sweep would have returned
  lands in the amend. Then the exemption rests on a handover that does not hold,
  and `typo3-core-patch-review` already records one session making that
  crossing.
- A review names the step it did not reach and a reader still cannot tell it
  from one that walked past it. Then the naming is not what the report owed.
