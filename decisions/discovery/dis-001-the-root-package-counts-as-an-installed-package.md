---
id: D-DIS-001
title: The root package counts as an installed package
date: 2026-07-29
status: confirmed
coveredBy:
  - InstanceTest::aMonorepoRootIsCountedBesideThePackagesItHolds
  - InstanceTest::aRootAlsoInstalledIntoVendorIsOnePackage
---

# D-DIS-001 — The root package counts as an installed package

**In an extension development checkout the root package is one of the installed
packages, and a root package on its own is not an installation.**

## Assumed

- In an extension development checkout, the extension being edited is the root
  package and is meant to be part of every answer about "this installation" —
  its icons and labels are as registered as any dependency's.
- A root package alone is not an installation. The root is only added when
  Composer's metadata yielded packages, so an extension repository whose
  dependencies were never installed still reports no installation rather than
  one holding a single package and no console.

## Wrong if

- A monorepo whose root declares a TYPO3 package type but is not the thing being
  worked on, or a setup that installs the root into the vendor directory as well
  — the two entries then resolve to the same realpath under one key, which is
  intended, but has not been seen in the wild here.

## Confirmed on `2026-08-01`

Both shapes were built as fixtures, which is as far as this goes without an
installation. The second collapses as the entry said: a root required through a
path repository resolves to one realpath, so it stays the project's own, and
reading the vendor path would have made the extension being edited a dependency
of the repository it is. The first displaces nothing. What a fixture cannot
settle is what the extra entry costs a monorepo somebody works in, and no guess
at which roots are containers was written, because nothing in the metadata
carries it.
