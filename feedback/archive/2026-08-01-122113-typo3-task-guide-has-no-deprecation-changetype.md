---
date: 2026-08-01T12:21:13+00:00
category: tool-gap
status: closed
closed: 2026-08-03
model: opencode/deepseek-v4-flash-free
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348). typo3_t...

## Observation

Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348). typo3_task_guide has no deprecation changeType — its enum is bugfix/feature/cleanup/test/documentation/unknown — so there is no checklist for a deprecation patch, a recurring core task with a fixed rule set: @deprecated docblock wording, trigger_error(..., E_USER_DEPRECATED) message format, changelog RST named Deprecation-<issue>-<slug> with anchor and Index.rst glob inclusion, removal staged for the next major, and no core caller of the deprecated API left behind. I verified each rule by grep against the DataHandler->setCorrelationId precedent and the changelog index instead of against a rule sheet.

## Query

Review patch: [TASK] Deprecate AssetCollector media handling

## Suggestion

Add changeType "deprecation" to typo3_task_guide (or a dedicated deprecation-patch checklist) covering: @deprecated docblock format, trigger_error E_USER_DEPRECATED wording, changelog file name/anchor/Index.rst glob inclusion, extension scanner matcher or explicit NotScanned, removal staged for the next major, and a check that no internal core caller of the deprecated API remains.
