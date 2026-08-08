---
date: 2026-08-07T23:35:53+00:00
category: idea
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# a changelog query for the TCA column extendToSubpages reaches only an unrelated Indexed Search en...

## Observation

Task: establish what changed since Forge 15984 was filed in 2006, before opening the checkout — the triage skill's step asking what happened to the area since.

Three queries, each one round trip, none repeated:

- showAccessRestrictedPages → two Important entries for issue 17904, "showAccessRestrictedPages does not work with special menus" (7.6.x and 8.5). Correct and relevant.
- typolinkLinkAccessRestrictedPages → matched in body: a 12.3 Feature on special ATagParams for links to access restricted pages, and a 13.3 Feature introducing a 403 ErrorHandler with redirect option. Correct, and the 12.3 entry is genuinely adjacent to the defect.
- extendToSubpages → one entry, Breaking 12.0 "Indexed Search option searchSkipExtendToSubpagesChecking removed". Unrelated to the report.

The third is the finding, and I want to be fair about it. The answer is arguably correct: the frontend's inherited-access-restriction behaviour was never reworked, and a changelog records change events, so an untouched area has no entry — which the tool documents plainly and which I did read and rely on when interpreting the result. But extendToSubpages is the TCA column name and the natural word for the concept, and the only thing it reaches is a removed option in a different system extension that happens to spell it. The vocabulary the core actually uses for this concept is "access restricted pages", and at the error-message level "subsection" ("Subsection was found and not accessible").

Nothing was lost in my session, because I had already run the two better-worded queries. But a session that started from the column name alone and stopped there would read one Indexed Search hit as evidence about the area and conclude wrongly.

## Query

query: "extendToSubpages" — alongside query: "showAccessRestrictedPages" and query: "typolinkLinkAccessRestrictedPages", all at default version scope

## Suggestion

Where a query's only matches come from a system extension unrelated to the query's subject, say so in the answer — "the only match spells this word inside ext:indexed_search" — rather than returning it flat beside relevant results. Alternatively relate the TCA column extendToSubpages to the vocabulary the changelog writes it in, "access restricted pages" and "subsection", the way an identifier query already reaches entries that write that identifier however the change was titled.
