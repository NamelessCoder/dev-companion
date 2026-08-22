---
id: D-KNW-056
date: 2026-08-04
status: open
---

# D-KNW-056 — A file skeleton is shipped as a version-bound document section

**A file a caller writes into its own repository is shipped as one document
section, fenced whole and bound there to the majors it holds for.**

What an extension needs to start testing is answered as prose today: which two
files to copy out of `typo3/testing-framework`, what to change in them, which
variables the run needs. The caller reconstructs the files from that description
every time, and for Playwright outside the core there is no description to
reconstruct from at all.

## Evidence

- The corpus already returns what a skeleton needs. `Documents` splits on `##`
  and returns a section with its original formatting, code fences included, and
  `typo3://core/{id}` serves the whole document uncut beside it.
- The scope is provided for rather than stretched. `Documents::isCoreOnly()`
  reads it off the `covers` entry naming the document, and those entries already
  carry `scope`, so a document answering for an extension is a topic declared
  with that scope. That all five documents today are the core's own is the
  subjects they were written about.
- One file fits a section and a file with its prose does not.
  `MAX_SECTION_LENGTH` is 2400 characters; `UnitTests.xml` is 1845 bytes,
  `FunctionalTests.xml` 1881, and the core's `playwright.config.ts` 1611. The
  cut is fence-aware, which prevents an unclosed code block and not half an XML
  file.
- The binding is not theoretical. Between `typo3/testing-framework` lines 8 and
  9 both XML files differ in two lines — the PHPUnit schema URL, `10.1` against
  `11.2`, and `beStrictAboutTestsThatDoNotTestAnything` — while line 9 and
  `main` are identical. A caller on the older line handed the newer file gets a
  schema URL for a PHPUnit it does not have.
- A document is the one part of the corpus with no binding at all. A hint
  carries `since` and `until`, a catalog entry carries them, `TestSuiteHints`
  filters by the target major, and `Documents` reads a title and its sections
  and nothing else.

## Decided

- The surface is the document corpus. A typo3_skeleton_lookup was drafted and
  dropped: it would have added a registry entry, an output schema, contract
  tests and a second place to state the scope, for an answer the corpus already
  returns in the right shape.
- One file per section, its explanation in the section beside it, because the
  budget fits one of the two. A file that outgrows the budget is one the corpus
  does not ship, rather than a reason to raise it.
- The binding is declared per section in the document itself and read by
  `Documents::sections()`, so one place carries it and a renamed heading cannot
  unbind a file without saying so. A sidecar mapping heading to range was
  rejected for that: it is the same statement in a place that can disagree with
  the first.
- One document per major was rejected. The divergence measured is two lines in
  forty-five, and a lookup would return both sections for one query and leave
  the caller to pick.
- The version stays out of the prose and out of the heading, which is what
  `HintsTest` already enforces for a hint.
- Shipping a copy is held by a check rather than by a promise: each derived file
  is compared against the release it came from below `.checkouts/`, over the
  pairing `Upkeep\TestingFramework` computes. Without it this is the file nobody
  updates, which is what `R-KNW-047` refused for the bootstrap.

## Assumed

- A caller handed a fenced file writes it out rather than paraphrasing it.
  Nothing here measures that.
- The skeletons worth shipping stay inside the section budget. The three
  measured do; a fixer configuration or a CI workflow may not, and the first one
  that does not is what tests the rule above.
- The Playwright skeleton can be verified by being run. It has no upstream to
  diff against — the core's own configuration points into the core tree — so it
  is authored here, and `skeleton:check` says nothing about it.

## Wrong if

- A skeleton reaches a caller cut by `MAX_SECTION_LENGTH`, as a file that is
  syntactically incomplete.
- A shipped file drifts from the release it was derived from and nothing fails.
- A second surface starts shipping files, so a caller has two places to ask and
  they can disagree.
- The declared binding line reaches a caller as part of the file it binds.
