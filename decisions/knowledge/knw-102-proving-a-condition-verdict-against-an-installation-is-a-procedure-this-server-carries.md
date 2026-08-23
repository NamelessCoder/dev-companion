---
id: D-KNW-102
title: 'Proving a condition verdict against an installation is a procedure this server carries'
date: 2026-08-18
status: open
---

# D-KNW-102 — Proving a condition verdict against an installation is a procedure this server carries

**How a condition verdict is made observable in a running frontend is a document
below `knowledge/documents/any/testing/`, beside the browser check.**

A session repairing an extension's own conditions on v14 was asked to prove the
breakage before fixing it. A verdict emits nothing and logs nothing, so the
proof is indirect, and the first marker the session grepped for was markup both
templates carry — a false positive it held for two round trips.

## Evidence

- The feedback's own query reaches nothing that answers it. Run in this checkout
  on 2026-08-18, `bin/cli hints:probe`
  `"prove that a TypoScript condition matches in the frontend"` reaches
  `typoscript-conditions` and `typoscript-condition-providers`, and
  `"how do I check that a TypoScript condition matched on a rendered page"` adds
  `site-set-migration`. All three are about what a condition is handed or how
  one is registered. `"verify a template swap in the rendered frontend output"`
  classifies as `php` and reaches `breaking-without-a-moved-member`.
- The two near documents have another boundary and each says so in its own
  `whenToUse`. `core/testing/proving-a-rendering` is a throwaway functional test
  below `typo3/sysext/frontend/Tests/Functional/Rendering/` run with
  `Build/Scripts/runTests.sh`, for what a snippet renders;
  `any/testing/browser-check` is how a browser in a container reaches a DDEV
  site, for a defect that has to be seen. Neither establishes that a branch was
  taken.
- The trap that cost the round trips is absent from the corpus in every wording.
  `discriminator` occurs once below `knowledge/`, about the exclamation mark in
  a 404 message; nothing says that two Fluid templates a condition switches
  between usually share their wrapper markup, and nothing anywhere names a
  negative control.
- The feedback's cache claim has the fact right and the conclusion backwards.
  `createPageCacheIdentifier()` puts `constantConditionList` and
  `setupConditionList` into the identifier on `.checkouts/13.4:255`, `14.3:337`
  and `main:338`, and `12.4` hashes the same two into `createHashBase()` at
  `TypoScriptFrontendController.php:1389`. Those lists are
  `IncludeTreeConditionMatcherVisitor::getConditionListWithVerdicts()` — the
  expression mapped to its verdict — so a page whose verdict flips lands on
  another identifier and the old entry is not served. What still needs a flush
  is the TypoScript itself, which the `caching` hint already states: an
  `@import` target or an `include_static_file` set is keyed on the file name
  alone, so an edited `.typoscript` file keeps its parsed include tree.
- `D-KNW-101`, judged in this directory on the same day, records that it carries
  only what a condition can reach at evaluation time and names this question as
  another card's.
- The cost is counted. The accounting feedback from the same session puts this
  cluster at 7 of roughly 30 round trips, about 2 of them lost to the marker
  that did not discriminate, which is what `D-FBK-027` measures.

## Decided

- Step 1a of the ladder, taken on as a document rather than as hints. What is
  missing is a procedure carried out in order — derive a marker, control it
  negatively, decide what to flush — and a procedure written as statements is a
  set of sentences nobody can follow (`D-FBK-043`).
- Its boundary is the running installation, and
  `core/testing/proving-a-rendering` is not rescoped to reach it. Every step of
  that page is a file below `typo3/sysext/`, a fixture and a suite invocation,
  none of which a site developer can run, so widening its scope would hand them
  a procedure that does not apply. What generalises is the question, not that
  page.
- Scope `any`. The session was an extension author working inside a site
  installation, which is the case that belongs to neither `extension/` nor
  `project/` alone, and nothing in the procedure turns on which of the two the
  caller is.
- Three things it has to carry: the marker derived by diffing what the
  conditional branch renders against what it replaces; the trap that shared
  wrapper markup is what the obvious grep finds; and the negative control — a
  page the condition must not match — which is what turns one green result into
  evidence.
- What has to be flushed between runs is the reading's first question rather
  than the feedback's answer, because the identifier already carries the
  verdict. The document says which change needs which flush; it does not copy
  the report's reason.
- Routed from `typoscript-conditions`, where a caller asking about a condition
  already lands. A document nobody is routed to is the same gap one step further
  in, which is `D-KNW-071`'s own finding.
- `normal`, not the `low` the card arrived at. What the gap produces is a wrong
  verdict believed rather than a slow answer, and the session reports it nearly
  shipped one. Not `high`: one session in one directory reported it.
- No card is taken over. `D-KNW-101` answers a different question about the same
  subject, and the accounting feedback beside it is cost data for the whole
  session.

## Assumed

- That the procedure is version-neutral. Nothing here started a frontend; what a
  verdict changes about rendered output is a property of the TypoScript rather
  than of the major, which was reasoned rather than measured.
- That one page covers both shapes — the condition that swaps a Fluid template
  and the condition that changes a value in place.

## Wrong if

- A flipped verdict turns out to serve the old page on some covered line. Then
  the flush is the first step rather than a caveat, and the identifier reading
  above is wrong about what it implies.
- A session with the document installed still greps for the first thing that
  looks related. Then what was missing was the vocabulary rather than the
  procedure, and it belongs on `typoscript-conditions` as a hint.
- The two shapes turn out to need different steps far enough that the page reads
  as two procedures under one heading, which would say the boundary was drawn
  around the wrong thing.
