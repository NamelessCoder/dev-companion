---
id: D-KNW-067
date: 2026-08-10
status: open
---

# D-KNW-067 — The JavaScript test layer is a hint, and a test query still answers from PHP

**The core's JavaScript unit test layer is one hint in the TypeScript domain;
the PHPUnit hints arriving beside it are a domain-detection question and stay
open.**

A session told by `backend-typescript` to write JavaScript unit coverage asked
how, and got PHPUnit: `UnitTestCase`, CSV fixtures, `createStub`. It read the
layer out of the checkout itself.

## Evidence

- `feedback/2026-08-10-101644`. The larger half of the answer was about a test
  framework the task could not use, and the one call the session made carried
  nothing about where a `.ts` test goes.
- The layer is uniform across the covered branches, so it is one unbound hint.
  `Build/web-test-runner.config.mjs` exists on `main`, `14.3`, `13.4` and `12.4`,
  each building one group per package under `Build/Sources/TypeScript` that has
  a `tests/` directory, over `tests/**/*.ts`, each with the same import map onto
  `Resources/Public/JavaScript/` and the same `"test": "wtr"` script. Only the
  `~labels/` middleware is younger — absent on `13.4` and `12.4`, where no
  TypeScript source names `~labels` at all — so that statement alone is bound.
- `runTests.sh -s unitJavascript` runs `npm run test` in `Build/` and passes no
  arguments through on any of the four, which is why the hint says a targeted
  run means calling the runner directly rather than offering a flag.
- The PHPUnit hints are not a ranking accident. `Domains::detect()` reads
  `unit test` as a PHP keyword, deliberately, since `D-KNW-009`: those phrasings
  are how somebody with no suite yet asks. A `.ts` path does not take it back,
  so the query selects the PHP domain and every PHPUnit hint becomes a
  candidate.

## Decided

- One hint, `javascript-unit-tests`, in the `typescript` domain and `core`
  scope. It carries where the file goes, what discovers it, that the import map
  points at built output so the branch has to be built first, the `~labels` stub
  and the `@open-wc/testing` plus mocha idiom.
- The suite entry for `unitJavascript` names it. `typo3_test_run_guide` answers
  how a suite is run and this hint answers how one of its tests is written; the
  session that has the first has no reason to guess that the second exists.
- The hint says what the layer cannot see, and names `browser-tests` for it.
  The same session's other report — no procedure for looking at a backend change
  in a real browser, `feedback/2026-08-10-101714` — is the layer above this one,
  and a session that reads this hint should not conclude a passing wtr run
  covers a positional defect.
- No skill and no document. What was missing is a set of statements about one
  layer, which is what a hint is; the order of the work was never in question.
- The domain half stays open, with a todo on it. Dropping the PHP domain
  whenever the paths are TypeScript would change the ranking of every query
  carrying a testing word, which is `src/` and is reviewed rather than
  improvised.

## Assumed

- That the hint arriving first is enough for the case it was filed from. The
  session's own call put `unit-test-doubles` at the top; it now ranks below
  `javascript-unit-tests`, with the PHPUnit hints still in the answer.

## Wrong if

- A session reads this hint and still writes the test against the TypeScript
  source rather than the built output. Then build-before-test is not carried by
  a sentence in a list and belongs where the suite is run.
- The PHPUnit hints go on being acted on for a TypeScript task. Then the
  domain change is the fix and the hint was only the half of it that was cheap.
