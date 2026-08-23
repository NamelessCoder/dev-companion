---
id: D-ANS-035
title: The matcher entry is owed to what the changelog tag claims
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::aRemovalIsToldWhatTheScannerMatcherRequires
  - KnowledgeTest::theBreakingRouteStatesWhatTheScannerMatcherRequires
  - KnowledgeTest::theMatcherListSaysWhatItsMissingRowsDoNotMean
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
  it raised its own weight from 1.61 to 3.22, and `## Review Readiness` — which
  carries none of it — fell from 0.51 coverage to 0.41 and out of the answer to
  "review readiness for my site package", which
  `ScopeTest::whatARuleAnswerWithheldIsNamed` asks.

## Decided

- `## Breaking Changes` of `knowledge/documents/typo3-commit-messages.md` states
  the matcher: which file takes which kind of removal, what an entry carries,
  what the tag claims, and that `checkExtensionScannerRst` checks one direction
  only. The feedback's failing query "removing public method extension scanner
  matcher breaking changelog" reached nothing and now returns that section at
  209.
- The feedback's other query stays a miss and is left one. "breaking change
  internal method removal changelog" is six terms no section covers half the
  weight of, and it is answered with the list of covered sections,
  `## Breaking Changes` among them. Widening the floor to catch it is the
  retrieval change `D-ANS-029` declined on the measurement.
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

## Confirmed on 2026-08-03

The rule reached a reviewer four hours and forty-seven minutes after it was
written, and the five rows were read as a closed list. `62ccdba` wrote them at
09:56; `feedback/2026-08-03-144316` quotes them back at 14:43, from a review of
`9f6c6eb9093` (#110359) in `/home/benji/projects/typo3-cms`. That patch removes
a protected method, `ImageService::getImageFromSourceString()`, from a class
that is neither final nor `@internal`. The session was about to demand a matcher
entry. It read the list, found no row for a protected method, and reported
instead that the matcher **cannot** exist for one, so the entry would be
`NotScanned` or `PartiallyScanned`. It filed that as the single call that
corrected it before it acted, and asked that the enumeration be kept verbatim
because "its value was in what it does NOT list".

The conclusion is false, and the rule's own routing sentence already said so.

- `MethodCallMatcher` documents itself as "a 'weak' match since we're just
  testing for method name but not connected class". Its `enterNode()` matches
  any `MethodCall` node whose `name` is an `Identifier` in the flattened
  definitions, and compares the argument count. It reads no declaration, so no
  visibility is available to it at all.
- The precedent is one minor version away from the patch under review.
  `Breaking-110277-FileRendererRegistrationAndInterfaceChanged` turns
  `RendererRegistry::getRendererInstances()` from public to protected, and
  `MethodCallMatcher.php:6998` carries
  `TYPO3\CMS\Core\Resource\Rendering\RendererRegistry->getRendererInstances`
  with that file as its `restFiles` entry. A method the core made protected is
  entered where a public one is.
- The list omits a row because none is needed, not because a case is excluded.
  Core ships 23 matcher files and the rule names five; read as closed over
  visibilities it also has no row for a private property or a trait, and nobody
  has drawn that conclusion because nothing invited it.
- What invited this one is the shape of the two property rows. Three of the five
  are routed by how the member is written — instance, static, class — and two
  name a visibility. So the list teaches that visibility is a discriminator, and
  then has no row for the method case it just taught the reader to look for.
- The session held both readings inside one debrief.
  `feedback/2026-08-03-144432`, seventy-six seconds later, says a removed public
  **or protected** member "makes it breaking, and then the subject needs
  `[!!!]`, a Breaking changelog entry and an extension scanner matcher".

Step 4, wording. Not 1a, because the fact was in the section already — "how the
removed member is written where it is used decides the file" is the whole
answer, one line above the list that contradicted it. Not step 2, because it was
delivered: the query was `typo3_rule_lookup "breaking change"`, and re-run on
2026-08-03 it returns `## Breaking Changes` at 100% with the five rows verbatim.

The section now says that visibility routes a property and never a method, why
the method matchers cannot see one, the `getRendererInstances` precedent, and
that the missing row says nothing about whether an entry is owed. The last
clause is what the keep-request asked for the opposite of, so it is written
rather than left to the reader.

One draft was thrown away for the reason this entry already records. It said
"the name at the call site", and
`aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt` failed at once: "how do I
push a patch for review from my site package" matched the new prose on `site`.
The word is still carried by nothing else in the corpus, so the evidence bullet
above holds a second time, and the sentence uses "where it is used" — the
vocabulary the bullet above it already spends.

The first **Wrong if** is untouched. The third is what this bears on, from an
angle it did not name: the removal was reviewed *with* the matcher rule
delivered. The wording is what to look at, which is the step it says to take.
