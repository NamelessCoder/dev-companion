---
id: R-ANS-001
status: held
restsOn: [D-ANS-001]
---

# R-ANS-001 — "Could not ask" never looks like "does not exist"

**An empty answer that means "could not ask" is never shaped like one that
means "does not exist".**

Every installation-backed tool carries `answeredBy`, and every count and every
boolean it declares is null where nothing was consulted. A `matchCount` of 0
says the installation has none, which is the statement `found: false` makes and
is not made about an installation nobody asked. Collections stay empty rather
than null: a caller loops over them, and the count beside them is what carried
the claim.

`answeredBy: "nothing"` is written in `Result\Unanswered` and nowhere else, so
it cannot be reached by a path that has no reason to hand over. The reverse
failure is the same one: a miss labelled `nothing` reports an installation that
answered as one that could not be asked.

## From

Two feedback asking that the unavailable case stop looking like an empty one,
and a client that twice concluded an extension registered no icons and no
labels (2026-07-29). Reading the tools against it on 2026-08-02 found the shape
still being made: `typo3_icon_lookup` answered a directory with no installation
with `matchCount: 0`, `suggestionCount: 0` and `exactMatch: false` — byte for
byte the miss it emits against a reachable one — and `typo3_extension_scope`
reported `answeredBy: "nothing"` for every miss, including against an
installation that had just listed 27 packages.

## Held by

- `ToolContractTest::everyClaimAnInstallationBackedToolMakesCanBeWithheld`
- `ToolContractTest::nothingIsClaimedAboutAnInstallationThatWasNeverAsked`
- `ToolContractTest::onlyTheUnansweredResultReportsThatNothingAnswered`
- `ScopeTest::anUnconsultedConfigurationPathIsNotReportedAsAbsent`
