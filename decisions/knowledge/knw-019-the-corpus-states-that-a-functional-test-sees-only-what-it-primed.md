---
id: D-KNW-019
title: The corpus states that a functional test sees only what it primed
date: 2026-08-02
status: open
---

# D-KNW-019 — The corpus states that a functional test sees only what it primed

**A functional test's database holds nothing but what that test imported, and
the corpus states every fixture rule without ever stating that premise.**

The premise is queued as a statement of its own, beside the rules that rest on
it. A session that does not hold the premise reads the fixture rules as a
convention it may also achieve another way. It then fetches records nobody
primed, and the failure looks like a broken query rather than an empty table.

## Evidence

- Of the three things the feedback names, two are answered today.
  `bin/cli hints:probe "correct request type in a functional test"` reaches
  `project-extension-tests`, which states the request type outright:
  `$this->executeFrontendSubRequest(new InternalRequest($uri))`, with the
  cache-hash conditions of a hand-built query string beside it. `DataHandler`
  against a direct `INSERT` is stated on `datahandler-persistence`, which ends
  on "what makes DataHandler the right way to seed and a direct INSERT the wrong
  one".
- The fixture rule is stated too, on `core-tests`: "State is set up and asserted
  with CSV fixtures, not hand-written inserts", with `importCSVDataSet()` in
  `setUp()` and `assertCSVDataSet()` for the result.
- The premise under that rule is stated nowhere.
  `bin/cli hints:probe "empty database per test run"` reaches
  `datahandler-persistence` on text alone and neither test hint. A search of
  `knowledge/` and `skills/` for an empty, truncated or primed database finds
  one sentence, on `project-extension-tests`, and it is about the file-backed
  caches that survive the truncation — what outlives a test rather than what a
  test starts with.
- The skill the reporting session names is silent in the same way.
  `skills/typo3-extension-testing/references/phpunit.md` asks for fixtures that
  are "minimal, deterministic, and explicit about their expected result" and for
  state that survives between tests to be reset. Neither sentence says the
  database begins with nothing in it.
- The premise holds and can be read precisely, which is what makes it a
  statement rather than a guess. On `.checkouts/testing-framework/9` at tag
  `9.6.1`, `FunctionalTestCase::setUp()` builds the instance and the schema for
  the first test of a class and, for every test after it, calls
  `initializeTestDatabaseAndTruncateTables()` — the comment above `$isFirstTest`
  says so in those words. `$initializeDatabase = true` is a property a test
  class may turn off.
- Where the statement lands is a question the word counts already narrow.
  `core-tests` is 212 words and its `appliesTo` already carries
  `importCSVDataSet`, `dataset` and `csv`; `project-extension-tests` is 947,
  which is past the 544-word ceiling `todo/progress/2026-08-02-163000` measured,
  so text added there answers fewer queries than it costs.
- The remaining halves of this feedback have owners.
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  maps the series: the live-database inserts are the subject of `003216`, the
  per-class test databases and the lost track of manual edits are `003929`. That
  nothing reached this session at all is `003356` and `003533`, which report
  that no skill was activated and no lookup was made before the user asked for
  one.

## Decided

- Step 1a of the ladder, and queued. The ladder was walked on the premise, not
  on the rules: the rules are all here, which is why what lands is one statement
  beside them rather than a hint of its own.
- Not closed on the spot. The statement is about `typo3/testing-framework`
  behaviour, so the run that writes it reads the package across the covered
  lines, and this run read one tag of one line.
- Nothing is derived from the request-type half. The query above is the re-run
  that answers it, and its cost to the session belongs to the two feedback that
  report why nothing was called.
- No card is written for DataHandler seeding or for the test databases. Both are
  a sibling's whole subject, and a second card for one step is the overlap
  `bin/cli todo:claim` warns about.
- The feedback is not archived. The priming half is real and open, one todo
  serves it, and that is
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant held rather than a state left behind.

## Assumed

- That the premise belongs on `core-tests`, beside the fixture rule it explains.
  The alternative is the hint a project session actually arrives at, and
  `project-extension-tests` opens by saying the conventions of a core test hold
  there too.
- That stating the premise would have changed this session. Nothing measures it.
  The same session reports that it called nothing before it was told to, so a
  premise it never read would have failed the same way.

## Wrong if

- The reading finds the premise is narrower than one sentence. A class with
  `$initializeDatabase = false`, a snapshot taken through
  `withDatabaseSnapshot()`, or a record the framework imports on its own would
  each make "only what the test primed" too strong as phrased.
- The statement lands on `core-tests` and a session working a project extension
  never reaches it. The gap was then a placement, step 2, and the hint it was
  written on is the evidence.
- `003929` writes the same premise while stating the per-class database model.
  The corpus then carries it twice, in two hints, and neither says which is the
  one to correct.
