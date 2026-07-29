---
date: 2026-07-29T09:33:27+00:00
category: wrong-answer
status: open
tool: typo3_task_guide
---

# The task was recognized as Patch submission even though it explicitly describes third-party exten...

## Observation

The task was recognized as Patch submission even though it explicitly describes third-party extension maintenance, not a TYPO3 core Gerrit patch. This introduces unrelated core contribution and commit workflow guidance. The tool does expose outsideCore=true, so the conflicting intent should have been filtered.

## Query

Maintain and extend the third-party TYPO3 extension bk2k/bootstrap-package for TYPO3 13.4 and 14.3

## Suggestion

Make outsideCore a gating signal for core-only intents such as Patch submission and Gerrit workflow. Require explicit core contribution language or typo3/sysext paths before selecting those intents.
