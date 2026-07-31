---
id: R-KNW-10
status: held
---

# R-KNW-10 — An answer says which half of TYPO3 it is for

**An answer says where it may be used when it is only usable in half of
TYPO3.**

The icon identifiers are the backend registry's, so every `typo3_icon_lookup`
answer carries that sentence — in the text and in the data — rather than
only the ones whose query happens to sound like frontend work. A tool that is
handed a query and not a task cannot tell the two apart. The hint that
describes the same registry states the same boundary: a list of backend APIs
reads as "here is how you render an icon" to whoever is writing a page
template.

**From:** backend icon identifiers about to be used in a frontend template,
stopped by the user: "die icons welche du findest sind übrigens nur für das
backend gedacht, nicht für das frontend" (2026-07-29); re-reported for the
hint after the tool half had shipped.

**Held by:**
`IconLookupTest::everyAnswerSaysTheIdentifiersAreTheBackendRegistrys`,
`IconLookupTest::theRoutingEntrySendsCallersThereForBackendWorkOnly`,
`HintsTest::theIconHintSaysWhichHalfOfTypo3ItIsAbout`
