---
id: D-GUI-009
title: 'A stated change type keeps the skeleton'
date: 2026-08-04
status: open
coveredBy:
  - HintsTest::aReviewThatStatesThePatchTypeNamesWhatItRemoves
  - HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist
  - HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief
  - ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore
---

# D-GUI-009 — A stated change type keeps the skeleton

**A stated `changeType` decides which skeleton a brief is composed into, and
what the task's words recognized is appended to it rather than dropped.**

`D-GUI-006` filtered the intents that write no file out of a call that stated a
type, on the reading that such a caller is authoring the change and reviewing it
in one sentence. The feedback that produced the review shape is the instance
against it: it stated the type of the patch under review.

## Evidence

- The reported call, re-run on 2026-08-04 in this repository: task "review the
  core patch replacing GD-based error thumbnails with a static SVG placeholder",
  `changeType` cleanup. It comes back "Recognized as: Review or audit", owned by
  `typo3-core-patch-review`, carrying the enumeration, the matcher entry, the
  changelog file, the `[!!!]` prefix and the two `.rst` checks — beside "Keep
  the patch focused on the stated task", "Keep the cleanup mechanical" and the
  commit-message step. Before this change it was the patch checklist and the two
  Gerrit steps, naming no removal, which is what `D-GUI-004` recorded.
- The Gerrit steps did not come from the type filter. The `submission` intent
  matched on the bare needle `review`, which subsumes all four needles of
  `audit` — `review the`, `review this`, `review of`, `reviewing` — so every
  review call confirmed Patch submission whether or not a type was stated.
  `D-GUI-006` saw that needle and routed around it by naming the type `audit`
  rather than `review`.
- The two skills already draw the line the needle crossed.
  `typo3-core-patch-development` carries the way from an issue to a pushed
  patch, and its own description hands "reviewing a patch without writing it" to
  `typo3-core-patch-review`.
- The push survives the needle. `knowledge/documents/typo3-gerrit-workflow.md`
  holds it, `typo3_rule_lookup` reaches it through the intent's `rulesQuery`
  "gerrit push patch set", and the intent itself is still matched by `gerrit`,
  `push`, `patch set`, `submit` and `backport`.

## Decided

- Both halves, rather than either. The skeleton says what kind of answer this is
  and only one of them can be right; the intents say what the answer is about
  and nothing forces those to be one. So the type keeps the first and the words
  keep the second.
- `review` leaves `submission`'s needles. The intent is the workflow for putting
  a patch up and for amending a patch set, and reading one is not that.
- `D-GUI-006` is not re-opened. A stated type still decides the skeleton, which
  is the whole of what it decided; what changes is that filtering the intent out
  was never needed to hold it.
- Rejected: letting the review words win the skeleton. It costs the caller who
  really is authoring the focused diff, the test coverage and the commit
  message, and it flips the boot half of "fix the post-start hook so the import
  runs" the same way.
- Rejected: leaving it, on the reading that a core review reaches the skill and
  the routing entry rather than this tool. The call the feedback reported is one
  a caller can still make, and what came back had no removal surface in it.
- `ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore` keeps its
  property on `push`. `D-SCO-002` is about an ordinary word being read as the
  core's process, and `push` is that word without being the wrong one.

## Assumed

- That a brief carrying both halves is read as one answer. A reviewer meets
  "Keep the patch focused on the stated task" and reads it as a criterion, which
  is what the item says from the other side rather than a second instruction.
- That `push` and `submit` carry the ambiguity `D-SCO-002` is about on their
  own. Both are as ordinary in extension maintenance as in the core's process,
  which is why the intent is offered under its condition rather than stated.

## Wrong if

- A session reports the brief for a stated type as two answers at once: a
  reviewer told to add test coverage, or an author told to enumerate what the
  diff removes. The lever is then the skeleton after all, and which caller it
  belongs to has to be read off something other than the type.
- A caller who wants to push a patch reaches no submission steps because the
  task said "put it up for review" and nothing else. `push` and `submit` are
  what carry it, and the needle would have to come back as a `matchWeak` one.
- A review of something that is not a change gets the removal surface anyway and
  reads it as noise. The item is worded "Where the review is of a change", so
  that would be the wording rather than the shape.
