# Decide which namespace this server keeps if `typo3.` is reserved

**Serves:** decisions/
**Priority:** high
**Waiting on:** which of the three namespaces this server keeps if the contract
    takes `typo3.` and `typo3://` — the `typo3_` tool prefix, the `typo3://core`
    URI scheme, the `typo3/cms-mcp` package name, or all three? The scheme is
    the one that collides rather than merely resembles, so a fourth answer is to
    say so in the comment period and keep it. Answering "all three, unchanged"
    is one of the answers and closes this the same way.

Read the draft RFC on an MCP interface contract for TYPO3
(https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5) against
the three namespaces this package already claims — the `typo3_` prefix on all 26
tool names in [`src/Tool/Registry.php`](../../src/Tool/Registry.php), the
`typo3://core` URI scheme in
[`src/Sdk/ResourceHandler.php`](../../src/Sdk/ResourceHandler.php), and the
`typo3/cms-mcp` package name in `composer.json` — and write a decision in
`decisions/scope/` saying which of them this server keeps. The RFC reserves
`typo3.` for the contract's mandatory elements and sends extensions to their own
prefixes, so the dot means the tool names do not literally collide; the URI
scheme does, because the same document requires the content model to be served
"as a resource under a uniform scheme" and `typo3://` is the scheme it would
reach for. Nothing in `decisions/` has ever said why the prefix is `typo3_`, so
this is a choice that was never made rather than one to defend. The comment
period opened on 2026-08-03 and the contract targets Q1 2027: before a scheme is
allocated this is a sentence in somebody else's draft, after it is a rename.
