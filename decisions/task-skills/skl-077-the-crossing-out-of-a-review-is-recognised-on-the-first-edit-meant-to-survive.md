---
id: D-SKL-077
title: 'The crossing out of a review is recognised on the first edit meant to survive'
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework
---

# D-SKL-077 — The crossing out of a review is recognised on the first edit meant to survive

**The crossing out of a core review names the act that begins the rework — the
first edit meant to survive — beside the sentence that asks for it.**

The section is written to be recognised in what the reader says, and a session
holding it reports noticing its own next act instead.

## Evidence

- **The sighting.**
  [`feedback/archive/2026-08-24-183420`](../../feedback/archive/2026-08-24-183420-the-review-skill-predicts-the-crossing-into.md),
  `/home/benji/projects/typo3-cms`: Gerrit change 91127 reviewed under
  `typo3-core-patch-review`. The session quotes the crossing section, states
  that it read it, and then removed three `markTestSkipped` calls from
  `SiteRequestTest.php`, added six body assertions and a fixture page, ran five
  checks and drafted an amended commit message — never invoking
  `typo3-core-patch-development`.
- **What the session was tracking instead is the register.** It names the
  question it asked at that moment — "does this belong in this commit" — and
  says it answered that one carefully. The section asks it to notice a property
  of the reader's sentence; it was noticing a property of its own next act.
- **The section is today's.** Read on 2026-08-25: `db92a9ea`, 2026-08-24
  17:22:40 +0200, is the newest commit to
  [`typo3-core-patch-review`](../../skills/typo3-core-patch-review/SKILL.md),
  and the feedback was recorded at 18:34:20+00:00 the same day. Whether the copy
  published into that project carried `db92a9ea` cannot be read from here.
- **The trigger sentence names no patch and no verb the section lists.** "wir
  sollten sie hier wieder mit aufnehmen" — we should take them back in here —
  about three tests the review had just reported as skipped for a stale reason.
  The section enumerates "finish it", "fix it", "amend it", "write the test".
- **The one sighting where it fired names one of them.**
  [`feedback/2026-08-24-225243`](../../feedback/archive/2026-08-24-225243-both-patch-skills-handle-a-linear-task-this-one.md),
  same directory and same pair of skills: "kannst du ihn fertigstellen das er
  backgeportet werden kann?", and the session reports invoking the patch skill
  rather than carrying on. That is "finish it", in the reader's own words.
- **The first sighting is the one the section is written from.**
  [`feedback/archive/2026-08-07-132559`](../../feedback/archive/2026-08-07-132559-the-review-skill-has-no-marker-for-the-point.md):
  `ColumnMap.php`, a fixture column, a functional test, seven suites and an
  amend, all under review rules. Its account is that the reader picked a scope
  and asked for the change — a sentence the enumeration would reach, from before
  the enumeration existed.
- **The other direction is already answered, and it is a reading of the same
  sentence.**
  [`feedback/archive/2026-08-11-055317`](../../feedback/archive/2026-08-11-055317-the-review-skill-s-handover-rule-fired-on-a.md)
  crossed on "I think the tests should prove it", and the paragraph excluding a
  remark about a finding's weight was written from it —
  [`D-SKL-022`](skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md).
  So the sentence is now read for two properties, and both failures on record
  are readings of it.
- **What the crossing carries could not arrive.**
  [`D-SKL-072`](skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md)'s
  three calls hang off the crossing, so the deprecation sweep the session names
  as its concrete cost was unreachable whether or not the published copy carried
  them. It states the exemption in that clause's own terms and did not run the
  sweep.
- **The sweep was owed, and [`base.md`](../../skills/base.md) already says so.**
  Step 5 skips only where the change touches no TYPO3 API, and reads which side
  a change falls on off the files it touches "and never off the task it started
  as". A functional test that renders the frontend is on the ordinary side, and
  the session's own "probably nothing for a test file" is the reasoning that
  sentence is written against.
- **The act was observable, and the probe is what it is not.** The review skill
  permits a scratch probe that writes files, and says the boundary is that the
  patch under review is not edited and the tree is put back. That sentence
  stands in the verification section, and the feedback reports the crossing as
  having nothing that tells a probe from rework.
- **The strength is the same boundary from the other side.** The same session
  reports the checklist's dropped-candidate rule and the severity rubric
  changing its output, and both are read while a finding is being formed — an
  act it was already performing —
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
- **The corpus.** `bin/cli feedback:list` on 2026-08-25 reports 49 open, 45 of
  them from `/home/benji/projects/typo3-cms`.
  [`feedback/2026-08-25-114802`](../../feedback/2026-08-25-114802-no-skill-activated-typo3-core-patch-development.md)
  is the same skill not firing from the client's own listing, on a session where
  the patch emerged from a diagnosis and no sentence announced it.

## Decided

- **Step 4 of the ladder, on the trigger rather than on the paragraph.** The
  rule was delivered, read and quoted, and it did not take. What is written as a
  recommendation to recognise something is the register that failed, not the
  length: the section is already the most explicit in either skill, and the
  feedback says so.
- **The crossing names the act beside the sentence.** The first edit to a file
  meant to survive is what asks the question, because it is observable from
  inside the session and a sentence to recognise is not.
- **The probe is named there as what the act is not** — restored and leaving no
  diff, against a change meant to survive. The discriminator is in the skill
  already, so the crossing names where it stands rather than carrying a second
  copy of it.
- **The enumerated phrases stay.** One sighting has them firing on
  "fertigstellen", and removing them would trade a trigger that works on the
  sentences it names for one nothing has run.
- **Queued rather than closed on the spot.** The change is a clause in a
  published skill, which [judging.rst](../../documentation/records/judging.rst)
  reviews rather than improvises. Nothing about TYPO3 has to be looked up for
  it.
- **`normal` rather than `low`.** Two sessions seventeen days apart report this
  crossing failing, the correction that landed between them was written for the
  other direction, and the lever is one clause.
- **Trimmed: the ask to state the sweep's obligation in
  `typo3-core-patch-development`'s entry is refused and comes off the
  feedback.** `base.md` step 5 answers it in the property a change is read by,
  that skill's own step 1 routes to `base.md`, and a second copy in one skill is
  the one that goes stale — `R-GUI-006`, and `D-SKL-072` rejected the same
  shape.

## Assumed

- That a session can tell a probe from a change at the moment of its first edit.
  This one could — it was drafting an amended commit message — and no session
  has reported being unable to.
- That an act-shaped check fires where a sentence-shaped one did not. The
  feedback asserts it about itself, and nothing has measured a session holding
  one.
- That the false-positive cost stays what `D-SKL-022` measured, one turn under
  the wrong skill's rules. A check attached to every first edit reaches every
  probe, and the probe clause is the whole of what bounds it.

## Wrong if

- A session reports the check firing on a scratch probe and backing out. The
  discriminator does not separate the two at the moment of the act, and what is
  left is a trigger on the sentence alone.
- A session edits a file meant to survive with the act-shaped clause in front of
  it and does not cross. The crossing cannot be held in this skill's prose, and
  [`D-SKL-049`](skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)'s
  gate is what is left.
- A session crosses on the act and makes none of the three calls the crossing
  names. That is `D-SKL-072`'s own first **Wrong if**, which nothing has been
  able to reach while the trigger was the thing failing.
