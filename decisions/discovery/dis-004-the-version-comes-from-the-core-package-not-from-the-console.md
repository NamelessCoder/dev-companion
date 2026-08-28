---
id: D-DIS-004
title: The version comes from the core package, not from the console
date: 2026-07-29
status: confirmed
coveredBy:
  - InstanceTest::theTypo3VersionIsReadFromTheCorePackage
---

# D-DIS-004 — The version comes from the core package, not from the console

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

- The `VERSION` constant in that class stays where it is and stays a literal. It
  has been in `Classes/Information/Typo3Version.php` on every branch this was
  checked against, and a missing or unparseable one yields null, which reads as
  "nothing to compare with" rather than as a wrong version.

## Wrong if

- The domain API is backported into a 13.x patch release, which would make the
  constant in `Tools` wrong. It is one number in one place for that reason.
- A caller works on a version other than the installation the server found — the
  second checkout, the backport branch. The version is then read from the wrong
  place, and nothing accepts a stated one yet.

## Confirmed on 2026-08-02

The first **Wrong if** has not happened and is guarded rather than promised. The
two older checkouts carry no `TranslationDomain*` class and the two newer ones
do, filed as a 14.0 feature. Two readers hold the constant from here:
`catalog:check` asks every covered checkout and fails where the major it derives
is not the constant — a backport is a release rather than an edit here — and
`VersionsTest` ties it to the majors `knowledge/versions.json` declares.

## Since then

The second **Wrong if** was live — this lookup asked for the major directly
while four other tools took a `targetVersion` — and was closed on 2026-08-02.
`typo3_translation_domain_lookup` resolves one through `Versions::target()` like
the others, reports the major it was composed for, and where nothing states a
version it names the version domains arrive in rather than answering as if that
were settled. It stays one major: an extension declaring two has no single
answer.
