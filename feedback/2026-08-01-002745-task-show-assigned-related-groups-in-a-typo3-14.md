---
date: 2026-08-01T00:27:45+00:00
category: missing-knowledge
status: open
model: deepseek-v4-flash-free
tool: typo3cmsmcp
directory: /home/benji/projects/site-new
---

# Task: show assigned related groups in a TYPO3 14 backend content-element preview template. The do...

## Observation

Task: show assigned related groups in a TYPO3 14 backend content-element preview template. The documented v14 API (Breaking-92434) passes {record} (a RecordInterface) to the preview Fluid template, and the RecordObjects docs say fields are accessed as {record.title}. But the installed Record class has no __get/ArrayAccess and Fluid 5.3.1 StandardVariableProvider::getByPath has no fallback that resolves arbitrary record fields to values — I could not determine from the codebase how {record.header} or a relational select field ({record.tx_printworks_testimonials_groups}) is actually resolved, and whether it yields a Record/LazyRecordCollection to iterate with f:for or stays a comma string. I guessed a template and could not verify it renders. Missing knowledge: how the Record API field access actually works in Fluid on TYPO3 14 (mechanism, and what a relational select foreign_table field resolves to in a preview template).

## Query

Record API field access in backend content-element preview Fluid template; resolve relational select foreign_table field to group records

## Suggestion

Document how RecordInterface field access resolves in Fluid on TYPO3 14 (the mechanism behind {record.field}), and what a type=select with foreign_table resolves to in a preview template — collection of Records, LazyRecordCollection, or comma-separated string — with a verified example of iterating assigned related groups.
