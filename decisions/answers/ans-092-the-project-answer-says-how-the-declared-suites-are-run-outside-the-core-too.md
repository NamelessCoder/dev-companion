---
id: D-ANS-092
title: The project answer says how the declared suites are run outside the core too
date: 2026-08-21
status: open
coveredBy:
  - ProjectTest::aDeclaredSuiteOutsideTheCoreIsToldWhatARunNeedsFirst
  - HintsTest::aSuiteThatWillNotStartIsAnsweredBeforeTheHarnessIs
---

# D-ANS-092 — The project answer says how the declared suites are run outside the core too

**`typo3_project_describe` points a repository outside the core at what its
declared test commands need before their first assertion, as it already points a
core checkout at `typo3_test_run_guide`.**

That arm of the answer exists only for a core checkout. Everywhere else the same
answer lists `test:php:functional` beside the rest and says nothing about how it
is run, while the knowledge that would have answered it sits one lookup away.

## Evidence

- `feedback/2026-08-19-094248` reports eight round trips in an extension
  repository working out how to run its own suite.
  `composer test:php:functional` returned 114 tests and 114 errors with
  *Database credentials for tests are neither set through environment variables,
  and can not be found in an existing LocalConfiguration file*, which the
  session nearly filed as a repository defect before diagnosing it as
  environmental.
- The tool the feedback names was passed over correctly. `typo3_test_run_guide`
  opens its description with *Say what this core checkout needs*, and a path
  that reads as a project or an extension already gets a decline rather than
  commands. Calling it would have cost the session a round trip and answered
  nothing.
- The answer is here and a caller who asks reaches it.
  `bin/cli hints:probe "composer test:php:functional fails with 114 errors database credentials"`
  returns `project-extension-tests` as its only hit, at a text score of 297.
  That hint states the five environment variables, the message that does not
  name them, and that the account has to be allowed to create a database per
  test class — root rather than the user the site runs as, under DDEV.
  `knowledge/documents/extension/testing/phpunit.md` says the same under
  *Database credentials for the functional suite*.
- Nothing routed the session there, and that reproduces today.
  `typo3_task_guide` with
  `task="full audit of the blog extension before its v14 release"`,
  `changeType="audit"` and one `packages/blog/Classes/` path matches
  `routing-request-handling` and `extension-ter-release` and no test hint, and
  its `nextTools` names `typo3_project_describe`, `typo3_extension_describe`,
  `typo3_changelog_lookup`, `typo3_hint_lookup` with the file paths,
  `typo3_task_guide` again and `typo3_feedback_record`. Its checklist carries
  *Run the checks this repository declares rather than recommending them* and
  names no answer for one that will not start.
- `ProjectDescribe::suites()` is where the core checkout gets that pointer, and
  it returns the empty string for every other kind of instance. The answer that
  lists the declared commands therefore says how they are run only where the
  suites are the core's.
- `D-ANS-086` decided the other precondition on the same answer — the PHP bound
  Composer wrote into the vendor tree — and rejected widening
  `typo3_test_run_guide` for the reason this feedback ran into.

## Decided

- Step 3 of the ladder, routing. The answer exists, and the surface that should
  have named it is the one the caller was already in.
- The pointer belongs on the project answer rather than on the task brief. It is
  the one surface that knows which kind of checkout it read, so it can name an
  extension-only lookup without handing it to a core patch review.
- Rejected, again: renaming or widening `typo3_test_run_guide`, which this
  feedback asks for first. Its suites are `Build/Scripts/runTests.sh`
  invocations and that script is in the core repository — `D-ANS-086`. `core`
  stays out of the name because this server is about the core throughout, so the
  segment separates nothing.
- Rejected: adding `typo3_test_run_guide` to the audit brief's `nextTools`,
  which this feedback asks for second. `TaskGuide` drops it outside the core
  through `Scope::isCoreOnly`, and putting it back would hand over commands that
  do not exist in that repository.
- Rejected: a line in the `audit` intent's `tools` or `checklist`. Both filters
  in `TaskGuide` only drop core-only lines outside the core, and neither drops
  an extension-only line inside it, so the pointer would reach a core patch
  review as well.
- Not step 1b. No tool and no skill is missing: `typo3_project_describe` already
  owns what this repository declares and whether it starts, and
  `typo3-extension-testing` already owns running a suite.
- The two things the feedback wants that this repository has not established
  stay out of the change and are the todo's first step: that `ddev exec` refuses
  from a git worktree, and that the PHP a container runs and the PHP a lockfile
  resolves PHPUnit for can disagree.

## Assumed

- That a caller looking at a declared test command has the project answer in
  front of it. The brief names it first, and that tool's own description ends by
  telling the caller to call it before recommending or running a check. Whether
  the reporting session called it is not in the report.
- That the credentials are what such a command actually stops on. One report
  says so and the hint states it.

## Wrong if

- The session that hits this never calls `typo3_project_describe` and reads
  `composer.json` itself. The pointer would then be written where nobody stands,
  and the routing would have to move to the brief after all — which needs a
  scope filter the intents do not have.
- A declared test command stops on something else in the repositories people run
  this against: a container the suite cannot reach, a driver it is not
  configured for, a fixture path. The pointer would then name the wrong half of
  the problem.
- The lookup is read and abandoned. `project-extension-tests` is titled *Setting
  a Test Suite Up in an Extension* and its first three hints are about writing
  the harness, so a caller whose harness exists may stop before the credentials.
  It would then take no round trip off anybody, which is the measure `D-FBK-027`
  sets.

## Since then

The pointer was written, and the fourth **Wrong if** was taken off the table
rather than left standing: `project-extension-tests` now opens on the suite that
will not start — the credentials, the account, the interpreter — and the harness
half follows it. What decided how much could be added there was the matcher and
not the prose. The body had room for two statements and not three: at three, the
dilution weight put *live database versus test database* below `MIN_COVERAGE`
and the hint stopped answering a question it owns.

The two facts the todo held back were measured on 2026-08-21 against DDEV
v1.25.1, in a project started for it and deleted afterwards. `.ddev/config.yaml`
is the one file DDEV writes that a repository tracks, so a `git worktree` names
the same project at another approot and both `ddev exec` and `ddev describe`
stop with *a project (web container) in running state already exists*. The
`docker exec` the reporting session fell back to reaches the container, and what
is mounted there is the original checkout — so it does not run the worktree's
sources either, which that session did not find out. The way out is the
published port: `ddev describe -j` carries it as `raw.dbinfo.published_port`,
and over it the project's own database user answered *Access denied* on a create
while root did not.

The interpreter half was read rather than run. `vendor/phpunit/phpunit/phpunit`
compares `PHP_VERSION` before the autoloader is reached, so the mismatch is
PHPUnit's own message and `composer/platform_check.php` never gets to report it;
`typo3/testing-framework` admits several PHPUnit majors at once, each with a
higher PHP floor than the last, and Composer picks between them against the PHP
it is itself run on. The hint carries the mechanism and no version numbers,
which is what `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` asks of
a statement whose numbers move.
