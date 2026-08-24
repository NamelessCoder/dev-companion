---
id: R-ANS-034
title: 'A suite an answer offers says what running it does to the checkout'
status: open
judged: 2026-08-24
restsOn: [D-ANS-099]
---

# R-ANS-034 — A suite an answer offers says what running it does to the checkout

**Every test suite this server hands over carries what running it does to the
checkout, in the values a declared command already carries.**

A task told not to change files runs the checks that hand the code back as they
found it and no others — `D-EVI-003` — and reads that off `runs` on every
command `typo3_project_describe` lists, which `R-PRJ-007` puts there. Where the
checks are `runTests.sh` suites, which is every core patch, nothing carries the
property and the instruction is one the caller has to settle by reading the
script.

The name does not carry it and never will. `build` regenerates the committed
JavaScript and `lintTypescript` reads it, one word each; `checkGruntClean` is a
check by its name and runs `git add *` over the working tree.

## From

`feedback/2026-08-24-100604`, a Gerrit review on 2026-08-24 that had to leave
the tree under review untouched. It was offered `-s build` first, established by
hand that the suite rewrites tracked files, and built a detached worktree to run
it in; `checkGruntClean` it found by reading `Build/Scripts/runTests.sh`, and
never in an answer. `D-ANS-099` carries the measurements, the three suites that
run git and what the values are.
