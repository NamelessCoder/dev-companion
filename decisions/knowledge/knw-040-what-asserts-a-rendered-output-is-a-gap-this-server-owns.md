---
id: D-KNW-040
title: What asserts a rendered output is a gap this server owns
date: 2026-08-03
status: revoked
revokedBy: D-KNW-044
---

# D-KNW-040 — What asserts a rendered output is a gap this server owns

**Finding the expectations that assert a rendered output, before a change alters
it, is inside this server's boundary and missing from it, so the feedback is
queued rather than closed.**

A change to one URI shape moved about 141 expectations across 23 files. The
session found them one failing suite at a time, over roughly fifteen full
functional runs, and the one statement this server has near the question tells a
caller to iterate narrowly.

## Evidence

- The tool the feedback names still answers the other question. Called on this
  branch on 2026-08-03 with `paths` set to
  `typo3/sysext/core/Classes/SystemResource/Http/CacheBustingUri.php` and a
  query naming rendered output, `typo3_test_run_guide` returns the `e2e`,
  `functional` and `unit` suites with their targeted invocations. Which suite
  can fail is what it says; which expectations exist is not in the answer.
- The feedback's own query reaches nothing. `bin/cli hints:probe` with the query
  verbatim classifies it `php` and returns `system-extension-boundaries`,
  `project-build-and-scripts`, `routing-request-handling` and `caching`. None of
  the four is about a test expectation.
- The nearest statement in the corpus points the caller into the expensive loop.
  `core-tests` in `knowledge/hints/testing.json` says to run the single test
  file or method while iterating, because a full functional run costs minutes
  per round. `TestSuiteHints::invocation()` emits the same sentence with every
  answer. Both are right for the ordinary change and wrong for this one.
- No skill says it either. `typo3-core-patch-development` routes the blast
  radius question to `typo3_test_run_guide` twice — once for whether the
  reproduction can be a test, once for verification — and neither is about what
  already asserts the output.
- The shapes the feedback names are in the checkout. Read on `.checkouts/main`
  at `c71b2bdb2f`: `contentMatchRegExp` keys in `ImageConvertIMViewHelperTest`
  and `ImageConvertGMViewHelperTest`; a PCRE at `ImageViewHelperTest:159` whose
  capture group is used as a file path; three `{$...}` placeholder fixtures
  under `backend/Tests/Functional/Template/Fixtures/`; `FluidEmailTest` in
  `core/Tests/Functional/Mail/`. `CacheBustingUri.php` is still at the path the
  feedback names.
- The same session reported the cost a second time from the other end.
  `feedback/2026-08-02-145128` names it step 9 of an assessment procedure: the
  blast radius belongs to the assessment, and it was found incrementally long
  after the change had been characterised.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about the core's test corpus, and this run has read this
  repository and one checkout.
- No tool is built for it. This server does not read the caller's repository, so
  it cannot enumerate what asserts anything. What it can carry is where those
  expectations hide and in which order to look, which is a statement.
- Not step 4, so the iterate-narrowly sentence is not rewritten. It is correct
  for the ordinary change, and what is missing is the exception for a change
  whose rendered output is asserted elsewhere.
- The category is not the answer. It arrived as `tool-gap` and needs none.
- The feedback's grep recipe is not copied down. Its author counted eight shapes
  in one change, and which shapes the corpus actually uses is what the reading
  settles.
- `normal` rather than `low`, because the cost is counted rather than asserted:
  fifteen functional runs of several minutes, 23 files, about 141 expectations.
  Not `high`, because one session reported it, twice.

## Assumed

- That the shapes generalise past one change. They were counted while moving a
  cache-busting URI, and another rendered value may hide in fewer of them or in
  others.
- That a recipe naming shapes outlives one naming files. Test files arrive every
  week, and the reading has to write the durable half.

## Wrong if

- The reading finds one search that reaches every shape. The gap is then a
  sentence on `core-tests` rather than a recipe, and this entry overstates it.
- A filtered full functional run turns out cheaper than any search. Then what
  the corpus owes is the ordering alone, and naming the shapes is noise a caller
  pays for on every unrelated call.
- The shapes turn out to belong to the image and asset area rather than to the
  corpus. A session changing some other rendered value would then reach a
  statement written for somebody else's paths.

## Revoked on 2026-08-03

The first **Wrong if** is what happened. One search does reach them — over the
whole `Tests/` tree rather than the files named `*Test.php`, and for the text
around the changed value rather than the value — and it reaches 24 of the 26
files the reporting session had to touch, while the two it misses hold no
expectation. The shapes are eight ways of encoding one value, not eight places
to look, so this entry's "which shapes the corpus actually uses is what the
reading settles" was the wrong question to have queued: what the reading settled
was where the files are.

The statement stopped describing this server with the same commit. `core-tests`
now says where the expectations hide and the invocation notes carry the
exception to iterating narrowly, so "missing from it" is no longer the case, and
`normal` rather than `low` was right for a reason this entry did not have — the
cost was one search away, not a corpus survey away.

[`D-KNW-044`](knw-044-one-search-over-the-whole-tests-tree-finds-what-asserts-a-rendered-output.md)
carries what holds instead, with the counts, the two checkouts that show the
shapes take no version range, and a **Wrong if** about the aim rather than about
the shapes.
