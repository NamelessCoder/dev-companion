---
date: 2026-07-28T15:09:53+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 0f4287c
subject: "Say how a core test is actually written"
tool: typo3_rule_lookup
---

# Testing is the single most-touched area of core patches and the thinnest part of this knowledge b...

## Observation

Testing is the single most-touched area of core patches and the thinnest part of this knowledge base. I measured 18 months of core history: Tests/** is the top changed bucket by a wide margin, 8285 file touches, more than Fluid templates (1678) and Build/Sources/TypeScript (1671) combined. Against that, the server holds nine generic bullets across "Testing" and "Testing Strategy", at the level of "add unit tests for isolated logic". A query for "functional test fixtures csv datasets base class" returns matchCount 0. What is missing is the entire concrete idiom of core functional tests, verified at the pinned revision: tests extend TYPO3\TestingFramework\Core\Functional\FunctionalTestCase (611 classes) or UnitTestCase (686); state is set up and asserted with CSV fixtures — 1998 .csv files under Tests/, 975 importCSVDataSet() calls and 1637 assertCSVDataSet() calls — and this is the expected way to write persistence tests, not hand-rolled inserts; the environment is declared through protected array $coreExtensionsToLoad (167 uses), $testExtensionsToLoad (155) and $configurationToUseInTestInstance (52); DataHandler behaviour has its own base class AbstractDataHandlerActionTestCase (16). An agent that only reads the current hints will write a functional test that looks nothing like its neighbours and will be sent back in review. Separately, both sections still recommend "acceptance or UI tests". The acceptance and acceptanceInstall suites no longer exist in Build/Scripts/runTests.sh on this branch — they were replaced by the Playwright e2e suite, which typo3_core_run_tests_help already knows about. The rules text and the suite list disagree.

## Query

query="functional test fixtures csv datasets base class" (0 matches); query="testing" (2 generic matches)

## Suggestion

Deepen the testing knowledge to the level the rest of the catalog already has, and make it retrievable under keywords like fixture, dataset, csv, FunctionalTestCase, test setup: which base class to extend for which kind of test; the importCSVDataSet/assertCSVDataSet pattern with a minimal example and where fixture CSVs live relative to the test class; the $coreExtensionsToLoad / $testExtensionsToLoad / $configurationToUseInTestInstance properties; and AbstractDataHandlerActionTestCase for DataHandler scenarios. Also replace the "acceptance or UI tests" wording with the e2e suite so the rules stop naming a suite that no longer exists.
