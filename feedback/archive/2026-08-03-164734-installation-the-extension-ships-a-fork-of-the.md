---
date: 2026-08-03T16:47:34+00:00
category: bug
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Trimmed on 2026-08-04 to the part that is left. Two of its three findings are
answered. Matching page bodies would not have helped: TYPO3 Explained 14.3
writes `.fluid.html` in 49 code-example captions and states the convention in no
sentence, so the corpus this asks for does not carry two of these three
questions either — `D-ANS-046`. And every result now carries the share of the
query it covers, with a sentence above the results where nothing covers half, so
these six no longer arrive in the shape of a good answer — `D-ANS-051`.

What is left is the second half of the suggestion. The six collisions are still
returned: re-run on 2026-08-04, `Fluid template file naming convention v14`
comes back with *Naming* at 40% and `layout root paths login screen override`
with *be.pagePath* at 22%, both now labelled and neither withheld. Dropping them
needs a floor, and no value of one both empties these queries and keeps
`login screen layout`, where the page that answers it — *LoginProvider* — covers
34%. The task the audit was doing is unaffected either way: the version boundary
it was after is `Feature-108166-FluidFileExtensionAndTemplateResolving`, which
`typo3_changelog_lookup` returns alone for `fluid file extension`.

## Query

typo3_documentation_lookup {queries: ["fluid.html file extension templates", "Fluid template file naming convention v14", "layout root paths login screen override"], targetVersion: "14"}

## Suggestion

Where nothing clears a relevance threshold, return that fact rather than the best six substring collisions: typo3_changelog_lookup already does the honest version of this by naming the largest part of a query that did reach entries, and the same convention here would let a caller distinguish "the manual does not cover this" from "your query did not reach the manual".
