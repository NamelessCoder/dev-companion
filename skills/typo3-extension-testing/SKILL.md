---
name: typo3-extension-testing
description: Set up, add, extend, repair, review, or run tests for a TYPO3 project or extension, including missing test infrastructure, PHPUnit unit and functional tests, fixtures, architecture checks, Playwright browser and accessibility tests, and local or CI commands. Use when a project has no working test harness yet, when existing coverage must grow, or whenever work touches Tests/, PHPUnit configuration, TYPO3 testing-framework, Playwright, test coverage, or failing tests.
---

# TYPO3 Extension Testing

Establish or grow the smallest useful test surface at the correct layer. Make
the first green run part of setup, and run only commands supported by the
checkout. Keep this skill as routing and workflow; never retain version-specific
APIs, paths, dependency constraints, or commands that the installation or
checkout owns.

## Establish the test surface

1. Call `typo3_project_scope` before recommending a command. Record the TYPO3
   version, PHP constraint, own extensions, and declared Composer/npm scripts.
2. If an extension is in scope, call `typo3_extension_scope` with its key.
3. Inspect the target code, existing tests and fixtures, test configuration,
   dependency manifests, declared commands, CI, and development environment in
   the checkout. The MCP server does not read the working tree.
4. Verify that the harness for every relevant layer can discover and run its
   tests. Treat missing or broken infrastructure as a prerequisite of the
   requested testing work, not as a separate kind of task or a reason to force
   the behavior into another layer.
5. Call `typo3_task_guide` with a short English description, affected area,
   target version, and change type to establish task-shaped checks.
6. Call `typo3_architecture_lookup` with the concrete paths and an English task
   description that names the layer and whether its harness is missing.
7. Call `typo3_documentation_lookup` with several short English queries and the
   target TYPO3 version when dependency setup, bootstrapping, fixtures, browser
   configuration, or an API needs confirmation.

If a lookup is unavailable, state the gap separately from a lookup that found
no match. Do not replace current project evidence with remembered TYPO3 setup.

## Choose the layer and its owner

Read [references/checklist.md](references/checklist.md) when selecting layers,
establishing missing infrastructure, choosing commands, or auditing coverage.
After selecting a layer, read only its implementation guide:

- [references/phpunit.md](references/phpunit.md) for unit or functional tests.
- [references/playwright.md](references/playwright.md) for browser,
  accessibility, or visual tests.

- Prefer a unit test for isolated logic without TYPO3 state or persistence.
- Use a functional test when TYPO3 bootstrapping, configuration, database
  schema, DataHandler, repositories, services, or integration between framework
  components is part of the behavior.
- Use a browser test for rendered user journeys, backend interaction, JavaScript,
  or accessibility behavior that cannot be established below the UI.
- Add architecture or static checks only when the project already uses them or
  the task explicitly includes establishing that infrastructure.
- Keep unit and functional infrastructure with the extension whose PHP it
  exercises. Keep browser infrastructure with the runnable project, because it
  needs a served site rather than an extension package alone.
- Establish only the layers the task can justify. A setup request does not
  require every possible test runner.

## Establish or repair the required harness

Before adding or extending coverage, fix any missing or broken prerequisite for
the selected layer. For an explicit setup request, this is the requested work;
for a review-only request, report the defect without changing it.

1. Determine compatible development dependencies from the project's constraints,
   installed packages, Composer resolution, and versioned documentation. Add a
   dependency only when changes are in scope and the selected layer requires it;
   never guess its version.
2. Take configuration and bootstrap templates from the installed dependency or
   the source named by `typo3_architecture_lookup`. Copy and adapt templates that
   say they are examples; do not point extension suites into a core checkout.
3. Preserve working configuration, scripts, and CI. Extend them instead of
   creating a parallel harness.
4. Give each selected layer one stable local command before adding CI. Derive
   functional database settings and browser URLs from the project's environment;
   do not commit credentials or machine-specific hosts.
5. For unit or functional tests, establish the suite configuration, bootstrap,
   test directories, extension loading, and environment the returned guidance
   requires. Never translate a core-only `runTests.sh` command into an extension
   command.
6. For browser tests, require a runnable site and establish project-owned runner
   configuration, scripts, artifacts, and one real target. Choose host, container,
   or dedicated browser image from the project rather than imposing one topology.
7. Make CI call the same commands that passed locally. Add a version matrix only
   for combinations the package declares and the dependency solver accepts.

## Add or extend tests

- Follow nearby passing tests and the established harness. If the required layer
  is missing, establish it first instead of forcing the behavior into a cheaper
  layer.
- Preserve a regression test that fails for the observed defect before applying
  its fix when practical.
- Keep fixtures minimal and deterministic. Avoid unrelated site data, execution
  order, wall-clock timing, and external services.
- Put reusable setup at the narrowest scope that removes meaningful duplication.
- Test observable behavior and public contracts; avoid assertions tied only to
  implementation details.
- Distinguish a broken runner, a missing environment prerequisite, and a failing
  assertion before changing production code.

## Prove the result

1. Prove setup with a meaningful test at every layer established by the task.
   Do not add `assertTrue(true)` or production code whose only purpose is to give
   the harness something to test. If no unit-testable behavior exists, prove
   discovery and report the unit suite as empty.
2. Run the narrowest relevant test first, then its containing local suite.
3. Run the declared CI-equivalent commands after the local commands pass.
4. For browser work, execute at least one real spec and confirm its expected
   artifact or report is produced.
5. Report the exact commands run, results, files added or changed, and checks not
   run with the reason.

This skill owns testing infrastructure, test changes, and test execution. For a
broad conformance audit, documentation rewrite, or backend-module implementation,
hand that work to the corresponding skill and retain only the testing part.
