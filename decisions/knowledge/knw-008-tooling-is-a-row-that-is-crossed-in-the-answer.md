---
id: D-KNW-008
date: 2026-08-02
status: open
---

# D-KNW-008 — Tooling is a row the answer crosses, not a dimension the corpus stores

**How a change is tested stays stored as it is, and the crossing happens where
the answer is composed.**

One file for the core harness, one hint per audience, one document for the
scripts — and a tool that owns one of them names the others.

The scatter is real: `test-suite-hints.json`, `core-tests`,
`project-extension-tests`, `browser-tests`, `extension-static-analysis` and
`typo3-core-scripts.md` are one row of the knowledge base in six places. What
was asked is whether it should become a dimension crossing the audience one.

## Evidence

- The crossing already happens, by name. `typo3_test_run_guide` on an extension
  path declines the suites and routes to `typo3_architecture_lookup` with
  `id=project-extension-tests` and `id=browser-tests` — the two cells of the
  same row for the other column — and says why `runTests.sh` is not one of them.
- The third cell was named on 2026-08-02, after this entry was written. Asked
  "set up phpstan for our extension" on an extension path,
  `typo3_test_run_guide` declined and routed to `project-extension-tests` and
  `browser-tests` alone — neither of which says what goes into a phpstan
  configuration — although `phpstan`, `cgl` and `lintPhp` are its own suites and
  static analysis is therefore one of the things an extension arrives there
  asking for. Both of its decline sentences now name `extension-static-analysis`
  beside the other two, and
  `skills/typo3-extension-testing/references/static-quality.md` names the same
  id where it stops at which packages to require.
- The cells are reachable. Of five project testing queries put to
  `ArchitectureHints::find()` on an extension path, four reach the right ones:
  "how do I test my extension" and "add functional tests to the extension" reach
  `project-extension-tests`, "set up phpstan for our extension" reaches
  `extension-static-analysis`, "browser tests for the site package" reaches
  `browser-tests`.
- The one that misses — "Set up tests for our site package extension" — comes
  back with `sitepackage-layout` and `sitepackage-initial-content`. That is
  ranking: "site package" is the more discriminating term in a corpus where two
  hints are named after it. A storage dimension does not change which term wins.
- The rot the axis was argued for already has a mechanism, and three of them. A
  statement carries `since` and `until`, `TestSuiteHints` filters the suites by
  the target major, and `src/Upkeep/TestingFramework.php` pins one
  `typo3/testing-framework` release line per covered major with
  `bin/cli catalog:check` reading it back.
- Since `D-KNW-007` the cells say which column they are: `core-tests` declares
  `core` and is labelled a convention when it reaches an extension answer,
  `project-extension-tests` declares `extension`.

## Decided

- No tooling dimension. What it would add over the current shape is a second
  place to say what `scope`, `since`/`until` and the ids already say, and every
  cell would have to carry its own versions — the expensive half, for a crossing
  the answers already make.
- The row is crossed by routing rather than by structure: a tool that owns one
  cell names the ids of the others when the caller is not in its column. That is
  what `typo3_test_run_guide` does, and it is what a new cell has to do.
- The ranking miss is a matching question and is queued as one.

## Assumed

- Six places for one row is readable as long as each says who it is for and each
  names the others. That is a property of the prose rather than of the model, so
  nothing but a reading catches it going wrong.

## Wrong if

- A cell is added that no answer routes to, so a caller reaches it only by
  guessing its words — the failure `hints:probe` exists to make visible.
- Two cells of the row start disagreeing about the same fact for the same
  audience, which is what a single stored dimension would have prevented and six
  places cannot.

## Covered by

- `HintsTest::theTestApiAProjectWritesItsTestsWithReachesTheProjectHint`
- `HintsTest::aProjectExtensionIsToldHowToGetASuiteAtAll`
- `ScopeTest::noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt`
- `ScopeTest::aStaticAnalysisQuestionFromOutsideTheCoreIsSentToItsOwnCell`
