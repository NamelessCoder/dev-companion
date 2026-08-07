# Let a Forge review URL settle what an empty Gerrit answer means

**Serves:** feedback/2026-08-07-132416-a-private-gerrit-change-is-reported-as-empty.md
**Priority:** low

The harm is fixed and this is the half that would make the answer definite
rather than careful. `typo3_gerrit_lookup` now says, where a named change comes
back empty, that it cannot separate "no such change" from "not one an anonymous
reader may see", and that the second is the likelier where the id came from a
commit in front of you — `R-ANS-027`. The report's own last idea goes further:
where `typo3_forge_lookup` would surface a review URL for the same issue, an
empty Gerrit answer for that number is positive evidence of a restricted change
rather than a guess between two. Gerrit Code Review posts that URL as a journal
entry on the issue, which is how the reporting session knew 95162 existed at
all. What it costs is the coupling — one tool call reaching a second host to
qualify its own answer — and that is the part to price rather than assume: a
caller that passed a `change` and no issue has given nothing to search Forge
with, so it only applies where the Change-Id or number can be tied back to an
issue. Do not reopen the status question: asked directly on 2026-08-07,
`change:95162`, its Change-Id and a number that exists nowhere all answer `200`
with `[]`, so the `source-not-answering` in the report was one call going
unanswered and not a second shape of the permission effect.
