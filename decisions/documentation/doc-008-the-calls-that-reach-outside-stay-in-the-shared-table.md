---
id: D-DOC-008
date: 2026-08-02
status: confirmed
---

# D-DOC-008 — The calls that reach outside stay in the shared table

**`Upkeep\ToolCalls` gains the two live `typo3_documentation_lookup` calls and
`ToolContractTest` drives them like every other, because a host that does not
answer is an answer the schema already declares.**

The recording showed one tool refusing a question and never answering one, and
the way out was assumed to cost the contract test its independence from
docs.typo3.org. It does not, and that is the whole of what this settles.

## Evidence

- `typo3_documentation_lookup` was the only tool of the twenty-two in the
  recording whose page never carried an answer. The one call in the table asked
  for TYPO3 999, so the page showed `status: "unavailable"`, an empty `results`
  and `version-not-covered` — and both modes the description documents, a search
  and a canonical URL handed back as `page`, were illustrated by nothing.
- The premise that a live call makes CI depend on the host is false in the sense
  that matters. Driven with `https_proxy` pointed at a closed port, the search
  answers `status: unavailable` with `cause: source-not-answering` in 0.1
  seconds, in three lines of text, and the data validates against the tool's own
  output schema with zero errors. `ToolContractTest` asserts non-empty text,
  non-empty data and schema validity: an unreachable host changes which path is
  validated, not whether the assertion holds.
- What it costs when the host does answer: one table of contents per manual at
  14.3 — 162 KB, 43 KB and 34 KB — plus one request per result, and one more for
  the page. The search entry passes `limit: 3` rather than the default 6, so a
  contract run makes six requests in about 0.6 seconds.
- The slow case is a host that accepts the connection and then hangs, which
  `CURLOPT_CONNECTTIMEOUT` 3 and `CURLOPT_TIMEOUT` 8 bound at 32 seconds over
  four fetches — still green. A refused or unresolved host costs nothing at all,
  which is the shape a runner without egress has.
- Nothing recorded why the 999 call was the one that went in. It arrived with
  the tool in `7d29c77`, whose message says only what the feature was, so the
  reason read into it afterwards was inference rather than a decision anybody
  wrote down.

## Decided

- One table, no skip list. Both new calls are validated by `ToolContractTest`
  alongside the other forty-four, so the recording keeps illustrating only calls
  something validates — which is why `D-DOC-006` put the table in one place.
- Neither option the todo offered was taken, and both fail for the same reason.
  A call the contract test skips by name and a second table read only by
  `tools:record` each buy independence from a host that the measurement says is
  not needed, and each pay for it in the one currency this table exists to save.
- Two calls rather than one, because the second's argument is the first's
  answer. `documentation: search` asks two queries at 14.3 and
  `documentation: page` hands the canonical URL of its first result back, which
  is the two-step the tool's description tells a client to make.
- `limit: 3`. Each result costs another request to somebody else's host, and
  three results show the shape of a list as well as six do.
- 14.3 is written into both calls literally, like every other argument in the
  table. It is the newest released covered line and the one `tools:record`
  answers against, and the page URL carries the version in it either way.

## Assumed

- docs.typo3.org tolerates the traffic. A push runs the suite on three PHP
  versions, so it is eighteen requests per push and about 700 KB, from a user
  agent that names this server and its release.
- The recorded page keeps existing at that URL. Nothing pins it, and a manual
  reorganised between releases is the ordinary way it would stop.

## Wrong if

- A CI run goes red on one of these calls. That would mean a path exists that
  answers neither a result nor `source-not-answering`, and the fix is in the
  tool or its schema rather than in a skip list around it.
- The suite has to run where nothing may leave the network, or the host starts
  refusing this user agent. Then the calls come out again, and the option to
  take is the skip list rather than the second table: one place that holds every
  call is worth more than one place that validates every call.
- The recording is re-run while the host is down and the page silently goes back
  to showing no answer. The head says which day it is of and says nothing about
  whether docs.typo3.org answered, so it would read as a defect in the tool.
- 14.3 leaves the covered versions and both calls fall to `version-not-covered`,
  which puts the page back where it started while everything stays green.

## Covered by

- `DocumentationTest::aSourceThatDidNotAnswerIsStillAnAnswerToTheSchema`
- `ToolContractTest::aToolCallAnswersWithTextAndMatchingData`

## Confirmed on 2026-08-23

The three calls are still in the shared table and the suite is green on them.
`typo3_documentation_lookup` answered `answered` for a live 14.3 search on
2026-08-23, and the third call — the version outside the covered ones — is what
holds the other half of the schema without reaching the network at all.

None of the four **Wrong if** has fired. No CI run has gone red on these, and
nothing has asked for the suite to run where the network is closed, which is the
one case the entry reserves the skip list for.

The fourth is the one with a date on it rather than a report: 14.3 is still
covered, beside 12.4, 13.4 and `main`. When it leaves, both live calls fall to
`version-not-covered` and the page goes back to what it was — so what to watch
is `knowledge/versions.json`, and the recording is where it shows.
