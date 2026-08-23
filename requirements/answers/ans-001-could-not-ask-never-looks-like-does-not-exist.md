---
id: R-ANS-001
title: '"Could not ask" never looks like "does not exist"'
status: held
restsOn: [D-ANS-005]
heldBy:
  - ScopeTest::anUnconsultedConfigurationPathIsNotReportedAsAbsent
  - StdioServerTest::aQuestionThatCannotBeAnsweredHereIsStillAnAnswer
  - ToolContractTest::aQuestionThatCannotBeAnsweredHereSaysOnlyThat
  - ToolContractTest::anInstallationBackedSchemaOffersEitherShape
  - ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer
---

# R-ANS-001 — "Could not ask" never looks like "does not exist"

**An answer that means "could not ask" is never shaped like one that means "does
not exist".**

A tool that cannot reach the installation answers with `unsupported` and states
nothing else: no count to read as a count, no flag to read as a fact, no empty
list standing in for a result. Beside it stands only the caller's own arguments
coming back. `unsupported` carries a `cause` — `no-installation`,
`misconfigured` or `installation-not-answering` — the reason, where discovery
looked, and what was set and could not be used.

`Result\Unsupported` is the only place that shape is built, so no path reaches
it without a reason to hand over. `answeredBy` says which of the two sources
answered and has no case for neither, because that is this key instead. The
output schema declares the result and the unsupported answer as `oneOf`, so a
hit promises every field it always did and an answer carrying both is invalid.

## From

Two feedback asking that the unavailable case stop looking like an empty one,
and a client that twice concluded an extension registered no icons and no labels
(2026-07-29). Reading the tools against it on 2026-08-02 found the shape still
being made: `typo3_icon_lookup` answered a directory with no installation with
`matchCount: 0`, `suggestionCount: 0` and `exactMatch: false` — field for field
the miss it emits against a reachable one — and `typo3_extension_describe`
reported `answeredBy: "nothing"` for every miss, including against an
installation that had just listed 27 packages.
