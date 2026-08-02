# Dissolve what is deliberately not queued into single todos

**Serves:** todo/

Take `todo/reference/not-queued-and-deliberately-so.md` apart, now that a
priority can say what only that page could say before. It is the last file here
that bundles several unrelated things, which is the shape `D-FBK-008` split
`todo.md` away from, and each of its three bullets is a card in a state that now
exists: `REVIEW-03` waits on a core checkout with uncommitted changes that
nothing produces, so it goes to `waiting/` with that as its question; the four
catalog roadmap items are not blocked and serve no open feedback, which is the
page's own reason for keeping them below everything that does — that is a low
priority, and they become four low cards; and the package updates come round
rather than waiting, so they fold into
`recurring/check-whether-mcp-sdk-has-released-a-newer-version.md`, which already
asks the same question every seven days about one dependency. What is then left
in `reference/` is the table of which checkout plays which environment, which is
no card in any state — decide there whether one page is worth a directory, and
where else it could go without landing in `scenarios/`, which AGENTS.md keeps it
out of on purpose.
