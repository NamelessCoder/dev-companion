---
id: D-KNW-017
date: 2026-08-02
status: open
---

# D-KNW-017 — A verification question reaches the subject cell and never the layer cell

**A question about whether something renders correctly reaches the hint that
says how to build it, and nothing on that path names the layer that would
verify it.**

`browser-tests` is reachable only by words that already name the answer. The
caller who needs it most is the one who has not yet decided that a browser is
involved, and that caller lands on `content-elements` instead.

## Evidence

- `bin/cli hints:probe "verifying the rendered testimonials frontend and the
  backend page module preview"` — the feedback's own subject, in the words a
  task would use — matches nothing. Forty hints come back as the index.
- Three plainer phrasings of the same question reach `content-elements` and
  nothing else: "verify that the content element renders correctly on the live
  site", "check the backend page module preview of a content element", "how do I
  verify rendered output of a content element".
- The feedback's `Query` line does reach `browser-tests`, at `appliesTo(22) +
  text(232)`. It contains the words "Playwright" and "browser test", which is
  the debrief naming the layer it had already worked out it should have used.
- `browser-tests.appliesTo` is ten terms and every one of them is the answer:
  `playwright`, `browser test`, `end-to-end`, `end to end`, `e2e`,
  `acceptance test`, `accessibility`, `axe`, `wcag`, `contrast`, `spec.ts`.
  None of them is a word for wanting to know whether a page came out right.
- `bin/cli hints:coverage` lists `browser-tests` among the 44 of 66 hints that
  no scenario prompt reaches. So the miss is not measured either.
- The hint that is reached says nothing about verifying. `content-elements`
  carries fourteen statements about registering an element and rendering its
  preview, and none about which layer establishes that either works. It names
  `sitepackage-layout` for the template naming and no cell of the testing row.
- The answer was already written, in the cell nobody arrives at.
  `browser-tests` holds it: "A functional test with executeFrontendSubRequest()
  runs no JavaScript, applies no stylesheet and speaks no HTTP. It is a
  rendering test, and calling it a frontend test is how a suite ends up with no
  browser in it at all."
- The rule is in the skill as well. `skills/typo3-extension-testing/SKILL.md`
  says to use a browser test "for rendered user journeys, backend interaction,
  JavaScript, or accessibility behavior that cannot be established below the
  UI", and routes to `references/playwright.md` after the layer is chosen.
- What this server delivered for the task shape points the other way.
  The `content-element` intent in `knowledge/task-intents.json` closes with
  "Cover the persistence of the child records and the order they render in with
  functional tests. A unit test of mocks asserts the mock, and browser
  behaviour that was not tested is reported as untested." That sends rendered
  coverage to the functional layer and licenses leaving the browser layer out.
- The feedback's own premise is withdrawn by its author.
  `feedback/2026-08-01-003736` corrects three siblings, this one among them:
  the "never activated" claim was made from a transcript that begins at an
  anchored summary, and the user reports having seen the skill activated. So
  the trigger is not the lever here, and `documentation/feedback/judging.md`
  would not have assessed the self-criticism in any case.
- Nothing about TYPO3 was established here. Every probe above is a query
  against this repository as it stands on 2026-08-02.

## Decided

- Step 3 of the ladder, routing. The knowledge is here, the skill carries the
  rule, and a caller who has not already named the layer cannot reach either.
- Queued rather than closed on the spot. Which of the three surfaces carries
  the crossing is open — `appliesTo` on `browser-tests`, a statement in
  `content-elements`, or the `content-element` intent's checklist — and each
  one costs something different.
- The judgement extends [`D-KNW-008`](knw-008-tooling-is-a-row-that-is-crossed-in-the-answer.md)
  rather than contradicting it. That entry checked the crossing from inside the
  tooling row, where `typo3_test_run_guide` names the other cells. This is the
  caller who never enters the row.
- No fix is named. The todo that follows measures the candidates before
  choosing one, because a term added to `appliesTo` is a term every neighbouring
  answer then pays for.

## Assumed

- That the four probe queries are what such a session would have asked. They
  are this run's paraphrases of the feedback's Observation, not its transcript,
  which nobody here has.
- That reaching `content-elements` is correct and the miss is the absent
  crossing rather than a ranking failure. `content-elements` is the right hint
  for a question about a content element; it is simply not a hint about tests.
- That the sentence in the `content-element` intent is a wording that can move.
  It may instead be a considered rule about not claiming untested behaviour.

## Wrong if

- The crossing is written and a rendered-verification query still reaches only
  `content-elements`. Then it is ranking rather than routing, and
  [`D-ANS-021`](../answers/ans-021-a-longer-manual-query-re-aims-rather-than-narrows.md)
  and [`D-ANS-022`](../answers/ans-022-a-hyphenated-compound-reaches-neither-the-phrase-nor-the-word.md)
  are where it belongs.
- Widening `browser-tests.appliesTo` pulls it into answers that did not want
  it, so every backend-preview question pays for a testing hint.
- The `content-element` checklist sentence turns out to be deliberate, and two
  surfaces of this server saying opposite things about the same layer is worse
  than the gap. Then this is step 5 and a question rather than a routing fix.
