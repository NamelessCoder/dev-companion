---
date: 2026-08-01T00:39:37+00:00
category: tool-gap
status: closed
closed: 2026-08-03
model: opencode/deepseek-v4-flash-free
tool: typo3-extension-testing
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: cache clearing was done by deleting fi...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: cache clearing was done by deleting files (rm on var/cache/code/fluid_template) instead of using the TYPO3 cache API or a documented cache-clear command. The user's corrective feedback included 'did not clear the caches properly after changes'.

## Query

clearing the fluid_template (and code) caches after template changes

## Suggestion

Document the one correct way to clear caches (cache:flush or the cache API) for template/TypoScript/TCA changes, instead of deleting cache directories by hand.
