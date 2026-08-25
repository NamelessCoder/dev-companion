# Carry the change status a backlog row already fetched

**Serves:** feedback/2026-08-24-173151-reading-an-abandoned-change-s-inline-comments.md, D-ANS-069
**Priority:** normal
**Branch:** todo/reading-an-abandoned-change-s-inline-comments
**Claimed:** 2026-08-25

Add `status` to each entry of `reviews` on the enumeration path.
`Forge::reviewed()` already runs the batched review query and already receives
`status` per change from `Gerrit::change_()`; it keeps `number` and `url` and
drops the rest, so this costs no further round trip. Extend the enumeration
`reviews` object in `ForgeLookup::outputSchema()` and the row the text half
renders. Leave the issue path alone — there `reviews` is lifted out of the
journal and the review server is not asked, so a status would be a call the read
does not make. The verdict stays where it is: it is the inline comment, one
`/changes/<n>/comments` per change. The schema sentence *a handle and not a
verdict* has to be reworded rather than deleted, and what it must not invite is
skipping an issue on an `ABANDONED` — the reporting session read that argument
and fixed #35069 anyway. `D-ANS-069`'s amendment of 2026-08-25 has the
measurement and the first **Wrong if** the wording answers.
