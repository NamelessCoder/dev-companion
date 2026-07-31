---
id: D-VER-4
date: 2026-07-31
status: standing
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

- **Evidence:** the transcript's own tool results. With `targetVersion: 14` the
  `extension-files` hint came back with nine statements and neither of the two
  bound to the older majors; the same call for the declared range returns both
  `ext_emconf.php` statements, each with its range beside it. The three findings
  are the three subjects where the two majors disagree.
- **Decided:** where a repository declares `typo3/cms-core` for more than one
  covered major and the caller states no version, the hint lookups answer for
  all of them and say so, naming the majors and the declaration. A stated
  version still narrows to one, because somebody who says "14" is asking about
  14. A constraint that cannot be read falls back to the installed version
  rather than to a guessed range.
- **Decided:** the catalogs keep filtering by a single version. Their
  `targetVersion` withholds markup rather than qualifying it, and a class that
  does not exist on the version being rendered fails in a browser whichever
  other major the package also supports.
- **Assumed:** asking a constraint about each covered major is enough to read
  it. The comparators an extension actually writes — `^`, `~`, `>=`, `<`, an
  exact version, `*` — are covered, and anything else yields nothing and falls
  back.
- **Wrong if:** a spelling in the wild answers false for a major it does serve,
  which shows up as a statement missing from an answer rather than as an error;
  or a repository declares a range far wider than it tests, and the answer then
  carries statements for majors nobody is maintaining. The second one is worth
  watching: the declaration is a promise, and this now treats it as one.
