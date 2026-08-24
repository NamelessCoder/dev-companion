---
date: 2026-08-24T13:36:51+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# PHPStan rejected a new Core test and finding the codebase's idiom cost four round trips

## Observation

Trimmed on 2026-08-24 to the one cost that is left. Both halves this report was
named after are answered. The narrowing idiom is the `core-static-analysis`
hint: the inline `/** @var Type $variable */` above the assignment, the four
rules the core writes itself with the identifier each fails under, and the
`AGENTS.md` rule that a new finding is not silenced in
`Build/phpstan/phpstan-baseline.neon` — `D-KNW-114` carries the reading. The
routing half is a clause in each of the two descriptions naming what the other
takes, which is `D-ANS-072`.

What is left is the adjacent cost from the same session, in its own words: I ran
`./Build/Scripts/runTests.sh -s functional` over a path list that included
`typo3/sysext/tstemplate/Tests/Functional`, which does not exist. The whole
container run died on "Test file not found" and had to be repeated with the path
removed. An `ls` would have caught it; so would anything that knows which
sysexts carry a functional suite.

## Query

Never called for this half either.

## Suggestion

A caller assembling a path list gets nothing back that says a path is not there,
and the failure costs a whole container start rather than a test.
