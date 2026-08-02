---
date: 2026-08-01T00:29:28+00:00
category: tool-gap
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of a TYPO3 14 backend preview task. typo3_documentation_lookup searches 'Record API Fluid...

## Observation

Trimmed on 2026-08-02 to the part that is left. The page was never missing from
the index: *Record objects* —
`ApiOverview/Database/DatabaseRecords/RecordObjects.html` — is one of the 1230
pages the three manuals contribute at 14.3, it scores above zero on all three
queries, and it ranks 28th, 13th and 11th against a `limit` of 6. `Record API`
on its own returns it third and `record objects` returns it first. `D-ANS-021`
has the ranks, the term weights and the readings.

What is left is that three calls answered in the shape of a good answer.
`status` was `answered` each time, with six results carrying titles, canonical
URLs and excerpts, and nothing said that the one term naming the subject had
contributed least of any word in the query. The index is a table of contents —
page titles and section paths, never the text of a page — so each further word
re-aimed the search instead of refining it: `has` reached
`PasswordHasBeenResetEvent`, `get` reached `getText`, `acces` reached
`RecordAccessGrantedEvent`. `record.header` arrived as the term `record` alone.
The two wasted round trips are what a caller was given no way to see coming.

## Query

queries: 'Record API Fluid template access record.header', 'Record API access relation field', 'Record API record get has'

## Suggestion

Say what a manual search matches on, where a caller composing the call reads it
or where the answer arrives — that page titles and section paths are the index,
and that words beyond the subject re-aim the search rather than narrow it.
