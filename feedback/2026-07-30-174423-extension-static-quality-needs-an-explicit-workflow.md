---
date: 2026-07-30T17:44:23+02:00
category: tool-gap
status: open
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup
---

# Extension static quality needs an explicit workflow

## Observation

PHPStan and CGL are release gates for an extension, not optional checks to
mention after its behavioral tests pass. The testing skill can retain an
existing architecture or static check, but it has no implementation guide for
discovering, establishing, repairing and proving PHPStan and code-style
infrastructure in a TYPO3 extension or sitepackage.

Without that workflow an agent is likely to copy a core-only `runTests.sh`
command, guess dependency constraints, grow a PHPStan baseline to hide new
errors, run a formatter across unrelated code, or add CI configuration before a
stable project-owned local command has passed. The answer also needs to
distinguish analysis failures, coding-style violations, configuration defects
and unsupported dependency combinations.

## Query

Set up PHPStan and CGL for this TYPO3 extension, fix the findings in the changed
code, and make the same checks run locally and in CI without replacing the
quality commands we already have.

## Suggestion

Add a static-quality branch to `typo3-extension-testing` with an on-demand
reference for PHPStan and CGL. It should inspect existing Composer packages,
configuration, baselines, scripts and CI first; resolve compatible development
dependencies from the extension's declared TYPO3 and PHP range; establish one
stable project-owned command per check; keep check and fix modes distinct; and
make CI invoke the commands that passed locally. New errors must be fixed rather
than added to a baseline, and automatic formatting must stay scoped to intended
first-party files.

Create a forward scenario for a reusable extension with incomplete static
quality infrastructure. Split this into a separate skill only if the scenario
shows that the testing skill does not activate or becomes responsible for two
unrelated workflows.
