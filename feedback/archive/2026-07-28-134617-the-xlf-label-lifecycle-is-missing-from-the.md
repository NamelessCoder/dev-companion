---
date: 2026-07-28T13:46:17+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: e0f8694
subject: "Add the XLF label lifecycle rules"
tool: typo3_rule_lookup
---

# The XLF label lifecycle is missing from the rules. Removing a label from an XLF file is not done ...

## Observation

The XLF label lifecycle is missing from the rules. Removing a label from an XLF file is not done by deleting the trans-unit — core convention is to mark it with x-unused-since="<next version>" on the trans-unit element, so translator history is preserved and third-party overrides that still reference the key do not break. typo3_rule_lookup with query="xlf label lifecycle unused removal" returned nothing on this: it produced hints on dependency injection, TCA, PSR-7 middleware and one CSS block, plus a short XLIFF section covering only naming, reuse and the checkIntegrityXliff / normalizeXliff commands. typo3_label_lookup does report x-unused-since per label, so the server clearly knows the marker exists — but nothing tells an agent when to set it, or that the version must be the next upcoming release rather than the current one.

## Query

query="xlf label lifecycle unused removal"

## Suggestion

Add a short XLF lifecycle rule: never delete trans-units, mark them x-unused-since with the next upcoming version, and do not mark labels that were introduced in the same patch. Ideally have typo3_label_lookup point at this rule when it reports a label that already carries the marker.
