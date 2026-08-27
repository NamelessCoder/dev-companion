---
id: R-ANS-028
title: 'An answer that names a document says how to read it whole'
status: held
restsOn: [D-ANS-061]
heldBy:
  - CommitMessageGuideTest::theCoreAnswerNamesThePageItsRulesAreWrittenIn
  - KnowledgeTest::aCutScriptSectionSaysHowToReadThePageWhole
  - KnowledgeTest::aDocumentIdReadsTheWholePageWithoutAResourceList
  - KnowledgeTest::anUnknownDocumentIdNamesTheOnesThereAre
  - KnowledgeTest::theTestRunGuideNamesTheBrowserCheckDocumentWithTheE2eSuites
  - KnowledgeTest::theTestRunGuideNamesTheRenderingProbeWithTheFunctionalSuite
  - KnowledgeTest::theTestRunGuideNamesTheScriptsDocument
---

# R-ANS-028 — An answer that names a document says how to read it whole

**A tool answering on a document's subject says how to read that document whole,
in a way that does not depend on the client rendering MCP resources.**

A `uri` is delivery to a client that lists resources. A client that lists none
leaves the caller a string it has no prior reason to read as an action, and the
rest of the document — which regularly holds the section the query was actually
looking for — is never reached.

## From

Three sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-132535`
held `typo3://guides/core/contribution/commit-messages` from two
`typo3_rule_lookup` calls and never fetched it, then finished a full patch
review having read no document end to end; the page it says it wanted is a
section of that document. `feedback/2026-08-07-130058` found
`typo3_script_lookup` returns a guide inline and still never saw one while
working, because `typo3_test_run_guide` answered first.
`feedback/2026-08-07-065313` is the same session earlier, reporting that no
resource list was rendered at any point.

**Built on 2026-08-07.** `typo3_rule_lookup` takes a `documentId` and returns
the document as written — no search, no version filter — and every answer that
carries sections names the ids they were cut from as that call.
`typo3_test_run_guide` names `core/testing/scripts` beside the invocation notes,
with the two things the guide carries and it does not, because the moment a
caller is about to run something is the one moment they are certainly reading.
`typo3_script_lookup` says the same where the section it returned was cut, which
is the case that produced the report.

**Widened on 2026-08-27.** `typo3_commit_message_guide` returns no section and
its whole subject is `core/contribution/commit-messages`, so the demand is on
what a tool answers about rather than on what it cut. Four sessions wanted that
page and none read it, one of them from inside this tool's answer
(`feedback/2026-08-25-114819`); the core answer now names it as the
`typo3_rule_lookup` call, beside the facts `D-GUI-020` inlined the day before.
The project answer does not, because the page describes the core repository and
says so in its own `whenToUse`.
