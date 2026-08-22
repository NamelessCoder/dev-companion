---
id: D-KNW-050
title: What a missing `target-language` does to a translation file is a gap this server owns
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt
  - HintsTest::whatAMissingTargetLanguageCostsIsWithheldFromTheBranchesItCostsNothingOn
---

# D-KNW-050 — What a missing `target-language` does to a translation file is a gap this server owns

**The corpus states that a locale-prefixed XLF with no `target-language` on its
`<file>` element is read as the default language on v14, so its `<target>`
values are discarded.**

The labels then render in the source wording, and nothing is raised, logged or
deprecated. `language-files` already names the attribute, inside the correction
of a source file that is not English. That is an authoring procedure, and an
audit does not author a file — it opens one. The conformance skill asks for
every returned rule to be read in both directions, and this rule has no second
direction to be read in, because it never says what the file that lacks the
attribute does.

## Evidence

- The TYPO3 claim holds, read in `.checkouts/14.3` at `faf60eea222`.
  `XliffLoader::parseXliff1()` opens on
  `$isDefaultLanguage = !isset($fileTag['target-language']);`, and the branch
  that guards takes `<source>` where the other takes `<target>`. Neither path
  raises, logs or deprecates anything.
- That loader is the one in play. `Configuration/DefaultConfiguration.php` maps
  `LANG/loader/xlf` to `XliffLoader`, while
  `Localization/Parser/XliffParser.php` carries
  `@deprecated will be removed in TYPO3 v15.0. Switch to Symfony Translation loaders`.
  The class whose name a session greps first answers a different question, which
  is what the reporting session found.
- The trap is new in v14. In `.checkouts/13.4` and `.checkouts/12.4` the same
  decision is `$this->languageKey === 'default'` — the language that was asked
  for, not an attribute of the file. A translation file with no
  `target-language` was read correctly there and is discarded here, so this is
  an upgrade finding as much as an authoring one.
- The manual does not carry it. `Feature-107436-SymfonyTranslationIntegration`
  and `Feature-108049-ModernizedTranslationWorkflow`, the v14 changelog entries
  for the loader switch, name neither `target-language` nor `source-language`.
- Step 1a, and not 2 or 3. `bin/cli hints:probe` reaches `language-files` from
  the symptom — "a German translation file renders English labels",
  `appliesTo(16) + text(60)` — and from the upgrade phrasing — "extension
  upgrade to TYPO3 14 translations stopped working",
  `appliesTo(11) + text(109)`. Delivery and routing work. The corpus has no such
  sentence to deliver: `target-language` occurs twice below `knowledge/`, in the
  `language-files` correction and in the same wording in `task-intents.json`,
  both about a source file in the wrong language.
- Not step 4. The rewrite
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md)
  queued landed in `0e6cf08` on 2026-08-02, a day before this feedback, and the
  session read it — its observation quotes the corrected wording back nearly
  verbatim. What it could not get from that wording is the consequence, and no
  rewording of an authoring procedure produces one.
- The corpus already writes a failure mode of this shape. `site-label-language`
  says of a `typo3Language` mismatch that it "silently falls back to the English
  source instead of reporting an error" — the neighbouring cause of the same
  symptom, stated the way this one is not.

## Decided

- Step 1a of the ladder, on the substance rather than on the wording, and queued
  rather than closed on the spot. What is missing is a statement about TYPO3
  with a version boundary on it, and the run that judged this read three
  checkouts to establish that much; where the statement has to reach is the
  reading the todo owes.
- The priority is `normal` and the judgement is what set it. One session
  reported it, which is not the several that would earn `high`. What lifts it
  off the floor is that the failure is silent in the version every current audit
  targets, and that the session found it by reading installed source rather than
  from any answer here.
- Not step 1b. No shape of answer is missing. The same audit's
  `feedback/2026-08-03-164805` asks for a lookup that reads an identifier out of
  the installed packages, and the four hops this feedback counts are that ask a
  second time; the card for `164805` carries it and is not folded in here,
  because such a lookup would have shortened the reading and still never have
  told the session that the attribute decides anything.
- Where the XLF schema check belongs is left to the todo rather than settled
  here. It is a question about the check layer `typo3-extension-testing`
  describes, and nothing read for this judgement says which answer is right.

## Assumed

- That the German file really was discarded by this path rather than by another
  silent one in the same loader. The package is in another checkout and was not
  read from here. The lever is the same either way, and there is a second such
  path beside it: `requireApprovedLocalizations` defaults to `true`, and every
  unit marked `approved="no"` is dropped on the same lines, which the corpus
  does not state either.
- That a statement about what a wrong file does reaches an auditing session
  where a statement about writing a right one did not. Nothing has measured it,
  and it is the premise the whole entry rests on.

## Wrong if

- The statement lands and a conformance run still passes a translation file that
  declares no `target-language`. Then the reach is what failed rather than the
  wording, and the answer was step 2 — the hint is not where such a task passes.
- The behaviour turns out to be a defect core corrects rather than the contract.
  A v14 patch that falls back to the locale in the file name would leave this
  describing one patch level.
- XLIFF 1.2 does not in fact require `original`, `source-language` and
  `datatype` on `<file>`. The feedback asserts it and this judgement did not
  check it, so a schema check recommended on that basis would rest on nothing.

## Since then

Step 1a landed as two statements in `language-files`, both `since: 14`: what the
missing attribute does to the file, and that no schema check reports it.

The third **Wrong if** is settled and it holds the other way round than the
feedback read it. The OASIS specification and its strict schema make `original`,
`source-language` and `datatype` required on `<file>` and `target-language`
optional, so the file whose translations are being discarded is valid XLIFF. The
check the feedback asked for exists in the core as `checkIntegrityXliff`, and
`Build/Scripts/checkIntegrityXliff.php` in `.checkouts/14.3` validates the
schema and then those three attributes and never reads `target-language` — it is
a core suite over source files. So the check belongs nowhere, and what a
validator cannot see is stated in the hint instead.

The reach holds without changing a skill. `skills/base.md` puts one
`typo3_hint_lookup` per subsystem in scope in front of every workflow, the
conformance checklist names translations among its surfaces, and
`bin/cli hints:probe` reaches `language-files` from the audit phrasing and from
the upgrade phrasing alike. `bin/cli hints:coverage` reports no hint its own
title fails to reach and does not count `language-files` among the ones no
scenario prompt reaches.

What step 2 would be is established, for whoever finds the first **Wrong if**
happening. `Installation\Extension::languageFiles()` already reads the
`source-language` of each source file out of its `<file>` element and reports
the locale-prefixed neighbours by existence alone, so reporting whether each of
those declares a `target-language` is the same regex one file over. It is not
queued: the decision above bets on the statement reaching, and taking the step
before the bet is read would leave nothing to read.
