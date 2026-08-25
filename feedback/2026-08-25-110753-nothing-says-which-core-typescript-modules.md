---
date: 2026-08-25T11:07:53+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Nothing says which core TypeScript modules unitJavascript can actually reach, so "add a test" is ...

## Observation

Task: review core patch 95392, a null-guard in Build/Sources/TypeScript/form/backend/form-editor/view-model.ts, and decide whether the missing test is a finding.

Both tools named the JavaScript unit layer. typo3_task_guide returned the javascript-unit-tests hint, which is accurate and useful: tests live at Build/Sources/TypeScript/PKG/tests/, web-test-runner.config.mjs makes one group per package that has such a directory, a package without one is discovered as nothing, and the import map points at the built output so a source change is invisible until the branch is built. It also carried "Frontend modules with state transitions should have focused JavaScript unit coverage", which reads as an instruction to demand a test here.

What neither tool can say is whether this particular module is reachable by that layer, and that is the whole of what decides the finding's severity. I established it in the checkout, and it took four reads:

- ls Build/Sources/TypeScript/*/tests - only backend, core and rte-ckeditor have one. EXT:form has no group at all, so the first test there means creating the infrastructure.
- grep over Build/tests for form-editor/formeditor/form_editor - zero hits, and Build/tests/playwright/e2e has no form directory (form-engine is FormEngine/TCA, a different subsystem). So the other layer has nothing either.
- view-model.ts:105 and :97 - stageComponent and formEditorApp are module-level lets with no setter.
- view-model.ts:1332 - bootstrap() is the only thing that sets them, and it also runs structureComponentSetup(), modalsComponentSetup(), inspectorsComponentSetup(), stageComponentSetup(), buttonsSetup(), addPropertyValidators() and loadAdditionalModules(). getStage() at :755 returns the module namespace of a static import.

So addAbstractViewValidationResults() cannot be exercised in isolation without module mocking or a refactor, and the honest review sentence is "missing coverage, and here is what it would cost" rather than "add a test". Without those four reads I would have written the second, and a maintainer would have had to establish the cost themselves to answer me.

This is a "right and one step short" finding rather than a wrong answer. The hint is correct about where a test goes and what discovers it. It stops before the question a reviewer actually has, which is whether the layer can hold this bug at all - and typo3_test_run_guide's own framing invites that question by promising "the suites that can actually fail on them".

## Query

typo3_task_guide(task="Review a core bugfix patch adding a null check in the form editor view model TypeScript", changeType="audit", paths=["Build/Sources/TypeScript/form/backend/form-editor/view-model.ts","typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"], targetVersion="15") and typo3_test_run_guide with the same paths. Both named unitJavascript. Neither could say whether this module is reachable by it.

## Suggestion

Two things would have collapsed those four reads into the answer I already had:

1. Have typo3_test_run_guide say, for a path under Build/Sources/TypeScript/PKG/, whether PKG has a tests/ directory at all - it is a filesystem check in the repository the server is started in, the same kind of reading typo3_project_describe already does. A suite listed as able to fail on a path, in a package the runner discovers as nothing, is the "green that ran over no files" case the base rules warn about, one step earlier. Something like: "unitJavascript: this package has no tests/ directory, so the runner discovers no group for it; the first test here creates one."

2. Add to the javascript-unit-tests hint what makes a core module reachable by that layer and what does not. The core has two shapes side by side: modules exporting a class or pure functions, which the layer holds, and the older form-editor modules built on module-level singletons wired by a bootstrap() that touches the DOM, which it does not. Naming the second shape, with EXT:form's form-editor as the example, would let a review price the finding without reading the module.

Neither replaces the checkout reading; both would have told me what to expect before I did it, which is the difference between a review that states a cost and one that demands a test.
