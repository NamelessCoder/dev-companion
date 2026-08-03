---
id: D-ANS-031
date: 2026-08-02
status: open
---

# D-ANS-031 — The core answer names the tool that runs the suites

**`typo3_project_scope` tells a core checkout that the core's testing suites are
not among its declared commands, and never that `typo3_test_run_guide` has
them.**

So the orphaned half of `feedback/2026-08-01-114807` is step 2 of the ladder and
is queued. A session read that sentence, went looking for a runner by hand, and
named one the checkout does not contain. The answer it held said the suites were
not there. It did not say where they were.

## Evidence

- The feedback is a whole-session transcript, so it was judged by the mapping
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  sets rather than walked down the ladder as a report. Its session is GPT-5 mini
  in `/home/benji/projects/typo3-cms`, and it filed three feedback in seven
  minutes:

  | Half of the transcript                                        | The sibling that owns it                          |
  | ------------------------------------------------------------- | ------------------------------------------------- |
  | `typo3_project_scope` fits the task and should be kept          | `114526`, in hand elsewhere; `115115` from the other session |
  | `processingTaskTypes` and `SYS.fal.processors` into that output | `114526`, whose **Suggestion** is the same sentence; `115220`, under *Requested changes to existing MCP endpoints* |
  | seven core classes read by hand to confirm the code paths       | `114526`, under *What I had to establish from elsewhere* |
  | a `grep_search` timeout and a relative-path `read_file`         | the caller's own editor, which `doesNotCover` places outside |
  | `runTests` answered "No tests found", and what to run instead   | nothing                                            |

- The last row is the first **Wrong if** of `D-FBK-021` firing. No sibling of
  this session owns it. `114526` mentions the same failure in one clause and
  files it under "not MCP issues"; the debrief here is the only place the
  session says what it concluded from it.
- What it concluded is wrong about the checkout. It reports preferring
  "`Build/bin/phpunit` or the project's `Build/phpunit/*` runner with proper
  bootstrap". `/home/benji/projects/typo3-cms` has no `Build/bin/` at all, and
  `Build/phpunit/` holds four files: `UnitTests.xml`, `FunctionalTests.xml` and
  a bootstrap for each. None of them is a runner.
- The answer is here, whole.
  `bin/cli hints:probe "run PHPUnit tests in the TYPO3 core checkout"` reaches
  `project-extension-tests`, and `knowledge/test-suite-hints.json` carries the
  invocation notes. `typo3_test_run_guide`, re-run on 2026-08-02 through
  `bin/typo3-cms-mcp` from that directory with the two processing classes the
  session read, answers `unit`, `functional` and `e2e`, each as a
  `CI=true ./Build/Scripts/runTests.sh` command with a targeted form beside it.
- One hop away, `typo3_task_guide` hands it over by name. Asked with the
  session's own task on the same day it answers "Find the file there, then ask
  typo3_test_run_guide for the targeted runTests.sh invocation", and lists that
  tool again under *Next lookups for this task*.
- Neither reaches the session. The routing line — "About to run tests or any
  other core check" — is in the `routing` block of
  `knowledge/server-scope.json`, which a caller sees only by calling
  `typo3_server_scope`. The `instructions` returned at initialize name two
  tools: `typo3_project_scope` in the imperative ("Start every task with"), and
  `typo3_task_guide` in a sentence describing what it gives. The session called
  the first and reports that no other lookup was activated.
- The answer it did hold is where the question came up. Re-run on 2026-08-02
  from the same directory, `typo3_project_scope` opens "core-checkout, TYPO3
  15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV", and its commands paragraph
  opens "Commands this repository declares — these exist here, the core's
  testing suites do not". Four `gerrit:setup` scripts follow it. No sentence in
  the answer names a tool.
- The other model in the same directory got there. `115716`,
  deepseek-v4-flash-free, nine minutes later, on the same patch:
  "typo3_script_lookup plus typo3_test_run_guide supplied the exact runTests.sh
  suites (functional/unit, cglGit, phpstan, checkExtensionScannerRst)". So the
  corpus and the routing are not what failed.
- The strength half credits the wrong tool, which is the pattern
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  names. It reports that `typo3_project_scope` "listed processors in the default
  configuration"; the re-run above lists no processor, and the sibling `114526`
  makes the stronger version of the same claim.
- Nothing about TYPO3 was established here beyond two file listings in the
  checkout the feedback was written in.

## Decided

- Step 2, delivery. Not 1a, because `knowledge/test-suite-hints.json` holds the
  invocation and the probe reaches it. Not 1b, because `typo3_test_run_guide` is
  the tool and `guide` is its verb. Not 3, because the routing entry exists and
  fired for another model in the same directory on the same patch — it sits
  behind a tool the task is not sent to, which is what step 2 describes.
- The placement is the sentence that names the absence. This is
  [`R-GUI-003`](../../requirements/guides/gui-003-a-guide-points-at-the-tool-that-performs-the-step.md)
  one directory over: the routing table is read once at the start of a session,
  and the step is taken out of whatever the last answer listed. That requirement
  is written for a brief and held by `ScopeTest` on briefs alone, and a scope
  answer that names a step is the same shape.
- Queued rather than closed on the spot. It changes `ProjectScope::text()` and
  the assertion in `ProjectTest::theAnswerNamesTheCommandsThatExistHere`, and
  [judging.md](../../documentation/feedback/judging.md) puts `src/` beyond a run
  that has read only this repository.
- The feedback's own **Suggestion** is not judged here. `processingTaskTypes`
  and `SYS.fal.processors` are `TYPO3_CONF_VARS` paths — both are in
  `typo3/sysext/core/Configuration/DefaultConfiguration.php` under `SYS.fal` —
  so what they ask of the project answer is what
  [`D-ANS-011`](ans-011-a-scope-answer-states-what-a-manifest-declares.md) keeps
  out of it and `typo3_configuration_lookup` is routed for. Saying more from
  here would be the copy-down onto two siblings' cards that `D-FBK-021` forbids.
- Nothing on another branch was touched. `114526` is in hand in another worktree
  and `115220` has a card in `todo/open/`, so neither file was edited and
  neither `Serves:` line gained this feedback.
- Whether this feedback may be archived is not decided here, and it is not the
  archiving question the three summaries in `todo/waiting/` carry. This one has
  a half no sibling owns, so it stays open behind the todo that half produced.

## Assumed

- That the session read the paragraph it was answered with. It reports the
  fields and the call, not the prose, and nothing records what a client renders.
  [`R-ANS-002`](../../requirements/answers/ans-002-the-reason-is-in-the-data-not-only-in-the-text.md)
  assumes the client that drops the text, and a pointer written into the prose
  alone would not reach one.
- That the descriptive second sentence of the `instructions` is why
  `typo3_task_guide` was never called. Nothing records why a tool was not
  called, and `D-ANS-004` caps that text at 2048 characters, so making the
  sentence an imperative is not free.
- That the three GPT-5 mini feedback are one session. They share a model, a
  directory and seven minutes, and the transcript here says the first debrief
  was its own earlier call.

## Wrong if

- A session holding a core answer that names `typo3_test_run_guide` still hunts
  for a runner by hand. The pointer would then be delivered and not taken, which
  is step 4 and a rewrite rather than a placement.
- The orphaned half turns out to have a sibling after all, in a feedback this
  reading did not open. The mapping above is then wrong and the ladder was owed
  to that sibling instead.
- The pointer belongs in the `instructions` rather than in the answer, because
  the tool a session skips is the one it was never told to call. That is a
  different change, priced against the 2048 characters `D-ANS-004` measured.
- `Build/Scripts/runTests.sh` stops being how the core's suites are run on a
  branch this server covers. The sentence would then point at a tool whose
  commands do not exist either.
