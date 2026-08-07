---
date: 2026-08-07T06:53:42+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# documentation_lookup has no page for Extbase query constraints against null

## Observation

Task: verify Forge 109572, a report claiming an Extbase repository query cannot filter for IS NULL on a nullable date field.

I asked typo3_documentation_lookup at targetVersion 13.4 with three phrasings and used none of the six results. The closest were the TCA Datetime reference page, which documents the column type but says nothing about what an empty value means when read back, and the Extbase Repository page, which does not cover query constraints at all. The reported coverage scores ran 0.274 to 0.731, and the top result matched on the word "datetime" appearing in a page title rather than on anything about the question. I answered from core source instead.

The undocumented surface, precisely: whether $query->equals($property, null) is supported API at all, and what it is defined to do on a column that cannot hold SQL NULL. That matters beyond this issue, because the answer decides whether a report like 109572 is a defect or a misuse — and I got that judgement wrong on the first pass for want of a stated intent to check against. The triage checklist requires a verdict of "not a defect" to name where the intent is stated; there was nowhere to point.

## Query

typo3_documentation_lookup(queries: ["Extbase query equals null", "nullable datetime TCA", "Extbase repository query constraints"], targetVersion: "13.4")

## Suggestion

Two things. If the manual genuinely has no page here, the answer is more useful saying so than returning six matches at 0.27 coverage that read as partial answers — an explicit "no page covers this" is a result a triage can act on, six weak matches are not. And if coverage is to be added, the QueryInterface constraint methods and their behaviour against null are the gap: it is the difference between calling a report a defect and calling it misuse.
