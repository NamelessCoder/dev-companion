---
id: D-VER-4
date: 2026-07-31
status: confirmed
---

# D-VER-4 — A supported range is a property of the package, not of the checkout

**Where a repository declares `typo3/cms-core` for more than one covered major
and the caller states no version, the hint lookups answer for all of them and
say so.**

`REVIEW-02` reported three things as accumulated debt that are the cost of one
codebase serving TYPO3 13.4 and 14.3: a deprecated upgrade-wizard interface, an
`ext_emconf.php`, and 73 suppressed deprecations. Reading the tool trace back
showed the session was never told: `typo3_architecture_lookup` filtered every
statement to the installed major, so the rule bound `until: 13` — the one that
says what that file is for — was removed before the answer was composed.

## Evidence

- The transcript's own tool results. With `targetVersion: 14` the
  `extension-files` hint came back with nine statements and neither of the two
  bound to the older majors; the same call for the declared range returns both
  `ext_emconf.php` statements, each with its range beside it. The three
  findings are the three subjects where the two majors disagree.

## Decided

- Where a repository declares `typo3/cms-core` for more than one covered major
  and the caller states no version, the hint lookups answer for all of them and
  say so, naming the majors and the declaration. A stated version still narrows
  to one, because somebody who says "14" is asking about 14. A constraint that
  cannot be read falls back to the installed version rather than to a guessed
  range.
- The catalogs keep filtering by a single version. Their `targetVersion`
  withholds markup rather than qualifying it, and a class that does not exist
  on the version being rendered fails in a browser whichever other major the
  package also supports.

## Assumed

- Asking a constraint about each covered major is enough to read it. The
  comparators an extension actually writes — `^`, `~`, `>=`, `<`, an exact
  version, `*` — are covered, and anything else yields nothing and falls back.

## Wrong if

- A spelling in the wild answers false for a major it does serve, which shows
  up as a statement missing from an answer rather than as an error; or a
  repository declares a range far wider than it tests, and the answer then
  carries statements for majors nobody is maintaining. The second one is worth
  watching: the declaration is a promise, and this now treats it as one.

## Confirmed on 2026-08-02

The first half of **Wrong if** did not happen. Every range spelling the three
checkouts that play `E-EXT` declare a TYPO3 major with — their root manifests
and every `typo3/cms-*` requirement in the vendor trees they installed —
answers for exactly the majors it serves: the two written by hand,
`^13.4 || ^14.3` and `^12.4.37 || ^13.4.15`; the released
`^13.4 || ^14.0 || 14.*.*@dev`; `^11.5 || ^12.4 || ^13.4 || ^14.0`;
`typo3/testing-framework`'s bare `13.*.*@dev || 14.*.*@dev` and its older pair;
and the exact `14.3.0` and `13.4.33` the core packages require each other with.
Each answer was compared against composer/semver's own — the constraint parsed
and intersected with `>=n <n+1` per major — rather than against a reading of
the spelling, and the two agree on all eight. They are
`VersionsTest::aSpellingFromTheWildAnswersForEveryMajorItServes`, one row per
spelling, so a comparator changing meaning is a failing test rather than a
missing statement.

What the corpus did produce is a shape one step to the side: a space between an
operator and its version, which Composer takes and this did not.
`georgringer/news` writes its php requirement `>= 8.1 < 8.5` in the same
manifest that declares the core range, and a core range spelled that way
answered for no major at all — the fallback, so the answer would have been
composed for the installed major alone and said nothing about the other. That
is the failure this **Wrong if** describes, reached by a spelling habit rather
than by a comparator, and `Versions` now collapses the space before splitting.
One shape is still unread and stays that way: Composer's hyphen range,
`12.4 - 14.3`. It occurs nowhere in the corpus — 0 of the 3179 constraints in
those vendor trees, and none of the three roots — so nothing was built for it.
