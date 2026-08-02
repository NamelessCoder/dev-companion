---
id: R-KNW-17
status: held
---

# R-KNW-17 — A convention read off the core carries its condition

**A convention read off a core reference implementation is stated with the
condition that made it right there.**

The core is not a project, so the unconditional form is the one that transfers
wrongly — and the condition is written as the test a reader can run on their
own extension, not as "camino does it differently".

## From

Backend layouts placed at extension level in a project sitepackage whose set
was the only path into any backend, so the placement had no effect at all —
stated as the rule because `theme_camino`, which ships an extension-level
`page.tsconfig` as well, has them there (2026-07-29).

## Held by

- `HintsTest::whereBackendLayoutsGoIsAnsweredWithTheConditionItDependsOn`
