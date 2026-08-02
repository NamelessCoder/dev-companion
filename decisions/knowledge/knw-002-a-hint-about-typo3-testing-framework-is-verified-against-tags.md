---
id: D-KNW-002
date: 2026-07-29
status: revoked
---

# D-KNW-002 — A hint about typo3/testing-framework is verified against tags, not against the checkouts

**A hint whose subject is `typo3/testing-framework` is verified against the tags
that pair with the covered majors, and only what the core itself decides carries
a range.**

`project-extension-tests` is the first hint whose subject is not the core: the
phpunit boilerplate, the environment variables, the extension-path resolution
and `setUpFrontendRootPage()` all live in `typo3/testing-framework`, a package
with its own release cycle that `.checkouts/` does not contain.


- **Since then** the gap in **Wrong if** is closed by the command it names.
  `bin/cli checkouts:update` keeps the package beside the core checkouts, one
  worktree per pinned line at that line's newest tag, and `bin/cli catalog:check`
  re-derives the pairing from the pins and reads the load-bearing half of the
  statements there: the four boilerplate files and the "copy it" line in their
  header, the five `typo3Database*` variables and the credentials message, the
  document-root-relative extension path, the package collection's missing
  dependency, and the `clear` flag `setUpFrontendRootPage()` writes. Nothing
  about the pairing is recorded, so a release inside a line arrives with the next
  update instead of with an entry somebody maintains, and one that changes
  nothing relevant passes without a word — which is what separates a guard from
  a reminder to go and look. What no needle covers stays unguarded, and the
  honest alternative above is still the answer if that is the half that moves.

## Decided

- Verify it against the tags that pair with the covered majors — 7.1.1 (v12),
  8.3.3 (v13), 9.6.1 (v14) and `main` (v15) — read from a clone of the package
  repository, and leave the statements unbound where all four agree. Only what
  the core itself decides carries a range: `SiteWriter` against
  `SiteConfiguration`, and the site set that `setUpFrontendRootPage()` drops.

## Assumed

- Those behaviours are stable within a major of the package. They have survived
  four majors unchanged, and the two that would hurt most — the hardcoded
  `clear` flag and the document-root-relative extension paths — are identical
  in all four.

## Wrong if

- A testing-framework release changes one of them inside a major. Nothing here
  would notice: `bin/cli catalog:check` re-reads the core checkouts, and this
  package is not one of them. The cheap fix is to teach that script the
  tag-to-major pairing; the honest alternative is to bind the statements to the
  package version instead of the TYPO3 one, which the hint format has no field
  for.

## Revoked on 2026-08-01

The pairing is off by one release line, and the core says so itself. Each
covered branch pins the package in its own `require-dev` — `^8.3.1` on 12.4,
`^9.2.1` on 13.4, `^9.5.0` on 14.3, `dev-main` on main — so v12 pairs with 8.x
rather than with 7.1.1, and v13 with 9.x rather than with 8.3.3. Composer
resolves a project the same way, because a line admits the major it was cut for
and the one before it: 8.x requires `typo3/cms-core: 12.*.*@dev || 13.*.*@dev`
and 9.x `13 || 14`, so an extension on v12 installs 8.3.3. The statements are
untouched by this: 7.1.1 answered for a major nobody here covers, and the three
refs the corrected pairing names — 8.3.3, 9.6.1 and `main` — were all read at
the time.
