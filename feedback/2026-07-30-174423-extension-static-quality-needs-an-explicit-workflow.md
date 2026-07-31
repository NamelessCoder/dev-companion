---
date: 2026-07-30T17:44:23+02:00
category: tool-gap
status: open
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup
---

# Extension static quality needs an explicit workflow

## Observation

Two recorded `REVIEW-02` runs now bound this, one against complete static
quality infrastructure and one against half of it.

Against bootstrap_package, where PHPStan and its baselines exist, the review
read them rather than replacing them: the baseline is judged a work list for the
release that drops 13.4, the existing suppressions are argued to be correct
while 13.4 is supported, and what comes out are verification gaps inside the
infrastructure that is there.

Against `bk2k/syntax`, which has php-cs-fixer and phplint but no PHPStan, no
baseline, no analysis step and no `Tests/` at all, the review never named static
analysis. Its leading finding is that a 2×4 CI matrix runs two
version-independent steps and proves nothing beyond "the files parse" — correct,
and it remediates with a PHPUnit harness alone. A missing static quality
workflow surfaces as a missing *test* workflow, and lands in
`typo3-extension-testing`, whose only sentence on the subject sends it back:
static checks are added "only when the project already uses them".

So the gap is not that an agent copies a core-only `runTests.sh`, grows a
baseline to hide errors, or formats unrelated files — neither run did any of
that. It is that nothing establishes the infrastructure in the first place, and
the skill that would own it declines by its own rule.

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

The runs settle the question this note left open: it stays one skill. Neither
made `typo3-extension-testing` responsible for two unrelated workflows, and
neither reached it by activation — both routed to it from
`typo3-extension-conformance`, which is the entry point a review actually uses.
