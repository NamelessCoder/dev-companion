---
id: D-DIS-1
date: 2026-07-29
status: standing
---

# D-DIS-1 — The root package counts as an installed package

**In an extension development checkout the root package is one of the installed
packages, and a root package on its own is not an installation.**

- **Assumed:** in an extension development checkout, the extension being edited
  is the root package and is meant to be part of every answer about "this
  installation" — its icons and labels are as registered as any dependency's.
- **Assumed:** a root package alone is not an installation. The root is only
  added when Composer's metadata yielded packages, so an extension repository
  whose dependencies were never installed still reports no installation rather
  than one holding a single package and no console.
- **Wrong if:** a monorepo whose root declares a TYPO3 package type but is not
  the thing being worked on, or a setup that installs the root into the vendor
  directory as well — the two entries then resolve to the same realpath under
  one key, which is intended, but has not been seen in the wild here.
