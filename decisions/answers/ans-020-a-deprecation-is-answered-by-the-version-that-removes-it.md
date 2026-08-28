---
id: D-ANS-020
title: A deprecation is answered by the version that removes it
date: 2026-08-02
status: open
coveredBy:
  - PackageSourcesTest::aDeprecationSaysWhichVersionItStopsWorkingIn
  - PackageSourcesTest::aRemovalClauseThatIsNotThisEntrysIsNotReadAsOne
  - PackageSourcesTest::whereTheEntryStatesNoRemovalTheRuleTravelsWithTheAnswer
---

# D-ANS-020 — A deprecation is answered by the version that removes it

**The removal version is what an upgrade audit decides on, and the changelog
answer carries neither it nor the rule that would supply it where an entry is
silent.**

`typo3_changelog_lookup` names the entry, its tags and the file to read. What it
does not say is when the deprecated thing stops working, which is the fact a "is
this future-proof" review turns on. Neither source is whole on its own: the
entries state a removal in prose for 44 of the 75 deprecations of one major, and
the rule that would cover the rest has an exception inside the same corpus.

## Evidence

- `feedback/2026-07-31-194821` re-run on 2026-08-02 through
  `ChangelogLookup::answer()` against `.checkouts/14.3`. `query: "yaml"` with
  `version: "14.2"` returns the two `#109412` entries, each with its type,
  version, issue, title, tags and `EXT:core/…` path. Neither the rendered text
  nor the `entries` data carries a removal version. The behaviour is unchanged
  since the feedback was written.
- The fact is in the file the answer points at, as prose in the Description
  section: `14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst` reads
  "The TypoScript-based paths will still be loaded during the deprecation period
  but will be removed in TYPO3 v15.0". That is the read the session reports
  doing by hand.
- Parsing it back out reaches half the corpus. Of the 75 deprecations
  `.checkouts/14.3` files under 14.0 to 14.3, 44 state a removal version
  anywhere in their prose and 31 state none; for 13.0 to 13.4 it is 27 of 63.
  The phrasing is free — "will be removed in", "will be removed with", "marked
  for removal in", against "TYPO3 v15", "v15" and "v15.0".
- Where 14 states one it is v15, without exception. All 44 name v15 and no other
  release; the two that also carry another number carry it out of a `:ref:`
  anchor in a different sentence, not out of a removal clause.
- The rule that would answer the other 31 is here and does not travel with the
  answer. `deprecated-apis` in `knowledge/architecture-hints/general.json`
  states "A deprecated API keeps working until the next major release", and
  `bin/cli hints:probe` reaches it from any query carrying "deprecat".
  `typo3_changelog_lookup` returns no hints at all, and its closing paragraph
  names the migration and the Extension Scanner and stops.
- For this entry the corpus is more specific than that, and a reader on 14 gets
  none of it. The `form-framework` hint states that the
  `plugin.tx_form.settings.yamlConfigurations` registration is "deprecated for
  removal in the next major", bound `until: 13` — so it is filtered out at the
  version the reviewer was auditing. `ArchitectureLookup` at `version: 14` with
  `form yaml registration deprecated` matched no hint whatsoever and fell back
  to listing the requestable ids.
- The rule alone would be wrong at least once in this corpus.
  `13.4/Deprecation-105297-DeprecateTableoptionsAndCollateConnectionConfiguration.rst`
  names "TYPO3 v15 (or later)" and skips v14, and it held: on `.checkouts/14.3`
  `ConnectionPool::migrateTableOptionsToDefaultTableOptions()` still migrates
  the key, under an
  `@deprecated since 13.4 and will be removed in v15 (or later as it does not hurt to keep them)`.
  So "the next major" is the default and the entry is what carries the
  exception.

## Decided

- **Step 2 of the ladder, delivery.** The statement exists in `knowledge/`, in
  two wordings, and reaches only a tool the session had no reason to call while
  reading a changelog answer. Not 1a — nothing about the removal of a 14
  deprecation is unknown here. Not 1b — no verb is missing, `lookup` is the verb
  and it is the one that was called.
- **Queued rather than closed on the spot.** The lever is
  `ChangelogLookup::answer()` and probably its declared `outputSchema`, and
  establishing what the changelog states about removal needed `.checkouts/`.
  Both are what [judging.md](../../documentation/records/judging.rst) puts
  beyond a run that has read only this repository.
- **The feedback's suggestion is right about the fact and understates the
  shape.** It asks for the removal version "for deprecation entries", which
  reads as a field parsed per entry; that field is empty for 31 of 75 entries of
  one major, and an empty field beside a populated one is read as "no removal
  planned" — the silence-as-verdict failure `D-ANS-009` was already built
  against.
- **Which shape closes it is not settled here.** A parsed clause, a stated rule
  in the closing paragraph, or both with the entry overriding the rule are three
  answers, and choosing between them is research the todo owns. The
  `@deprecated` annotation at the trigger site is a fourth source and is more
  exact than the prose in the one case checked above.
- Recorded against a new entry rather than against
  [`D-SKL-003`](../task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md)
  or [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md). Those
  two are about reaching the entry — the bounds a sweep is written with, and
  what a miss owes the caller. This one is about what the entry says once it has
  been reached, and the call that produced it hit.

## Assumed

- That an audit needs the release and not only the ordering. The session says so
  itself: it had the deprecation in hand, knew it was deprecated, and still
  opened the file. Nothing here can check that a version number rather than "the
  next major" was what it went for.
- That the 14 corpus is representative of how the core states a removal.
  `.checkouts/14.3` and its 13 directories are what was counted; 12 and older
  were not, and the writing conventions have moved before.

## Wrong if

- A changelog answer that states the removal version is followed by the same
  finding. The fact would then be delivered and not taken, which is step 4 and a
  rewrite rather than a placement.
- A reviewer acts on a stated removal version that the entry contradicts — a 14
  deprecation the core keeps past 15.0, in the shape `#105297` already has on
  the branch below. Then the rule was asserted where only the entry could speak.
- The count moves under this. If a later `.checkouts/` update files 14
  deprecations that name a removal other than v15, the "always v15" reading was
  a snapshot rather than a policy, and the parsed clause is the only honest
  source.

## Since then

Step 2 is done and the open question was the shape: the clause parsed out of the
entry **and** the rule stated once, with the entry overriding. Every entry
carries the field, empty where it states none, and an answer carrying a
deprecation carries the rule beside it — both in the data as well as in the
text, because a client that renders the payload and drops the text is what
`R-ANS-002` is written against.

The rule is not materialised into a per-entry number, and that is the whole
difference from a fourth candidate nobody proposed: a number derived from "the
next major" would contradict an entry the core kept, which is the second **Wrong
if** happening in the field a caller acts on.
