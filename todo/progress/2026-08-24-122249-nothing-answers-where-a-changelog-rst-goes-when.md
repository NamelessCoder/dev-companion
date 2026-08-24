# Write the changelog procedure as a guide of its own

**Serves:** feedback/2026-08-24-122249-nothing-answers-where-a-changelog-rst-goes-when.md
**Priority:** normal
**Branch:** todo/nothing-answers-where-a-changelog-rst-goes-when
**Claimed:** 2026-08-24

Judged on 2026-08-24 as the ladder's step 2 and written up in `D-KNW-111`: the
placement rule for a backport was here three times over and correct, and the one
surface the session read — the `guides` list — named no page for the changelog.

Move `## Changelog Files` out of
`knowledge/documents/core/contribution/commit-messages.md` into
`knowledge/documents/core/contribution/changelog.md`, announce it in
`knowledge/server-scope.json`, and have the commit-message page name it for the
entry a message announces.

Then read the byte level against `.checkouts/`, which is what both sessions
guessed at: the spacing of the `include` directive, how long the title's fence
has to be, the `.. index::` tag vocabulary, and that `Index.rst` globs. Those go
into the `documentation-changelog` hint, and one report contradicts the checkout
on the first of them.
