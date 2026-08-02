---
id: D-DIS-4
date: 2026-07-29
status: confirmed
---

# D-DIS-4 — The version comes from the core package, not from the console

**The installed version is read from the core package's `Typo3Version` class
rather than asked of `bin/typo3 --version`.**

The catalogs are pinned to one revision and every answer was phrased as timeless
fact, while the server had the other number all along: the installation it reads
for icons and labels states its own version. Both were known and never
contrasted.

## Decided

- Read it from the core package's `Typo3Version` class rather than ask
  `bin/typo3 --version`. The version decides whether an answer holds, so it has
  to be available exactly when the console is not — and a console call costs a
  TYPO3 boot, on every answer that carries the catalog pin.
- Translation domains are the one answer that is withheld rather than qualified
  below a version. `13.4` has no `TranslationDomain*` class at all, `14` ships
  the mapper, so the domain string is syntactically fine and resolves to
  nothing: the label renders empty, at runtime, silently. Everything else the
  catalogs hold is markup and class names, where a qualified answer is still
  worth having.

## Assumed

- The `VERSION` constant in that class stays where it is and stays a literal.
  It has been in `Classes/Information/Typo3Version.php` on every branch this
  was checked against, and a missing or unparseable one yields null, which
  reads as "nothing to compare with" rather than as a wrong version.

## Wrong if

- The domain API is backported into a 13.x patch release, which would make the
  constant in `Tools` wrong. It is one number in one place for that reason.
- A caller works on a version other than the installation the server found —
  the second checkout, the backport branch. The version is then read from the
  wrong place, and nothing accepts a stated one yet.

## Confirmed on 2026-08-02

The first **Wrong if** has not happened, and it is now guarded rather than
promised. At `bin/cli checkouts:update` state of 2026-08-01, 12.4 (`31f881a2`)
and 13.4 (`1d104f3b`) carry no `TranslationDomain*` class anywhere below
`typo3/sysext/`, while 14.3 (`faf60eea`) and main (`c71b2bdb`) both carry
`TranslationDomainMapper` and `TranslationDomainResolver` in
`core/Classes/Localization/`, and the feature is filed as
`Changelog/14.0/Feature-93334-TranslationDomainMapping.rst`. Two readers hold
the constant from here on. `bin/cli catalog:check` asks every covered checkout
for the class and fails where the major it derives is not the constant — a
backport is a release rather than an edit in this repository, so the checkouts
are the only place one is visible. `VersionsTest` ties the constant to the
majors `knowledge/versions.json` declares, so a covers list that stops carrying
a major below it fails instead of leaving the withheld answer addressed to
versions nothing covers. The constant is public for those two and is still one
number in one place.

## Since then

The second **Wrong if** is live, and reading for the first one is what showed
it. `TranslationDomainLookup` asks `Instance::typo3Major()` directly and takes
`path` alone, while `SystemExtensionLookup`, `TestRunGuide`, `CatalogScope` and
`DocumentationLookup` all take a `targetVersion` through `Versions::target()`.
The feature that would close it is queued as
`todo/360-let-the-translation-domain-answer-be-asked-for-a`.
