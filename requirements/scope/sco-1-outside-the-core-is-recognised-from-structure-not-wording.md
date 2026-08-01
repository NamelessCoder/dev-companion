---
id: R-SCO-1
status: held
---

# R-SCO-1 — Outside the core is recognised from structure, not wording

**Work outside the core is recognised from structural evidence — the kind of
installation, the shape of the paths, an area the installation knows as
somebody's extension — rather than from wording.**

Evidence of core work wins over the weaker signals, in this order: a
`typo3/sysext/` path or the contribution workflow named outright, then an
outside-core marker, then the area, then the path shape, then the contribution
workflow named in prose, and last which installation the session sits in. A
`typo3/sysext/` path is the only marker that ends the question outright: prose
that names the core in order to rule it out reads to a substring search exactly
like claiming it.

What a path carries is read before anything said about the call, which is what
keeps two paths of one call apart ([`R-AUD-2`](../audience/aud-2-the-audience-is-a-property-of-the-task.md)).
A path shape is evidence only where it can be: `Build/Scripts/` and
`Build/Sources/` are the core's own, a bare `Build/` is any repository that
compiles something, and neither is core evidence where the manifest at the root
already says this checkout is not the core.

**From:** `outsideCore` flipping only after the caller spelled out "not TYPO3
core" in prose (2026-07-29).

**Held by:**
`ScopeTest::namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork`,
`ScopeTest::anAreaTheInstallationKnowsAsSomebodysExtensionIsOutsideTheCore`,
`ScopeTest::inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise`,
`ScopeTest::inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone`,
`ScopeTest::aPathInsideAnExtensionIsRecognisedByItsShape`
