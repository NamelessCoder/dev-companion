---
id: R-SKL-010
title: "A skill's description names every side of what it owns"
status: held
restsOn: [D-AUD-003]
---

# R-SKL-010 — A skill's description names every side of what it owns

**A skill's description names every side of the domain it owns, so a backend
preview of a content element matches the skill that owns the element.**

The description is the only part of a skill read before it is chosen. A domain
named by one of its halves leaves the other half reading as somebody else's
work, and the body that covers it in as many words is never loaded: the task
matches a word in a neighbouring description, or nothing at all, and the session
does the work from whatever it can read in the checkout.

## From

A session in `site-new` on 2026-08-01 that wrote a custom backend preview for a
TYPO3 content element with no skill activated and no tool called, and did the
work by reading vendor code —
`feedback/2026-08-01-002926-debrief-of-a-typo3-14-backend-content-element.md`.
It ran a day after the entry point reached the `instructions`, so the channel
that failed was the descriptions: `typo3-content-element-development` opened on
"frontend content elements" and reached `previews` ninth of eleven, while
`typo3-backend-module-development` promised "backend UI work" and meant a
module. `D-AUD-003` carries the reading of the three channels behind it.

## Held by

- `SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement`
