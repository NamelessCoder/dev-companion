---
id: D-ANS-117
title: The commit draft names the workflow that owns the commit
date: 2026-08-27
status: open
coveredBy:
  - CommitMessageGuideTest::theDraftNamesTheGuideThatOwnsTheWorkflowItWasAskedWith
---

# D-ANS-117 — The commit draft names the workflow that owns the commit

**`typo3_commit_message_guide` names `typo3_task_guide` and the workflow the
commit belongs to, because that answer is the one a session under momentum still
asks for.**

The imperative to make both opening calls was in this session's context from the
first token, is quoted back in its report word for word, and produced neither
call in five turns.

## Evidence

- **The session.**
  [`feedback/archive/2026-08-25-114735`](../../feedback/archive/2026-08-25-114735-the-mandated-opening-calls-project-describe-and.md),
  `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`. The task was whether
  `./Build/Scripts/runTests.sh -s cglGit` runs inside a worktree of the core
  checkout, then the fix, then the commit. Roughly 49 tool calls, 46 of them
  `Bash`, and two MCP calls — both `typo3_commit_message_guide`, both in the
  final turn, both made only after the user challenged the work.
  `typo3_project_describe` and `typo3_task_guide`: zero.
- **The instruction was delivered and read.** `knowledge/server-scope.json`
  opens its `start` with "Start every task with typo3_project_describe" and
  carries "Then call typo3_task_guide for the workflow the task belongs to, and
  again at the first test, check, commit or shipped file the task did not name".
  The feedback quotes that second sentence verbatim and calls it right. So this
  is not step 4: the imperative
  [`D-AUD-012`](../audience/aud-012-the-second-call-of-the-entry-point-is-an-imperative.md)
  wrote arrived, was read, and did not take.
- **The turn it had to fire on was turn 3, thirty calls in.** The session
  reports the task changing shape under it — diagnosis, then a second fix, then
  "ich denke wir sollten beides zusammen nehmen", which is where it became a
  core patch with a commit message and a Releases decision. Its own reading:
  mid-session the trigger fires against momentum rather than against an empty
  context.
- **The competing channel is outside anything this server publishes.** The
  session names its harness prompt for that turn — do the work through `Bash`
  wherever `Bash` can — and says the gravity of it pulled the whole session to
  the shell. That is the row of 2026-08-25 in
  [`D-SKL-033`](../task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md),
  read on the tool half instead of the skill half.
- **The cost is counted in the report.** Asking at draft time in turn 3 rather
  than under challenge in turn 5 would have saved the user a pushback, saved the
  session a wrong claim about its own subject line, and saved one full turn.
- **The answer it did receive carries nothing onward.** Read in this checkout on
  2026-08-27, `src/Tool/CommitMessageGuide.php` names `typo3_rule_lookup` with
  `documentId` "core/contribution/commit-messages" in its `Source`, and the
  string `task_guide` is in no description, no schema and no line of its text.
- **The same placement is already made for a neighbouring tool.**
  [`D-SKL-038`](../task-skills/skl-038-the-change-answer-names-the-skill-that-owns-the-patch-it-describes.md)
  put the two core patch skills and the call the order opens on into
  `typo3_gerrit_lookup`, on this reasoning: the answer of the tool the session
  did call is the shortest channel this server has.
- **A second session asks for the same channel.** `feedback/2026-08-24-173211`
  proposes that the answer of any lookup touching changelog, testing or
  contribution carry a one-line pointer at the procedure, from a task that
  hand-assembled the changelog conventions in six calls with the guide id
  sitting in a list it had already been given.
- **A name delivered in a tool's own answer has failed once.** `D-SKL-033`'s row
  of 2026-08-19: `typo3_task_guide` answered
  `skills: ["typo3-content-element-development"]` and the session went on
  through three more tools and `Bash` without a `Skill` call. That is an
  activation, which `D-SKL-033` puts with the client and the model. A **tool**
  named in a tool's answer has not been measured either way.

## Decided

- Step 2, delivery. Not 1a, because nothing about the core commit workflow is
  missing — `typo3_task_guide` answers it and
  `core/contribution/commit-messages` holds the conventions. Not 1b, because the
  tool exists and `guide` is its verb. Not 3, because the routing entry exists
  and the session quotes it. Not 4, because the wording was already rewritten to
  the imperative and this session read that wording.
- The placement is `typo3_commit_message_guide`'s answer, at the phase rather
  than at the opening.
- **The orientation answer is not reopened.**
  [`D-ANS-091`](ans-091-the-project-answer-leaves-the-second-call-to-the-instructions.md)
  declines a sentence at the foot of `typo3_project_describe`, and this decides
  a different position: an answer asked for mid-task, at the phase it belongs
  to. That answer never reached this session, so nothing here is evidence about
  the one that did not.
- Queued rather than closed on the spot. It changes
  `src/Tool/CommitMessageGuide.php` and the output schema it declares, which is
  the bullet
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  left standing.
- Bounded to the workflow the call already carries. `workflow` is a parameter of
  the call, so the answer knows which workflow the commit belongs to without
  guessing, and the line is written for the one it was asked with.
- The priority is `normal`, set by the second session asking for the channel. It
  is one placement on one tool rather than a domain, so it does not go above
  that.
- What the line says, and whether it is prose or a field, belongs to the work. A
  judgement ends at the diagnosis, and `R-ANS-002` is what decides the second
  half.
- No requirement is written yet, and the reason is the one
  [`D-ANS-031`](ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md)
  gives for refusing to widen `R-GUI-003`. The general demand — a guide answer
  naming the tool that owns the workflow it drafts for — reaches every `guide`
  tool, and one of them has evidence. The demand is written when the work has
  shown its shape.
- The report's other half is answered rather than queued. It asks that
  `typo3_test_run_guide`'s description carry the characters "runTests.sh" if the
  tool covers the dispatcher, and that description has opened "Say what this
  core checkout needs before a test can run at all, and which
  Build/Scripts/runTests.sh commands to run once it can" since `ff83bb4d` on
  2026-08-08, seventeen days before the session. The feedback is trimmed to the
  half above.
- The report's note that `typo3_test_run_guide` never fired across eight runs of
  the dispatcher stays where it is. It is a second instance of the same failure
  inside one session rather than a half of its own — the session made no call at
  that phase at all, so there is no answer to place a pointer on, and what the
  routing already does there is
  [`D-ANS-031`](ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md).

## Assumed

- That an answer read under challenge in turn 5 would have been read at draft
  time in turn 3. The session says so and nothing records what it would have
  done.
- That the harness prompt is what won rather than what the session reconstructed
  afterwards. Its account is what it believed at the debrief, which is the limit
  `D-SKL-033`'s row of 2026-08-25 draws on reading a cause out of one report.
- That a tool named in a tool's answer fires where a skill named there did not.
  Nothing has measured the two apart, and the one measurement on record is the
  skill half failing.

## Wrong if

- A session holding a commit draft that names `typo3_task_guide` writes the
  message without asking for the workflow. The pointer would then be delivered
  and not taken, which is step 4 and a rewrite rather than a placement.
- The next session of this shape reports the harness prompt again with the
  pointer in place. Then the competing channel wins wherever this server stands,
  and no answer of it reaches a session under momentum.
- A run counts the two opening calls and comes back at the ratio `D-ANS-091`'s
  first **Wrong if** names. The instruction channel is then spent, the
  orientation answer is the lever, and this placement is the narrower half of a
  change that had to be made anyway.
- `typo3_commit_message_guide` stops being what a session under momentum reaches
  for at the commit. The pointer would then sit on an answer nobody asks for.

## Since then

Built on 2026-08-27: the guide names the brief and what one for these paths
adds, in the words of the workflow the call was asked with — the core arm the
sweep, the coverage and the suites, the project arm the conventions that
transfer.

It fires on every answer, because this server sees one call rather than a
session and nothing here can tell which is the first. The line is prose for a
client that renders the text and a field for one that renders the payload and
drops it, which is the reading `R-ANS-002` was written from, and the record is
the shape the brief already answers its own next calls in.
