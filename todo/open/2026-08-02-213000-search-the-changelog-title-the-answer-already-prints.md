# Search the changelog title the answer already prints

**Serves:** feedback/2026-08-01-115112-during-the-same-review-i-wanted-to-confirm-no.md
**Priority:** low

Judged as
[`D-ANS-030`](../../decisions/answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md),
step 1b of the ladder: `Changelog::entries()` hands `LabelSearch` the file name
and the words it spells, while `Changelog::read()` opens the file for the title
the answer then prints, and 708 of the 3794 entries in
`/home/benji/projects/typo3-cms` carry a title word their name does not. Give
the entry a searchable field carrying that title, decide where the read goes —
always, or only where the names match nothing — against the numbers that entry
measured (48 ms for the names of all 3794, 818 ms cold to open them, 4 ms for
the 352 entries of 13), correct the `query` description, which says "matched
against its title" today and means the file name, and hold it with an assertion
that `getTemporaryImageWithText` reaches
`7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions`.
