---
id: R-SKL-7
status: held
---

# R-SKL-7 — An upgrade establishes what breaks before it chooses a range

**Crossing a package from one supported TYPO3 range to another is ordered
work.**

What breaks is established from the sweep before a range is chosen, the range is
resolved by the dependency solver rather than asserted, the lowest declared
major decides every shape that is written, and every declared combination is
proven or named as unproven.

The sweep the base fixes is where it starts, so it is not restated here — what
this workflow adds are the two sources a changelog query cannot reach. The
Extension Scanner, whose silence is worth what the `FullyScanned` /
`PartiallyScanned` tag says it is worth and nothing more, and the deprecation
annotations on the symbols this package actually calls, because an annotation
sits on the class while an entry has to be matched by a query. Both answer from
the core that is **installed**, which is the boundary the order rests on: what
the target major changed is official documentation until the installation is on
it, never recall, and the sweep is run again once it is.

That the shapes an older declared major requires are a requirement rather than
debt is a decision the assessment workflow already makes correctly; it is stated
here as the boundary of what may change, not as a judgement to arrive at. What
the work list does not justify is not this workflow's to change, and each of
those has a named owner.

**From:** the `REVIEW-02` run of 2026-07-31 in an extension declaring two majors
against an installation a major behind, which established both halves at once.
The multi-major decisions were made, and made well — the older major's YAML
registration argued as required because the attribute form is unavailable there,
and the same excuse refused for a deprecated ViewHelper shape whose replacement
works on both. What was absent was the order: the deprecation with the largest
consequence for that package's next major sat on 24 call sites in 11 files and
the surface was reported as clean, the one deprecated API named was reached
because a finding walked into it, and the Extension Scanner was never called in
a checkout that has one.

**Held by:** `SkillTest::anUpgradeIsOrderedWorkAndOwnsOnlyTheCrossing`,
`SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`, `EXT-01`.
That a session works in this order is not guarded and will not be by a forward
run — `D-EVI-1` admits only an open review as forward evidence, and a review
stops at findings by design.
