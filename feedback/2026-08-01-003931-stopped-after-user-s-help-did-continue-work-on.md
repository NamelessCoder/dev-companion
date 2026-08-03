---
date: 2026-08-01T00:39:31+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: 'stopped after user's help, did contin...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: 'stopped after user's help, did continue work on its own'. The user flagged that after they provided help (e.g. the f:then fix, the 'data comes from the record' correction), the assistant did not stop and confirm direction but continued working autonomously, and conversely that it sometimes stopped at the wrong moment instead of continuing to verify. The exact boundary behavior is ambiguous in my transcript and was never recorded; it should be captured precisely from the user's account.

## Query

when to stop after user help vs continue autonomously — unclear behavior

## Suggestion

Clarify and record the intended behavior: after a user correction, confirm the corrected direction before continuing, and finish verification (e.g. actually rendering the preview) rather than stopping or shipping unverified work.
