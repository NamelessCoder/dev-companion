---
id: D-ANS-017
title: A union-typed argument gets the wording a client can compose against
date: 2026-08-02
status: open
coveredBy:
  - ToolContractTest::everyToolDeclaresSchemasAndAnnotations
  - ToolContractTest::noArgumentDeclaresMoreThanOneType
  - StdioServerTest::severalToolNamesTravelInOneStringAndAListIsRefusedWithTheTypeItWanted
  - FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares
  - FeedbackTest::severalToolsStaySeveralToolsRatherThanOneWord
---

# D-ANS-017 — A union-typed argument gets the wording a client can compose against

**An input schema is a grammar a client generates against, and the surface's
only union-typed argument is the only one a client never managed to send.**

It is not only a contract a client reads. `typo3_feedback_record` declares
`tool` as `["string", "array"]`, the one union in any input schema the server
offers, and one model recorded four feedback in 49 seconds carrying none.

## Evidence

- Re-run on 2026-08-02 against the server as it is now —
  `bin/typo3-dev-companion` over stdio from this worktree.
  `tool: "typo3_extension_describe"` is written as
  `tool: typo3_extension_describe`;
  `tool: ["typo3_extension_describe", "typo3_feedback_record"]` is written as
  `tool: typo3_extension_describe, typo3_feedback_record`. Both forms are
  accepted and both are stored as names. The server does not drop the value, on
  either branch of the union.
- The message the feedback quotes has no producer in this checkout. A `tool` the
  schema refuses comes back as JSON-RPC `-32602`,
  `Property '/tool': Invalid type. Expected `string|array`, but received `integer`.`
  — a validation error with a pointer, not a parse error, and nothing renders a
  value as a dot. "JSON Parse error Unexpected EOF" is the client failing to
  produce the call, before anything was sent.
- The call never reached the server, and the session was otherwise talking to it
  fine. `feedback/2026-07-31-194459`, `feedback/archive/2026-07-31-194504`,
  `feedback/2026-07-31-194510` and the report itself at `194548` are four writes
  from `/home/benji/projects/site-new` inside 49 seconds. All four are on disk.
  None of the four carries a `tool:` line.
- Nothing else in the surface is shaped like this. Walking every offered tool's
  `inputSchema` — properties, items and the `oneOf` branches under them — finds
  exactly one union: `typo3_feedback_record` `/properties/tool`. Every other
  union in the code is `[X, "null"]` and lives in an output schema.
- The corpus says which clients it stops. Of 207 feedback, 201 carry a `tool:`
  line, across six distinct model identifiers —
  `opencode/deepseek-v4-flash-free` 31 times, `nemotron-3-ultra-free` 9,
  `deepseek-v4-flash-free` 8, `big-pickle` and `GPT-5 mini` 3 each,
  `opencode/ling-3.0-flash-free` 2, plus 139 recorded before the model field
  existed. Six carry none: four are this session's, and the other two are
  archived and predate the model field. `opencode/mimo-v2.5-free` is the only
  model that has ever recorded feedback and never once sent `tool`.
- The union buys nothing the string branch does not. `Channel::toolNames()`
  splits a string on `[\s,;]+`, the argument description already says "Several
  may be named, as a list or separated by commas", and
  `FeedbackTest::severalToolsStaySeveralToolsRatherThanOneWord` covers the comma
  form on its own. `15405e5`, which introduced the list, wanted names that stay
  apart — not a second declared type.
- This is not a delivery gap. `documentation/clients/tools.md:1812` renders the
  argument as *(string or array)* and `tools/list` carries the union unaltered,
  so what the session read was accurate.

## Decided

- The judgement is **step 4 of the ladder**, wording — in the same sense as
  [`D-ANS-012`](ans-012-an-oneof-alternative-is-stated-where-the-call-is-composed.md),
  where a declaration reached the caller and the caller could not act on it. No
  tool is missing, nothing is misrouted, and the reference says the truth.
- The feedback is **queued whole**, not trimmed and not closed on the spot. It
  makes one claim, the claim is about a declared schema, and
  [judging.md](../../documentation/records/judging.rst) puts a tool's contract
  on the reviewed side of that line.
- Its own suggestion is rejected as a diagnosis: the value is not dropped during
  serialization and `tool` collides with no internal variable. What it got right
  is the parameter and the symptom, which is what a report from outside can see.
- Two candidates, and neither is chosen here: declare `tool` as a plain `string`
  and let the comma form carry the several, or keep the union and accept that it
  costs a client. The first gives up a machine-readable list and would start
  refusing an array over the wire while the unit case covering the list — since
  renamed `FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares` —
  stays green, because that test calls `Channel::record` directly. `R-FBK-001`
  is what either has to keep held.
- [`D-ANS-005`](ans-005-an-unmet-precondition-is-answered-not-raised.md) and
  `D-ANS-012` both bet that a client reading less of a schema than it declares
  gets a weaker promise and nothing worse. This is the counter-example: the
  degradation was an argument that could not be sent at all.

## Assumed

- That the union type is what the client failed on. Nothing here can run that
  client, so the reasoning is circumstantial: the call never arrived, the
  session wrote four notes without it, and `tool` is the only argument in the
  surface with that shape. The same pattern is what `D-ANS-012` reasoned from.
- That `opencode/mimo-v2.5-free` is one client's tool-call layer rather than one
  bad session. Four attempts across three subjects is what makes it look like a
  property; four attempts is still one session.

## Wrong if

- `tool` is declared as a plain `string` and a feedback from
  `opencode/mimo-v2.5-free` still arrives without one. Then the union was not
  what stopped it, and the failure is somewhere this repository has not looked.
- A feedback arrives from `opencode/mimo-v2.5-free` carrying a `tool:` line
  while the union still stands. Then the schema was never the obstacle and this
  entry read one session's failure as a property of the shape.
- Another argument in this surface is given a union type and every client
  composes calls for it. Then a union is not a shape that costs a caller, and
  the diagnosis here was about `tool` alone.

## Since then

The first of the two candidates was taken: `tool` is declared `string`, the
several are named in one string separated by commas, and the `items` beside the
union is gone with it. Four readings decided it rather than a preference between
them.

The union really was on the wire and really was alone there. `tools/list`, run
over stdio from this worktree, carries `["string","array"]` with its `items`
unaltered, so a client that reads the schema was told both branches and one
still could not produce the call. Walking every offered tool's input schema
again finds no second union, and the 91 unions in the output schemas are all
`[X, "null"]` — a nullable field, not an alternative a caller has to choose
between. So nothing else in this package now says the opposite about the shape,
which is the reading that kept the keyword in
[`D-ANS-012`](ans-012-an-oneof-alternative-is-stated-where-the-call-is-composed.md)
and does not apply here.

The string branch gives a client nothing less. `Channel::toolNames()` splits on
`[\s,;]+`, the description said so before this change and says so more plainly
now, and `R-FBK-001` is held on the wire for the first time rather than only
below it.

The two failures are not the same kind. A client that cannot compose the call
sends no argument at all: nothing is refused, nothing is said, the feedback
lands unattributed, and the session wrote the report this entry answers. A
client that sends a list now gets
`Property '/tool': Invalid type. Expected `string`, but received `array`.`
before the tool runs — the property, the type it wanted, the type it got. The
first correction is one nobody receives; the second is one a caller can act on.
That is the cost being accepted, and it is what `D-ANS-012` weighed the other
way, where dropping the keyword would have bought a message this repository does
not own.

The **Wrong if** is what settles the order, as it did there. The first of the
three only exists once `tool` is a plain string — a feedback from
`opencode/mimo-v2.5-free` arriving without one is what would show the union was
never the obstacle. The second falsifier is a wait on a client that has already
stopped sending the argument, and waiting for it would have established nothing.
The third stays as it stands: a union declared in a second tool would say a
union costs no caller, and `ToolContractTest::noArgumentDeclaresMoreThanOneType`
is where somebody who means to try one has to disagree in writing.
