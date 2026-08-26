# A page read whole names the hints it declares

**Serves:** D-ANS-114, feedback/2026-08-24-225022-backend-modules-run-in-an-iframe-and-no-page.md
**Priority:** normal

Judged on 2026-08-27 as step 2 of the ladder, delivery: the iframe fact is in
`browser-tests` verbatim, the page the session read declares that hint in its
front matter, and `RuleLookup::wholeDocument()` hands the declared hints over as
`alsoInHints` data and raw YAML while `RuleLookup::answer()` names its lexically
matched ones in prose. Make the whole-document answer close with the same
`alsoInHints()` line the search answer carries, and assert it in
`tests/Unit/KnowledgeTest.php` beside
`aDocumentIdReadsTheWholePageWithoutAResourceList`, which reads this path
already. The credentials half of the feedback is done; it stays open until this
lands, and `bin/cli feedback:archive` is what closes it.
