---
date: 2026-07-30T07:36:32+00:00
category: tool-gap
status: closed
closed: 2026-07-30
commit: 51e5e5a
subject: "[FEATURE] Answer a content-element task with what the element owns"
tool: typo3_task_guide, typo3_architecture_lookup
---

# The content-element workflow initially validated a technically possible generic record-reference ...

## Observation

The content-element workflow initially validated a technically possible generic record-reference design without forcing the editor ownership decision between reusable references, arbitrary nested content, and dedicated inline child records.

## Query

Add a Hero Carousel that rotates different elements

## Suggestion

Route content-element tasks through an explicit data-model and editor-workflow gate; keep the new installable content-element skill and scenario as the regression surface.
