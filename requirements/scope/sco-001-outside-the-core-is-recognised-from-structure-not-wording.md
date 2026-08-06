---
id: R-SCO-001
status: held
---

# R-SCO-001 — Outside the core is recognised from structure, not wording

**Work outside the core is recognised from structural evidence rather than from
wording.**

That evidence is the kind of installation, the shape of the paths, and a path
the installation knows as somebody's extension.

Evidence of core work wins over the weaker signals, in this order: a
`typo3/sysext/` path or the contribution workflow named outright, then an an
extension or project marker, then the package the installation knows that path
as, then the path shape, then the contribution workflow named in prose, and last
which installation the session sits in. A `typo3/sysext/` path is the only
marker that ends the question outright: prose that names the core in order to
rule it out reads to a substring search exactly like claiming it.

That last signal is the installation the session is **standing in**, not the one
`TYPO3_DEV_COMPANION_ROOT` names. The variable says which registry the icons and
labels are read from and moves nothing else; only where the walk-up reaches no
installation is it the sole evidence there is, and then it answers.

What a path carries is read before anything said about the call, which is what
keeps two paths of one call apart
([`R-AUD-002`](../audience/aud-002-the-audience-is-a-property-of-the-task.md)).
A path shape is evidence only where it can be, and that holds in both
directions: `Build/Scripts/` and `Build/Sources/` are the core's own, a bare
`Build/` is any repository that compiles something, and neither is core evidence
where the manifest at the root already says this checkout is not the core.
`Classes/`, `Configuration/` and `Resources/` are the mirror — the shape of a
package, and no evidence of one inside a core checkout, where they are what a
path relative to a system extension directory looks like.

## From

`outsideCore` flipping only after the caller spelled out "not TYPO3 core" in
prose (2026-07-29).

## Held by

- `ScopeTest::namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork`
- `ScopeTest::aPathTheInstallationKnowsAsSomebodysExtensionIsOutsideTheCore`
- `ScopeTest::inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise`
- `ScopeTest::inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone`
- `ScopeTest::aPathInsideAnExtensionIsRecognisedByItsShape`
- `ScopeTest::aPackageShapedPathInACoreCheckoutIsCoreWork`
- `ScopeTest::namingAnInstallationToReadDoesNotMoveWhereTheWorkIs`
- `ScopeTest::whereNothingElsePlacesTheSessionTheNamedInstallationIsTheEvidence`
