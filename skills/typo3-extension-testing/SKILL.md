---
name: typo3-extension-testing
description: Add, repair, review, or run tests for a TYPO3 project or extension, including PHPUnit unit and functional tests, extension test infrastructure, fixtures, architecture checks, browser tests, accessibility tests, and CI test commands. Use whenever work touches Tests/, Build/phpunit/, phpunit configuration, TYPO3 testing-framework, Playwright, test coverage, failing tests, or choosing the checks for a TYPO3 change.
---

# TYPO3 Extension Testing

Build the smallest useful test at the correct layer and run only commands the
project actually declares. Keep this skill as routing and test strategy; never
retain version-specific APIs, paths, or commands that the installation or
checkout owns.

## Establish the test surface

1. Call `typo3_project_scope` before recommending a command. Record the TYPO3
   version, PHP constraint, own extensions, and declared Composer/npm scripts.
2. If an extension is in scope, call `typo3_extension_scope` with its key.
3. Inspect the changed or target code, nearby tests, PHPUnit configuration, and
   existing fixtures in the checkout. The MCP server does not read the working
   tree.
4. Call `typo3_task_guide` with a short English description, affected area,
   target version, and change type to establish task-shaped checks.
5. Call `typo3_architecture_lookup` with the concrete paths and an English task
   description about extension testing.
6. Call `typo3_documentation_lookup` with several short English queries and the
   target TYPO3 version when framework setup, bootstrapping, fixtures, or an API
   needs confirmation.

If a lookup is unavailable, state the gap separately from a lookup that found
no match. Do not replace current project evidence with remembered TYPO3 setup.

## Choose the test layer

Read [references/checklist.md](references/checklist.md) when selecting layers
and commands or auditing coverage.

- Prefer a unit test for isolated logic without TYPO3 state or persistence.
- Use a functional test when TYPO3 bootstrapping, configuration, database
  schema, DataHandler, repositories, services, or integration between framework
  components is part of the behavior.
- Use a browser test for rendered user journeys, backend interaction, JavaScript,
  or accessibility behavior that cannot be established below the UI.
- Add architecture or static checks only when the project already uses them or
  the task explicitly includes establishing that infrastructure.
- Preserve a regression test that fails for the observed defect before applying
  its fix when practical.

Follow nearby, passing project tests rather than inventing a parallel harness.
Do not attach TYPO3 core-only `runTests.sh` suites to project or extension work.

## Implement and verify

- Keep fixtures minimal and deterministic. Avoid depending on unrelated site
  data, execution order, wall-clock timing, or external services.
- Put reusable setup at the narrowest scope that removes meaningful duplication.
- Test observable behavior and public contracts; avoid assertions tied only to
  implementation details.
- Run the narrowest relevant test first, then the declared containing suite.
- For browser work, run the project's existing E2E command and report any
  environmental prerequisite distinctly from a test failure.
- Report the exact commands run, results, and any checks not run with the reason.

This skill owns test changes and test execution. For a broad conformance audit,
documentation rewrite, or backend-module implementation, hand that work to the
corresponding skill and retain only the testing part.
