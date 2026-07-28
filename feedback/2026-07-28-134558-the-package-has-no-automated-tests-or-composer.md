---
date: 2026-07-28T13:45:58+00:00
category: bug
status: open
tool: servertestsuite
---

# The package has no automated tests or Composer test/static-analysis scripts even though search ra...

## Observation

The package has no automated tests or Composer test/static-analysis scripts even though search ranking, translation reference mapping, commit validation, feedback file safety, and the MCP transport contain behavior that can regress. The current wrong label reference and low-relevance lookup behavior are examples that contract tests would catch.

## Suggestion

Add unit tests for Knowledge, ArchitectureHints, TestSuiteHints, catalog ranking, CommitMessage and Feedback; golden/contract tests for every tool; and stdio MCP smoke tests covering tools/list, resources/read, schema validation and error responses. Run them in CI across supported PHP versions.
