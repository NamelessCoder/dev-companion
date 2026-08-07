# A core PHP path may not draw sitepackage authoring hints

**Serves:** feedback/2026-08-07-233501-typo3-hint-lookup-answered-three-core-frontend.md
**Priority:** normal

Three core frontend `Classes/` paths — `PageLinkBuilder`,
`AbstractMenuContentObject`, `RecordAccessVoter` — answered with
`frontend-records` and `page-variables-and-processors`, which are sitepackage
and TypoScript authoring hints about TCA files, `dataProcessing`, `PAGEVIEW` and
menu processor `excludeDoktypes`. Reproduced on 2026-08-08. One of the four
returned was adjacent and paid for the call: `persistence-reading` on
`FrontendRestrictionContainer`.

This is `D-ANS-060`'s shape on a different subsystem, and yesterday's ranking
fix is not enough for it — both wrong hints score above zero, so they answer
something and are not caught by the tier order. What separates them is that the
caller named `typo3/sysext/frontend/Classes/`, which is core PHP, while the
hints are about files a sitepackage author writes. Weigh the path against what a
hint is for, rather than only against the words it carries. Measure with
`bin/cli hints:coverage` before and after, and keep the two cases `D-ANS-060`
already protects: `thumbnail` reaching `fal-processing` through
`ThumbnailViewHelper.php`, and the DataHandler path reaching
`datahandler-basics`.
