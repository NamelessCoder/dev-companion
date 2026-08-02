---
id: R-KNW-024
status: held
---

# R-KNW-024 — A check is offered only where the command exists

**A check is offered only where the command exists.**

Every check this server carries is a `runTests.sh` invocation, and which suites
that script offers changes between majors — so a check names a suite, and the
suite carries the range. The binding is declared once, in
`knowledge/test-suite-hints.json`, and every hint and intent that names the
suite in `-s <suite>` inherits it; the suite listing itself is filtered the
same way and carries its range where it has one. A command the caller's
checkout does not have is not a weaker answer than none — it sends them to
debug their own checkout for something this server invented for another branch.

The prose documents cannot inherit anything, so the rule reaches them as a
restriction instead: a markdown document may name a suite only where every
covered major has it, and a narrower one belongs in the hints, where
`targetVersion` can pick the right command.

## From

Seven checks naming a suite absent from at least one covered branch, found
while unifying the obligation vocabulary — a 13.4 core contributor asking about
labels was handed `runTests.sh -s checkIntegrityXliff`, which arrives in 14
(2026-07-30).

## Held by

- `HintsTest::aCheckIsNotOfferedOnABranchWhoseScriptHasNoSuchSuite`
- `HintsTest::theSuiteListItselfIsFilteredByTheBranchItIsAskedFor`
- `KnowledgeTest::noProseDocumentNamesACheckOnlySomeBranchesHave`
