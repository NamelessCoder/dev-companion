---
id: D-ANS-099
title: 'A suite that stages the working tree is offered marked rather than withheld'
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aTypeScriptChangeIsOfferedTheSuiteThatStagesTheWorkingTree
  - HintsTest::everySuiteSaysWhatRunningItDoesToTheCheckout
  - HintsTest::theNoteOnConfirmingASuiteSaysWhyGreppingTheCaseLabelMisses
  - HintsTest::thePreconditionSaysWhatABareWorktreeRunsWithoutASetup
---

# D-ANS-099 — A suite that stages the working tree is offered marked rather than withheld

**`typo3_test_run_guide` marks every suite it returns by what running it does to
the checkout, and offers the ones that run git rather than leaving them out.**

A task told not to change files runs the checks that hand the code back as they
found it and no others — `D-EVI-003` — and reads that property off `runs` on
every command `typo3_project_describe` lists. Where those checks are
`runTests.sh` suites, which is every core patch, no answer here carries it, so
the instruction is one the caller has to settle by reading the script.

## Evidence

- `feedback/2026-08-24-100604` reports a Gerrit review whose whole point was to
  change nothing. It was offered `-s build` first, worked out by hand that the
  suite regenerates the committed JavaScript, and set up a detached worktree in
  a scratchpad to run it in. `checkGruntClean` it found only by reading
  `Build/Scripts/runTests.sh`, and it says the suite's `git add *` would have
  staged an untracked `response.json` sitting at the repository root.
- Re-run on 2026-08-24 with the feedback's own arguments — the two form-wizard
  paths, `targetVersion="15"`. Seven suites come back: `build`,
  `lintTypescript`, `unitJavascript`, `e2e`, `e2e-prepare`, `e2e-browser` and
  `npm`. Each record carries `suite`, `command`, `targeted`, `description`,
  `whenToUse`, `domains` and `versions`, and none of those says what a run does
  to the tree. `checkGruntClean` is in neither the answer nor
  `knowledge/test-suite-hints.json`.
- Nothing in the corpus says it either.
  `bin/cli hints:probe "which runTests.sh suites rewrite tracked files"` and
  `bin/cli hints:probe "checkGruntClean"` both matched nothing on 2026-08-24.
- The class is three suites wide rather than one, measured in `.checkouts/` on
  2026-08-24, main at `v14.3.0-531-g3cbdea24dd`. `checkGruntClean`,
  `checkIsoDatabase` and `checkCharsets` each end in `git add *`, and the last
  two open with `git checkout -- composer.json; git checkout -- composer.lock`,
  which discards uncommitted edits to those two files before anything is
  generated. The first two are on all four covered majors, `checkCharsets` from
  14.
- `build` rewrites tracked files. Its body is
  `cd Build; npm install && npm run build`, and what that writes is the
  committed JavaScript below
  `typo3/sysext/*/Resources/Public/JavaScript/`.
- The property exists one tool over. `typo3_project_describe` carries `runs` per
  command as `check`, `change` or `unknown`, read off the declared body and
  never by running it — `R-PRJ-007`.
- The near-miss is a second gap in the same answer. The session tried to confirm
  `-s build` with `grep -n "^    build)" Build/Scripts/runTests.sh`, got
  nothing, and came one step from filing a correct answer as wrong. The label is
  `build*)` on 13.4, 14.3 and main, and 12.4 carries `buildCss)` and
  `buildJavascript)` instead, so that grep finds nothing on any covered branch.
  The invocation note that settles it says `runTests.sh -h` lists what the
  branch supports; it does not say that this is how a suite is confirmed to
  exist, or that a case label is a glob pattern.

## Decided

- Step 1a, gap, with the shape missing beside it. The fact is in
  `Build/Scripts/runTests.sh` and in no file here, and
  `Schema::testSuiteRecord()` has no field to put it in — so writing it into
  `description` would leave it as prose, which is what a caller told not to
  change files cannot filter on.
- Queued rather than taken on. The tool exists and already returns the suites;
  what changes is a field on a declared `outputSchema` and entries in
  `knowledge/test-suite-hints.json`, which are reviewed rather than improvised.
- Priority `normal`, off the `low` the card arrived at. What sets it is the harm
  rather than a second report: three suites run git against the caller's working
  tree, two of them discard uncommitted edits to `composer.json` and
  `composer.lock`, and `skills/base.md` sends every task to run the checks it is
  offered.
- The values are `typo3_project_describe`'s, so that a caller reading both
  answers reads one model: `check`, `change` and `unknown`, in the readings
  `R-PRJ-007` gives them.
- A fourth value for the suites that run git, rather than folding them into
  `change`. What is at stake there is the working tree and the index rather than
  the sources, and a review free to rewrite generated files is still not free to
  stage them. What it is called belongs to the work.
- A test suite is `unknown` for the reason `R-PRJ-007` already gives: it runs
  the core's own code, and nothing in the script covers what that code writes.
- `checkGruntClean` is added to what a change below `Build/Sources/TypeScript`
  gets back, carrying the mark. It answers whether the committed JavaScript is
  in sync with its source, which is the standing obligation of such a patch, and
  withholding the one suite that answers it leaves the caller to find it in the
  script — which is what happened.
- The mark is data before it is prose. The text half says it beside the command,
  because that is where a caller about to paste one is reading.
- The near-miss is the same card's second step and the cheaper one: the `-h`
  note is rewritten to say how a suite is confirmed on a branch, and that
  grepping the case label misses a glob.
- What the feedback asks for third — that the node suites run in a bare worktree
  while the PHP ones need `-s composerInstall` there first — is queued as
  something to establish rather than as something to write. The report states it
  as an inference that held once.

## Assumed

- That a suite body is readable the way a declared command is. `R-PRJ-007` reads
  a manifest line; a `runTests.sh` case is a shell body assembled into
  `COMMAND=` and handed to a container, which is the same kind of reading and
  not the same code.
- That reading it beats running it, which is `D-EVI-003`'s own **Assumed** for
  the project half: what is claimed is that the sources come back unchanged, not
  that the filesystem does.
- That the four values are what a caller acts on. Nothing on record yet shows a
  session filtering a suite list by such a mark, as against the project
  commands, where three runs that ran nothing are what `D-EVI-003` was written
  from.

## Wrong if

- A run reports a checkout modified, or an index staged, by a suite this answer
  marked as reporting only.
- No caller ever separates the fourth value from `change`, which would say the
  distinction is this repository's rather than the caller's, and `change` with a
  sentence was enough.
- `checkGruntClean` comes back on changes it cannot answer for, so that a
  warning is most of what the suite list carries.
- A branch adds a suite whose body cannot be read this way — a case dispatching
  to a script outside the file — often enough that `unknown` is the common
  answer, in which case the field says nothing where it is needed most.
- A session grepping for a suite is misled again with the `-h` note rewritten,
  which would say the note is not where the session was looking.

## Since then

Built on 2026-08-24. The fourth value is called `git`, because what the three
suites have in common is the command they run and not one effect of it: two
discard uncommitted edits before the `git add *` they all end in. Every suite in
`knowledge/test-suite-hints.json` carries `runs`, read off its body in
`.checkouts/` on the same day.

`checkIsoDatabase` and `checkCharsets` stayed out of the suite list. Nothing
narrows to them — the hints are a curated set, and a php-domain entry would
offer them to every PHP patch, which is this entry's third **Wrong if**. They
are named in an invocation note instead, because `runTests.sh -h` does hand them
over.

The third ask was settled by reading rather than left open. The node suites run
npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked,
and install their own `node_modules`, so a bare worktree runs them and a PHP
suite still needs `-s composerInstall` there first. `invocation.preconditions`
says both halves and says which of the two was read rather than run.
`checkGruntClean` is the exception the same reading found: from a worktree its
git calls fail inside the container, the way `cglGit`'s already do.
