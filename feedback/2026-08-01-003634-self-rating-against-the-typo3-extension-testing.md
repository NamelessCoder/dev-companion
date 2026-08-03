---
date: 2026-08-01T00:36:34+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3-extension-testing
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: self-rating against the typo3-extension-testing ski...

## Observation

Debrief of the TYPO3 14 testimonials session: self-rating against the typo3-extension-testing skill, which was never activated, so none of its workflow could run. Section-by-section score: (1) Establish the test surface — 1/5: did not read references/base.md, did not call typo3_project_scope or typo3_extension_scope first, relied on remembered composer test:unit/test:functional commands instead of the project's declared ones; (2) Choose the layer and its owner — 2/5: the functional PHPUnit layer for the rendering defect was right and TestimonialsTest.php + testimonials.csv were built, but references/checklist.md and phpunit.md were never read, and rendered-frontend/backend-preview verification was routed to no browser layer at all although the skill assigns that to Playwright; (3) Establish or repair the harness — 2/5: ran the existing functional suite but never confirmed project-owned commands, never touched the browser harness despite Tests/E2E/enquiry.spec.ts existing, verified rendered output by curling HTML; (4) Add or extend tests — 2/5: kept a regression test that now passes, but also 'added tests for core viewhelpers instead of reading documentation' and polluted the regression test with fwrite/extract debug and a throwaway LinkDebugTest; (5) Prove the result — 2/5: ran 50/50 functional and 26/26 unit and reported them, but never ran a browser spec or confirmed an artifact, so the rendered-output layer was unproven. Overall 2/5. Reasons: skill never activated via the skill tool; memory substituted for project/checkout evidence; cheapest layer (curl + vendor-source reads) chosen for rendered verification; rendered verification treated as throwaway debugging instead of a test layer; nothing routed 'verifying a rendered content element' to the testing skill's Playwright branch.

## Query

self-assessment: following the typo3-extension-testing skill workflow during the testimonials session

## Suggestion

Surface the typo3-extension-testing skill whenever test layers are touched, and route rendered-output verification (frontend journeys, backend previews) to its Playwright branch by default; require the base.md -> project_scope/extension_scope -> checklist.md -> implementation-guide order before any test is written.
