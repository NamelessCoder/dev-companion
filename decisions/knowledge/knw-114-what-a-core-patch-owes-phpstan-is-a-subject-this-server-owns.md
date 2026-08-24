---
id: D-KNW-114
title: What a core patch owes PHPStan is a subject this server owns
date: 2026-08-24
status: open
---

# D-KNW-114 — What a core patch owes PHPStan is a subject this server owns

**The corpus gains a core-scope hint for what the core's own PHPStan run
enforces on the code a patch adds, beside the extension-scope hint that only
sets PHPStan up.**

A patch that fails the analysis learns which line PHPStan rejected and nothing
about which of several legitimate fixes the core writes, and the one hint the
query reaches answers how to configure the tool rather than how to satisfy it.

## Evidence

- `bin/cli hints:probe "PHPStan CacheManager getCache returns FrontendInterface PhpFrontend var annotation narrow"`
  reaches `extension-static-analysis` and `caching`. The first carries
  `scope: extension` and its six hints are the configuration's location,
  `tmpDir`, `bootstrapFiles`, the `ext_emconf.php` exclusion, what a baseline is
  for and that both packages analyse at level 5. None of them is about the code
  being analysed.
- Nothing else in the corpus is. `knowledge/` names PHPStan in four files, and
  the other three are the run rather than the code: the suite entry in
  `knowledge/test-suite-hints.json`, the "Run PHPStan" command in
  `knowledge/documents/core/testing/scripts.md`, and a `reference` catalog
  entry.
- Read in `.checkouts/main` at `3cbdea24dd`, dated 2026-08-18.
  `CacheManager::getCache()` is declared `: FrontendInterface` on line 115, so
  every caller that needs a `PhpFrontend` has to narrow, and the feedback's
  account of its own failure holds.
- The narrowing the session spent four round trips finding is four lines, and
  two of them are in a functional test.
  `typo3/sysext/core/Classes/TypoScript/TypoScriptStringFactory.php:51` is the
  production site it found by hand; `Bootstrap.php:102` is the second; and
  `typo3/sysext/redirects/Tests/Functional/Service/RedirectServiceTest.php` does
  it on lines 125 and 897, which is the situation the session was in.
- The configuration is a subject rather than a level.
  `Build/phpstan/phpstan.neon` runs level 5 with four rules the core writes
  itself — `UnneededInstanceOfRule`, `ForbidAttributeRule` forbidding
  `PHPUnit\Framework\Attributes\WithoutErrorHandler`, `NamedArgumentUsageRule`
  allowed in three test namespaces only, and `UnserializeRule` — beside
  `phpstan-phpunit`'s rules and `treatPhpDocTypesAsCertain: false`. A patch is
  held to all of them and none is written down here.
- `Build/phpstan/phpstan-baseline.neon` is 313 lines and carries no entry for
  `SysTemplateTreeBuilder`, so the grep the session ran to check that was
  correct. Its report says what such a grep costs: an empty result is the answer
  it wanted and reads identically to a failed search.
- The cost is counted in the feedback — four round trips for what one call
  carries, which is the measure `D-FBK-027` sets.
- `feedback/2026-08-24-133515` is the same session and reports that the whole
  patch ran with zero calls to this server. That is the wider finding and it has
  its own card; what is decided here is the answer that was missing when the
  session did look.

## Decided

- Step 1a, the knowledge is missing, and taken on. The subject is what the
  core's analysis demands of code a patch adds: the narrowing idiom, the four
  rules above, and what the baseline may and may not take.
- A hint rather than a document. What is missing is a set of statements that
  each stand alone at the moment a finding is read, not an order of steps, and
  that is the line between the two corpora.
- Beside `extension-static-analysis` rather than inside it, with `scope: core`.
  The existing hint answers a caller setting the analysis up in a package of
  their own, and widening its scope would offer that whole configuration
  checklist to a core patch that has one already.
- The feedback's own suggestion is not adopted as written. Its narrowing fact is
  verified above and its two `AGENTS.md` rules are not: the checkout stands at
  2026-08-18 and carries no `AGENTS.md` at all, so what that file says about the
  baseline is read by the todo before it is written.
- The priority is `normal` rather than the `low` the card arrived at. The gap is
  in the core-patch path, which is the busiest subject in the corpus, and the
  reading it needs is a checkout away.

## Assumed

- That the `@var` annotation is the core's idiom rather than four sites that
  happen to agree. Four is a small population, and the reading that writes the
  hint is what settles whether the core also narrows with an assertion.
- That the four own rules are stable enough to state. They are configuration one
  commit changes, and a hint about them is a hint about a file.

## Wrong if

- The reading finds more than one narrowing in use, so what the hint would state
  is a choice rather than a rule. Then the subject is a procedure and belongs to
  `typo3_rule_lookup`, or to nothing.
- The subject turns out to be one fact. Then it belongs inside
  `extension-static-analysis` with the scope of that statement widened, and a
  second hint is the duplicate this repository spends commits removing.
- A session on a core patch is offered the hint and casts or asserts anyway.
  That is step 4 and a rewrite.
- A later session reports the same four round trips after the hint lands. Then
  the hint is not reached by the query a PHPStan failure produces, which is step
  2 or step 3 rather than this.
