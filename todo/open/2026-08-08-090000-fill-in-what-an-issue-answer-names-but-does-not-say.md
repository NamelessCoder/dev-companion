# Fill in what an issue answer names but does not say

**Serves:** feedback/2026-08-07-231225-an-issue-s-relations-come-back-as-bare-numbers.md, feedback/2026-08-07-231146-gerrit-change-references-arrive-as-comment.md, R-ANS-029
**Priority:** high

Two fields of `typo3_forge_lookup`'s issue answer name a record and say nothing
about it, and a triage skips both. Relations come back as `{issue, relation}`
pairs: on 15984 that is four numbers, one of them `32756` marked `precedes`,
which is "Massive Memory Leak in 4.5.8+ / 4.6" — the issue the 2012 revert was
filed under, and the single record that answers what a fix would cost. The
session skipped all four rather than spend four issue reads and found it in a
git commit message afterwards. Give each relation the subject, tracker and
status a search hit already carries; measured on 2026-08-08,
`issues.json?issue_id=22860,26484,78825,32756&status_id=*` fills all four in
**one** call, so the price is one bulk read per issue answer rather than one per
relation. Then the Gerrit references: they are in the notes as prose — "Patch
set 3 … It is available at http://review.typo3.org/38419" — where they read as
history rather than as a handle, and the session never loaded
`typo3_gerrit_lookup`'s schema at all. Lift them into a field of their own,
change number and status where known, and name that tool as the way to read
them. Both are parsed out of what the payload already holds. `D-ANS-064` carries
the judgement.
