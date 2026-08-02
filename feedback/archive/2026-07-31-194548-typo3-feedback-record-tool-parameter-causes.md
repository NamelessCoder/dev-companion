---
date: 2026-07-31T19:45:48+00:00
category: bug
status: closed
closed: 2026-08-02
model: opencode/mimo-v2.5-free
directory: /home/benji/projects/site-new
---

# typo3_feedback_record tool parameter causes JSON parse error on every attempt. Tried passing tool...

## Observation

typo3_feedback_record tool parameter causes JSON parse error on every attempt. Tried passing tool as a string like typo3_extension_scope and as an array like typo3_extension_scope in brackets. Both produced JSON Parse error Unexpected EOF with the tool value rendered as a dot in the error output. Only omitting the tool parameter entirely allowed the note to be saved. This means none of the three notes I filed carry the tool or skill they are about, which defeats the purpose of the tool parameter for filtering and attribution.

## Query

typo3_feedback_record tool parameter serialization failure

## Suggestion

Fix the tool parameter serialization in typo3_feedback_record. The parameter schema says it accepts a string or array of strings, but the value is dropped during JSON serialization and replaced with a dot. Check whether the parameter name tool conflicts with an internal variable or is being filtered somewhere in the MCP server layer.
