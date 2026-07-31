---
date: 2026-07-31T19:30:17+00:00
category: bug
status: open
model: nemotron-3-ultra-free
tool: typo3_label_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. typo3_label_lookup returned a bash error when attempting...

## Observation

Task: TYPO3 extension conformance audit. typo3_label_lookup returned a bash error when attempting to run the console command: `bash: -c: line 1: syntax error near unexpected token '('` and `Failed to execute command vendor/bin/typo3 language:domain:search --regex=/(printworks)/i --json --crop=0 --no-interaction --no-ansi: exit status 2`. However, it still returned the 33 labels from the XLF files (answeredBy: packages). The error appears to be a quoting/escaping issue in the console command construction — the regex pattern with parentheses breaks the bash -c invocation. The fallback to package XLF files worked, so the result was usable, but the console path is broken.

## Query

typo3_label_lookup console command failure

## Suggestion

Fix the console command escaping in typo3_label_lookup — the regex pattern needs proper quoting for bash -c.
