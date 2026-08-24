---
id: D-SKL-074
title: 'A skipped step is named where the report is written'
date: 2026-08-24
status: open
---

# D-SKL-074 — A skipped step is named where the report is written

**The obligation to name a step that was not reached belongs where a report is
written, rather than inside each step it is about.**

It is stated twice today, both times inside the step it exempts, and three
sessions from three task shapes took the exemption and reported nothing.

## Evidence

- **The sighting.** `feedback/2026-08-24-110949`: a core patch review of a local
  commit in EXT:impexp in `/home/benji/projects/typo3-cms`, one modified class
  and two new test files. It took step 5's exemption correctly — a review
  produces no change — and did not name the step in its report. Its own reading
  is the position: the sentence is the last of nine paragraphs, after two other
  conditions, and it is the only instruction of that step that has to survive
  into a document written half an hour later.
- **The file it describes is today's.** `skills/base.md` is unchanged since
  `04ffc96d` (2026-08-21), which rewrote what each runtime lookup adds; step 5's
  closing sentence is `7f5657d9` (2026-08-14), the commit
  [`D-SKL-037`](skl-037-the-sweeps-exemption-names-what-a-task-produces.md)
  wrote. `skills/typo3-core-patch-review/SKILL.md` gained two paragraphs after
  the report was filed at 11:09:49+00:00 — `ea48a398` at 16:19 and `db92a9ea` at
  17:22 local — and neither is in its *Report* section.
- **Two earlier sightings, from another task shape and another directory.** Both
  are `/home/benji/projects/blog` on 2026-08-18.
  `feedback/archive/2026-08-18-070611` reports the obligation "stated twice and
  complied with neither" on a DDEV boot and reads the failure as placement
  rather than wording; `feedback/archive/2026-08-18-074327` reports steps 2, 4
  and 5 skipped on a task that changed code, with nothing naming the skips.
  Three sessions, three task shapes — a boot, a change, a patch review.
- **Both statements sit inside a step.** Step 2 says it of itself — a step
  passed over in silence cannot be told from one that was dropped — and step 5
  closes with it. Nothing at the end of the order says it, and no task skill's
  report section does: `typo3-core-patch-review` closes on the checklist's
  surfaces marked assessed, unassessed or not applicable, which are surfaces of
  the patch rather than steps of the order.
- **What holds it today is blind to where it stands.**
  `SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched` asserts the
  sentence is somewhere in `skills/base.md`, and `R-SKL-005` carries it inside
  its paragraph about the sweep. A move leaves both green.
- **The exemption itself held.** The session read the property, applied it to a
  shape the examples do not name and got the right answer, which is
  `D-SKL-037`'s first **Assumed** met on a third shape. What failed is the
  reporting half of the same entry.
- **The corpus was probed.** `bin/cli hints:probe` on the feedback's own subject
  matched nothing and returned 102 candidates as the index, which is the
  expected answer: the subject is this repository's own skill file rather than
  TYPO3.

## Decided

- **Step 4 of the ladder, on where the sentence sits rather than on what it
  says.** It was delivered into the active skill, read whole and understood —
  the session quotes both halves of the exemption — and the ladder names "buried
  below the part that answers the common case" as step 4's own evidence.
- **Queued rather than closed on the spot.** `skills/base.md` is published into
  somebody else's project as `references/base.md`, where a wrong sentence is not
  corrected by the next release of this server, and `R-SKL-005` and `SkillTest`
  carry the same wording.
- **It stays written once, in the base.** `R-SKL-005` already says a skill
  states what it adds to the order and never a second copy of it, and the place
  the caller is when the exemption is taken is the end of the order rather than
  the middle of one step.
- **Rejected: the feedback's own suggestion**, a clause in each task skill's
  *Report* section. Every published skill carries the base, and each copy is one
  that cannot be corrected — which is why `D-SKL-037` rejected a review-shaped
  sweep in the review skill.
- **Rejected: leaving it to the gate `D-SKL-049` weighs.** That entry recommends
  waiting because a fourth intervention against three unmet corrections could
  not be attributed afterwards; this is one obligation already written moved to
  where it is discharged, which is the kind of correction the same entry
  credits, and its 2026-08-24 note carves
  [`D-SKL-072`](skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md)
  out of the same corpus on that ground.
- **The priority is `normal`**, set by three sessions from three task shapes,
  and by a change that is one paragraph in one file.
- **The wording is not written here**, and neither is what becomes of the two
  statements now standing in steps 2 and 5. Both are the todo's first question.

## Assumed

- That a session writing a report reads the end of the order it started from.
  All three sightings say the report is written last and far from the step, and
  none of them measures what it reread.
- That saying it once where the report is written beats saying it twice where
  the work is done. It is said twice today and was complied with neither time.
- That the three shapes report the same failure. Each names the position rather
  than the sentence, and none disputes what the sentence asks.

## Wrong if

- A session with the obligation at the end of the order still closes a report
  without naming the step it skipped. The position was then not the lever, and
  what is left is the gate `D-SKL-049` weighs.
- A session reads the exemption, stops at the step and never meets the closing
  line. Then stating it inside the step is what reached the shapes that did
  comply, and the move traded a near statement for a far one.
- A session names the step and a reader still cannot tell the report from one
  that walked past it. That is `D-SKL-037`'s third **Wrong if** arriving here,
  and what was owed is the naming rather than the placement.
- The next sighting comes from a session that read its skill's *Report* section
  and not the base's order. Then the obligation belonged in the skill after all,
  and "written once" is what it cost.
