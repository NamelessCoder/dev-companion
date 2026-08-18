# a client that defers MCP schemas lists the tools by name only, and a whole TYPO3 session went by ...

**Serves:** feedback/2026-08-18-113308-a-client-that-defers-mcp-schemas-lists-the.md, feedback/2026-08-18-080710-a-whole-session-ran-with-zero-calls-because-a.md
**Priority:** low

Judge this feedback rather than fix what it reports: re-run the query that
produced it against the server as it is now, then close it, trim it to the half
that is still open, or write the todo that takes it on. Write the judgement into
`decisions/` — the entry it was made against, or a new one where nothing says it
yet — because the commit that closes a feedback is the one place nobody can
search afterwards. `documentation/records/judging.rst` is the ladder and the one
question it opens with, and what this feedback actually says is in the file it
serves rather than here.

The second feedback was folded in on 2026-08-18 and adds evidence rather than a
question. `feedback/2026-08-18-080710` is a whole session in
`/home/benji/projects/blog` that called nothing either, and it names the one
line that would have caught it: the `routing` entry sending a caller onto a
major they have not built on recently to `typo3_changelog_lookup`, which has
stood in `knowledge/server-scope.json` since 2026-07-29 and is reachable only
through a call. So two directories and two task shapes now rest on this answer,
and `D-SKL-060` is what left it here. The judgement sets the priority.
