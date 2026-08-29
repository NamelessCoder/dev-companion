---
id: R-SKL-029
title: "A skill reading a project checks its pinned versions against the day's release"
status: open
---

# R-SKL-029 — A skill reading a project checks its pinned versions against the day's release

**A skill that reads a project checks the versions it pins — node, the GitHub
Actions, DDEV, the libraries — against the current release, and reports the ones
behind.**

The check is made on the day, never against a number in the skill. A skill is
installed into somebody else's project, so a version written into it is not
corrected by the next release of this server, and a project that follows it is
pinned to whatever was current when the file was published.

What comes of it is a finding with the raise offered, not a raise carried out.
What the session was asked to do decides that, and what the installed TYPO3 and
the project's own supported lines require is a reason that can speak against
raising at all. Where the raise leaves what was asked for, the session asks
instead of widening its own task.

`R-COD-004` is the same demand on this repository, and `R-ANS-037` on what an
answer may name.

## From

The maintainer's instruction of 2026-08-29, and the session behind
[the feedback of 2026-08-19](../../feedback/archive/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md),
which was asked to take an extension's dependencies to their newest versions and
found no skill covering the build those versions sit in.

## Held by

- Nothing yet. No published skill's checklist names the check, and no test reads
  the skills for it.
