---
id: D-ANS-084
title: 'A curated phrase crosses the domain gate'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aSymptomReachesTheHintThatExplainsItFromAnotherDomain
  - HintsTest::aTypeScriptTestPathIsNotAnsweredWithPhpunit
  - HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# D-ANS-084 — A curated phrase crosses the domain gate

**A hint outside the domains a query selects is a candidate where the query
spells out a multi-word `appliesTo` phrase of it that no selected hint claims.**

The gate asks the query where the work belongs, which is what a task description
says and the opposite of what a symptom says. A phrase somebody curated is the
query they wrote it for, so it crosses — and the claim check is what keeps it to
the case it was measured on, because a phrase the selected layers carry
themselves is one they can answer.

## Evidence

- The measurement `D-ANS-081` asked for, run on 199 queries: the twelve of the
  sweep and the recorded misses beside them, every hint title, and every forward
  and contract prompt, before and after.
- Any curated pattern past the gate crosses on 39 of them. It displaces rather
  than adds, because an answer holds six: one word of
  `css-icon-text-layout-stability` — "label", "icon" — pushes
  `site-set-labels-and-layouts` and `extbase` out of five label and language
  answers.
- A phrase of several words crosses on 9 and undoes `D-KNW-067`. "unit test" is
  curated on `core-tests`, `project-extension-tests` and `unit-test-doubles` as
  well as on `javascript-unit-tests`, so a `.ts` test path is answered with
  PHPUnit test doubles again.
- The phrase no selected hint claims crosses twice. "the content elements render
  in reverse order" reaches `datahandler-placement`, and a sitepackage task
  saying "backend form" outright reaches `tca-formengine`. No answer lost an
  entry anywhere, and the sweep's two negative controls still return nothing.
- A phrase is compared by its terms rather than as written, or the two hints
  claiming one phrase in different word forms claim two: `javascript-unit-tests`
  carries "unit test" and `unit-test-doubles` carries "unit tests", and the
  `.ts` path failure came back through that gap.

## Decided

- Only the task text opens the gate. A path carries its domain in its extension,
  so a pattern matching one says nothing the gate did not already have, and
  `R-ANS-026` is the requirement that a path decides the subsystem.
- Only a phrase of several words. A one-word pattern is what a hint is filed
  under rather than a phrasing somebody anticipated, and placing one is what the
  domain gate is for.
- The crossing is not reported as its own field. `domains` says what was
  selected and now says what may come back from outside it, which is one
  sentence in the schema rather than a second list on every answer.
- `typo3_hint_lookup` says on the `task` parameter and in the `routing` block
  that a symptom is a query it takes, which is what makes a debugging caller try
  it at all.
- Rejected: opening the gate only where the paths say nothing. It keeps
  `D-KNW-067` as well, and it costs the caller who is standing in the file the
  symptom showed in — which is the caller this is for.

## Assumed

- That two crossings over 199 queries is the shape of it rather than an artefact
  of a corpus written by subject. A corpus curated with symptoms in its
  `appliesTo` would cross more often, and nothing here says how much more.
- That a claimed phrase means the selected layers can answer. It means they were
  indexed for the words, which is the same evidence the gate itself runs on.

## Wrong if

- A session reports a hint from a layer its query never meant, and the hint
  crossed on a phrase rather than being selected.
- A symptom whose mechanism is in another layer misses because a selected hint
  claims its phrase in passing — the claim check turning from a guard into the
  gate.
- `bin/cli hints:coverage` or the sweep shows crossings becoming ordinary as the
  corpus grows, which would make the rule a second matcher rather than an
  exception to the gate.
