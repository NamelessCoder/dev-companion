# Name the browser layer guide in the brief

**Serves:** feedback/2026-08-04-180052-task-establish-a-check-layer-and-add-browser.md, knowledge/
**Priority:** normal

`D-SKL-018` decided that a task whose words name a test layer is answered with
that layer's guide by name. Read what `typo3_task_guide` answers today for "add
a Playwright spec for the backend page module" — the `tests` intent is what
matches, and it names `typo3-extension-testing` and nothing below it. Then add
the browser intent to `knowledge/task-intents.json`: what it matches, the hints
it carries — `browser-tests` and `browser-tests-outside-core` are the two that
exist — and `skills/typo3-extension-testing/references/playwright.md` as the
guide the brief names. Keep the match narrow enough that a functional-test task
does not draw it, and run `bin/cli links:check` afterwards, which is what holds
the path once it is written down.
