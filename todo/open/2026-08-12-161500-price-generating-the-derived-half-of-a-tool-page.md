# price generating the derived half of a tool page at render time instead of committing it

**Serves:** documentation/server/tools/
**Priority:** low

Half of `documentation/server/tools/` is reproducible anywhere and is committed
anyway. Measured on 2026-08-12: the 26 pages hold 8,533 derived lines — the
head, plus the whole page for the eight tools in `ToolCalls::derived()` — and
8,481 recorded ones. The derived half needs the registry, `knowledge/` and
`CoreFixture`, all of which are here, so `documentation:prepare` could write it
into the copy and the checkout could carry a page holding the recording alone.

The recorded half is not on the table. `tools:record` refuses without
`.checkouts/` and this repository is the only place that evidence exists, so
whatever happens it stays committed — `D-DOC-006` and `D-DOC-007` are why it
also stays on the tool's own page rather than in a directory beside it.

What decides it is not the 8,533 lines but what they are for. They are a review
artifact: a changed tool description or schema shows up as a documentation diff
in the pull request, and `tools:check` compares the committed page against the
registry. Generated at render time the page is never stale and the change is
never visible either, so the question is whether the diff is read by anybody. Go
and look: how many of the last thirty commits touching `src/Tool/` carried a
page diff, and did any review turn on it.

Ask before doing it. Splitting a file `D-DOC-007` deliberately made one needs a
decision of its own, and this todo is the reading rather than the change.
