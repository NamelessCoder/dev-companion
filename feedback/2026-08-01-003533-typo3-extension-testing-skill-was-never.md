---
date: 2026-08-01T00:35:33+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3extensiontesting
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the typo3-extension-testing skill was never activat...

## Observation

Debrief of the TYPO3 14 testimonials session: the typo3-extension-testing skill was never activated and its Playwright branch was never followed, even though the work explicitly needed it. The session verified the rendered frontend (testimonials cards, ratings, external-URL links on the live site) and the backend page-module preview by curling HTML and reading vendor source, and tested only PHPUnit functional/unit layers. The skill routes exactly this to a browser test: 'Use a browser test for rendered user journeys, backend interaction, JavaScript, or accessibility behavior that cannot be established below the UI', with references/playwright.md for browser/accessibility/visual tests and a required step to 'execute at least one real spec and confirm its expected artifact'. The project even has an existing Playwright harness (Tests/E2E/enquiry.spec.ts) that was never used. The user flagged 'did not test the backend preview' and that the skill's Playwright mention never appeared in the work.

## Query

verifying rendered testimonials frontend and backend content preview — Playwright browser test layer per typo3-extension-testing skill

## Suggestion

Ensure the testing skill's Playwright branch is surfaced whenever rendered-output verification (frontend journeys, backend previews, JS, a11y) is part of the task, including that an existing E2E harness (Tests/E2E) is reused; PHPUnit functional tests alone are not the layer for verifying rendered HTML or the page module preview.
