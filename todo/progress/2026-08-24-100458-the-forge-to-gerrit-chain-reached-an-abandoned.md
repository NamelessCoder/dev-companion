# A change answer names the issues its commit message resolves

**Serves:** feedback/2026-08-24-100458-the-forge-to-gerrit-chain-reached-an-abandoned.md
**Priority:** normal
**Branch:** todo/the-forge-to-gerrit-chain-reached-an-abandoned
**Claimed:** 2026-08-24

Judged on 2026-08-24 as step 2, delivery, and written up as `D-ANS-098`, which
carries the measurements and what the field owes: build it. Have
`Gerrit::change()` ask `o=CURRENT_COMMIT` as `changesForIssue()` already does,
read the `Resolves:` and `Related:` trailers of the commit message into a field
on every change entry the answer carries, and fill each issue with the subject,
tracker and status through the bulk read `Forge::issuesOf()` makes — which
needs a seam, since it is private today. Declare the field in
`GerritLookup::outputSchema()`, print it in the text half, and hold it in
`GerritTest` with a change naming two issues, one naming none, and the
`Resolves: #` with no number that change 91563 carries.
