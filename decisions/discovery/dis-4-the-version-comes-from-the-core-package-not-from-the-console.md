---
id: D-DIS-4
date: 2026-07-29
status: standing
---

# D-DIS-4 — The version comes from the core package, not from the console

**The installed version is read from the core package's `Typo3Version` class
rather than asked of `bin/typo3 --version`.**

The catalogs are pinned to one revision and every answer was phrased as timeless
fact, while the server had the other number all along: the installation it reads
for icons and labels states its own version. Both were known and never
contrasted.

- **Decided:** read it from the core package's `Typo3Version` class rather than
  ask `bin/typo3 --version`. The version decides whether an answer holds, so it
  has to be available exactly when the console is not — and a console call costs
  a TYPO3 boot, on every answer that carries the catalog pin.
- **Decided:** translation domains are the one answer that is withheld rather
  than qualified below a version. `13.4` has no `TranslationDomain*` class at
  all, `14` ships the mapper, so the domain string is syntactically fine and
  resolves to nothing: the label renders empty, at runtime, silently. Everything
  else the catalogs hold is markup and class names, where a qualified answer is
  still worth having.
- **Assumed:** the `VERSION` constant in that class stays where it is and stays
  a literal. It has been in `Classes/Information/Typo3Version.php` on every
  branch this was checked against, and a missing or unparseable one yields null,
  which reads as "nothing to compare with" rather than as a wrong version.
- **Wrong if:** the domain API is backported into a 13.x patch release, which
  would make the constant in `Tools` wrong. It is one number in one place for
  that reason.
- **Wrong if:** a caller works on a version other than the installation the
  server found — the second checkout, the backport branch. The version is then
  read from the wrong place, and nothing accepts a stated one yet.
