---
date: 2026-07-30T18:55:39+02:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 8f0f589
subject: "[TASK] State the source language for new labels"
tool: typo3_architecture_lookup, typo3_label_lookup, typo3_translation_domain_lookup
---

# Nothing says which language a new label is written in. The language-files hints

## Observation

Nothing says which language a new label is written in. The language-files hints
cover how a translation domain is derived, when to reuse an existing trans-unit
and how to retire one, but not that the source XLF is English and that a locale
file beside it carries the translation.

In a forward run of EXT-04 against a sitepackage whose existing XLF files are
German, every new trans-unit was written in German. That matched the package
and continued a defect: the source file is the English one, and German belongs
in de.locallang.xlf next to it. The session had called typo3_label_lookup,
typo3_translation_domain_lookup and typo3_architecture_lookup with a task naming
language files, and none of the three answers mentions a source language.

site-label-language is about something else — a site's typo3Language, its locale
and its language pack — and reads like an answer to this question without being
one.

## Query

backend module registration controller and language files in a project sitepackage extension

## Suggestion

Add the authoring rule to the language-files hints: a new trans-unit is written
in English in the source file, and a translation goes into <locale>.<file>.xlf
beside it. Say what to do when the package at hand does it differently — an
existing non-English source file is a defect to report, not the convention to
follow, because the next label that reuses it spreads it.
