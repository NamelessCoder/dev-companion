# Say that a backend spec goes through the content iframe

**Serves:** feedback/2026-08-04-175916-task-add-playwright-coverage-proving-the.md, knowledge/hints/, knowledge/documents/
**Priority:** normal

`D-KNW-060` decided where this lands and that it is read off a running backend
rather than off the report. Start the 14.3 environment, log in with the stored
state the Playwright document already describes, open the page module, and
record what the frame and one content-element tile actually carry — the frame's
`id` and `name`, the tile's class and `id`, the preview body, and the accessible
name the module menu answers to. Verify each against the templates in
`.checkouts/14.3` so the statement has a source, and check the same markup on
`.checkouts/13.4` to decide whether it needs a `since`. Then write one sentence
into the `browser-tests` hint — a backend module renders inside the content
iframe, so every locator in a backend spec goes through a frame — and the
selectors into `knowledge/documents/project/testing/playwright.md`, beside the
backend spec that asserts the URL alone today.
