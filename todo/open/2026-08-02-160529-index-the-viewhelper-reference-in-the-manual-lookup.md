# Index the ViewHelper reference in the manual lookup

**Serves:** feedback/2026-08-01-003000-underlying-failure-was-a-systemic-lack-of-fluid.md
**Priority:** normal

Step 1b of the ladder, on the evidence in
[`D-ANS-023`](../../decisions/answers/ans-023-no-viewhelper-is-documented-in-any-manual-this-lookup-indexes.md):
the three manuals in `Documentation::DOCUMENTS` document no ViewHelper, so
`f:if f:then f:else condition ViewHelper` comes back with Developing a custom
ViewHelper and the Translate ViewHelper. Read what
`https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/` publishes
at its root against `Documentation::links()` before changing anything, since
that base is `/other/` rather than the `/m/` every entry is built with today —
then carry the manual in `DOCUMENTS` with the base it needs, re-run the two
queries in the decision at `targetVersion: "14"`, and check what the same change
does to a question that only mentions Fluid, which is the second **Wrong if**.
