# Say what an `f:else` does to the branch beside it

**Serves:** feedback/2026-08-01-003448-specific-fluid-f-if-f-then-f-else-failure-a.md
**Priority:** normal
**Branch:** todo/say-what-an-f-else-does-to-the-branch-beside-it
**Claimed:** 2026-08-02

Step 1a of the ladder, on the evidence in
[`D-KNW-016`](../../decisions/knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-gap-this-server-owns.md):
`bin/cli hints:probe` reaches nothing on the feedback's own query, no statement
below `knowledge/` or `skills/` says which child of an `f:if` renders, and the
ViewHelper reference says the opposite in its Basic usage. The reading that is
owed first is the range, because
[`D-VER-003`](../../decisions/versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)
pins one engine per major and only 5.3.1 has been read: make its throwaway
directory per major — `^2.15.0`, `^4.6.1`, `^5.3.1` — and render
`<f:if condition="1">A<f:else>B</f:else></f:if>` plus the same with `f:else if`
through a probe ViewHelper, so what the statement says about 12 and 13 is
measured rather than carried over. Then write it onto the `fluid-templates`
entry of `knowledge/architecture-hints/fluid.json` with the feedback's own
working markup as the example and a `since` or `until` only where the
measurement asks for one, run `bin/cli knowledge:format`, and check with
`bin/cli hints:probe` on the feedback's query that it now reaches.
