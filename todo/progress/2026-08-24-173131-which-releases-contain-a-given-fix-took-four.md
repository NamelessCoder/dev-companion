# Take a commit hash as a handle and name what a Releases: trailer claims

**Serves:** feedback/2026-08-24-173131-which-releases-contain-a-given-fix-took-four.md
**Priority:** normal
**Branch:** todo/which-releases-contain-a-given-fix-took-four
**Claimed:** 2026-08-25

Judged on 2026-08-25 as the ladder's step 1b, a missing shape, and taken on. The
reading is
[`D-ANS-106`](../../decisions/answers/ans-106-a-commit-in-a-checkout-is-a-handle-the-review-lookup-takes.md),
which also says why the tracker half is not built and why release versions stay
outside the boundary.

What the reading established against `review.typo3.org` on 2026-08-25, so it is
not established again. `commit:cf227b18e20` answers change 89740 on `main`, and
the message it comes back with carries `Releases: main, 13.4, 12.4`. The
Change-Id query `Gerrit::change()` already makes answers the two backports
beside it — 90012 on `13.4` at `aaec618cf33`, 90014 on `12.4`.
`change:cc880c67777` answers HTTP 400 `Invalid change format`, so passing a
commit as `change` today tells the caller the review server did not answer.

Two steps, both in `typo3_gerrit_lookup` and `Contribution\Gerrit`. First,
`commit` as a fourth way in, in the `oneOf` beside `issue`, `change` and the
search, querying `commit:` and reading on exactly as `change` does. Second, a
`releases` field per change, lifted from the `Releases:` trailer of the message
`o=CURRENT_COMMIT` already brings back — beside the `Resolves:` and `Related:`
trailers `Gerrit::trailers()` reads out of it — with the text half saying that
the trailer is the author's claim and the siblings are what landed.

What is still to be read is the description and the routing, since a handle
nothing points at is one nobody passes: the tool description, the `routing`
block of `knowledge/server-scope.json`, and `knowledge/task-intents.json` for
the triage shape that asks which releases carry a fix.
