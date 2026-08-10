---
date: 2026-08-10T10:16:44+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# a JavaScript test query answers from PHPUnit, and the run guide restates the task guide

## Observation

Task: review and rework a core patch adding a backend TypeScript module (later a
Lit web component) for the page module, and write a JavaScript unit test for it.

The layer itself is now `javascript-unit-tests` and this is what is left of the
report. The call above returned backend-ui, backend-typescript,
css-motion-transitions — and unit-test-doubles, core-tests and
project-extension-tests, all three about PHPUnit: UnitTestCase,
FunctionalTestCase, #[Test], CSV fixtures, createStub vs createMock,
typo3/testing-framework. None of that applies to a JavaScript test, and it was
the larger half of the answer. The lexical match is on "unit tests", which the
knowledge base spells toward PHP.

Secondary observation on the same call: typo3_test_run_guide with the changed
paths largely restated what typo3_task_guide had already returned in its
`checks` and `testSuites` keys. In a session that called task_guide first, it
was a round trip that added the invocation notes and little else.

## Query

typo3_hint_lookup task="JavaScript unit tests for a backend TypeScript module
with state transitions"
paths=["Build/Sources/TypeScript/backend/tests/layout-module/sticky-language-header-test.ts"]

## Suggestion

Make typo3_hint_lookup not return the PHPUnit hints when the paths are .ts under
Build/Sources/TypeScript — the path already says which layer is meant.
