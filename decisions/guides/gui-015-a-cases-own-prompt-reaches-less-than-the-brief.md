---
id: D-GUI-015
title: "A case's own prompt reaches less than the brief"
date: 2026-08-19
status: open
coveredBy:
  - ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout
---

# D-GUI-015 — A case's own prompt reaches less than the brief

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
against the neighbours it could steal from: the bare gerund is gone from `audit`
in favour of the forms that carry what is under review, and the other three
gained the goal beside the mechanism. What the reading corrected here is that
one of them names no skill either way, because its intent routes to none.

The fourth **Decided** is spent: the needles are curated, so a test feeding each
case's own prompt no longer fixes a miss into the suite. The half one case was
not taken for was closed the same day by an intent of its own (`D-SKL-066`),
since widening the changelog intent would have handed a manual the core's
release artifact. Found beside it: a brief naming the right workflow is not
evidence that what it states is the right work.
