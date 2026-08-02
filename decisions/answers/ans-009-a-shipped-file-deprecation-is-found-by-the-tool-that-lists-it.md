---
id: D-ANS-009
date: 2026-08-02
status: open
---

# D-ANS-009 — A shipped-file deprecation is found by the tool that lists the file

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
- `ExtensionTest::aFrameworkPackageIsExemptFromBoth`
- `ExtensionTest::declaringOneOfTheTwoFieldsStillReadsTheFile`

## Since then

Step 1a is done and the first **Wrong if** did not bite. It assumed `since: 14`
would be a new imprecision, and it is the one the corpus already carries:
`#108345` is itself a mid-major arrival — `.checkouts/14.3` files its changelog
under `14.2`, and the changelog says the fields are not yet mandatory in v14 —
while the `extension-files` statement that carries it is bound `since 14 until
14` and has been green under
`HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` all along. A
statement that starts holding inside a major is therefore already bound to the
whole of it here, and `#109438` binding the same way loses the same granularity
rather than a new one. Nothing needed a minor in the text, so the check that
forbids it was never in the way.

So `#109438` is written into `extension-files` as two statements. The
deprecation is `since 14 until 14`, read at both trigger sites in
`Configuration\Extension\ExtTablesFactory` — `createCacheEntry()` and
`loadSingleExtTablesFiles()`, neither of which `load()` reaches on a cache hit,
which is why the statement says an uncached request and cache warm-up and says
a cached request raises nothing. The removal is `since 15`: on `.checkouts/main`
the class is gone, `ext_tables.php` appears in no PHP file under
`typo3/sysext/`, and `15.0/Breaking-109783` names it as no longer considered
during bootstrap, so a registration left there is lost without a report. The
migration targets in the statement are the four the deprecation changelog
names.

What stays open is the delivery half for both files — what
`typo3_extension_scope` says about the two it already lists — and the feedback
is archived by that, not by this.

Step 2 is done and the open question was the shape: a field of its own, or the
hint carried beside the answer. It is a field, `deprecatedFiles`, one entry per
listed file that a deprecation names, each carrying the changelog number, the
predicate, and what it costs. Three things settled it. The data half of a
`ToolResult` is a contract clients validate, so a cost said only in the rendered
text is invisible to half the callers — the reason `D-ANS-008` put the `Classes`
measure in both. The hint route was already taken by step 1a and did not close
the feedback: the reviewer had this answer in hand and no reason to type either
file name into a lookup, which is what a pointer would have asked of them. And
the second **Wrong if** is about a silence being read as a verdict, which is
answered by where the boundary is stated rather than by whether there is a
field — a schema description is a place to state it once, and rendered prose is
not.

That **Wrong if** is what the shape is built against and it is not covered by
anything. The field is not called `deprecations`: it is the registration files
this answer already lists, its description says an empty list is not a clean
bill and names `typo3_changelog_lookup` as what answers that, and the rendered
block says the same and is omitted entirely where there is nothing — a rendered
"none" is the line that would read as a verdict. Whether that is enough is a
later feedback's to report.

Two things were decided against the todo's own reading and are worth
disagreeing with. The finding is **not** filtered by the installation's major:
it names the version it starts at instead, because `bootstrap_package` requires
`^13.4 || ^14.3` and withholding it on 13.4 hides exactly the migration surface
this exists to surface. And a framework package is exempt from **both**, where
core exempts one — `getComposerManifest()` has no such check. It is the right
answer anyway: on 14 no system extension ships an `ext_emconf.php`, on 13 all
36 of them do and none is the caller's to migrate, and `#108345` itself says a
core extension may omit the version because `Typo3Version` supplies it.

The cost sentences now stand in `src/Installation/Extension.php` as well as in
the `extension-files` hint, which is two spellings of one rule and the thing
most likely to go wrong here. Nothing holds them to each other and nothing
could: they are written for different readers, one long and one short. What
would show it is a reader finding the two disagreeing about when a deprecation
fires.

Still no requirement. What must hold is one behaviour of one tool, the tests
above hold it, and a requirement restating them would be a third place the same
sentence lives.
