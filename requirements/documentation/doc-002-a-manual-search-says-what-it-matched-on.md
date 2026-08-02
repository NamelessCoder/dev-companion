---
id: R-DOC-002
status: held
restsOn: [D-ANS-021]
---

# R-DOC-002 — A manual search says what it matched on

**A caller can tell an aimed manual answer from a confident one.**

`R-DOC-001` says what the index is — page titles and section paths, a table of
contents and never the text of a page. This says the caller is told. A search
that ranks on titles alone answers a five-word question in the shape it answers
a two-word one, so the caller learns nothing from six results carrying canonical
URLs and excerpts. What is owed is either the rule where the call is composed —
that words beyond the subject re-aim the search rather than refine it — or the
match where the answer arrives, so that a query whose subject contributed least
is visible as one.

Answered by the second: every search result names the query words the index
carried and the field each was found in, and the answer says once that page
titles and section paths are all there is to match.

## From

`feedback/2026-08-01-002928`, re-run on 2026-08-02. Three queries naming the
Record API returned six results each, `status: answered`, and ranked the present
*Record objects* page 28th, 13th and 11th of 1230 — behind pages matched on
`has`, `get` and `acces`. Two round trips went on a miss the answers gave no
sign of.

## Held by

- `DocumentationTest::aResultNamesTheWordsOfTheQueryItWasMatchedOn`
- `DocumentationTest::aPageReadBackCarriesNoMatch`
- `ToolContractTest::aToolCallAnswersWithTextAndMatchingData`
