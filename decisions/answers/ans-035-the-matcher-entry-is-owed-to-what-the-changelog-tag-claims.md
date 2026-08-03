---
id: D-ANS-035
date: 2026-08-03
status: open
---

# D-ANS-035 — The matcher entry is owed to what the changelog tag claims

**A removal owes an extension scanner matcher entry for everything its changelog
entry says it removed, and the entry's `NotScanned`, `PartiallyScanned` or
`FullyScanned` tag is what that is measured against.**

`D-ANS-029` left what the rule says to the reading. Read in `.checkouts/main`,
the mandatory part is the tag rather than the matcher, and the tag is a claim
about the matchers.

## Evidence

- `typo3/sysext/core/Documentation/Changelog/Howto.rst:268` requires exactly one
  of `NotScanned`, `PartiallyScanned` and `FullyScanned` on every Breaking and
  Deprecation entry, and defines `FullyScanned` as every item the entry is about
  being findable by the extension scanner.
- The tag is the only part CI reads.
  `Build/Scripts/runTests.sh -s checkExtensionScannerRst` tests that the `.rst`
  files the matchers reference exist — the dangling-reference direction alone,
  stated at `runTests.sh:613`. Nothing checks that a removal has an entry.
- `Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst` is
  `FullyScanned` and its matchers back it item for item: 28 instance methods in
  `MethodCallMatcher.php`, the one static `readPngGif` in
  `MethodCallStaticMatcher.php`, 10 removed public properties in
  `PropertyPublicMatcher.php`, and the 7 that became protected in
  `PropertyProtectedMatcher.php`. The call site decides the file, not the kind
  of change.
- `restFiles` is on every entry shape; `numberOfMandatoryArguments` and
  `maximumNumberOfArguments` are on the method matchers only.
- Nothing about a removed public method makes it unfindable. `MethodCallMatcher`
  documents itself as a weak match on the method name without resolving the
  class, so the argument that only some removals are statically findable does
  not reach methods. `Breaking-101948-FileBasedAbstractRepositoryClassRemoved`
  is what `PartiallyScanned` is actually for: the removed class and the removed
  method are entered, and the inherited surface that went with the parent class
  cannot be enumerated.
- A missing entry is a defect the core goes back and fixes.
  `ac4ce377e2fc493510b35bbe31d23815ded73fd8` audits the matcher files, finds
  entries for functionality earlier patches removed without referencing the
  breaking changelog file, and adds them.
- The section's wording is measured rather than written freely. A first draft
  said "what the call site looks like", and the word "site" occurred nowhere
  else in the prose corpus: `TermSearch` weighs a term nothing carries as
  `log(25)/2` and one exactly one section carries as `log(25)`, so introducing
  it raised its own weight from 1.61 to 3.22, and
  `## Review Readiness` — which carries none of it — fell from 0.51 coverage to
  0.41 and out of the answer to "review readiness for my site package", which
  `ScopeTest::whatARuleAnswerWithheldIsNamedRatherThanMissing` asks.

## Decided

- `## Breaking Changes` of `knowledge/documents/typo3-commit-messages.md` states
  the matcher: which file takes which kind of removal, what an entry carries,
  what the tag claims, and that `checkExtensionScannerRst` checks one direction
  only. The feedback's failing query
  "removing public method extension scanner matcher breaking changelog" reached
  nothing and now returns that section at 209.
- The feedback's other query stays a miss and is left one.
  "breaking change internal method removal changelog" is six terms no section
  covers half the weight of, and it is answered with the list of covered
  sections, `## Breaking Changes` among them. Widening the floor to catch it is
  the retrieval change `D-ANS-029` declined on the measurement.
- The `breaking` intent stops recommending it. "Consider an extension scanner
  matcher" becomes the entry and the tag, as two checklist items, because the
  reading found an obligation rather than an option.
- `feedback/2026-08-01-115525` is answered against its suggestion. It asked that
  a removed public method of a non-`@internal` class owe the matcher and the
  Breaking entry even where the member is `@internal`, with only `[!!!]` waived.
  The core did the opposite with the patch that review was about:
  `b08282345cd6175b02d69b710f19cd9cd40a04f8` removes
  `GifBuilder::getTemporaryImageWithText()`, annotated
  `@internal will soon be renamed`, as a plain `[TASK]` with no marker, no
  changelog entry and no matcher, reviewed by three core members. The sibling
  `GraphicalFunctions::getTemporaryImageWithText()` carried no `@internal` in
  `.checkouts/12.4` and was removed as `Breaking-101955` with an entry, which is
  why the precedent reads the way it does.
- So the annotation on the member decides over the class around it, and it
  waives the whole entry rather than the marker alone. `deprecated-apis` gains
  that third case beside the two `D-FBK-038` recorded; the statement that the
  call sites settle it is unchanged.
- No lookup over the matcher files, unchanged from `D-ANS-029`.

## Assumed

- That `Breaking-101955`, `Breaking-101948` and the audit patch are the practice
  rather than three picks. No sweep of every Breaking entry's tag against its
  matchers was run, and the tag is what such a sweep would have to measure.
- That a caller reaching `typo3_task_guide` phrases the removal so the
  `breaking` intent fires. "Remove public method X" matches and "Remove the
  public method X" matches nothing, because `match` holds phrases and an article
  breaks one.

## Wrong if

- A Breaking entry is written `FullyScanned` with no matcher entries and merges.
  The tag is then a habit rather than a claim, and what a removal owes has to be
  stated on the matcher rather than on the tag.
- A session is told the matcher is owed and files an `@internal` removal as
  Breaking that the core would have taken as a `[TASK]`. The three cases in
  `deprecated-apis` are then read as a rule that the last one wins, and the
  statement needs the call-site test in front of them.
- A feedback reports a removal reviewed without a matcher after this. Delivery
  was then not the gap and the wording is what to look at — step 4 of the
  `D-ANS-029` ladder.

## Covered by

- `KnowledgeTest::theBreakingRouteStatesWhatTheScannerMatcherRequires`
- `HintsTest::aRemovalIsToldWhatTheScannerMatcherRequires`
