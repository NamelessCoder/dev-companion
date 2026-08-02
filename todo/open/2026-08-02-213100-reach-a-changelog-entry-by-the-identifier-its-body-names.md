# Reach a changelog entry by the identifier its body names

**Serves:** feedback/2026-08-01-115112-during-the-same-review-i-wanted-to-confirm-no.md
**Priority:** low

Judged as [`D-ANS-030`](../../decisions/answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md),
step 1b of the ladder, and the half the title alone does not answer:
`getTemporaryImageWithText` is in the body of `13.0/Breaking-101955`,
`8.0/Breaking-72426` and `7.1/Deprecation-46770`, in the title of the last one
only, and in no file name. Establish first whether the whole `:php:` index is
usable or only the Removed-lists the feedback asks for — there are 10842
distinct roles across 1951 of the 3794 entries, `Breaking-101955` writes
`GraphicalFunctions` 44 times while being titled about image generation, and
that entry's first **Wrong if** is a query for one class returning entries about
another — then make an identifier reach the entries that name it, and hold it
with an assertion naming `Breaking-101955` for the method above.
