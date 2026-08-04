---
date: 2026-07-29T17:09:21+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 04417ec
subject: "[TASK] Say what is out of scope, so a thin answer reads as a gap"
tool: typo3_architecture_lookup, typo3_task_guide, typo3_server_scope
directory: /home/benji/projects/site-new
---

# TWO THINGS: nothing about browser tests, and a scope signal I could not read.

## Observation

TWO THINGS: nothing about browser tests, and a scope signal I could not read.

1. Playwright is absent. Asking for acceptance or end-to-end browser tests returns no hint at all — the answer falls back to the id list and a knowledge section about site sets, which has nothing to do with the question. Meanwhile the core itself has Build/playwright.config.ts and Build/tests/playwright/ with e2e/frontend/, e2e/accessibility/ (axe), e2e-install/, fixtures/ and helper/. That is where the conventions are worked out, and it is the same situation as theme_camino before it was added: the core has the reference, the knowledge base does not point at it.

What a hint would carry: that Playwright replaced codeception for acceptance testing; where the config and the specs live in the core; the fixture/helper split; that accessibility checks run through @axe-core/playwright. And for a project rather than the core, the part the core layout does not answer: the browsers belong on the host or in a dedicated image rather than in the web container of the dev environment, and the suite runs against a served site instead of a per-class test instance — which makes it the opposite trade-off to the functional rendering tests, and worth stating side by side. A functional test with executeFrontendSubRequest() never runs JavaScript, applies no CSS and speaks no HTTP; naming it a "frontend test" is how I described mine to the user until they asked whether it was Playwright, and it was the wrong word.

2. The scope signal is contradictory, and it cost me confidence rather than time. typo3_server_scope lists under doesNotCover: "Work outside TYPO3 core contribution: site setup, project or third-party extension development" and refers to docs.typo3.org. But typo3_task_guide answers project tasks readily, sets outsideCore: true, and filters the core-only items out — and over this session the knowledge base grew hints that are explicitly about project work: sitepackage-layout, extbase, frontend-records, content-elements, sitepackage-initial-content. So the behaviour is "project work is covered", while the declared scope still says it is not.

I could not tell from the server whether a thin answer meant "out of scope, go to the documentation" or "in scope, not written yet" — and those call for different reactions from me. Every gap I reported this session I reported as the second, because the hints kept appearing. The scope text is the last place that still says the first.

## Query

typo3_architecture_lookup task="acceptance and end-to-end browser tests for a TYPO3 site with Playwright", targetVersion=14.3 → no hint matched; the knowledge section returned was about site sets and TSconfig

## Suggestion

Add a Playwright/acceptance hint built from the core's Build/tests/playwright, and include the project-shaped part: host-side browsers, a served site instead of a test instance, and the explicit contrast with functional rendering tests so the two are not confused. Separately, bring the doesNotCover text in typo3_server_scope in line with what the server now does — project and sitepackage work is covered; what is genuinely out of scope is a project's own CSS and JavaScript.
