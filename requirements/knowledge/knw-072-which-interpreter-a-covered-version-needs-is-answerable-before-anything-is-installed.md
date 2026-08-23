---
id: R-KNW-072
title: 'Which interpreter a covered version needs is answerable before anything is installed'
status: held
restsOn: [D-KNW-086, D-KNW-091]
heldBy:
  - HintsTest::eachCoveredLineCarriesItsOwnFloorAndTestedRange
  - HintsTest::whichInterpreterAVersionNeedsIsAnsweredFirst
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# R-KNW-072 — Which interpreter a covered version needs is answerable before anything is installed

**What PHP a covered TYPO3 line requires, resolves against and runs its own
suites on is answerable without an installation.**

The number is chosen at the moment the container is declared, which is before
there is anything to ask: an installation lookup answers no-installation there,
and by the time it answers at all the container has been built and the number
has stopped looking like a decision. So the answer belongs in the corpus, bound
per line, and the workflow step that declares the container is where it is
fetched.

Three numbers are owed and they are three claims. The constraint
`typo3/cms-core` declares is the requirement, and nothing else is.
`config.platform.php` is what the core repository resolves against, which is a
property a project has only if it sets the key. The `-p` option of
`Build/Scripts/runTests.sh` is the core testing itself and says what a branch is
exercised against rather than what the project supports. An answer that returns
the numbers without saying which is which is what the report already had.

The fourth demand is the relation, because that is the defect the report found:
a declared floor below the interpreter every configured environment runs is a
claim no run tests.

## From

`feedback/2026-08-17-211157`, whose session declared `^8.3` against a core
requiring `^8.2` and executed every command on 8.4. The half of it that derives
the same relation inside `typo3_project_describe` is a todo of its own
(2026-08-17).
