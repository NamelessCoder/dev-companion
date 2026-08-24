---
id: D-SKL-062
title: 'The workflow question is asked again on a new subject'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefNamesTheActsTheWorkflowQuestionIsAskedAgainAt
---

# D-SKL-062 — The workflow question is asked again on a new subject

**`typo3_task_guide` is named again where the work enters a subject the opening
did not name, because the moment is what this server places and the wording
already fits.**

A skill is chosen against the request, and the work is what would have matched
it: the request is the least informative moment of the session, and it is the
only one this server points at.

## Evidence

- **The session.** `/home/benji/projects/blog` on 2026-08-18,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-18-081159`](../../feedback/archive/2026-08-18-081159-skills-are-matched-once-against-the-opening.md).
  A German symptom report about two TypoScript conditions, one file selected in
  the editor. What followed was an API removal on a major, two new test
  directories, the repository's declared check suite, and a commit on a branch —
  three subjects with an owner here, none of them named in the opening line. No
  skill activated, and the same session called nothing at all
  ([`feedback/2026-08-18-080710`](../../feedback/archive/2026-08-18-080710-a-whole-session-ran-with-zero-calls-because-a.md)).
- **The brief answers the sub-step and not the opening.** Measured in this
  worktree on 2026-08-18, through `TaskGuide::answer()`. "Add unit tests for a
  TypoScript condition provider in an extension" with the test path is
  recognized as `tests`, names `typo3-extension-testing` and the guide
  `extension/testing/phpunit`; so is "run the declared quality checks of this
  extension: phplint, phpstan, php-cs-fixer, unit and functional tests", which
  adds `coding-standards`. The session's own opening — "fix TypoScript
  conditions broken on TYPO3 v14 while keeping them working on v13", with the
  provider path — matches no intent, names no skill and names no guide.
- **Every channel that names the tool names it at the start.** The
  `instructions` of `knowledge/server-scope.json` open "Start every task with
  typo3_project_describe" and continue "typo3_task_guide then gives the workflow
  the task belongs to". Its `routing` entries fire on starting a core task, on
  taking a patch on, and on upgrading an installation.
  [`skills/base.md`](../../skills/base.md) is headed "Where every task starts",
  and its step 3 asks for the guide once per session.
- **Not step 1b.** The skill is published, the intent that routes to it matches,
  and the tool answers — the three measurements above are that answer. A
  `bin/cli hints:probe` of the feedback's own subject reaches nothing and
  returns its 98 candidates as the index, which is right: what is missing is not
  a statement about TYPO3.
- **The description route is closed.** The twelve published descriptions cost
  3595 characters of the 3600
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows, measured the
  way that test measures on 2026-08-18. `D-SKL-033` decided against paying
  listing characters for more request shapes, and mid-task entry points written
  into a description are more of them.
- **The second session of the shape.**
  [`feedback/2026-08-18-113343`](../../feedback/archive/2026-08-18-113343-no-skill-covers-reviewing-an-incoming-pull.md)
  reports it from another checkout and another task: "a skill is chosen before
  any of them are read: only the PR URL", everything else found during the work.
  Its own subject is a missing owner, and this half of it is not that.

## Decided

- **The ladder's step 2, delivery.** The answer is here, it is one call away,
  and it reaches a session only where something asks for it — which everything
  here does at the opening. What is missing is the moment rather than a tool, a
  skill or a word in a description.
- **The moments are the caller's own acts**, which this server can name without
  seeing the checkout: the first file created under a test directory, the first
  run of a check the repository declares, the first branch or commit, the first
  edit to the documentation the package ships. A subject cannot be named the
  same way — the session is inside one before it can say which.
- **The placement is weighed across three channels and the shape is the todo's
  first step.** The `instructions` are the one that reaches a session which
  calls nothing, which is what this one was (`D-AUD-003`); a `routing` entry
  reaches a session that read the scope; and the brief's own answer reaches one
  that called once, where `D-GUI-012` already put the guide pointer. One
  sentence in one place, because three that each say it differently are three
  things to keep true.
- **Queued rather than made on the spot.** The strongest placement is the
  brief's own answer, which is `src/`, and what the `instructions` say is what
  every client receives at initialize — both are what
  `documentation/records/judging.rst` keeps off the spot.
- **At `normal`.** Two sessions in two checkouts report the same asymmetry, and
  the words that would have matched the work were already in the file.
- **Against the feedback's own suggestion.** Mid-task entry points written into
  the descriptions cost characters that are not there, and they would say in
  twelve installed files what one sentence here says once.

## Assumed

- That a session which passed over an instruction at the opening reads one at
  the moment the act happens. Nothing here measures that, and it is the same
  assumption `D-GUI-012` records for the guide it moved into the brief.
- That the sub-step reaches the intent matcher in the session's own words. The
  three briefs above were composed from task texts written here, and a session
  mid-task writes its own.
- That the acts above are ones a session notices itself taking. Creating the
  first test file is visible in the transcript and nothing in this server sees
  it.

## Wrong if

- A session reports the re-ask in its context and a test directory created
  without it. Then the moment is not the lever either, and what is left is the
  client's own activation, which `D-SKL-033` stops at.
- A brief called mid-task names a skill that does not own the sub-step. Then the
  recognition is a search rather than a placement, and the acts are matching
  words instead of work.
- Sessions start calling the guide at every file they open. Then the moments
  were written as subjects after all, and what they cost is the round trips
  `D-FBK-027` weighs a tool against.

## Since then

### 2026-08-18 — the placement is the brief, and the instructions are full

**The sentence is a `nextTools` entry of `typo3_task_guide`, naming the tool
again with the four acts as its `when`.** The three channels were weighed
against what each costs and reaches today, and two of them lose:

- The `instructions` are full. Measured in this worktree: 1830 characters plain,
  2038 in the case `ScopeTest::theInstructionsFitWhatAClientKeeps` sizes them
  against — a caller that excluded every tool, in a project whose skills are
  stale — of the 2048 a client keeps. A sentence there costs a sentence that is
  already there, which is the trade `R-DIS-025` was the last to make.
- A `routing` entry reaches a session that called `typo3_server_scope`, and that
  table is read at the opening if at all. `R-GUI-003` is the same finding from
  the other side: the pointer to `typo3_commit_message_guide` stood in the
  routing table, and the session wrote four commit messages hours later out of
  the brief, which is why the brief carries it.

**It is the same list the commit message pointer sits in**, unconditional and
outside the core as readily as in it — the acts are the caller's own, so none of
them is a step `Scope::isCoreOnly()` drops. What is not written is a second copy
in the checklist: the checklist is the order of this task, and asking again is
what happens when the task is no longer the one the brief was composed for.

### 2026-08-24 — the acts were taken by a session the placement cannot reach

[`feedback/2026-08-24-133515`](../../feedback/2026-08-24-133515-a-full-core-patch-session-ran-green-with-zero.md)
is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`: a core patch carried
to green — two classes changed, a test added to an existing case, a new
functional test case, unit, functional, cgl and phpstan all run — with zero
calls to this server and no skill opened.

Two of the four acts were taken. The new functional test case is the first file
under a test directory, and `Build/Scripts/runTests.sh` is the first run of a
check the repository declares. The sentence naming them sits in
`TaskGuide::answer()`'s `nextTools` and reached nothing, because it rides on an
answer and no answer was asked for.

That is the placement's first recorded cost, and it is narrower than the
**Decided** bullet reads. A session that calls once is served. A session that
calls nothing is not, and it is the case `D-AUD-003` named as the reason the
entry point sits in the `instructions` at all.

None of the three **Wrong if** fires: each describes the re-ask arriving and not
working, and here it did not arrive.

The room was re-measured in this worktree on 2026-08-24 — 1943 characters plain,
2028 in the case `ScopeTest::theInstructionsFitWhatAClientKeeps` sizes against,
of the 2048 `R-ANS-013` holds. So the trade above stands as a trade: twenty
characters is not a sentence. What nobody has weighed is binding the imperative
already there to the acts as well as to the opening, which is a rewrite rather
than an addition, and that is the queued step.

The session named two acts of its own, and one of them is outside the four: the
first edit to code the package ships. It is nameable without seeing the checkout
the way the other four are, and whether it belongs beside them is part of the
same step rather than something this reading settles.
