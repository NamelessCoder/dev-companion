# read what the core files as breaking with no PHP member moved, and write it

**Serves:** feedback/2026-08-12-092607-nothing-answers-whether-a-changed-frontend.md
**Priority:** normal

Sweep `.checkouts/main`, `14.3`, `13.4` and `12.4` for Breaking entries that move
no PHP member — a changed rendered frontend markup for unchanged content, a
changed default TypoScript — starting from
`Changelog/12.0/Breaking-98308-LegacyHTMLAttributesBorderAndLongdescRemovedFromFrontendRendering.rst`
and `Changelog/12.0/Breaking-96831-EnforceHTMLSanitizerDuringFrontendRendering.rst`,
and establish whether that is the rule or two picks, where the boundary against
`Important` runs, and whether any of them sits in an `<lts>.x` directory. Then
write what holds into the corpus, choosing between the three placements
`D-KNW-072` names, and record the reading in that entry.
