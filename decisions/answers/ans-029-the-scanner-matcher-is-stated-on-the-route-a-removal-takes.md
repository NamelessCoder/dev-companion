---
id: D-ANS-029
date: 2026-08-02
status: revoked
revokedBy: D-ANS-035
---

# D-ANS-029 — The scanner matcher is stated on the route a removal takes

**Every route from a breaking change to the extension scanner matcher passes a
section that states the `[!!!]` marker and the changelog file and nothing
else.**

That is step 2 of the ladder, and it is queued as
[`R-ANS-017`](../../requirements/answers/ans-017-a-removal-is-told-what-the-scanner-matcher-requires.md).

`feedback/2026-08-01-115109` asked `typo3_rule_lookup` for the convention on
removing a public method and was given the marker and the RST requirement
without the matcher. The corpus does state the matcher. It states it under
`## Deprecations`.

## Evidence

- Both of the feedback's own queries still miss.
  `bin/cli hints:probe "removing public method extension scanner matcher breaking changelog"`
  returns `deprecated-apis` and `installation-upgrade` as candidates only, and
  `bin/cli hints:probe "breaking change internal method removal changelog"`
  reaches nothing at all. `RuleLookup::answer()` re-run on 2026-08-02 answers
  the first with "no section that holds outside the core matched" and the second
  with the list of document topics.
- The short query the feedback asks for works already, and lands in the wrong
  place. `typo3_rule_lookup "extension scanner"` returns `## Deprecations` of
  `knowledge/documents/typo3-commit-messages.md` at 100% of the query terms —
  the section that names `Configuration/ExtensionScanner/Php/`. Reachability was
  never the failure.
- The section a breaking change reaches names no matcher. The `breaking` intent
  of `knowledge/task-intents.json` carries
  `rulesQuery: "breaking change changelog"`, which `TaskIntents::sections()`
  puts through `Documents::search` at `src/Knowledge/TaskIntents.php:168`. Run
  today that query returns `## Breaking Changes`, `## Changelog Files`,
  `## Review Readiness` and `## Summary Line`, and none of the four mentions the
  extension scanner.
- The rule exists on one route and is hedged there. The same intent matches on
  `remove public` and `removed public`, its checklist says "consider an
  extension scanner matcher", and its checks carry `checkExtensionScannerRst`. A
  caller reaching `typo3_task_guide` with a task text that names a removal gets
  that much; a caller asking `typo3_rule_lookup` gets none of it.
- The feedback's claim about TYPO3 holds, read in `.checkouts/main`.
  `typo3/sysext/core/Documentation/Changelog/13.0/Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst`
  is there, and
  `typo3/sysext/install/Configuration/ExtensionScanner/Php/MethodCallMatcher.php:5969`
  carries `TYPO3\CMS\Core\Imaging\GraphicalFunctions->getTemporaryImageWithText`
  with that file as its only `restFiles` entry.
- The lookup the feedback asks for answers already. `typo3_changelog_lookup`
  with `type: breaking` and the query "removed public methods image generation",
  run against `.checkouts/main` on 2026-08-02, returns the 13.0 entry tagged
  `FullyScanned`, and its own closing line reads that tag back as "has an
  extension scanner matcher behind it".

## Decided

- Step 2, delivery. Not 1a, because the subject is in `knowledge/` twice over —
  on the deprecation side of the prose and in the `breaking` intent. Not 3,
  because nothing failed to fire: the session asked a lookup and the lookup
  answered out of the section its query matched.
- Queued rather than closed on the spot. What the `## Breaking Changes` section
  should say about a matcher is a statement about TYPO3, and
  [judging.md](../../documentation/records/judging.md) keeps a run that has read
  only this repository out of writing one.
- The feedback's second suggestion is declined on the measurement. A single-term
  query reaches the matcher sentence today; the sentence sits under the wrong
  heading, which no retrieval change repairs.
- Its third suggestion is declined too. A lookup over the matcher files would
  answer what the changelog entry's `FullyScanned` tag already answers, and
  `typo3_changelog_lookup` both returns that tag and reads it out in the same
  answer.
- What the work has to establish is not named here: whether a removal owes a
  matcher entry or only admits one, which matcher file a removed method belongs
  in, and that the entry names the Breaking restFile.

## Assumed

- That the `typo3_changelog_lookup` call which found the precedent belongs to
  the same session. `feedback/2026-08-01-115716` reports it from the same review
  of the same patch, and the run above reproduces the answer either way.

## Wrong if

- `## Breaking Changes` gains the matcher sentence and a later feedback still
  reports a removal reviewed without one. Delivery was then not the gap, and the
  wording is what to look at instead — step 4.
- A reading of `.checkouts/` finds that a matcher is not owed to every removed
  public method. The sentence is then a condition rather than a rule, and
  `R-ANS-017` demands the wrong thing.
- A session is given the matcher rule and goes on to read the matcher files by
  hand anyway. The gap is then the shape of the entry rather than the
  requirement, which is the lookup this entry declines.

## Confirmed on 2026-08-03

The measurement reproduced before the change: `Documents::search` on the
`breaking` intent's own `rulesQuery` returned `## Breaking Changes`,
`## Changelog Files`, `## Review Readiness` and `## Summary Line`, and none of
the four named a matcher. The feedback's own query "removing public method
extension scanner matcher breaking changelog" still matched nothing at all.

The second **Wrong if** fired in part. A matcher is not owed to every removed
public method, because not every removed public method is a breaking removal —
`b08282345cd6175b02d69b710f19cd9cd40a04f8` removes an `@internal` one as a plain
`[TASK]`. What it is owed to is what a Breaking or Deprecation entry's scanned
tag claims, so the sentence is stated on the tag. `R-ANS-017` demanded delivery
rather than content and stands unchanged.

## Revoked on 2026-08-03

`## Breaking Changes` now states the matcher, so the route this entry is about
no longer passes a section that stops at the marker and the changelog file. What
the rule says is
[`D-ANS-035`](ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md),
whose **Wrong if** is a different list: what can go wrong now is a tag nobody
backs, not a section that says nothing.
