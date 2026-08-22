---
id: D-KNW-024
title: The Fluid namespace prefix is what a template question is written in
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::aQueryWrittenInFluidTagSyntaxReachesTheFluidHints
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# D-KNW-024 — The Fluid namespace prefix is what a template question is written in

**`f:` is a domain keyword: a query that names a Fluid tag reaches the Fluid
category, where it used to fall back to PHP and see none of it.**

A session reporting what a template did writes `f:if`, `f:else`,
`f:link.typolink`. It names no path, no file extension and never the word Fluid,
so `Domains::detect()` found no signal at all and returned the PHP fallback —
and `fluid.json` was filtered out before a single hint was scored.

## Evidence

- The query is the one a session actually arrived with, recorded in
  `feedback/archive/2026-08-01-003448-specific-fluid-f-if-f-then-f-else-failure-a.md`:
  *f:if with f:else but no explicit f:then swallows the inline then-branch /
  f:link.typolink output*. `bin/cli hints:probe` on it printed `Domains: php`
  and returned the 40 PHP and General hints as the index.
- Writing the statement it was missing did not make it reachable. The
  `fluid-templates` entry gained the branch rule and the same probe still
  matched nothing, because the gate is in front of the scoring rather than
  inside it.
- [`D-KNW-016`](knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-gap-this-server-owns.md)
  read this the other way — *routing is not what failed* — on the strength of
  `fluid template conditional link`, which carries the word and reaches the
  entry at `appliesTo(14)`. Both readings are of the same corpus and only the
  second used the feedback's own words.
- Nothing else moved. Over the 107 texts this repository has to hand — 41
  scenario prompts and 66 hint titles — the domains detected and the first three
  hints returned are identical before and after, for every one of them.
- The prefix cannot land inside a word. `Text::containsWord()` anchors at a word
  boundary, so `f:` is reached by `<f:if` and by `f:render` and not by `conf:`
  or `if:`.

## Decided

- The keyword is the prefix rather than a list of tags. Naming `f:if`, `f:then`
  and `f:else` would route this feedback and leave `f:for`, `f:render` and
  `f:format.*` where they were, which is the sixth phrasing
  [`D-KNW-009`](knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md) had to be
  written twice for.
- The hint vocabulary was widened with it, which is that entry's rule: `f:if`,
  `f:then` and `f:else` are `appliesTo` patterns on `fluid-templates`, so the
  category being a candidate and the right entry winning inside it are settled
  together.
- Only the `f:` namespace. `be:`, `core:` and an extension's own prefix are
  declared per template rather than global, and `be:` is two letters a caller
  writes for other reasons.

## Assumed

- That a caller writing `f:` is asking about a template rather than about a
  ViewHelper class. Both hints are in the same category, so the assumption only
  decides which of the two wins, and their own `appliesTo` is what does that.

## Wrong if

- A query that is not about Fluid carries `f:` and gets the category. A message
  id, a shell prompt or a drive letter would do it, and the symptom is a PHP or
  TypoScript question answered with template conventions.
- The Fluid category starts winning where the question is about the ViewHelper
  class rather than the template — `fluid-templates` now carries three patterns
  that a class-level question also spells out.
