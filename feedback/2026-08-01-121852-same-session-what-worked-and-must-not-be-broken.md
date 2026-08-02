---
date: 2026-08-01T12:18:52+00:00
category: idea
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_server_scope, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Same session: what worked and must not be broken later. typo3_server_scope's explicit boundary — ...

## Observation

Same session: what worked and must not be broken later. typo3_server_scope's explicit boundary — "this server never reads, inspects or runs anything against a TYPO3 core checkout; determine the changed paths yourself and pass them to typo3_architecture_lookup and typo3_task_guide" — was the single most useful answer of the whole review. It stopped me from trying to feed the diff to the server and pushed me to read the checkout, which is where every substantive finding (GIFBUILDER not dispatching AfterFileProcessingEvent, no size column in sys_file_processedfile, ProcessedFile::getSize() falling back to a storage stat) came from. typo3_test_run_guide called with the changed paths returned the exact runTests.sh invocation — CI=true, -- passthrough, -n dry-run for cgl — that ran clean first try, and typo3_commit_message_guide confirmed the message with no false readiness flags.

## Query

Review patch: [TASK] Deprecate AssetCollector media handling

## Suggestion

Keep the "never reads a checkout" boundary and the per-tool attribution of which source answered; they are what made the review result trustworthy rather than plausible. Keep typo3_test_run_guide's targeted-invocation contract (paths + targetVersion narrows the suites). Consider naming this review flow explicitly in typo3_server_scope's routing table (see companion note).
