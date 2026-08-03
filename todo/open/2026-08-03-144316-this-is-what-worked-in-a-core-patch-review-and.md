# This is what worked in a core-patch review and must not be broken later. Full transcript availabl...

**Serves:** feedback/2026-08-03-144316-this-is-what-worked-in-a-core-patch-review-and.md
**Priority:** low

Judge this feedback rather than fix what it reports: re-run the query that
produced it against the server as it is now, then close it, trim it to the half
that is still open, or write the todo that takes it on. Write the judgement into
`decisions/` — the entry it was made against, or a new one where nothing says it
yet — because the commit that closes a feedback is the one place nobody can
search afterwards. `documentation/feedback/judging.md` is the ladder and the one
question it opens with, and what this feedback actually says is in the file it
serves rather than here.
