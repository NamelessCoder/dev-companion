# Say which filter emptied a changelog miss

**Serves:** feedback/2026-08-01-115112-during-the-same-review-i-wanted-to-confirm-no.md
**Priority:** normal
**Branch:** todo/say-which-filter-emptied-a-changelog-miss
**Claimed:** 2026-08-03

Judged into
[`D-ANS-016`](../../decisions/answers/ans-016-a-miss-names-the-query-that-would-have-hit.md),
step 4 of the ladder: a miss narrowed by `version` or `type` counts the per-term
reach inside that narrowing and never says so, so
`"GifBuilder placeholder preview thumbnail"` at `version: "15"` reports "preview
reaches 1 entry" where all four words reach without it. Raised above the two
`D-ANS-030` todos because this is the sentence that cost the reporting session
its call and it needs no new read. Count the terms over the unfiltered entries
as well — 48 ms for all 3794 in `/home/benji/projects/typo3-cms` — and where a
term reaches there and not here, open the miss with the filter that emptied it
rather than with the narrowed counts; leave `largestReachingSubsets()` on the
narrowed set, which `D-ANS-016` explains, and hold it with an assertion that the
version-15 miss names the version.
