---
date: 2026-07-31T19:36:19+00:00
category: wrong-answer
status: closed
closed: 2026-08-02
model: nemotron-3-ultra-free
tool: typo3_extension_scope, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. My finding #5 recommended "Add en.xlf files with English...

## Observation

Task: TYPO3 extension conformance audit. My finding #5 recommended "Add en.xlf files with English source (or switch source to en and add de.xlf)". This is incorrect per TYPO3 convention: the XLF files should have English as the source language (source-language="en") with German translations in de.xlf files alongside. The current files have source-language="de" which is wrong — they should be switched to English source with German translations in separate files, not the other way around.

## Query

XLF translation recommendation incorrect - source language should be English

## Suggestion

typo3_architecture_lookup or typo3_extension_scope should clarify the correct XLF source language convention (English source, translations in language-specific files).
