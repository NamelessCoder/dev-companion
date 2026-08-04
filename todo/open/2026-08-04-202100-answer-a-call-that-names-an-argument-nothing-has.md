# Answer a call that names an argument nothing has

**Serves:** feedback/2026-08-04-175819-task-repo-wide-cleanup-of-a-typo3-14-3-5.md, src/Tool/
**Priority:** normal

`D-ANS-053` decided that a rejection names the argument that was not understood
and left the two candidates to this step. Find out what the SDK's
`SchemaValidator` does with an unknown property under
`additionalProperties: false` — the same path `D-ANS-012` traced through
`Mcp\Server\Handler\Request\CallToolHandler` — by calling
`typo3_documentation_lookup` over stdio with `query` where `queries` is meant,
once as the schema stands and once with the keyword declared. Where the
rejection then names `query`, declare it on this tool's input schema and decide
in the same reading whether every input schema takes it, since a caller that
misspells an argument gets nothing back on any of them. Where it does not, the
singular alias is what is left, and `D-ANS-053` says why it is the weaker
answer. Hold whichever lands with a case in `ToolContractTest`.
