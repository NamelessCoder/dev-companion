---
date: 2026-08-02T14:44:56+00:00
category: idea
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_project_scope
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403). Recording what worked,...

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403). Recording what worked, so it is not lost in a later refactor.

Three answers changed what I did, concretely:

1. typo3_task_guide, fluid-viewhelpers: "ViewHelpers are covered by functional tests under Tests/Functional/ViewHelpers/, not unit tests: a ViewHelper needs a rendering context, so a unit test asserts the wrong thing." I wrote the reproduction directly as a functional test in typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php and never attempted a unit test. The stated reason, not just the rule, is what made it obviously right — and it held: the eventual fix needed a real FAL file with a real sha1 behind it.

2. typo3_task_guide, fluid-viewhelpers: "A ViewHelper is public API. A new one, or a changed argument list, needs a changelog entry under typo3/sysext/core/Documentation/Changelog/." The change started out as an exception-message improvement, where I would not have written a changelog. It ended up altering rendered output for four ViewHelpers, and this hint is why a changelog entry existed from the moment the scope grew rather than being remembered at the end.

3. typo3_project_scope: typo3Version 15.0.0-dev on a checkout whose branch was "main". That is what told me the changelog directory is Changelog/15.0/ and the Releases: trailer is "main", without reading composer.json or guessing from the branch name. It also let me pass targetVersion=15.0 to task_guide with confidence.

The shape that made these work is the same in all three: a rule plus the reason for it, tied to a concrete path in the repository. The hints that were noise in the same responses were the ones stated as topic headlines without a path or a reason.

## Query

typo3_project_scope (no arguments); typo3_task_guide task="Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource", changeType=bugfix, area=fluid, targetVersion=15.0

## Suggestion

Keep these three, and keep the rule-plus-reason-plus-path shape when hints are rewritten. If the fluid-viewhelpers block is ever trimmed, the functional-tests-not-unit-tests hint and the public-API-needs-a-changelog hint are the two that carried the session and should survive the trim. Extending the same shape would help most where a rule is currently stated bare: naming the concrete directory and the reason for it in the changelog hint (which of Breaking/Deprecation/Feature/Important, and why) would have saved me deciding that by reading neighbouring files.
