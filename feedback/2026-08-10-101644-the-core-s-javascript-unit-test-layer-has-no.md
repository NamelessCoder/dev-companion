---
date: 2026-08-10T10:16:44+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# the core's JavaScript unit test layer has no hint; a JS-test query answers with PHPUnit

## Observation

Task: review and rework a core patch adding a backend TypeScript module (later a Lit web component) for the page module, and write a JavaScript unit test for it.

backend-typescript says "Frontend modules with state transitions should have focused JavaScript unit coverage", which is exactly the obligation I needed to act on. Nothing then tells you how. The call above returned backend-ui, backend-typescript, css-motion-transitions — and unit-test-doubles, core-tests and project-extension-tests, all three about PHPUnit: UnitTestCase, FunctionalTestCase, #[Test], CSV fixtures, createStub vs createMock, typo3/testing-framework. None of that applies to a JavaScript test, and it was the larger half of the answer. The lexical match is on "unit tests", which the knowledge base spells toward PHP.

What I actually needed, and read out of the checkout myself:
- tests live at Build/Sources/TypeScript/<package>/tests/**/*.ts, discovered per package by Build/web-test-runner.config.mjs, which only creates a group for a package that has a tests/ directory;
- the runner is @web/test-runner ("wtr"), invoked by runTests.sh -s unitJavascript as "cd Build; npm ci; CHROME_SANDBOX=false BROWSERS=chrome npm run test";
- the import map maps @typo3/core/ and @typo3/backend/ to the built JavaScript under Resources/Public/JavaScript/, so a test exercises the built output and the branch has to be built before the tests see a source change;
- ~labels/ is stubbed by a middleware that returns { get: key => key };
- assertions come from @open-wc/testing, mocha types via `import type { } from 'mocha'`.

typo3_test_run_guide names the unitJavascript suite and says "Run the branch's frontend build first so the tests see the current output", which is the one piece of that list it does carry, but it offers no targeted invocation and nothing about where the files go.

Secondary observation on the same call: typo3_test_run_guide with the changed paths largely restated what typo3_task_guide had already returned in its `checks` and `testSuites` keys. In a session that called task_guide first, it was a round trip that added the invocation notes and little else.

## Query

typo3_hint_lookup task="JavaScript unit tests for a backend TypeScript module with state transitions" paths=["Build/Sources/TypeScript/backend/tests/layout-module/sticky-language-header-test.ts"]

## Suggestion

Add a hint for the core's JavaScript test layer, findable from words like "JavaScript unit test", "web-test-runner", "backend module test", carrying: the Build/Sources/TypeScript/<package>/tests/ location and the per-package discovery rule, the wtr runner, the import map to built JavaScript and the resulting build-before-test order, the ~labels stub, and the @open-wc/testing idiom. Also make typo3_hint_lookup not return the PHPUnit hints when the paths are .ts under Build/Sources/TypeScript — the path already says which layer is meant.
