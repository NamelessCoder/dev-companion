---
id: D-KNW-051
title: The public-asset answer names the internal static beside the supported route
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::thePublicAssetAnswerSeparatesTheSupportedRouteFromTheInternalStaticBesideIt
---

# D-KNW-051 — The public-asset answer names the internal static beside the supported route

**Where a class holds a supported entry point beside a deprecated and an
`@internal` one, the corpus names all three and says which is which.**

`public-assets` named the supported route and the deprecated predecessor, and
left `PathUtility::getSystemResourceUri()` to whoever opened the class. The
reporting session opened it, read the signature, and cleared the call as current
API. One line above the signature the docblock reads
`@internal Will be removed (or made private) before v14 LTS release`.

## Evidence

- The query re-run against the server as it is now, `typo3_hint_lookup` with the
  feedback's own arguments at `targetVersion: 14.3`. `public-assets` returns the
  factory-and-publisher route `[TYPO3 v14 and newer]`, and directly under it
  "PathUtility::getPublicResourceWebPath() is what computes such a URL here.
  `[up to TYPO3 v14]`" — a method deprecated on that very version, presented as
  what computes the URL, with no word of the deprecation. `getSystemResourceUri`
  occurred nowhere in the answer.
- The TYPO3 claim, read in `.checkouts/14.3` and `.checkouts/main`.
  `PathUtility::getSystemResourceUri()` is at `Classes/Utility/PathUtility.php`
  line 135 on 14.3 and line 93 on `main`, carrying the same docblock on both:
  `@internal Will be removed (or made private) before v14 LTS release`. It is
  four lines of body that call the factory and the publisher.
- The feedback's premise fails on the half that decides it. It reports the
  method as "current in the installed core" and cites the signature; the
  docblock one line above says the opposite. So the hint listing one route is
  correct, and the finding the session feared as a false positive is a true one:
  extension code calling a static the core has reserved for its own migration.
- Its suggestion would have written that method into `knowledge/` as a supported
  shorthand — the failure `judging.md` names, a feedback's own guess about TYPO3
  copied into the corpus.
- `knowledge/versions.json` has 14 at branch `14.3`, status `stable`. The
  removal the docblock announces is ahead rather than behind, which is what
  makes the finding current rather than historical.
- The deprecated sibling is deprecated in the version the hint was answering
  for. `getPublicResourceWebPath()` at line 112 opens on
  `trigger_error(..., E_USER_DEPRECATED)`,
  `Deprecation-107537-getPublicResourcesWebPath.rst` in the 14.0 changelog says
  it is "deprecated first, before being removed with TYPO3 v15.0", and it is
  absent from `main`. The statement's `until: 14` band was therefore rendering
  it as current advice on the one version where it warns.
- The same changelog is evidence for the reading rather than against it: it says
  `getPublicResourceWebPath` "was marked internal since its introduction" and
  was deprecated anyway because "there were no good alternatives" — the core's
  own account of an internal-marked static on this class is that it was never
  the answer.
- That changelog's migration example imports `SystemResourceFactory`,
  `SystemResourcePublisherInterface` and `UriGenerationOptions` from
  `TYPO3\CMS\Core\Resource`. None of the three is there:
  `Classes/Resource/SystemResourceFactory.php` does not exist, and the classes
  are under `Classes/SystemResource/` and `Classes/SystemResource/Publishing/`.
  A session following the only published migration gets three unresolvable
  imports.
- Step 4, and not 1, 2 or 3. `bin/cli hints:probe` on the feedback's query
  reaches `public-assets` as the PHP hit, `appliesTo(17) + text(41)`; it was
  delivered, and the feedback quotes it back. What it says is what did not take.
- The cost is counted twice in the same audit. The sibling
  `feedback/2026-08-03-164805` lists this method among the identifiers it had to
  settle by grepping installed source, and repeats the same "current" reading
  from the same signature. Both sessions stopped one line short.

## Decided

- `public-assets` gains two `since: 14` statements: that
  `getPublicResourceWebPath()` still resolves the path and is the way out rather
  than the way in, raising `E_USER_DEPRECATED` per call and gone in the next
  major; and that `getSystemResourceUri()` is a third thing beside the two —
  `@internal`, reserved for the core's own call sites, a finding rather than a
  shorthand.
- Both are written without a version number, a changelog id or a count, which is
  what `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` holds a hint
  to: the band carries the branch and the sentence has to read the same on all
  of them. "The next major has removed it" and "before this major reaches LTS"
  are what that leaves, and they are the reason the deprecation could not simply
  be written into the sentence that was already there.
- The `getPublicResourceWebPath()` statement is rebanded from `until: 14` to
  `until: 13`. On 13 it is what computes such a URL; on 14 the sentence above
  replaces it. A hint that names a deprecated method as current on the version
  it warns on is the defect, not the wording around it.
- The internal method is named rather than merely left out —
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md).
  A session arrives at this hint having already found the method in the class,
  and a hint that is silent about it leaves what the class suggested standing.
  That is the shape of
  [`D-KNW-043`](knw-043-a-rule-carries-the-strength-of-its-claim-and-its-source.md)
  read from the other side: there the corpus flattened two neighbouring APIs
  into one strength, here it stated one and said nothing about the third.
- The general rule the feedback proposed is inverted rather than adopted. It
  asked that a hint state every supported alternative; what this case shows is
  that the reader's ambiguity is not *which of two supported routes* but *this
  one compiles and works, am I allowed to call it* — so what a hint owes is the
  entry points that are **not** for a caller, with the marking they carry.
- The three classes the route is injected from are named with their namespace,
  against the migration example that imports them from one they are not in.
- `appliesTo` gains `PathUtility`, `getSystemResourceUri` and
  `getPublicResourceWebPath` — `R-KNW-002`, the subject asked about in the words
  of the API. The second of those reached this hint from no phrasing before.
- The feedback is archived by this commit and its card deleted. Nothing here
  needed a lookup beyond the checkouts the ladder already owes a feedback making
  a claim about TYPO3, and no schema, tool or skill contract is touched.
- Not decided here: whether calling `@internal` core API should be a named
  finding class in `typo3-extension-conformance`. `@internal` occurs in no skill
  under `skills/`, so the reviewer currently has the marking explained nowhere;
  but that orders a task rather than stating a fact about TYPO3, and no feedback
  asks for it. It is left for the card that reaches the skill.
- Not folded in either: the identifier lookup `feedback/2026-08-03-164805`
  proposes. It would have surfaced this docblock in one call, and it is a
  capability rather than a wording fix; its own card carries it.

## Assumed

- That `@internal` on a public static is what settles whether extension code may
  call it. The docblock and the 107537 changelog are what this rests on, not a
  policy statement of the core's read for this judgement.
- That a session which found the method in the class reads the corpus again
  before deciding. Nothing measured that; what is known is that this session
  went to the class *because* the hint accounted for neither of the two statics
  it found there.
- That the reporting session's audit target really calls the static as quoted.
  The extension is in another checkout and was not read from here. The lever is
  the same either way.

## Wrong if

- v14 LTS ships with `getSystemResourceUri()` public and the `@internal` gone.
  The statement then describes a boundary that closed, an extension calling it
  was never a finding, and what is owed is a `since`/`until` rather than a
  correction — the docblock's own wording makes this the likeliest failure.
- A session reads the new statements and still reports the call as conformant,
  or still goes to `PathUtility` to decide. The wording was then not the missing
  part, and what is left to suspect is step 2 — that an audit's question does
  not pass through this hint at all.
- Another hint is found naming one entry point of a class that also holds an
  internal or deprecated lookalike. One correction is then a fix rather than a
  rule, and what is needed is a sweep of the corpus instead of a sentence here.
- The 107537 namespaces turn out to be right at some patch level, or the classes
  move. The clause about the migration example then describes a docs bug that
  was fixed, and it goes.
