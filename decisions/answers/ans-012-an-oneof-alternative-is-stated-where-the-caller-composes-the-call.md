---
id: D-ANS-012
title: An `oneOf` alternative is stated where the caller composes the call
date: 2026-08-02
status: open
coveredBy:
  - ToolContractTest::anArgumentInAnAlternativeNamesTheOnesItExcludes
  - StdioServerTest::aCallCarryingNeitherOfTwoAlternativeArgumentsNamesBoth
---

# D-ANS-012 — An `oneOf` alternative is stated where the caller composes the call

**An alternative between two arguments declared only as `oneOf` reaches no
caller: the reference renders none of it, and the rejection names one branch at
a time.**

`typo3_documentation_lookup` takes `queries` or `page`, never both, and the
schema says so in the one keyword nothing here reads out. What a caller is shown
is `targetVersion` required and two optional arguments; what a caller that then
sends only `targetVersion` gets back is two sentences, each demanding a
different property and neither stating the rule.

## Evidence

- `feedback/2026-07-31-185900`, re-run on 2026-08-02 against the server as it is
  now — `bin/typo3-dev-companion` over stdio from this worktree.
  `{"queries": ["encryption key environment variable TYPO3_ENCRYPTION_KEY"], "targetVersion": "14.3"}`,
  no `page`, is answered: six results from docs.typo3.org, the first the coreapi
  page on environment variables in site handling at 14.3. The chicken-and-egg
  the feedback reports does not exist and did not exist on the day it was
  written.
- The message it quotes has one producer. Arguments of
  `{"targetVersion": "14.3"}` alone are rejected with
  `Missing required properties: queries.; Missing required properties: page.` —
  one per `oneOf` branch, joined, because the SDK's `SchemaValidator` collects
  the leaves of a failed `oneOf` and formats each on its own. A caller that acts
  on the last half sends `page: ""`, which is the second thing the feedback
  reports and is rejected correctly: `Minimum string length is 1, found 0`.
- The session called this checkout. `/home/benji/projects/site-new/.mcp.json`
  runs `/home/benji/projects/typo3-dev-companion/bin/typo3-dev-companion`, and
  `9ced27c` — 2026-07-30, the day before the report — is where `required` became
  `['targetVersion']` with the `oneOf` beside it. So the schema the session read
  and the server it called are the ones above, and its reading of the schema was
  right.
- The keyword is on the wire and nowhere else. `tools/list` carries the `oneOf`
  whole, while `documentation/clients/tools.md` lists `queries` and `page` as
  plain optional arguments: `ToolSurface::alternatives()` runs on the output
  schema only (`src/Upkeep/ToolSurface.php:75`), where it renders "the answer
  carries exactly one of these sets of fields" for nine tools.
- Nothing else here is shaped like this. `typo3_documentation_lookup` is the
  only tool declaring an input-side `oneOf`, which is why one tool's callers hit
  it and the rest of the surface never did.
- The tool already says the rule in one sentence and never gets to.
  `src/Tool/DocumentationLookup.php:113` throws "Pass targetVersion and exactly
  one of queries or page", and the validator rejects the call before the tool
  runs.

## Decided

- The judgement is **step 4 of the ladder**, wording. The tool is not missing a
  verb and answered in one call; what was missing is the rule stated where a
  caller composes the call, and the message that could have corrected it.
- The search half is **answered** and the feedback is **trimmed** to what is
  left. The tool's own suggestion — document a two-step workflow, or pass a
  placeholder page URL — describes a server that never existed.
- The rest is **queued**, not closed on the spot. Both candidates touch the
  declared schema, the argument descriptions or the reference generator, and
  [judging.md](../../documentation/records/judging.rst) puts a tool's contract
  on the reviewed side of that line.
- The two candidates are named and neither is chosen here: render the input
  `oneOf` in the reference and put the rule in the two descriptions, or drop the
  root `oneOf` so the tool's own message answers instead of the validator's. The
  second buys a legible rejection and gives up the machine-readable exclusivity
  a client that does read `oneOf` gets today.

## Assumed

- That the call reaching the server carried no `queries`. The message has no
  other producer in this checkout, and nothing recorded the call itself — the
  session reports omitting `page`, which cannot be what was rejected.
- That a caller composing a call reads `required` and the argument descriptions
  rather than the `oneOf` beside them. That is what the session did, and it is
  one session.

## Wrong if

- The rule lands in the descriptions and the reference, and a session still
  calls with `targetVersion` alone. Then the shape is what misleads, not the
  wording, and dropping the keyword is the answer rather than explaining it.
- A feedback reports a call that carried `queries` being rejected for a missing
  `page`. Then the validator is at fault and the delivery diagnosis here is
  wrong.
- Another tool grows an input-side alternative and its callers compose the call
  correctly. Then `oneOf` does reach a caller, and this entry was reading one
  session's mistake as a property of the keyword.

## Since then

On 2026-08-04, a session hit the same rejection without the **Wrong if**
holding. `feedback/2026-08-04-175819` composed a search and spelled its argument
`query`, which this tool does not have: the unknown property was ignored, the
`oneOf` failed on both branches, and the message named `queries` and `page`
exactly as this entry describes. The session read it and called correctly on the
second attempt.

## Since then

The first of the two candidates was taken: the keyword stays, the reference
renders it, and the rule stands in the `queries` and `page` descriptions. Three
readings decided it rather than a preference between them.

`tools/list` was run against this checkout over stdio and carries the root
`oneOf` whole, unaltered by the SDK. So the exclusivity is already on the wire
in a form a client can validate against, and dropping the keyword would take
that away in exchange for a message this repository does not own — the two
sentences are built in `Mcp\Server\Handler\Request\CallToolHandler` from the
schema, and reaching them without dropping the keyword means replacing the SDK's
request handling rather than wording a tool.

`D-ANS-005` made the same bet deliberately on the other side: the output schemas
declare their two shapes as `oneOf` and accept that a validator ignoring the
keyword reads a weaker promise. Dropping it here would have had one package
saying opposite things about one keyword.

The **Wrong if** above is what settles the order. It is written as "the rule
lands in the descriptions and the reference, and a session still calls with
`targetVersion` alone" — a falsification that only exists if the wording is
tried first, and the answer it names if it fails is dropping the keyword. So the
second candidate is not discarded; it is what this entry is now waiting to find
out about.

So the wording is not what failed, and the falsification above is not satisfied
— a call that carries a query under another name is not a call composed with
`targetVersion` alone. What that report is about is the name rather than the
alternative, and it is `D-ANS-053`.
