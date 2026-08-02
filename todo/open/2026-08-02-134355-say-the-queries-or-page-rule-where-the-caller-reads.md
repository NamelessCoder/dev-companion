# Say the queries-or-page rule where the caller of the manual lookup reads

**Serves:** feedback/2026-07-31-185900-the-typo3-documentation-lookup-tool-has-a.md
**Priority:** normal

Step 4 of the ladder, wording: `typo3_documentation_lookup` takes `queries` or
`page` and says so only in the root `oneOf`, which the tool reference does not
render and the validator reports one branch at a time — a call carrying
`targetVersion` alone comes back demanding `queries` and demanding `page`, in
that order, and the caller acts on the second. `D-ANS-012` has the re-run, the
messages and what would show the diagnosis wrong.

Next: settle which of the two shapes it becomes, because both touch the
contract. Either the input `oneOf` is rendered in
`documentation/clients/tools.md` — `ToolSurface::alternatives()` already does
this for output schemas, `src/Upkeep/ToolSurface.php:75` — with the rule in the
`queries` and `page` descriptions, or the root `oneOf` goes and the check in
`DocumentationLookup::answer()` answers instead, which already says it in one
sentence and today never runs. Read what a client is shown by `tools/list`
before choosing, and put the case beside
`StdioServerTest::invalidArgumentsAreRejectedBeforeTheToolRuns`.
