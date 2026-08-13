---
date: 2026-08-13T21:47:08+00:00
category: wrong-answer
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# the e2e-prepare description talks a session into running a local playwright binary, which the bas...

## Observation

Task: review Gerrit change 93319, a Playwright-only diff adding two e2e specs and reworking the page-wizard fixture. I wanted to run the two new specs and nothing else.

typo3_test_run_guide returned the right suites. But its `e2e-prepare` entry reads, verbatim:

  "Use to look at a backend change in a real browser, and to run Playwright yourself against
   the instance. It prints the URL to open and the two commands to run the specs locally,
   headless and in the UI, with PLAYWRIGHT_BASE_URL already set — which is how a single spec
   or project is selected, since the containerised suites pass no arguments through."

Read while holding a diff whose whole content is two spec files, that is an instruction: -s e2e cannot be narrowed, e2e-prepare is how you narrow it, and the way you narrow it is a local playwright run. I followed it. I ran e2e-prepare, took the printed PLAYWRIGHT_BASE_URL, and invoked `npx playwright test` from Build/ against it.

The review skill's own base document says the opposite, in the same session, in stronger words: "Take the commands from typo3_test_run_guide and typo3_script_lookup, never from memory and never from the host's own PHP: a check run outside the project's runner is evidence about your machine." typo3-core-patch-checkout says it a third time about worktrees. The e2e-prepare description is the one place in the answer set that points the other way, and it is the one I acted on — because it was the only one that addressed the thing I actually wanted, which was not running 287 tests to see two.

It also did not work, for reasons that are their own finding but belong to the same decision: the local Playwright resolved a browser build (chromium_headless_shell-1194) that was not in the host cache, and downloading it did not finish inside ten minutes. Two false starts and roughly fifteen minutes went into a path that produced no evidence at all.

The user stopped me — "warum nutzt du nicht die typo3 testing suite?" — and I ran `CI=true ./Build/Scripts/runTests.sh -s e2e` instead, which is the whole 287-test suite because the suite genuinely takes no path argument. That is the correct answer and it is expensive, and the guide should be the thing that says so plainly rather than the thing that offers a way around it.

## Query

typo3_test_run_guide(paths: ["Build/tests/playwright/e2e/page-wizard/page-creation-via-drag-and-drop.spec.ts", "Build/tests/playwright/fixtures/page-wizard.ts", "Build/tests/playwright/fixtures/page-tree.ts"], targetVersion: "15.0")

## Suggestion

Rewrite the e2e-prepare entry so that what it is for is looking at a change in a browser, and stop naming the local playwright commands as the way to select a single spec. If the local run is genuinely sanctioned for a human iterating, mark it as such and say the result is not reportable evidence — a review or a patch says "-s e2e", never "npx playwright test".

Then answer the question the current wording is standing in for: -s e2e passes nothing through, so a Playwright-only diff costs a full-suite run. Say that in the e2e entry itself, next to the command, the way the invocation notes already say `--` is passthrough for the phpunit suites. A session that reads "no way to narrow this, budget for the whole suite" makes the right call in one step; one that reads the current text goes looking for the way around and finds one that does not work.

Worth considering as the real fix: give -s e2e a passthrough for a test path or --grep, the way -s unit and -s functional have one. It is the missing capability underneath both of the above.
