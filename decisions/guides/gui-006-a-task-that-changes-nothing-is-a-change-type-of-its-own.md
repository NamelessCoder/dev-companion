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

The statement holds and the enum turned out to be one value short of it. A task
that changes nothing is not only a review: `feedback/2026-08-03-154508` booted a
Composer project from a fresh clone — start the environment, import a database
and files, build the assets, create a backend user — and had no value to state.
`audit` asks for a review brief, so `unknown` was the honest choice, and
`unknown` is this skeleton.

Re-run on 2026-08-03 in this repository, the reported call still comes back with
"Confirm the target TYPO3 core branch and issue context", "Keep the patch
focused on the stated task", "Add or update the narrowest useful test coverage"
and the commit-message step. Which is the same three items this entry was
decided on, reached from the other side: not a review that fell through, but
work that operates an installation and has no type at all.

[`D-GUI-008`](gui-008-operating-an-installation-is-a-change-type-of-its-own.md)
is where that lands. It takes the mechanism this entry established — a value on
the enum and an intent of the same word, because the type is fed to the intent
matcher — and it keeps the skeletons apart rather than sharing the review's:
"report what the review did not reach" is a step a boot does not take either.

The second **Assumed** met its instance on 2026-08-04 and is now half true. A
caller who states a type and describes a review can also be a reviewer naming
the type of the patch under review, which is what `feedback/2026-08-01-115711`
was. What follows from that is
[`D-GUI-009`](gui-009-a-stated-change-type-keeps-the-skeleton-and-the-words-keep-their-surface.md):
the stated type still decides the skeleton, which is what this entry rests on,
and the intent it used to filter out is appended instead of dropped, because
nothing in a call tells the two callers apart.
