# a matched preamble is named as an empty heading in a rule lookup answer

**Serves:** src/Result/
**Priority:** low
**Branch:** todo/a-matched-preamble-is-named-as-an-empty-heading
**Claimed:** 2026-08-14

A document's text above its first `##` is a section with no heading, and where
the query matches it the answer names it as nothing: "of which the query matched
The Probe, Putting the Snippet Into TypoScript, ." Seen on 2026-08-14 for
`core/testing/proving-a-rendering` and, with a query taken from its opening
paragraph, for `any/testing/browser-check`, so it is the renderer rather than
one page. Either leave the preamble out of that list or name it by what it is —
the reader is being told a heading matched that the document does not carry.
