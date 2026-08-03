---
date: 2026-08-01T11:51:15+00:00
category: idea
status: open
model: deepseek-v4-flash-free
tool: typo3_project_scope, typo3_rule_lookup, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# A compound rule query loses the section that answers it, and is then told a boundary emptied the answer

## Observation

Trimmed on 2026-08-03 to the clause the praise qualifies. The three answers this
report asks be kept reproduce in its own words, re-run through
`bin/typo3-cms-mcp` from `/home/benji/projects/typo3-cms`: `typo3_project_scope`
answers `core-checkout, TYPO3 15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV`
with no extensions and no sites, `typo3_rule_lookup "breaking change"` returns
`## Breaking Changes` and `## Changelog Files` at 100% of the query terms, and
`typo3_commit_message_guide` on the patch's own subject answers *the summary
line is 68 characters long*.

What is left is the last sentence. The compound query this session recorded — in
`2026-08-01-115109`, now archived — is answered today, but the shape is still
there and it is not a miss. `commit message summary line length` returns two
Gerrit workflow sections at coverage 0.525 and score 38, while `## Summary Line`
— score 124, and the section carrying the 52-character rule — sits at 0.429 and
is dropped by the coverage floor. `summary line length` returns it first. The
two words naming the document are what cost it: a document title is in no
searched field.

Where a compound query does miss altogether, the reason is wrong.
`RuleLookup::answer()` reaches its no-match answer only where prose, hints and
withheld documents are all empty; where hints matched, an empty prose result
prints `No section that holds outside the core matched` whatever the scope, so
inside a core checkout the caller is told a boundary emptied an answer it
withheld nothing from. `D-ANS-037` has the readings.

## Query

review of core patch replacing GD error thumbnails with SVG placeholder

## Suggestion

Let the section the scoring already prefers survive a longer query, and let a
miss name the words rather than the core boundary.
