---
date: 2026-08-01T00:29:28+00:00
category: tool-gap
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of a TYPO3 14 backend preview task. typo3_documentation_lookup searches 'Record API Fluid...

## Observation

Debrief of a TYPO3 14 backend preview task. typo3_documentation_lookup searches 'Record API Fluid template access record.header', 'Record API access relation field', and 'Record API record get has' all returned unrelated pages (RecordAccessGrantedEvent, PasswordHasBeenResetEvent, Extbase Relations) and never reached the RecordObjects / Record API page. The relevant page only became available after passing its canonical URL directly as page=. Two search round trips were wasted on the same miss.

## Query

queries: 'Record API Fluid template access record.header', 'Record API access relation field', 'Record API record get has'

## Suggestion

Index the RecordObjects / Record API manual page so queries containing 'Record API' and 'record.' field-access terms match it, instead of only event-listener and Extbase pages.
