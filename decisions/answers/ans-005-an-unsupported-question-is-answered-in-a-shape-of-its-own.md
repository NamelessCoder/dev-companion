---
id: D-ANS-005
title: 'An unsupported question is answered in a shape of its own'
date: 2026-08-02
status: open
coveredBy:
  - ToolContractTest::aQuestionThatCannotBeAnsweredHereStatesThatAndNothingElse
  - ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer
  - ToolContractTest::anInstallationBackedSchemaOffersEitherShape
  - StdioServerTest::aQuestionThatCannotBeAnsweredHereIsStillAnAnswer
---

# D-ANS-005 — An unsupported question is answered in a shape of its own

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
- The output schema declares the two as `oneOf`, so a hit keeps every field it
  ever required and an answer carrying both shapes is invalid. The specification
  makes conformance a server MUST and validation a client SHOULD, so the schema
  is the promise and it says which of the two arrived.
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
- A client validates `structuredContent` and cannot read `oneOf`. The two shapes
  are declared as alternatives, which is what keeps a hit's full promise in the
  document rather than only in a test here; a validator that ignores the keyword
  reads the relaxed outer required list instead and gets a weaker promise than
  the server keeps.

## Since then

A client met the shape on 2026-08-04, which is what `D-ANS-001` inherited this
entry and never had. `META-02` ran in the `E-NONE` this checkout now makes
itself — `bin/cli environment:create E-NONE` — as a session driven with the
case's prompt and nothing besides. `typo3_icon_lookup` and `typo3_label_lookup`
both answered `unsupported` with `cause: no-installation`, the reason, and the
seven directories discovery walked from that directory to the root.

Neither **Wrong if** about a client reading the answer happened. The session
told the user the installation could not be asked, named
`TYPO3_DEV_COMPANION_ROOT` and `TYPO3_DEV_COMPANION_CONSOLE` as the way out, and
wrote that an identifier recalled from memory is the answer it would not give —
it branched on the key rather than reading the fields under it, and it surfaced
a non-error answer rather than swallowing it. The third was not exercised: the
client read `structuredContent` and validated nothing, so a validator that
cannot read `oneOf` is still a client nobody here has met.

That leaves the `instructions` lever `D-ANS-001` named unspent, and there is
nothing left asking for it. The sentence it would have added — that a miss and
an unanswerable answer are two things — is what the client did without being
told, and `R-ANS-013` holds those instructions to a budget it would have had to
displace something to enter.

The other half of `META-02` never reached this shape at all. Its `E-STOPPED` run
asks the same prompt of a site project whose DDEV is stopped, and both lookups
it reaches read the package files where the console cannot (`R-ANS-008`), so the
session was answered rather than told nothing could be asked. What `todo/`
carries from that is where the case now stands, and one defect the run found on
the way.

That last sentence held for `.environments/e-site-main`, where no interpreter on
this machine satisfies what the installation pins. It does not hold for the
three released lines, and the run that settled it was made on 2026-08-04 in
`.environments/e-site-14.3`: `bin/typo3-dev-companion` over stdio, one process
throughout, the project stopped when the session opened, five calls, a real
`ddev start` — 18.2s, exit 0 — and then the same five.

**Four of the five answers are byte-identical across the start**,
`structuredContent` included. The whole of what moves is inside
`typo3_server_scope`, and inside that it is one object: `installation.console`
goes from `via: php` on PHP 8.3.23 with the caveat naming `ddev start` and the
root, to `via: ddev` on PHP 8.4 with `caveat: null`. The call itself falls from
0.951s to 0.054s. `typo3_project_describe` answers `packages` in both halves and
names neither the stopped project nor the command that would start it.

**Both of the prompt's own lookups answer `answeredBy: installation` in both
halves, and neither carries a caveat.** The label query returns the same four
labels from `EXT:core` and `EXT:frontend` before and after; the icon query for
`publish` returns nothing in both, because `publish` is not an icon name, and
the `workspace` query returns the same nine.

That closes what the 13.4 run could not decide. On that branch
`typo3/cms-lowlevel` ships no `TranslationDomainSearchCommand`, so the label
lookup falls back to the package files whatever DDEV is doing. 14.3 ships the
command, and the command turns out to need no database at all: run straight
against the stopped project on host PHP 8.3 — which carries `pdo_mysql` and no
`pdo_sqlite`, while every covered installation is configured for `pdo_sqlite` —
`language:domain:search` answers exit 0 with its payload.

So on every covered line a stopped project answers this prompt exactly as a
running one does, and what the declared runtime brings is not reached by the
prompt at all. `META-02`'s criteria are rewritten to that: the two states are
told apart by `typo3_server_scope` and by nothing the prompt asks. What would
still separate them is a lookup whose answer needs the database, because that is
the one service host PHP cannot reach here — the missing driver is what 13.4's
stopped half reported in its caveat. Naming one is what giving the case a second
prompt would take, and no tool has been measured for it.
