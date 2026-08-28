---
id: D-GUI-006
title: A task that changes nothing is a change type of its own
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist
---

# D-GUI-006 — A task that changes nothing is a change type of its own

**`typo3_task_guide` answers a task that changes nothing from a `changeType`
value and an intent of its own, and a stated type overrules the task's words.**

The enum offered six kinds of change and `unknown`, and all eleven intents were
kinds of change as well, so a review fell through to the patch checklist.

## Evidence

- The re-run recorded in `R-GUI-006` on 2026-08-02:
  `task="review the TYPO3 project and site package"` with `changeType=unknown`
  matches no intent, and what comes back is "Keep the patch focused on the
  stated task", "Add or update the narrowest useful test coverage" and "Write
  the commit message with typo3_commit_message_guide".
- `feedback/2026-07-31-194826`, from a model that had loaded
  `typo3-extension-conformance` for a conformance review of a site package in
  `site-new`: the brief restated the skill's own checklist and added little for
  a pure audit, and the session said it would not call the tool again after that
  skill.

## Decided

- A value on the enum **and** an intent, because the change type is fed to the
  intent matcher: the caller who classifies the work and the caller who
  describes it reach the same brief. That is the shape `fb35b61` gave the
  deprecation type, and the reason the change-type block stays empty.
- The word is `audit` rather than `review`. `review` is a strong needle of the
  `submission` intent, so a caller stating it would have been answered with the
  Gerrit push steps — the failure this entry is about, in a second shape.
- The checklist skeleton is what changes, and not only what is appended to it.
  An intent can add items and the three the requirement names are in the
  skeleton, so no intent could ever have satisfied it.
- The commit-message step leaves the follow-up calls together with the
  checklist. One list naming a step the other dropped is one answer disagreeing
  with itself.
- Rejected: acknowledging the skill and routing to it, which is what the
  feedback suggested. The tool is reachable without a skill and cannot see what
  the client loaded, so that half is the question the two `D-SKL-001` waiting
  cards carry and nothing here answers it.

## Assumed

- The words that select the intent — `audit`, `conformance`, `code review`,
  `review the`, `review this`, `review of`, `reviewing` — name the work rather
  than its subject. None of them is a `matchWeak` needle, so each one flips the
  shape of the whole brief on its own.
- A caller who states a change type and describes a review is authoring the
  change and reviewing it in one sentence, which is why the stated type wins.

## Wrong if

- A brief comes back in the review shape for work that does change something —
  "reviewing the failing test and fixing it" is the form it would take. The
  answer is then to move that needle to `matchWeak` with a condition, rather
  than to give up the shape.
- A session that has the conformance skill loaded reports the two duplicating
  each other again. What this hands over is meant to be the difference from a
  patch brief rather than a second audit workflow, and if it reads as one, the
  question is `D-SKL-001`'s.

## Since then

The statement holds and the enum was one value short of it: a task that changes
nothing is not only a review. A session booting a Composer project from a fresh
clone had no value to state, and `unknown` gave it this skeleton — the patch
checklist, reached from the other side. `D-GUI-008` is where that lands, taking
the mechanism this entry established and keeping the skeletons apart, because
"report what the review did not reach" is a step a boot does not take.

The second **Assumed** met its instance and is half true: a caller who states a
type and describes a review can be a reviewer naming the type of the patch under
review. `D-GUI-009` follows from that.
