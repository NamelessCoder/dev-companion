---
date: 2026-08-01T00:33:13+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3labellookup, typo3extensiondocumentation
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the assistant violated the language-file rules by w...

## Observation

Debrief of the TYPO3 14 testimonials session: the assistant violated the language-file rules by writing German strings into the default XLF files as the source language. Because the site is German-only, new labels (backend_fields.xlf and messages.xlf additions for the testimonials content element) were added directly as German text in the default resource, marking German as the source language of files whose source-language declaration and conventions the assistant did not verify. The user's corrective feedback listed 'invalid added german languages to the default language files'. The assistant did not consult the localization/XLIFF documentation or typo3_label_lookup for how the default file, source-language attribute, and language override files must be structured before editing the XLF files.

## Query

adding German labels to default XLF files as source language; language file structure conventions

## Suggestion

Provide, before any label work: how the default XLF file and its source-language attribute must be structured, when German may or may not be the source language, and how to add translations (language override XLF files) — with a check that XLF edits follow it.
