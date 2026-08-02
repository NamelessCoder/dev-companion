---
id: D-ANS-005
date: 2026-08-02
status: open
---

# D-ANS-005 — A question that is not supported here is answered in a shape of its own

**A question this server cannot answer here answers with `unsupported` and a
reason, replacing the result rather than labelling it — and it is never an MCP
tool error.**

`D-ANS-001` kept the result shape and added the reason beside it. Holding that
shape means emitting every field the schema requires, and those fields are the
answer: a count, a flag, a list. What they said had to be unsaid, so the counts
and the flags became nullable — a shape kept by faking the numbers out of it.
The shape was never the thing worth keeping.

## Decided

- The unsupported answer replaces the result. `Result\Unsupported` builds it and
  nothing else does: `unsupported` with a `cause`, the `reason`, the diagnosis,
  where discovery looked and what was set wrong. Beside it stands only the
  caller's own arguments coming back, which claim nothing about anything.
- `cause` is `no-installation`, `misconfigured` or `installation-not-answering`.
  Prose already carried the difference and only prose did; `META-02` requires
  "nothing found" and "found but not running" to be distinguishable, and a
  client cannot lexically match its way to that.
- `answeredBy` loses `nothing` and keeps `installation` and `packages`. Which of
  two sources answered is a question that has no answer where neither did, and
  the third case is now a different key rather than a third value of this one.
- No tool sets `isError`. Nothing failed: the question is simply not supported
  in this directory, and the server knows why and says so. `Result\ToolResult`
  has no such field, so the only route to an MCP error stays a thrown exception
  — an unknown tool, or arguments the schema rejected.

## Assumed

- A client shows a non-error answer as readily as an error one. An error is not
  more visible, only more alarming, and a caller that has to tell "no icon" from
  "no installation" needs the reason rather than the severity.
- A client branches on a key being present before reading the fields under it.

## Wrong if

- A client reads the answer fields without checking for `unsupported` and treats
  their absence as a crash or an empty answer. The nullable shape would have
  handed it something to misread; this one hands it nothing, which is the bet.
- A client swallows an answer it cannot act on and surfaces only errors, so the
  user is told nothing where an error would have reached them.
- A client validates `structuredContent` strictly and rejects an answer whose
  fields left the required list. That list is now the caller's own arguments and
  no more, because the SDK's `SchemaValidator` has no `oneOf` and the two shapes
  cannot be related in the schema itself — so what a hit guarantees is held by
  `ToolContractTest` here rather than by the document a client reads.

## Covered by

- `ToolContractTest::aQuestionThatCannotBeAnsweredHereStatesThatAndNothingElse`
- `ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer`
- `StdioServerTest::aQuestionThatCannotBeAnsweredHereIsStillAnAnswer`
