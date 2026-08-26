# A change answer says what its patch set touches

**Serves:** feedback/2026-08-24-195307-reviewing-open-gerrit-changes-materialises-refs.md
**Priority:** normal

Judged on 2026-08-26 as step 1b, a missing shape, and `D-ANS-112` is the entry.
A change read by name answers everything about the review and nothing about the
patch, so the fetch ref is the only route this server offers to the changed
paths and the commit message — which is what a review is told to establish
first.

Add both to `typo3_gerrit_lookup` on a change read by name: `files` from
`o=CURRENT_FILES` on the query already being made, and `message`, which
`o=CURRENT_COMMIT` already fetches and `Gerrit::issues()` drops. Verify the
option against `review.typo3.org` and weigh what a file list adds to an answer
before deciding it rides on every named change. The diff itself stays out —
`D-ANS-112` has the boundary and what would show it wrong.

Then the two skills, which is why this is not only a schema change:
`typo3-core-patch-review` tells a review to establish the four things from "one
reading of the diff", which presumes a checkout, and neither skill says a
shortlist is triaged without fetching anything.

The second half of the feedback needs no build. The recoverability test it asks
for — is this local branch tip a patch set somebody pushed — is
`typo3_gerrit_lookup` with `commit`, measured on 2026-08-26 to reach superseded
patch sets as well, and the dated section on `D-ANS-106` is that reading. What
is missing is the routing: `typo3-core-patch-checkout` still says git's refusal
to delete a branch is the last thing that asks whether it is disposable. Name
the call there, before the forced deletion.
