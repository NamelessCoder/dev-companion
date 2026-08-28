---
id: D-ANS-113
title: 'A suite whose mark warns the caller off names what answers its question instead'
date: 2026-08-26
status: open
coveredBy:
  - HintsTest::everySuiteThatRunsGitNamesTheDocumentAnsweringItsQuestion
---

# D-ANS-113 — A suite whose mark warns the caller off names what answers its question instead

**A suite this server marks `runs: git` carries the way to the same answer that
leaves the checkout alone.**

`checkGruntClean` is the only suite that answers whether the committed
JavaScript still matches its TypeScript source, and its warning is the last
thing any answer says about that question. Three sessions read the warning, did
not run it, and each invented the same replacement.

## Evidence

- `feedback/2026-08-24-205223` reviewed Gerrit 95392 in the user's own checkout,
  which held 23 branches of their work. `typo3_task_guide` and
  `typo3_test_run_guide` both returned `checkGruntClean` with the `git add *`
  warning, the session did not run it, and it reports the warning as correct.
  What it did instead was `git show` of the committed `.js` across the change,
  then a `difflib` match over the two minified one-line revisions printing the
  differing region with context. It took three attempts to get readable output
  out of a one-line 130 KB diff.
- `feedback/2026-08-25-110726` is the same review's strength report. It names
  the warning as "the concrete disaster the server prevented" and says it
  answered the question by tokenising the minified file and diffing.
- `feedback/2026-08-25-110635` is the 14.3 backport of that same change, a
  different task on a different day. It names `feedback/2026-08-24-205223` and
  says the alternative is the same one: a throwaway worktree off the target
  branch, `-s build` inside it, then a token-level diff of the minified file
  against the branch's committed version.
- Read on 2026-08-26 in `knowledge/test-suite-hints.json` as it stands. The
  `checkGruntClean` entry carries `runs: git`, and its `whenToUse` ends "Run it
  in a checkout whose index you can throw away, and not in one holding work of
  your own. A git worktree is not the way out". Nothing in that entry and
  nothing in `invocation.notes` names another way to the answer.
- Nothing in the corpus names one either.
  `bin/cli hints:probe "verify committed JavaScript matches its TypeScript source without running a build"`
  reached `backend-typescript` and `backend-ui` on 2026-08-26, and neither says
  how.
  `bin/cli hints:probe "diff a minified generated file against the committed version"`
  reached `extension-repository-layout`, which is about a different subject.
- `D-ANS-099`'s third **Wrong if** was read against this and is not satisfied.
  `checkGruntClean` came back on a change it does answer for, and the warning
  stopped a run that would have staged somebody's working tree.
- The feedback's second half was settled by reading `.checkouts/` on 2026-08-26.
  `Build/Scripts/runTests.sh` carries `PHP_VERSION="8.5"` on main and
  `PHP_VERSION="8.2"` on 13.4 and on 14.3, and main's `composer.json` has
  required `^8.5` since `195d480f44` of 2026-07-16 — 187 commits behind main,
  which is the distance the report gives for the base it ran from. The pre-raise
  script accepts `-p 8.5`, so the option is a way past the platform check on the
  revision that hits it.

## Decided

- Step 1a, gap, for the alternative. The warning was delivered, read and acted
  on, and reported as a save; what is missing is the sentence after it, which is
  in no file here.
- The answer is a document rather than a hint. What the three sessions were
  missing is a procedure and not a statement, and the caller is the one who was
  lost, so it is `knowledge/documents/` — `D-FBK-043`.
- Taken on rather than closed on the spot. What the document says about the core
  has to be verified in `.checkouts/`, and the run that judged this has read
  nothing about how `-s build` behaves from a worktree beyond one report.
- The two feedback are one gap, so one card carries both.
  `feedback/2026-08-25-110635` names `feedback/2026-08-24-205223` itself and
  reports the same replacement from another task, so the document covers
  verifying a committed artifact and rebuilding one, and the second card is
  deleted by the commit that writes this — `R-FBK-014`.
- Priority `normal`, off the `low` the card arrived at. Three sessions in two
  days from two task shapes reported it, and the one concrete harm on record was
  avoided by a session reading a warning rather than by anything this server
  could repeat.
- `feedback/2026-08-25-110726` keeps its own card and its own judgement. Three
  of the four saves it reports are about other answers, so it is evidence here
  and not this gap.
- The PHP half is settled here rather than queued. The reading is in
  `.checkouts/`, this run made it, and what it changes is a precondition string
  — `D-FBK-052`.
- That precondition states the mechanism and one measured instance. A default
  PHP version per covered branch is a number that turns on the next raise, and
  nothing would fail when it did.

## Assumed

- That the replacement is one procedure and not three. The three sessions
  differed in how they diffed — a match over the region, a split on separators,
  a token count per side — and what they share is the shape rather than the
  command.
- That `-s build` in a throwaway worktree reproduces the branch's committed
  output. `feedback/2026-08-25-110635` reports one run on 14.3 where it did, and
  that is the claim the document has to verify rather than repeat.
- That a document is where the caller looks. The procedure is reached from a
  task rather than from a query, so it depends on being named from the
  `checkGruntClean` entry and from `build`'s.

## Wrong if

- A session offered `checkGruntClean` with the alternative beside it runs the
  suite in a working checkout anyway, which would say the warning rather than
  the dead end was doing the work.
- A session reads the document and still invents its own diff, which would say
  what was missing is discoverability rather than the procedure.
- `-s build` from a worktree turns out not to reproduce the committed file, in
  which case a throwaway clone is the only honest answer and the document says
  that instead.
- A branch drops `-p` or narrows the versions it accepts, so the way past the
  platform check named in the precondition does not exist where it is needed.
- No session ever reaches an older revision without rebasing first, which would
  say the platform check is a failure nobody meets.

## Since then

Measured in throwaway worktrees off the bare repository, one at each tip: the
build ran in both without an install, succeeded, and left the tree clean. So the
second **Assumed** holds and the third **Wrong if** does not — the procedure is
a worktree and not a clone.

Reverting one commit's hunk and building again modified the source and the one
generated file belonging to it. The cleanliness suite was run in the same
worktree to see what its warning is: the build succeeded, every git call failed
on the worktree's gitdir, and the suite reported failure over a clean tree.
