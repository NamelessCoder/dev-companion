---
id: D-GUI-014
title: Looking at a change is an intent of its own
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefRecognizesLookingAtAChangeInABrowser
  - KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther
---

# D-GUI-014 — Looking at a change is an intent of its own

**`typo3_task_guide` recognizes looking at a change in a running installation as
work of its own, and names `any/testing/browser-check` on both sides of the core
boundary.** Four filed sessions did that work with the page listed in an answer
they had already read, and the brief that knew what the task was recognized
nothing at all.

## Evidence

- Measured in this worktree on 2026-08-18, before the change. "Prove a rendering
  change in the browser after fixing a frontend crash" matched no intent with no
  path, with an extension path and with a core path, so the brief named no skill
  and no guide; "take a screenshot of the page module" matched none either.
- The four sessions are the same shape from four sides.
  `feedback/2026-08-18-074226` verified a rendering change in a browser in a
  project checkout, `feedback/2026-08-10-182417` reviewed a backend CSS patch in
  a core one and told its reader five times that it could not judge the change
  visually, and `feedback/2026-08-17-205945` and `2026-08-17-212218` shipped six
  backend previews unverified. Each had `any/testing/browser-check` in the
  `guides` key of `typo3_project_describe` and none called `typo3_rule_lookup`.
- `browser-tests` is the nearest intent and is other work. Its needles are
  `playwright`, `e2e`, `spec.ts` and `acceptance test`, its first checklist item
  is to read the Playwright reference before writing the first spec, and it
  routes `typo3-extension-testing` — a whole test layer for a session that wants
  to see something once.
- The same boundary was settled once already, one step further in. `D-SKL-045`
  put the browser step in the content-element skill on the reasoning that
  "looking is not a test layer", which is what `browser-check` opens by saying:
  a spec asserts what somebody already knows.
- The other page the words reach is already routed. `D-KNW-071` built
  `core/testing/proving-a-rendering` and put its route in the scratch-probe
  paragraph of `typo3-core-patch-review` and the throwaway-test rules of
  `typo3-core-issue-triage`, so it is unreachable from a brief and reachable
  from the two skills whose work grants the probe.

## Decided

- **An intent rather than `browser-tests` widened.** A widening would hand that
  intent's checklist and its skill to a session that is only looking, and the
  words it would have to reach — a browser, a screenshot — are not the words a
  suite is asked for in.
- **The needles are the act and not the subject**: `in the browser`,
  `in a browser`, `browser check`, `screenshot`, `visually`. `browser` alone is
  left out, because it is `browser-tests`' subject as much as this one's, and a
  needle that reaches both intents is the false route `D-SKL-013` watches for.
- **It names no skill on either side.** No published workflow owns looking; the
  content-element skill carries it as one step of building something, which is a
  step in a workflow rather than a workflow to load.
- **`changesNothing`, so the page survives a review brief.** The core session
  that needed it was reviewing a patch, and a brief that changes nothing routes
  only what changes nothing either (`D-SKL-039`).
- **One page on both sides.** `guide` and `guideCore` name
  `any/testing/browser-check`, which is the first `guideCore` any intent
  carries: what made the field empty everywhere was that a core intent's page
  would be the core's own contribution process, and this page is scoped `any`
  and writes up the core checkout in its own sections.
- **`core/testing/proving-a-rendering` stays named by no intent.** It answers
  what a TypoScript snippet renders rather than how a change is looked at, the
  needles here are about looking, and its two skill routes are the ones its own
  work arrives through. Whether the same question is owed an `any` page outside
  the core is `feedback/2026-08-18-081100`.
- **No `rulesQuery`.** The three documents a brief searches are the core's
  contribution process, and a looking brief has nothing to take from them.

## Assumed

- That a session which is only looking says so in the words above. "Check the
  fix works" names no browser and is recognized by nothing here, which is the
  same limit every intent has and is not measured for this one.
- That the page serves the core side as well as the one it was written from. The
  core session on record never opened it.

## Wrong if

- A brief names the page for a session writing a suite. Then the needles reach
  the subject rather than the act, and `browser` is in the corpus somewhere this
  reading did not look.
- A session gets the page in the brief and needs the probe instead. Then the two
  questions are one kind of work after all, and the core side is
  `core/testing/proving-a-rendering`.
- A session reports that the guide arrived with the work and the view still
  shipped unverified. Then the route was not what was missing, which is where
  `D-SKL-045`'s own first **Wrong if** would land as well.
