---
id: D-ANS-009
date: 2026-08-02
status: open
---

# D-ANS-009 — A shipped file is a deprecation predicate no sweep reaches

**A deprecation whose predicate is a file the extension ships is found by the
tool that lists that file, not by a changelog sweep over what the code calls.**

`typo3_extension_scope` prints both file names this feedback is about, in one
line beside four files nothing is wrong with, and says nothing about either.

## Evidence

- The query of `feedback/2026-07-31-172757-…` re-run over
  `/home/benji/projects/bootstrap_package` today answers
  `Registration files: ext_localconf.php, ext_tables.php, ext_tables.sql,
  ext_emconf.php, Configuration/Services.yaml,
  Configuration/JavaScriptModules.php`. The two predicates are in the answer as
  a file listing, and `ext_localconf.php` beside them carries nothing.
- Both predicates hold, read at their trigger sites in `.checkouts/14.3`.
  `Configuration\Extension\ExtTablesFactory` raises `E_USER_DEPRECATED` in
  `createCacheEntry()` and in `loadSingleExtTablesFiles()` for every active
  package that has an `ext_tables.php` and is not `isFrameworkType()`.
  `Package\PackageManager::getComposerManifest()` raises it where
  `isComposerOnlyCapable()` is false, which is `providesPackages` unset, or
  neither a top-level `version` nor `extra.typo3/cms.version`.
- The feedback dates both to v14.3. `#109438` is 14.3; `#108345` is 14.2, and
  its changelog states no impact on a Composer-based installation. A signal
  reading "composer.json missing version/providesPackages: yes" would therefore
  be true and misread by the Composer majority. `bootstrap_package` declares
  neither field, so both fire for the extension that was reviewed.
- `#108345` is already written whole in the `extension-files` hint of
  `knowledge/architecture-hints/php.json`, bound `since 14 until 14`, naming
  `failOnDeprecation` as what surfaces it — which is how the reporting session
  found it. `#109438` is in `knowledge/` nowhere: `ext_tables.php` occurs once,
  as an `appliesTo` string, and `bin/cli hints:probe` reaches that hint whose
  text is silent on the file.
- `typo3_changelog_lookup` answers `ext_tables.php` with `#109438` today, which
  it did not when this was reported — `D-ANS-006`. That closes the retrieval
  half and not this one: a reviewer with no rule saying the file matters has no
  reason to type its name into a changelog.
- The extension-key sweep `feedback/2026-07-31-172753-…` asked for would not
  have reached either entry. Both are tagged `ext:core` and `NotScanned`,
  because both describe what core does with any extension's files rather than
  an API that extension calls.

## Decided

- Queued rather than closed on the spot. The lever is `typo3_extension_scope`,
  so the change touches `src/` and a declared `outputSchema`, and establishing
  the predicates needed the checkouts — both of the two things
  `documentation/feedback/judging.md` puts on the far side of that line.
- Step 2 for the `ext_emconf.php` half: the rule is here, complete and bound,
  and the answer naming the file does not carry it. Step 1a then step 2 for the
  `ext_tables.php` half, since the statement it would deliver does not exist.
- Not step 1b. No verb is missing and no skill is: `typo3_extension_scope`
  already reads every file both predicates turn on, and
  `typo3-extension-conformance` is the skill the review ran under.
- No requirement yet, and the todo names no field. Whether this is a hint, a
  section of the extension answer, or both is not established by a run that read
  only this repository.

## Assumed

- The reviewer would have acted on a signal in the answer it already read. That
  is what the feedback reports about itself and nothing here can check it.

## Wrong if

- The statement turns out unwritable where it has to go. A hint binds by major
  integer — `since` and `until` carry `12` to `15` across all of `knowledge/` —
  and `#109438` holds from 14.3, so `since: 14` is false for 14.0 to 14.2 while
  `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` forbids saying the
  minor in the text.
- A later feedback reports the opposite cost: an extension answer that
  volunteers deprecations is read as a compatibility verdict, and a caller
  treats the absence of a signal as a clean bill for the next major.

## Covered by

- `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch`
