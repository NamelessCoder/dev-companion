# Settle what the `tool` argument of `typo3_feedback_record` declares

**Serves:** feedback/2026-07-31-194548-typo3-feedback-record-tool-parameter-causes.md
**Priority:** normal

Step 4 of the ladder, wording: `tool` is declared `['string', 'array']` at
`src/Tool/FeedbackRecord.php:46` and is the only union-typed property in any
input schema this server offers. The server accepts both branches — the re-run
is in `D-ANS-017`, which also has the corpus count and what would show the
diagnosis wrong — but one client never composed a call carrying it at all.

Next: settle which of the two shapes it becomes, because both touch the
contract. Either `tool` becomes a plain `string` and the comma form carries the
several — `Channel::toolNames()` already splits on `[\s,;]+` and the description
already says so — or the union stays and the cost is written down as accepted.
The first needs a case that goes over the wire before it can be trusted:
`FeedbackTest::aListOfToolsIsAcceptedAsOne` calls `Channel::record` directly, so
it would stay green while an array sent by a client starts being refused. Put
that case beside `StdioServerTest::invalidArgumentsAreRejectedBeforeTheToolRuns`,
and keep `R-FBK-001` held either way.
