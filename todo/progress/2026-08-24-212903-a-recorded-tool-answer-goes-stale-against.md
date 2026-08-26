# A recorded tool answer goes stale against knowledge/ and nothing says when

**Serves:** documentation/server/tools/
**Priority:** low
**Branch:** todo/a-recorded-tool-answer-goes-stale-against
**Claimed:** 2026-08-26

`tools:index` writes the eight derived pages and `tools:check` holds them, while
a recorded page is carried over as it stands. So a change to
`knowledge/server-scope.json` leaves `typo3_server_scope.rst` showing an answer
the server no longer gives, under a heading saying it was recorded that day.

Measured in this worktree on 2026-08-24:
`bin/cli tools:record .checkouts/14.3 2026-08-24 typo3_server_scope` moved three
`covers` entries, one `routing` entry and the `instructions` sentence, against a
page recorded the same morning.

The re-recording is not the step: it is one command over the whole tree, needs
the checkouts and the fixture console, and belongs to whoever merges rather than
to a branch that changed one sentence. What is open is whether the drift is
reported — a check saying which recorded page its own tool would no longer
answer with — or accepted on the strength of the date each page carries.
