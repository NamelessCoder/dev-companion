# Stop a bare appliesTo word claiming a path segment, and return the subsystem's own hints

**Serves:** feedback/2026-08-07-132426-extbase-persistence-paths-return-fal-storage.md, R-ANS-026
**Priority:** high

Make `typo3_hint_lookup` with paths under
`typo3/sysext/extbase/Classes/Persistence/` return `persistence-reading` and
`extbase-domain-mapping`, and not `fal-storages-drivers`. Both halves are
measured and neither is in the corpus alone: dropping the bare `storage` from
`fal-storages-drivers`'s `appliesTo` removes the false positive and the three
FAL queries that reach it through that pattern each still rank it first on their
text, but the two Extbase hints are still not returned afterwards — they match
no `appliesTo` pattern for this task text and `Hints::find()` sorts `keywords`
above `score`, so a hint scoring 0 on a seven-character pattern outranks one
whose own words answer. Start in `Hints::scoreKeywords()`, where a bare-word
pattern is asked of the paths with `TermSearch::carries()`, a prefix match with
no word boundary that `D-ANS-050` set for `ThumbnailViewHelper.php`; whatever
replaces it has to keep that case working. Run `bin/cli hints:coverage` before
and after — this run measured three FAL queries by hand and did not sweep — and
add the regression the feedback asks for.
