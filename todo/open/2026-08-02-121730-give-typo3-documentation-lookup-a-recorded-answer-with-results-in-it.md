# Give typo3_documentation_lookup a recorded answer with results in it

**Serves:** documentation/clients/tool-answers/
**Priority:** normal

Add a call to `Upkeep\ToolCalls` that reaches the manuals, so the page for
`typo3_documentation_lookup` shows a result. The one call recorded today is
`documentation: unsupported version`, which asks for TYPO3 999: the page carries
`status: "unavailable"`, an empty `results` list and the cause
`version-not-covered`. Both modes the tool documents — a search over a covered
version, then a canonical URL handed back as `page` — are illustrated by
nothing, and this is the only tool whose recording never shows an answer.
Settle what the call costs `ToolContractTest` before adding it: that test drives
the same table, so a call reaching docs.typo3.org makes CI depend on a host
outside this repository, which is why the unsupported path is the one in there
now. Either the shared table gains a call the contract test skips by name, or
`tools:record` reads a second table — which `ToolCalls` argues against, because
two tables drift and the recording then illustrates calls nothing validates.
Write which of the two it is into a decision, since the next session will
otherwise reopen it.
