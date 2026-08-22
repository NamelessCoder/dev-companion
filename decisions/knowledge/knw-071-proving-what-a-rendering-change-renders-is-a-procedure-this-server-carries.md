---
id: D-KNW-071
title: Proving what a rendering change renders is a procedure this server carries
date: 2026-08-14
status: open
---

# D-KNW-071 — Proving what a rendering change renders is a procedure this server carries

**How a throwaway functional test renders frontend output in a core checkout,
and how what it rendered is read at all, is a document below
`knowledge/documents/core/testing/`.**

Two skills grant the probe and neither says what one consists of. A session
reviewing a three-line TypoScript change had no other evidence available — no
core test covered the constellation and the diff says nothing about what it
renders — and spent five of six container rounds on its own harness.

## Evidence

- Re-run on 2026-08-14 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own question — "how do I render an RTE bodytext snippet
  through lib.parseFunc_RTE in a functional test and read the resulting HTML" —
  reaches `core-tests` and `project-extension-tests` on text alone. Rephrased as
  "read the rendered HTML a functional test produces" it adds `security-sinks`
  and `browser-tests-outside-core`. None of the five is about producing output
  to look at.
- The vocabulary is absent. `parseFunc` occurs nowhere below `knowledge/` or
  `skills/`.
- The nearest hint answers the other question. `extension-test-frontend-request`
  is `executeFrontendSubRequest` and the cache-hash conditions: how to assert a
  response whose expected value is already known. What was needed was the
  opposite — the value was the unknown, and a functional test that passes prints
  nothing.
- The permission is written down twice and the recipe neither time.
  `typo3-core-patch-review` grants the scratch probe as "add a temporary fixture
  column, a model property or a test of your own, run a targeted suite against
  it"; `typo3-core-issue-triage` carries the three rules a throwaway
  reproduction owes — where it goes, seen failing before it is believed, taken
  out again. Both assume the test is already written.
- `D-SKL-032` left the question open for "the run that has both", and this is
  it: a session that could run a suite, that got every substantive finding out
  of running it, and that still paid for the harness. It counted the cost — six
  rounds at roughly two minutes, three of them producing nothing but its own
  mistakes — which is what `D-FBK-027` measures.
- One of the four things the feedback names is already answered.
  `knowledge/documents/core/testing/scripts.md` states that sqlite is the
  default database for `-s functional` and the fastest, so the document belongs
  beside it rather than repeating it.
- The feedback's account of the second trap is not confirmed. It reports that a
  leading `<` in a TypoScript value is read as a reference; the tokenizer's
  operator table
  (`.checkouts/main/typo3/sysext/core/Classes/TypoScript/Tokenizer/LosslessTokenizer.php:432-451`)
  makes `=<` the reference and a bare `<` after the identifier the copy, with
  whitespace stripped before either is read. What actually bit that session is
  what the reading has to establish.

## Decided

- Built, as a document rather than as hints. What was missing is a procedure —
  which cObj, which operator form, how output is made visible, which suite
  invocation — and a procedure written as statements is a set of sentences
  nobody can carry out in order (`D-FBK-043`).
- Its boundary is proving, not rendering. It says how a throwaway test is built
  so that a rendering becomes readable, and how it is read: a functional test
  that passes prints nothing, so the output has to be forced out of it. What
  `lib.parseFunc_RTE` does belongs to the frontend-rendering hints, and the
  document names no patch.
- It is reached from where the probe is already granted. The review skill's
  scratch-probe paragraph and the triage skill's throwaway-test rules point at
  it, which is the half that decides whether the next session finds it at all —
  a document nobody is routed to is the same gap one step further in.
- The feedback's own recipe is not copied down. Its author was guessing about
  TYPO3 exactly as much as this judgement would be, and one of its four claims
  is already contradicted by the tokenizer. Every step is established against
  `.checkouts/` and bound where it does not hold on all covered lines, which is
  the todo's first step.
- The priority is `normal`. One session reported it, and the cost it counted is
  a class of patch — TypoScript defaults, `ext_localconf.php` TypoScript,
  anything below `lib.parseFunc` — where reading the diff is not evidence at
  all.

## Assumed

- That the shape generalises past this one patch. The session named a class of
  change rather than its own; whether one page covers a TEXT probe, a
  FLUIDTEMPLATE probe and a page-request probe is what the reading decides.
- That the sentinel-assertion trick is the way to read rendered output and not
  the session's own invention. It is stated as the only way and nothing here
  checked it against what the framework offers.

## Wrong if

- The reading finds a core helper that already prints rendered output from a
  functional test, which would make the document one sentence naming it.
- A session with the document installed still builds its probe by hand, which
  would say the gap was the route rather than the procedure.
- The steps turn out to differ per covered line far enough that the page is a
  table of versions rather than a procedure — then it is hints with `since` and
  `until`, and this entry is what was wrong about the shape.

## Since then

Built as `core/testing/proving-a-rendering`, and routed to from the
scratch-probe paragraph of `typo3-core-patch-review` and the throwaway-test
rules of `typo3-core-issue-triage`. The probe was run on all four covered lines
with `-s functional -d sqlite`, and both **Assumed** were settled by running it.

The feedback's second trap does not exist as reported, and the reading replaced
it with the one that does. `value = <figure …>` assigns the markup as text on
every covered line. `value =<figure …>` is the reference operator, whose
identifier stops at the first space, so the line becomes a reference to `figure`
and the page renders `< figure` where the markup should be — a failure with no
error in it, which is what a session sees as "unusable". What the multi-line
`value ( … )` form is needed for is markup that spans lines, because a
single-line assignment ends at the newline.

The sentinel assertion is not the only way to read rendered output and is the
worse one. `echo` from the test body prints whether the probe passes or fails,
because the core's functional PHPUnit configuration sets no
`beStrictAboutOutputDuringTests` on any covered line. No core helper prints
rendered output, so the first **Wrong if** did not hold.

The third did not hold either, and one section pair carries what differs: until
12 `lib.parseFunc_RTE` is `fluid_styled_content`'s and needs both
`$coreExtensionsToLoad` and the static include, either alone leaving
`LogicException` 1641989097; since 13 the frontend registers it and the probe
needs neither. The rest of the procedure holds on all four.
