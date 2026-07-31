---
id: D-KNW-2
date: 2026-07-29
status: standing
---

# D-KNW-2 — A hint about typo3/testing-framework is verified against tags, not against the checkouts

**A hint whose subject is `typo3/testing-framework` is verified against the tags
that pair with the covered majors, and only what the core itself decides carries
a range.**

`project-extension-tests` is the first hint whose subject is not the core: the
phpunit boilerplate, the environment variables, the extension-path resolution
and `setUpFrontendRootPage()` all live in `typo3/testing-framework`, a package
with its own release cycle that `.checkouts/` does not contain.

- **Decided:** verify it against the tags that pair with the covered majors —
  7.1.1 (v12), 8.3.3 (v13), 9.6.1 (v14) and `main` (v15) — read from a clone of
  the package repository, and leave the statements unbound where all four agree.
  Only what the core itself decides carries a range: `SiteWriter` against
  `SiteConfiguration`, and the site set that `setUpFrontendRootPage()` drops.
- **Assumed:** those behaviours are stable within a major of the package. They
  have survived four majors unchanged, and the two that would hurt most — the
  hardcoded `clear` flag and the document-root-relative extension paths — are
  identical in all four.
- **Wrong if:** a testing-framework release changes one of them inside a major.
  Nothing here would notice: `bin/verify-catalog` re-reads the core checkouts,
  and this package is not one of them. The cheap fix is to teach that script the
  tag-to-major pairing; the honest alternative is to bind the statements to the
  package version instead of the TYPO3 one, which the hint format has no field
  for.
