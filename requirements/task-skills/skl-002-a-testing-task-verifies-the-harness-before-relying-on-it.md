---
id: R-SKL-002
title: 'A testing task verifies the harness before relying on it'
status: held
---

# R-SKL-002 — A testing task verifies the harness before relying on it

**A project or extension testing task verifies the harness for the behavior's
required layer before relying on it.**

Missing or broken infrastructure is established or repaired when changes are in
scope, then the requested coverage is added or extended without replacing
working tests and commands. Unit and functional harnesses stay with the
extension; browser harnesses stay with the runnable project. Every newly
established layer has a meaningful local proof before CI calls the same command,
and review-only work reports setup defects without changing them.

Static analysis and coding standards are a layer of this workflow and are
established when the task asks for them, whether or not the project already runs
one. What is missing is read off what a complete check surface covers, each
check gets one project-owned command, the command that reports stays apart from
the one that writes, a new finding is fixed rather than recorded in a baseline,
and automatic formatting stays inside the first-party files. Where the check is
new to a repository that does not yet pass it, the conformance commits come
first and the commit adding the check comes last, verified by running it at the
new HEAD, so no commit fails the check it introduces.

## From

`EXT-05`, `SITE-06`, `SKILL-05`, `SKILL-06`, `SKILL-08`; a request for one
testing skill that can add or extend PHPUnit and Playwright coverage while
checking and repairing its setup (2026-07-30); and two recorded `REVIEW-02` runs
in which a missing static-quality workflow surfaced as a missing test workflow
and was declined here (2026-07-30). The commit order came from
`feedback/2026-08-04-055741`: a session establishing a fixer and an editorconfig
check in `/home/benji/projects/ext-guidedtour` found the page said to split the
formatting pass off and nothing about which half goes first, worked the order
out itself, and reported that anyone landing a first check on a non-conformant
repository has to.

## Held by

- `SkillTest`
- `InstallerTest`
