# price generating the derived half of a tool page at render time instead of committing it

**Serves:** documentation/server/tools/
**Priority:** low
**Waiting on:** whether the derived half stays committed. The reading is done
    and the measurement is below, so what is left is a want rather than a fact:
    whether 9,011 lines nobody edits are worth the diff they produce. Put to the
    maintainer on 2026-08-14 with three answers priced.

**Keep it, and delete this card.** The lines cost nothing to carry.
    `bin/cli tools:index` rewrites them in one command, and
    `ToolSurfaceTest::everyPageIsWhatTheRegistryDeclares` runs in `composer ci`,
    so a session that forgets is told before it commits rather than after.

    **Generate the whole derived half in `documentation:prepare`.** 9,011 lines
    leave the checkout, and eighteen of the last thirty commits touching
    `src/Tool/` then carry no documentation change at all. It cuts through a
    page, which `D-DOC-016` decided against: the eighteen recorded pages would
    open at `Answered` with no heading and no label, which is a fragment rather
    than a document, and `readme.rst` cannot go with them because its head is
    written by hand above the generated listing.

    **Generate the eight wholly derived pages alone.** 6,439 lines leave, the
    line stays between pages rather than through one, and every file left is a
    whole document. It costs the diff on the eight pages where it has actually
    been read — `3501f48d` is the one below.

    The recommendation is the first. Nothing in the measurement says the
    committed half is in the way of anything, and the other two pay a review
    diff for a saving in a directory nobody edits.

Half of `documentation/server/tools/` is reproducible anywhere and is committed
anyway. Measured on 2026-08-14 over the 28 pages: 9,011 derived lines — the head
of each, plus the whole page for the eight tools in `ToolCalls::derived()` — and
8,815 recorded ones. The derived half needs the registry, `knowledge/` and
`CoreFixture`, all of which are here, so `documentation:prepare` could write it
into the copy and the checkout could carry a page holding the recording alone.

The recorded half is not on the table. `tools:record` refuses without
`.checkouts/` and this repository is the only place that evidence exists, so
whatever happens it stays committed — `D-DOC-006` and `D-DOC-007` are why it
also stays on the tool's own page rather than in a directory beside it.

What decides it is not the lines but what they are for, and that was measured on
2026-08-14 over the last thirty commits touching `src/Tool/`. Twenty-seven
carried a page diff, and every one of those twenty-seven touched the derived
half; nine touched the recorded half as well, so eighteen commits' entire
documentation change was derived and would disappear. The paths moved three
times in that window — `documentation/clients/tool-answers/`,
`documentation/clients/tools/`, `documentation/tools/`, then
`documentation/server/tools/` — and a sweep naming only the current one reads
back three of thirty, which is the opposite answer.

The diff is produced rather than chosen: `ToolSurfaceTest` holds every page to
the registry, so a commit that rewrites a description and leaves the page
standing fails `composer ci`. It has also been read at least once. `3501f48d` is
a bugfix whose body states which hint ids the answer stopped offering and which
19 it now withholds, and the 1,063-line diff on `typo3_hint_lookup` is the only
place that is visible — a page in the eight, so the third answer above is the
one that would have taken it away.

Whether the other twenty-six were read is the part this repository cannot
answer. It merges fast-forward, and the five merge commits it carries are all
from 2026-08-01 and 2026-08-02, so nothing in the checkout records what somebody
looked at before a branch came home. No feedback, decision or commit message
reports a page diff catching something either. That is what the question above
is for.
