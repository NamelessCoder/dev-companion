# Say what a manual search matched on

**Serves:** feedback/2026-08-01-002928-debrief-of-a-typo3-14-backend-preview-task.md, R-DOC-002
**Priority:** normal

Judged on 2026-08-02 as step 4 of the ladder, wording, with the evidence and the
two candidates in `D-ANS-021`: the page was indexed all along, and three
long queries ranked it 28th, 13th and 11th of 1230 while answering `answered`
with six results each. Take one of the two candidates and implement it — state
in the `typo3_documentation_lookup` description and the `queries` description
that page titles and section paths are what is matched and that words beyond the
subject re-aim the search, or carry it in the answer, where the result says what
the match was on or names the shortest sub-query still returning these pages.
Both touch the tool's declared schema or its answer shape, which is why this was
queued rather than done in the judging run. The reproducer is
`DocumentationLookup::answer(['queries' => ['Record API Fluid template access
record.header'], 'targetVersion' => '14'])`, and `Record API` alone returns
*Record objects* third.
