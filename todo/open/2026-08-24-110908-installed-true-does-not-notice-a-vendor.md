# installed:true does not notice a vendor directory older than composer.lock; cost a phantom red suite

**Serves:** feedback/2026-08-24-110908-installed-true-does-not-notice-a-vendor.md
**Priority:** low

Judge this feedback rather than fix what it reports: re-run the query that
produced it against the server as it is now, then close it, trim it to the half
that is still open, or write the todo that takes it on. Write the judgement into
`decisions/` — the entry it was made against, or a new one where nothing says it
yet — because the commit that closes a feedback is the one place nobody can
search afterwards. `documentation/records/judging.rst` is the ladder and the one
question it opens with, and what this feedback actually says is in the file it
serves rather than here.
