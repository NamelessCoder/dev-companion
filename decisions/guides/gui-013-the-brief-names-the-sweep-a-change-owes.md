---
id: D-GUI-013
title: The brief names the sweep a change owes
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefForAChangeNamesTheDeprecationSweepItOwes
---

# D-GUI-013 — The brief names the sweep a change owes

**`typo3_task_guide` names the deprecation sweep as what a task that writes PHP
still owes, in the brief itself rather than only in `skills/base.md` step 5.**

The order is read once, at the start, and the clause that says to read it again
sits inside the step the reader has just exempted itself from.

## Evidence

- `feedback/2026-08-18-074327` is a 404 in a blog installation that became three
  commits touching `Classes/ViewHelpers`, `Classes/Service`,
  `Classes/Controller` and `Configuration/DataHandler`. It reports steps 2, 4
  and 5 of the order skipped, and names the cause as the order being walked once
  against the task as it was first phrased.
- Re-run on 2026-08-18 in this repository, with the change type the session
  passed. The `operations` brief says "Pass changeType where the task does
  change something" and names the focused diff, the test coverage and the commit
  message as what it left out. The sweep is not among them.
- The same re-run with `changeType=bugfix` and those four paths. `nextTools`
  names `typo3_changelog_lookup` "for what 14 changed about this area" — no
  `type: deprecation`, no call per declared major, no `tag` bounding. So the
  brief a caller reaches after reclassifying does not carry the step either.
- `bin/cli hints:probe "deprecation sweep when a task turns into a code change"`
  matches nothing. The obligation is in [`base.md`](../../skills/base.md) and in
  no answer this server composes.
- The wording of step 5 is not what failed. The feedback quotes its re-entry
  clause back correctly — a review asked to make the change starts the order
  again — and that clause is the last paragraph of the step whose exemption the
  caller has already taken.
- Half of the report is answered as it stands. `omittedHints` came back `[]` and
  the brief printed `HINTS_COMPLETE` verbatim beside it, so the sentence step 4
  sends the caller to read was in the answer the session was holding.
- That misreading has a sighting already.
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  records a session on 2026-08-14 quoting `"omittedHints": []` and then asking
  for a machine-readable form of the sentence beside it.
- The corpus carries the cause rather than this one report.
  `feedback/2026-08-18-081159` reports skill selection made once against the
  opening framing, from another task shape in the same checkout, and the **Since
  then** of
  [`D-SKL-049`](../task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
  already reads three sightings of the shape across two directories.

## Decided

- **Step 2 of the ladder, delivery.** The rule exists, it is correct, and it is
  in no answer the caller holds at the moment the task changes shape.
- **Queued rather than closed on the spot.** The change is a sentence in
  `src/Tool/TaskGuide.php` and a clause in a published skill, which
  [judging.rst](../../documentation/records/judging.rst) reviews rather than
  improvises. Nothing about TYPO3 has to be looked up for it.
- **`normal` rather than `low`.** More than one session reports the cause, from
  two task shapes.
- **Refused: a second, machine-readable form of the "did the brief carry
  everything" signal.** `omittedHints` is that form and a required key of the
  declared schema. What is written instead is one clause in step 4 of `base.md`
  naming it, because that step points at the sentence and warns off `hints`
  while never naming the field two sessions have now read and doubted.
- **Rejected: restating step 5 in the brief.** The sweep is five paragraphs, and
  the brief points at the skill that carries the order wherever one owns the
  work. What the brief owes is the obligation and its axes — one call per
  declared major per tag — rather than the step's reasoning.

## Assumed

- That a caller re-reads the brief and not the skill file when the task changes.
  The feedback says that of itself, and it is why it names `typo3_task_guide` as
  the place the rule would have reached it.
- That the sweep transfers outside the core. `base.md` addresses it to the
  package rather than to the core repository, and every path in this report is
  an extension's.

## Wrong if

- A session with the obligation in its brief skips the sweep anyway. The
  placement was then not the lever, and what is left is the closing gate
  [`D-SKL-049`](../task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
  waits on.
- A session whose change touches no TYPO3 API reports the line as noise.
  `base.md` exempts that case on the files a change touches, and a brief cannot
  read them, so the obligation would be arriving where the step does not apply.
- A session names `omittedHints` as the answer to its own ask before the clause
  is written. The field was then readable as it stood, and the two recorded
  doubts were about something else.

## Since then

**2026-08-18.** Both placements are written. `TaskGuide::DEPRECATION_SWEEP`
stands in the checklist of every brief whose change type produces a change, at
the majors that brief was answered for, and the line above a brief that changes
nothing names the sweep beside the diff, the coverage and the commit message it
already left out. Step 4 of `skills/base.md` names `omittedHints` as its own
sentence in data. The entry stays open: each **Wrong if** is about what a
session does with a line it is now holding, and none of them has been measured.

**2026-08-24.** A session skipped the sweep on a core patch and reported it, and
the first **Wrong if** did not fire. `feedback/2026-08-24-100534` reviewed a
Gerrit change and was then asked to rework it, and it states that it never
re-ran `typo3_task_guide` with `changeType="bugfix"` — so the placement this
entry made was never in the brief it was holding, because there was no second
brief. What the report tests is the call rather than the line in it, and
[`D-SKL-072`](../task-skills/skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md)
is where that went. Both **Wrong if** here are still unmeasured.
