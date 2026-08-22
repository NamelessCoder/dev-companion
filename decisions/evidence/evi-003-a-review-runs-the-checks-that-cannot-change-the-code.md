---
id: D-EVI-003
title: A review runs the checks that cannot change the code
date: 2026-07-31
status: confirmed
coveredBy:
  - ProjectTest::aCommandThatWritesIsNeverReportedAsACheck
---

# D-EVI-003 — A review runs the checks that cannot change the code

**A task told not to change files runs the project's own commands that are
declared to hand the code back as it was, and no others.**

The property is reported by `typo3_project_describe` per command, read off the
declared body.

Three recorded `REVIEW-02` runs read "do not change files" as "run nothing" and
quoted the commands as subjects of findings instead.

## Evidence

- The three runs — bootstrap_package at 02:55 and 08:15, syntax at 12:21 —
  executed none of the ten and five commands they were offered. Of the fifteen,
  `composer cgl:ci` (`php-cs-fixer --diff -v --dry-run fix`) and
  `composer test:php:lint` (`phplint`) appear in both repositories and rewrite
  nothing, and in the syntax run two findings about php-cs-fixer and phplint
  were derived from CI configuration that either command would have settled in
  one invocation. Against them: `composer cgl` is the same tool without the
  flag, `composer set-version` and `composer changelog` write, and
  `composer test` reaches a functional suite. The two groups are one flag and
  one subcommand apart and no script name separates them.

## Decided

- The checks are run, and the answer says what they printed. The objection that
  a failing command tells you less than the configuration that would make it
  fail is about what a finding says, not about whether to gather it, and
  survives as a limit: the finding still names the configuration, and the run is
  what takes it from derived to established. The objection that a review must
  not change files is answered by the property rather than by abstaining from
  all of them. Rejected: running everything, which breaks the instruction the
  user gave; running nothing, which is what was measured; and asking the user
  per command, which is a question the answer could not have been formed against
  before `typo3_project_describe` carried `runs`.

## Assumed

- That a body declaring a check is one. `php-cs-fixer --dry-run`,
  `phpstan analyze`, `phplint` and `eslint` without `--fix` do not rewrite the
  sources they are pointed at — but they are configurable, a fixer can be given
  a rule set that writes elsewhere, and a caching checker leaves a file behind.
  What is claimed is that the code comes back unchanged, not that the filesystem
  does.

## Wrong if

- A run reports a checkout modified by a command marked `check` — the
  classification would then be a promise the declaration cannot carry, and what
  is left is naming the command and letting the user decide. Or the reverse:
  reviews that now run the checks report the same findings they read out of CI
  files, in which case the runs cost time and settled nothing, and the base's
  three-way distinction was doing all the work by itself.

## Since then

The first **Wrong if** was gone looking for instead of waited for, on
2026-08-02, and the classification did carry it: a declared line was read as the
tool in front of it, so `phpstan analyse && php-cs-fixer fix` and four shapes
like it answered `check` while rewriting the sources. The **Assumed** held per
tool and broke on the line — chaining is the convention in a `package.json`,
where `tsc --noEmit && vite build` is one script. Every command on a line is
read now, and `ProjectTest::aCommandThatWritesIsNeverReportedAsACheck` holds the
writers to never getting that answer, which is what makes waiting for a run to
report a modified checkout unnecessary. The decision stands: what was wrong was
the reading, not that the declaration can be read.

## Since then

A review did run one of those checks and it would not start.
`feedback/2026-08-18-113412` reports `composer cgl:ci` aborting in the vendor
tree's `platform_check.php` on an interpreter a minor below what the installed
packages require, so a command offered as safe to run never reached the code it
was pointed at. Neither **Wrong if** covers it: the classification was right,
and the checkout came back unmodified because nothing ran at all. What it shows
is that the property answers what a command does to the sources and never
whether it can start here, which is the second question
[D-ANS-086](../answers/ans-086-the-project-answer-carries-the-bound-that-stops-a-declared-command-from-starting.md)
puts into the same answer. The decision stands; what was missing is a number
beside the property rather than a different reading of it.

## Confirmed on 2026-08-22

The mechanism holds. `runs` is declared per command as `check`, `change` or
`unknown`, read off the body in `Project::runs()`, and
`ProjectTest::aCommandThatWritesIsNeverReportedAsACheck` still keeps the writers
off the first value. Neither **Wrong if** has fired: no run has reported a
checkout modified by a command marked `check`, and the one failure since — a
command that would not start, in the section above — is neither of them.

The tool is `typo3_project_describe` now, renamed by `D-SCO-011` after this was
written, and the paragraph under the statement still calls it
`typo3_project_describe`. The record is left as it read; this line is where a
reader finds out.
