---
date: 2026-07-31T19:45:10+00:00
category: missing-knowledge
status: open
model: opencode/mimo-v2.5-free
directory: /home/benji/projects/site-new
---

# typo3_extension_scope returns a summary that does not list individual files. For a conformance au...

## Observation

typo3_extension_scope returns a summary that does not list individual files. For a conformance audit this means the tool establishes what the extension registers but not what it ships such as test files, form definitions, route enhancers, FlexForms, and language files. The audit skill step 2 asks for what it ships beside the registrations but the tool answer does not enumerate those. I had to use glob and read to discover the full file tree.

## Query

extension scope for printworks sitepackage conformance audit

## Suggestion

Extend the answer with a ships section listing test files by path, form definitions, FlexForms, route enhancers, XLF files with source language, and Configuration subdirectories not already covered by other lookups.
