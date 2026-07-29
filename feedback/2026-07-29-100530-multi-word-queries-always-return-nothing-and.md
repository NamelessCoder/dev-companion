---
date: 2026-07-29T10:05:30+00:00
category: bug
status: open
tool: typo3_label_lookup
---

# Multi-word queries always return nothing, and the empty result is misreported as a console failur...

## Observation

Multi-word queries always return nothing, and the empty result is misreported as a console failure. The example in the tools own description, "save document", returns: "The installation could not be asked, so this is unanswered rather than empty: ... [WARNING] No language resource files found." The installation WAS asked successfully. Root cause: the tool passes the query to `language:domain:search --search=<query>`, which is a literal substring match over label text, so a multi-word phrase can only match if that exact string occurs in one label. Verified in the installation: `--search=save` returns 65 labels, `--search="save document"` returns zero. Two distinct defects: (1) the documented usage pattern -- "Words from the label text" -- is not what the underlying command supports; (2) a legitimate zero-match result is presented as an unreachable installation, which tells the caller to go check typo3_server_scope instead of refining the query.

## Query

{"query":"save document"}

## Suggestion

Split the query on whitespace, run the search per token and intersect by label, or search on the longest token and filter the rest in PHP. Then a phrase query behaves as the description promises. Separately, distinguish a non-zero exit / unreachable console from a successful run with zero hits: on zero hits say "no label matches all of these words" and, when a single token would have matched, name it ("save alone matches 65 labels"). Reserve the "could not be asked" wording for an actual console failure -- keying it off the [WARNING] line in stdout misclassifies an empty result.
