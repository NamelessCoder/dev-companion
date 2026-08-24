# typo3_rule_lookup hands a whole page over on one thin match

**Serves:** feedback/2026-08-24-110851-signed-off-by-is-in-no-rule-document-and.md, D-ANS-101
**Priority:** normal

Put the floor and the measurement into `RuleLookup::oneDocument()` and
`Prose::pageRecords()` as `D-ANS-101` decides — the page only where more than
one section matched, and the record carrying the score and the coverage
`Documents::search()` produced instead of the constant `0` and `1.0` it returns
today. Measure what the floor costs first over `Documents::topics()`, which says
how many of the corpus's subjects reach exactly one section, and settle there
what the `documentId` path reports, where nothing was matched and the declared
schema requires both fields. `query="signed-off-by"` at `targetVersion="15.0"`
is the reproduction: it answers `any/testing/proving-a-condition` whole on one
section carrying "signed in". Hold it with a `KnowledgeTest` case beside
`aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage`, carrying
`#[Decision('D-ANS-101')]`.
