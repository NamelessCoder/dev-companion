---
id: D-KNW-036
title: The standards check handed over is the one that cannot pass empty
date: 2026-08-03
status: confirmed
coveredBy:
  - KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold
---

# D-KNW-036 — The standards check handed over is the one that cannot pass empty

**`cgl -n` is the coding-standards check this server hands over, and `cglGit` is
kept where the entry has room to name the checkout it needs.**

`cglGit` reports SUCCESS from a git worktree having read no file, and a session
that trusted it reported a standards check as passed having run none.

## Evidence

- Reproduced in this repository's own `.checkouts/main`, which is a git worktree
  of `.checkouts/typo3.git`: `CI=true ./Build/Scripts/runTests.sh -s cglGit`
  prints `fatal: not a git repository: (null)`, then
  `cglFixMyCommit.sh: No PHP files to check, all is well.`, then `SUCCESS`, and
  exits 0. It does the same after `composerInstall`, so the green does not
  depend on the fixer being absent.
- The mechanism is the mount, not the worktree. `runTests.sh` runs the container
  with `-v ${CORE_ROOT}:${CORE_ROOT}`, the worktree's `.git` is a file naming a
  gitdir outside `CORE_ROOT`, and git inside the container cannot follow it. The
  same `git diff-tree --no-commit-id --name-only -r HEAD` run on the host in
  that worktree answers with the two files of the last commit.
- `cglFixMyCommit.sh:129` and `cglFixMyCommitFileHeader.sh:129` are the same
  five lines: an empty `DETECTED_FILES` prints "all is well" and exits 0.
- The control, in the same worktree: `-s cgl -n` reported
  `Found 0 of 6271 files that can be fixed in 14.330 seconds` and SUCCESS. It
  asks git nothing — it runs php-cs-fixer over the paths its config names.
- `checkGitSubmodule` from the same worktree fails rather than passes:
  `git submodule status` prints one line of `fatal:`, the script counts lines,
  and it reports "Found a submodule definition in repository". A false red,
  which a session can see.
- `checkExtensionScannerRst`, named as a suspect in the report, does not ask git
  anything: `runTests.sh` runs `extensionScannerRstFileReferences.php`, which
  reads the files itself. `checkGruntClean`, `checkIsoDatabase` and
  `checkCharsetTables` do run git in the container and cannot work from a
  worktree either; none of the three is recommended anywhere in `knowledge/`,
  and `runTests.sh` already calls the first of them CI-only.

## Decided

- The `checks` of the patch-submission intent hand over `cgl -n`. A `checks`
  entry is a bare command string with nowhere to put a condition, so what goes
  there is the command that holds from either kind of checkout.
- `cglGit` stays in `knowledge/test-suite-hints.json` and in
  `typo3-core-scripts.md`, where the entry has prose around it, and both now
  name the worktree condition beside the command. It is the faster variant and a
  session in a normal checkout should be able to reach it knowingly.
- The prose document leads with `cgl -n` rather than `cglGit`, because the first
  command in a section is the one that gets copied.
- Running `Build/Scripts/cglFixMyCommit.sh` directly, outside the container, was
  considered and is not what gets handed over. It is what `cglGit` runs, and on
  the host git resolves the worktree, so the file list is correct: in
  `.checkouts/main` it found the commit's files and got as far as php-cs-fixer.
  What it needs instead is the branch's PHP on the host — `main` pins 8.5 and
  the host has 8.3, so it stopped at Composer's platform check. That trades the
  worktree condition for a host-PHP condition, which is the condition
  `runTests.sh` exists to remove, and it is the same defect as offering a
  command the caller's checkout does not have (`R-KNW-024`).
- The todo asked for this in the `checks` array of the hint corpus. That array
  left the corpus with `D-KNW-031`, so the recommendation lives in the two JSON
  files and the one document instead, and the todo was read against the
  repository rather than followed.

## Assumed

- A normal checkout is the case `cglGit` was written for, and CI runs it there,
  so nothing is lost by keeping it. Nobody has checked what it does in a
  container-in-container CI where the mount may differ again.
- `cgl -n` over the whole tree is cheap enough to be the default. Measured at
  fourteen seconds of fixer time on `main`, against a `cglGit` run whose cost
  was never measured because it read nothing.

## Wrong if

- A session reports a standards check as passed and the patch is rejected in
  review for a formatting defect, which would mean the false green survived
  somewhere this did not reach.
- `cgl -n` starts reporting files a session did not touch, which would make the
  full-tree dry run useless as a pre-push check and put `cglGit` back on the
  table with its condition.
- A caller in a normal checkout reads the worktree condition and stops using
  `cglGit` at all, which would mean the qualification cost the fast path rather
  than making it safe.

## Confirmed on 2026-08-24

A core patch session in `/home/benji/projects/typo3-cms` reached for the direct
invocation this entry rejected, and its user corrected it to the container
twice. It filed four feedback in thirty-five minutes doing so —
`2026-08-24-104546`, `-111637`, `-111728` and `-112010` — and the last one
settles the thread on the reading this entry already held: `cglGit` is that
script, so the container costs nothing and removes the host-PHP condition. None
of the three **Wrong if** above had happened. The session had run `cglGit` in
the review and still did not know the two were one command.

What it took the session four round trips and two corrections to establish was
the identity, which the corpus stated in
`knowledge/documents/core/testing/scripts.md` and nowhere the task passed. The
suite entry in `knowledge/test-suite-hints.json` carried the worktree condition
and not this, against the rule `R-KNW-049` states in its own second paragraph:
nothing carries a caller from the entry that offers a command to a sentence
elsewhere in the corpus. The entry now says what the suite runs, in the sentence
before the condition.

The last feedback also asked for the direct script to be named for the two file
lists the suite cannot pass, `-f cache` and `-f stdin`. That is refused: it puts
the invocation back into the corpus carrying the host-PHP condition this entry
was written to keep out, and neither is what the thread went wrong on.

Naming the script moved one recorded answer, which is the price of saying it
here. `typo3_task_guide` keeps the four best-scoring suites and matches a query
term as a substring, so `cglFixMyCommit` scores on the "Fix" of "Fix the query
that reads the events" and takes `composerInstall` out of that answer. The name
is what the sentence is for, so it stays; what a matcher does with a script name
is a finding about the matcher.

## Since then

On 2026-08-25 a core session established this entire finding again from scratch,
and asked this server nothing while doing it. `feedback/2026-08-25-114910` is a
session whose task was whether `-s cglGit` runs correctly in a git worktree of
the core checkout. It reports the mount contract, the empty file list, and the
SUCCESS on nothing as things it had to prove itself — with a worktree run
against the main checkout, `"Found 0 of 5 files"` beside
`fatal: not a git repository: (null)`, which is what this entry measured three
weeks earlier. Its own "single answer that would have shortened this session
most" is a paraphrase of the sentence the corpus already carried.

That is not a gap and none of the three **Wrong if** above happened. Re-run here
on 2026-08-27 with the session's own words, `typo3_script_lookup` answers
"cglGit in a git worktree" and "runTests.sh container mount" with both sections,
and `typo3_test_run_guide` states the mount contract in the preconditions block
it prints before any suite and the false green in the `cglGit` entry. What the
session says is that nothing was asked, which is the report beside it —
`feedback/2026-08-25-114653`, on `typo3_script_lookup` never being called in a
session about nothing but its subject — and that one is judged under its own
card.

What the re-run did find is a sentence of the corpus that was wrong in this
entry's own area. The *Pre-Commit Hook* section named `-s cglGit` as what
settles the hook's file-header error, and `cglGit` runs
`Build/php-cs-fixer/config.php`, which carries no header rule: the header check
is `-s cglHeader` over `Build/php-cs-fixer/header-comment.php`. Read in
`.checkouts/main` on 2026-08-27, and the pair of scripts the hook runs is one
per message. So the **Decided** above — `cglGit` stays where the entry has prose
around it — held for the condition and not for the config: the prose named it
for a check it does not perform.

What kept the wrong sentence there is `R-KNW-024`: prose may name only a suite
`knowledge/test-suite-hints.json` declares on every covered major, and neither
header suite was declared at all. Both are entries from the same commit, and
`bin/cli versions:check` reads them against `.checkouts/` on all four.

`cglHeaderGit` carries its worktree condition beside the command, which is what
`R-KNW-049` demanded of both suites and nothing held: the test read the
documents for `cglGit` alone, and `cglHeaderGit` does not contain that string.
It reads every suite of `GIT_DRIVEN_SUITES` now, which is the list it was
already using on the JSON half.
