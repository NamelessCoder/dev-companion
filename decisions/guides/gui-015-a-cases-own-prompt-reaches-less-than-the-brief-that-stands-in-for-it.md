---
id: D-GUI-015
date: 2026-08-19
status: open
---

# D-GUI-015 — A case's own prompt reaches less than the brief that stands in for it

**Four contract cases are held by tests that feed a brief naming the answer, and
the prompt each case is actually written in reaches less than that brief does.**
So what those tests confirm is the vocabulary, not that the work arrives.

## Evidence

Measured on 2026-08-19 through `TaskIntents::detect()`,
`TaskIntents::confirmed()` and `typo3_task_guide`, with the prompt each case
carries and paths a session would plausibly stand in.

- `EXT-08` reaches nothing. Its prompt — a line added to the mail the core
  sends, without overriding the class — detects no intent, and the guide answers
  the events hint only where the caller already passes `Classes/EventListener/`.
  With no path, with `Classes/` and with `ext_localconf.php` it does not. The
  brief its proxy feeds is "Register an event listener for the PSR-14 event a
  package dispatches", which names the answer the case is about finding.
- `SKILL-11` reaches nothing. A security review asked for in the words a
  maintainer uses, with `Classes/` and a template path, names no skill — while
  the case is written about `typo3-extension-conformance` narrowing correctly.
- `SKILL-07` reaches the wrong one. It detects `backend-module`, `backend-ui`
  and `audit`, confirms `backend-module` and `audit`, and the guide names
  `typo3-extension-conformance`. The word doing that is `reviewing`, inside the
  module's own subject — "a backend module for reviewing imported records" — and
  the documentation half the case exists for reaches no documentation intent at
  all.
- `SITE-09` detects `site-setting` and confirms nothing, so the guide names no
  skill. The proxy asserts on `confirmed()`, which the case's own prompt does
  not reach.

## Decided

- **The four cases keep their `not guarded` line and gain the measurement.** The
  reading is what the recurring todo asks for, and a case saying what was
  measured against it is what the next reading is held to.
- **The repair is queued rather than made here.** Each of the four is a needle
  curation for a different intent, and `D-SKL-013` is the standing warning that
  a needle reaching two intents is a false route — four at once is four chances
  to make one.
- **`SKILL-07` is the one to take first.** It is not silence but a wrong answer:
  a session asking for a backend module is handed a conformance audit, and
  `D-AUD-003`'s reasoning about a description that swallows a neighbouring task
  applies to a needle the same way.
- **No test is added for this.** A test feeding the case's prompt would hold the
  arrival, and holding it before the needles are curated fixes today's miss into
  the suite.

## Assumed

- That the prompts are how a caller writes. They were written as such, and no
  filed session carries these four task shapes in its own words.
- That paths are what a session passes. `EXT-08` is measured on three, and the
  other three on one plausible each.

## Wrong if

- The needles are curated and a session still arrives by another route. Then the
  brief was never the channel, and `D-SKL-062`'s mid-task question is what
  carries these shapes.
- A filed session reports one of these four task shapes reaching its workflow
  today. Then the wording measured here is not the wording that arrives.
- `SKILL-07`'s conformance answer turns out to be the wanted one. Then `audit`
  is right to fire on `reviewing`, and what is missing is the documentation half
  alone.

## Since then

All four were repaired on 2026-08-19, each measured on its own prompt and
against the neighbours it could steal from.

- `audit` no longer sees a bare `reviewing`. The gerund is `reviewing the`,
  `reviewing this` and `reviewing my`, so it reaches the intent only where the
  thing under review follows the word, and `SKILL-07` confirms `backend-module`
  alone. `review only` and `security review` went in with it, which is what
  `SKILL-11` is written in.
- `event-listener` gained the goal beside the mechanism — `without overriding`,
  `instead of overriding`, `do not want to override` — and `EXT-08` confirms it
  with no path passed.
- `site-setting` gained `add a setting`, `add the setting` and `per site`, and
  `SITE-09` confirms it rather than detecting it weakly.

What the reading corrected in this entry: `SITE-09` names no skill either way,
because `site-setting` routes to none. What confirming it buys is the checklist
stated as fact rather than under a condition, and the same holds for `EXT-08`.

The fourth **Decided** is spent. The needles are curated, so a test feeding each
case's own prompt no longer fixes a miss into the suite, and
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout` is what holds
the arrival for all four.

The half `SKILL-07` was not taken for was closed the same day. "Document the
public workflow ... in the right place" reached no documentation intent because
the only intent naming `typo3-extension-documentation` was `changelog`, and
widening that one would have handed a manual the core's release artifact;
`documentation` is an intent of its own since
[`D-SKL-066`](../task-skills/skl-066-documenting-a-package-for-its-readers-is-an-intent-of-its-own.md),
which carries the measurement. So the crossing is measured on both sides, and
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout` holds one row
per half.

What that reading found beside it: `SKILL-03` reached the documentation skill
all along and every checklist item it was handed was about
`typo3/sysext/core/Documentation/Changelog/`. A brief naming the right workflow
is not evidence that what it states is the right work, which is the same
distinction this entry opens with.

