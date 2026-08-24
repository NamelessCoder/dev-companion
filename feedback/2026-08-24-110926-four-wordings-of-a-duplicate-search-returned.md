---
date: 2026-08-24T11:09:26+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Four wordings of a duplicate search returned nothing; the category enumeration answered on the fi...

## Observation

Task: establish whether a bug report about EXT:impexp importing page translations onto the wrong page was already filed on Forge.

I made twelve typo3_forge_lookup calls in the session. The text queries, in order:

1. "impexp import page translation wrong pid pagetree" — 1 hit, a closed 2014 issue.
2. "impexp translated pages imported root level" — 0.
3. "writePagesOrder importNewIdPids" — 0.
4. "impexp translation import" — 5 hits, one of them relevant by luck (#93470 appeared as a relation of another issue, not as a match).
5. "import page tree translations pid" — 1 hit, an unrelated 2007 JSMENU issue.
6. "translated pages import root page" — 1 hit, unrelated.
7. "impexp l10n_parent" — 6 hits, none about impexp.
8. "import pages wrong parent page" — 5 hits, all unrelated.

What actually settled the question was one call: open "stale", category "import export", limit 50, which returned all 26 open issues of Import/Export (T3D) at once. Reading 26 subjects is what let me tell the user "no existing issue" with confidence, and it is also where #93470 and its relation chain came from. The identifier queries (3) are the ones I would not make again — a method name reaches nothing, because issues are written by users who do not know method names.

The description does document all of this: nothing is ranked, one wording does not settle it, and the category route is described as the way in for "are there known bugs in the RTE". I still spent eight calls before taking it, because the natural first move on a written report is to search with its own words.

## Query

typo3_forge_lookup query="impexp import page translation wrong pid pagetree" / "impexp translated pages imported root level" / "writePagesOrder importNewIdPids" / "impexp translation import" / "import page tree translations pid" / "translated pages import root page" / "impexp l10n_parent" / "import pages wrong parent page"; then typo3_forge_lookup open="stale" category="import export" limit=50

## Suggestion

Say it where a caller reads it under time pressure rather than only in the argument prose: for "has this already been reported", enumerate the category first and read the subjects, then use query to confirm a candidate. One sentence at the top of the tool description, or in the answer of a text query that returned nothing — "0 matched; Import/Export (T3D) has 26 open issues, enumerate them with open+category" — would have replaced seven of my eight calls. The empty-result answer is the best place for it, because that is exactly the moment the next wording gets tried instead.
