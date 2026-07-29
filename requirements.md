# What this server has to do

A feedback note is a question, and it is deleted once it is answered. The
requirement the note established is not a question and does not go away with it:
it has to keep holding while everything around it changes. This file is where it
survives the note.

Every entry states one thing that must be true, names where the demand came
from, and says what holds it to that — a test where there is one, `not guarded`
where there is none. An entry marked **open** is a requirement that has been
accepted and not yet met; that is the backlog, and it is deliberately the same
list, because a requirement nobody has implemented yet and one that could
silently regress are the same kind of thing.

Rules for keeping it usable: an entry is added when a note is worked off, not
when it arrives — a note nobody has judged yet is a note, not a requirement. An
entry is never deleted because it was implemented; it is deleted only when the
requirement itself is withdrawn, and then the reason goes in
[decisions.md](decisions.md). Assumptions and evidence live there too; this file
holds only what must be true.

## Audience — who the answer has to be right for

These four govern the rest. Where a requirement below conflicts with one of
them, this section wins and the other one is what needs rewriting.

- **R-AUD-1** Three audiences are served deliberately, not one: the core
  contributor, the extension author, and the site developer. An answer is right
  when it is right for the one that asked. Where knowledge holds only for core
  contribution, it is marked as core-only rather than handed over as the rule —
  a core checklist given to an extension author is a wrong answer, not a partial
  one.
- **R-AUD-2** **open** — The audience is a property of the task, not of the
  directory. Extensions are routinely developed inside a site installation, a
  site package is an extension, and a core checkout can be the place someone
  debugs their site. Signals are combined, and where they disagree the answer
  says the audience is uncertain instead of picking one silently.
- **R-AUD-3** Commit conventions differ by audience. The subject line and body
  rules transfer; `Releases:`, Forge issue numbers, and the Gerrit `Change-Id`
  are core rules and belong to core work only. A site or extension repository
  has its own workflow, and the guide must be usable there without producing
  trailers that mean nothing in it.
  *Held by:* `CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded`,
  `CommitMessageTest::outsideTheCoreTheSubjectAndBodyRulesStillHold`,
  `CommitMessageTest::theSecurityKeywordIsTheRepositoryOwnOutsideTheCore`
- **R-AUD-4** The knowledge is bound to versions. Which TYPO3 lines are covered
  is declared in `knowledge/versions.json`; a statement that does not hold on all
  of them carries `since`/`until` as data rather than saying so in its prose; and
  an answer is composed for the version the caller stated, else for the one the
  installation being read runs, else for none — and then every statement comes
  back with the range it holds for. A catalog entry carries the same binding for
  the whole entry, because markup and a class list are pasted together, and a
  target version withholds it rather than qualifying it: a class that is not
  there fails in a browser without an error. What is withheld is named, with
  what to verify it against, so silence never reads as "does not exist". The
  markdown documents carry no range and are not filtered; a prose answer says so
  and names where the bound form is.
  *From:* v15 markup handed to a 13.4 caller, and a translation domain handed to
  an installation that resolves none (2026-07-29).
  *Held by:* `VersionsTest` in full — the range model, the precedence, the
  filtering, that no statement dates itself in prose, and that a prose answer
  says it is not the bound half —
  `CatalogTest::aComponentNotVerifiedOnTheTargetIsDeclinedRatherThanHandedOver`,
  `CatalogTest::aComponentVerifiedOnTheTargetIsAnsweredWithTheRangeItHoldsFor`,
  `CatalogTest::withoutATargetTheWholeCatalogAnswersAndEachEntryCarriesItsRange`,
  `CatalogTest::theCatalogSaysHowMuchOfItWasVerifiedOnAStatedVersion`,
  `CatalogTest::everyRecordedBindingNamesACoveredVersion`,
  `CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead`,
  `CatalogTest::anInstallationWithoutTranslationDomainsIsGivenTheFileReference`,
  `InstanceTest::theTypo3VersionIsReadFromTheCorePackageRatherThanAskedOfTheConsole`

## Discovery — which installation is read, and how

- **R-DIS-1** The installation is never derived from `getcwd()` on its own; only
  `bin/typo3-cms-mcp` enables discovery, because an HTTP endpoint has no such
  relationship to its callers.
  *Held by:* `InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance`
- **R-DIS-2** The packages of a Composer installation are read from the vendor
  directory it declares, not from the default.
  *From:* the extension checkout with `config.vendor-dir=.build/vendor` that was
  reported as "no installation found" (2026-07-29).
  *Held by:* `InstanceTest::aProjectThatMovedItsVendorDirectoryIsFoundThereRatherThanMissed`
- **R-DIS-3** The console is looked for at the `config.bin-dir` the installation
  declares, before the Composer defaults.
  *From:* `.build/bin/typo3` existing, working, and never being probed
  (2026-07-29).
  *Held by:* `Typo3CliTest::aConsoleInTheDeclaredBinDirectoryIsFound`
- **R-DIS-4** The extension being worked on is part of the answers about its own
  installation, although Composer lists dependencies rather than the root.
  *From:* 29 registered icons of the root extension reported as non-existent
  (2026-07-29).
  *Held by:* `InstanceTest::theExtensionBeingWorkedOnIsAmongThePackagesAlthoughComposerListsOnlyDependencies`
- **R-DIS-5** A repository whose dependencies were never installed is not
  reported as an installation.
  *Held by:* `InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne`
- **R-DIS-6** Nothing on the caller's machine is started as a side effect of a
  lookup; a stopped DDEV project is reported with the command that would fix it.
  *Held by:* `Typo3CliTest::aDdevProjectThatIsNotRunningIsReportedRatherThanStarted`
- **R-DIS-7** The installation root and the console command can be set
  explicitly, and the answer says which of the two was used. Every
  layout-specific discovery failure is then a one-line fix for the user instead
  of five tools silently going quiet. A stated setting that cannot be used is
  reported, never quietly replaced by a discovered one.
  *From:* a session where two links broke at once — a moved bin-dir and a host
  PHP below the required one — with no lever available (2026-07-29).
  *Held by:* `InstanceTest::anInstallationNamedOutrightIsReadWithoutAnySearch`,
  `InstanceTest::aNamedInstallationThatDoesNotExistIsReportedRatherThanSearchedPast`,
  `Typo3CliTest::aStatedCommandIsUsedInsteadOfWorkingOneOut`,
  `Typo3CliTest::aStatedCommandThatIsNoProgramIsReportedRatherThanReplaced`
- **R-DIS-10** Reachable and ready are two questions. A console found on this
  machine while the project it belongs to is meant to run elsewhere is reported
  with what that costs — everything that boots TYPO3 against its database — in
  the text and in the data, and a failing lookup repeats it where the error
  alone does not say it.
  *From:* `typo3_server_scope` reporting the console as reachable via host PHP
  8.3 with the DDEV project stopped, so five installation-backed tools were
  presented as usable while four of them could not answer (2026-07-29).
  *Held by:* `Typo3CliTest::aStoppedProjectReachedThroughHostPhpIsReportedAsTheHalfAnswerItIs`
- **R-DIS-9** Nothing that says "there is no installation" is remembered. A
  successful resolution is memoized for the process; a failure is retried on
  every call, because the caller who reads that answer is the one likely to
  install, migrate or start something and ask again in the same session.
  *From:* a session lost to a cached negative — the agent ran `composer
  install`, started DDEV, verified `bin/typo3` answered, and every tool kept
  reporting no installation until the client was restarted (2026-07-29).
  *Held by:* `InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`
- **R-DIS-8** When discovery fails, the answer names where it looked, in text and
  in data, so a layout that cannot be read is distinguishable from a server
  started in the wrong directory.
  *From:* the same session; "no installation found" was indistinguishable from
  "started in the wrong directory".
  *Held by:* `ScopeTest::theInstallationDiagnosticIsDataRatherThanProse`

## Answers — what a caller may conclude from one

- **R-ANS-1** An empty answer that means "could not ask" is never shaped like one
  that means "does not exist". Every installation-backed tool carries
  `answeredBy`.
  *Held by:* `ToolContractTest`
- **R-ANS-2** The reason behind `answeredBy: "nothing"` is in the structured
  data, not only in the text, and `typo3_server_scope` carries the installation
  and console diagnostic as data too. Nothing a caller needs in order to act
  lives in the text alone.
  *From:* a client that renders `structuredContent` and drops the text block; the
  agent twice concluded an extension registered no icons and no labels
  (2026-07-29).
  *Held by:* `ScopeTest::anUnanswerableLookupCarriesItsReasonInTheData`,
  `ScopeTest::theInstallationDiagnosticIsDataRatherThanProse`
- **R-ANS-4** The query language a tool documents is the one it implements.
  Where the command behind it can only match a literal string, the tool composes
  the search out of the words rather than handing the phrase through, so a
  multi-word query means what the description says it means.
  *From:* every multi-word label query coming back empty, the tool's own
  documented example included (2026-07-29).
  *Held by:* `LabelSearchTest::aLabelAnswersOnlyWhenItCarriesEveryWord`,
  `LabelSearchTest::theWordsMayComeInAnyOrderAndAnyCase`
- **R-ANS-5** A console that ran and answered "none" is an empty answer.
  `answeredBy: "nothing"` is reserved for a console that could not be reached or
  that failed — this is R-ANS-1 in the other direction, and a zero-hit answer
  dressed as a breakage sends the caller to fix an installation instead of
  narrowing a query.
  *From:* the same note; the console's zero-match warning was read as an
  unreachable installation (2026-07-29).
  *Held by:* `LabelSearchTest::aConsoleThatFoundNothingIsAnAnswerRatherThanAFailure`,
  `LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered`
- **R-ANS-8** An installation-backed answer is not lost to a console that cannot
  boot where the files hold it anyway. `typo3_label_lookup` falls back to the XLF
  files of the same packages and `typo3_fluid_namespace_list` to their
  `Configuration/Fluid/Namespaces.php`; both report `answeredBy: "packages"` and
  name what the weaker source leaves out. Where nothing can answer, the failure is
  diagnosed rather than passed through: a query against a missing table means
  the database has no schema, not that the installation is broken.
  *From:* an installed TYPO3 13.4.33 before the dump was imported, where the
  labels sat in the files and both console-backed lookups returned a raw SQL
  stack trace (2026-07-29).
  *Held by:* `PackageSourcesTest::withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne`,
  `LabelSearchTest::aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead`,
  `LabelSearchTest::aDatabaseWithoutASchemaIsNamedRatherThanLeftAsAStackTrace`,
  `Typo3CliTest::aFailureIsDiagnosedOnlyWhereTheMessageDoesNotSayEnough`
- **R-ANS-7** A query is scored by the terms that separate one section from the
  rest, not by term overlap. A word half the knowledge base carries decides
  nothing, a term is matched as a word rather than as a substring, and which of
  the two corpora — the prose or the architecture hints — holds a subject is not
  the caller's problem: `typo3_rule_lookup` names the hints that match the same
  query.
  *From:* "site set settings definitions" answered with the backend's Sass class
  naming, at a stated 75% of the query terms (2026-07-29).
  *Held by:* `KnowledgeTest::theDiscriminatingTermsOfAQueryDecideTheAnswer`,
  `KnowledgeTest::aTermMatchesAWordRatherThanAnythingThatContainsIt`,
  `ScopeTest::aRuleQueryIsPointedAtTheHintCorpusItBelongsIn`
- **R-ANS-6** A lookup that returns nothing says what there would have been to
  find, and what it names can be asked for outright. `typo3_architecture_lookup`
  lists the hint ids of the searched domains on every miss and accepts one as
  `id`, so "your words did not match" is distinguishable from "nobody wrote this
  down" without trying another phrasing.
  *From:* a query naming XLF, labels and language files returning the TCA hint
  and nothing else, with no way to see that a Language Files hint existed
  (2026-07-29).
  *Held by:* `HintsTest::aMissNamesWhatThereWouldHaveBeenToFind`,
  `HintsTest::aHintCanBeAskedForByItsIdInsteadOfGuessedAt`,
  `HintsTest::anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo`
- **R-ANS-3** What a component answer describes is qualified by the revision it
  was taken from, inside the entry rather than only in a trailing block.
  *From:* 15.0 markup handed to a caller supporting 13.4 and 14.3 (2026-07-29).
  *Held by:* `CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead`,
  and the `describesVersion` field the component schema requires
  (`ToolContractTest`).

## Project — the repository the caller is standing in

- **R-PRJ-1** The project around the installation is describable from its files
  alone: its TYPO3 and PHP constraints, the extensions that are its own rather
  than TYPO3's, the sites it configures with the sets they depend on, and the
  commands it declares. No console, no database, so it answers on a fresh clone.
  *From:* three sessions asking for a project mode, and a guide that recommended
  `runTests.sh` to repositories that declare `composer t3g:cgl` (2026-07-29).
  *Held by:* `ProjectTest::theProjectIsDescribedFromItsFilesAlone`,
  `ProjectTest::withoutAnInstallationThereIsNoProjectToDescribe`
- **R-PRJ-3** What a version changed is read from the changelog the installed
  core ships, never bundled. A snapshot would answer for the version it was
  taken from; the installation's copy answers for the version the caller runs,
  and the answer names the versions it covers so a gap is visible rather than
  silent.
  *From:* "what did v14 deprecate that affects my code" answered with how to
  author a deprecation (2026-07-29).
  *Held by:* `PackageSourcesTest::theChangelogOfTheInstalledCoreIsSearchable`,
  `PackageSourcesTest::theChangelogIsNarrowedByTypeAndVersion`,
  `PackageSourcesTest::anInstallationWithoutAChangelogSaysSoRatherThanAnsweringEmpty`
- **R-PRJ-5** What an extension registers is answered from its own files: the
  tables its TCA defines and the ones it extends, the content elements it adds,
  its backend modules and routes, its icons, its site sets, its service tags,
  its middlewares, its Fluid roots and namespaces. The table an override file
  extends is read from what the file does, never from its name — extensions
  number those files to fix their load order — and the content elements are the
  identifiers of the items it adds to `tt_content.CType`, in both the positional
  and the keyed item shape, rather than the pointer at `tt_content` that says
  where they are registered. Each carries the template it renders through, read
  from its own TypoScript and left unknown where that says nothing, because a
  template name derived from the identifier sends the caller to a file that is
  not there. What is declared is answered; what an extension
  does at runtime is named as not covered rather than guessed. The project's
  Composer patches are part of what the project is.
  *From:* an evaluation for a site with a sitepackage and its own extension,
  where the scope named the extension and nothing inside it (2026-07-29).
  *Held by:* `ProjectTest::whatAnExtensionRegistersIsReadFromItsOwnFiles`,
  `ProjectTest::theContentElementsAnExtensionAddsAreNamedRatherThanPointedAt`,
  `ProjectTest::anExtensionTheInstallationDoesNotHaveIsAMissWithTheKeysItDoes`,
  `ProjectTest::aPatchedDependencyIsPartOfWhatThisProjectIs`
- **R-PRJ-4** Upgrading an installation is answered as an order of operations,
  not as a list of commands: the code, then the schema, then the wizards, then
  the caches, with what is irreversible named before the first step that is.
  The knowledge carries it and the guide composes it with what the project scope
  and the changelog lookup already know.
  *From:* "what do I do, in which order, and what breaks" answered with how to
  author a deprecation (2026-07-29).
  *Held by:* `HintsTest::upgradingAnInstallationIsAnsweredAsAnOrderOfOperations`
- **R-PRJ-2** One unreadable site configuration costs that site and no other. A
  repository mid-edit is a state it is genuinely in.
  *Held by:* `ProjectTest::aSiteConfigurationThatCannotBeParsedCostsThatSiteAndNoOther`

## Scope — core conventions where they apply, and nowhere else

These four are how R-AUD-1 and R-AUD-2 are met in the tools that exist today.
`outsideCore` is a boolean, and an audience is not — the flag is the shape this
took before the requirement above was written down, and it will not survive it.

- **R-SCO-1** Work outside the core is recognised from structural evidence — the
  kind of installation, the shape of the paths, an area the installation knows
  as somebody's extension — rather than from wording. Evidence of core work
  wins over the weaker signals, in this order: a `typo3/sysext/` path or the
  contribution workflow named outright, then an outside-core marker, then the
  area, then the path shape, then the contribution workflow named in prose, and
  last which installation the session sits in. A `typo3/sysext/` path is the only
  marker that ends the question outright: prose that names the core in order to
  rule it out reads to a substring search exactly like claiming it.
  *From:* `outsideCore` flipping only after the caller spelled out "not TYPO3
  core" in prose (2026-07-29).
  *Held by:* `ScopeTest::namingTheCoreInOrderToRuleItOutIsNotEvidenceOfCoreWork`,
  `ScopeTest::anAreaTheInstallationKnowsAsSomebodysExtensionIsOutsideTheCore`,
  `ScopeTest::inASiteInstallationTheWorkIsOutsideTheCoreUnlessSomethingSaysOtherwise`,
  `ScopeTest::inACoreCheckoutNothingIsPushedOutsideByTheInstallationAlone`,
  `ScopeTest::aPathInsideAnExtensionIsRecognisedByItsShape`
- **R-SCO-2** `outsideCore` changes the payload. Core-only commands, checklist
  items and checkout discovery are dropped; conventions that transfer stay and
  are marked as such. The line is drawn per entry, not per section, because a
  checklist mixes both.
  *From:* an answer that reported `outsideCore: true` and then returned four
  `runTests.sh` suites for a repository that has no `Build/Scripts/`
  (2026-07-29).
  *Held by:* `ScopeTest::aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas`,
  `ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`,
  `ScopeTest::anArchitectureHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks`
- **R-SCO-5** Recognising work outside the core is not one tool's business.
  Every tool whose payload is core-only applies the same check and opens with
  the same sentence, so a caller does not have to know which of them looked.
  *From:* `typo3_task_guide` prefixing the disclaimer for paths that
  `typo3_test_run_guide` answered with four unrunnable commands in the same
  session (2026-07-29).
  *Held by:* the two tests above, and
  `ScopeTest::noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt`,
  `ScopeTest::aScriptAnswerSaysWhichRepositoryItsCommandsRunIn`
- **R-SCO-6** Every covered topic states what its answers are worth outside the
  core: `core-only`, `transferable`, or `installation`. The boundary runs through
  the middle of this server rather than around it, and a caller that has to work
  that out per tool ends up trusting all of it or none of it.
  *From:* a site developer for whom five installation-backed tools answered
  correctly while the curated half handed over runTests.sh commands, with
  nothing in the scope separating the two (2026-07-29).
  *Held by:* `ScopeTest::everyCoveredTopicSaysWhatItIsWorthOutsideTheCore`
- **R-SCO-7** A client is offered only the half of the server it can use. In a
  Composer project the core contribution surface is left out of the tool list —
  the review rules, the Gerrit workflow, the `runTests.sh` suites — and
  `TYPO3_MCP_PROFILE` decides it outright. Whatever the profile, nothing the
  server hands out points at a tool it does not offer, and `typo3_server_scope`
  is in every profile and names the active one, what it left out, and how to be
  offered it anyway: a shorter list a client cannot explain is a broken server
  as far as it can tell.
  *From:* marking a topic core-only telling a site developer what an answer is
  worth without keeping the tool that gives it out of the list (2026-07-29).
  *Held by:* `ProfileTest` in full — the derivation, the override, the
  misconfiguration, and that the scope both the tool answer and the resource
  index are built from routes to no omitted tool
- **R-SCO-3** Core-only intents such as patch submission are not selected for
  work that is not core work. They need positive evidence of core work — a
  `typo3/sysext/` path or the contribution workflow named outright — because
  the words that match them ("review", "push", "submit") describe maintenance
  anywhere. Outside the core they are dropped; where nothing says either way
  they are offered under their condition, never stated.
  *From:* third-party extension maintenance recognised as a Gerrit patch
  submission (2026-07-29).
  *Held by:* `ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore`,
  `ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission`,
  `ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll`
- **R-SCO-4** The backend CSS conventions are answered as what they are — the
  backend interface's — and do not match a frontend theme, nor a PHP file whose
  name merely contains "scss". They are named `Backend CSS` and
  `Backend TypeScript` in every answer, and where a task names the frontend they
  are withheld with the reason rather than applied.
  *From:* four confidently inverted hints for a Bootstrap 5 frontend theme
  (2026-07-29).
  *Held by:* `HintsTest::aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions`,
  `HintsTest::stylingABackendModuleStillReachesTheBackendCssHints`,
  `HintsTest::aPhpClassNameThatCarriesTheWordScssIsStillPhp`

## Guides — what a returned draft is worth

- **R-GUI-1** The checks a guide returns describe the draft it returns. A
  trailer the tool adds itself is never reported as missing, and what the draft
  cannot know it carries as a placeholder rather than as a default.
  *From:* `Releases: main` being appended and `missing-releases` warned in the
  same answer (2026-07-29).
  *Held by:* `CommitMessageTest::theDraftNeverCarriesAReleaseTheCallerDidNotName`,
  `CommitMessageTest::aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing`
- **R-GUI-2** The TYPO3 commit message rules are available without the Gerrit
  trailers, because the conventions are used well outside the core and the
  trailers are not. This is R-AUD-3 for the one guide that has the problem
  today. Which of the two rule sets was applied is part of the answer, in the
  text and in the data.
  *From:* the same note.
  *Held by:* the three tests above, and `ToolContractTest` for the `workflow`
  field the output schema now requires.

## Feedback — what the backlog has to stay usable for

- **R-FBK-1** A note is about as many tools as it is about. The names survive
  recording as names, are listed as a list, and the backlog can be filtered by
  one of them — the obvious question to ask a backlog is what is open about one
  tool.
  *From:* four tool names recorded as one unsearchable word, because everything
  that was not `[a-z0-9_]` was stripped from the field (2026-07-29).
  *Held by:* `FeedbackTest::severalToolsStaySeveralToolsRatherThanOneWord`,
  `FeedbackTest::aListOfToolsIsAcceptedAsOne`,
  `FeedbackTest::theListCanBeRestrictedToOneTool`

## Knowledge — what is covered

- **R-KNW-12** Whether an extension is part of the core, and on which versions,
  is answerable rather than recalled. It is asked about a package that is not
  installed — that is exactly when it is asked — so it is a catalog derived from
  one checkout per covered version, and a miss says the name is not a system
  extension there rather than that it does not exist.
  *From:* a community package cited to the user as evidence of what the core
  does, corrected with "content blocks die extension ist kein core code", and a
  system extension nobody knew existed until the user named it (2026-07-29).
  *Held by:* `CatalogTest::whetherAnExtensionIsPartOfTheCoreIsAnswerable`,
  `CatalogTest::aTargetVersionDecidesWhichExtensionsAreShipped`,
  `CatalogTest::everyShippedRangeNamesACoveredVersion`, and
  `bin/verify-catalog`, which re-derives the list from the checkouts
- **R-KNW-11** Extbase is covered as its own subject, including what breaks
  while writing a plugin: the cache hash of a GET form, the property mapping of
  an object argument, an unpersisted argument dropped from a link, a paginator
  clamping an out-of-range page, and the routes a paginated plugin needs. Each
  of them answers with a wrong page or an error page rather than with a stack
  trace anyone could search for.
  *From:* a catalog with fifty hint ids and not one about Extbase, and the five
  failure modes met afterwards while building the plugin it had nothing to say
  about (2026-07-29).
  *Held by:* `HintsTest::anExtbasePluginHasAHintOfItsOwn`
- **R-KNW-10** An answer says where it may be used when it is only usable in
  half of TYPO3. The icon identifiers are the backend registry's, so every
  `typo3_icon_lookup` answer carries that sentence — in the text and in the
  data — rather than only the ones whose query happens to sound like frontend
  work. A tool that is handed a query and not a task cannot tell the two apart.
  *From:* backend icon identifiers about to be used in a frontend template,
  stopped by the user: "die icons welche du findest sind übrigens nur für das
  backend gedacht, nicht für das frontend" (2026-07-29).
  *Held by:* `IconLookupTest::everyAnswerSaysTheIdentifiersAreTheBackendRegistrys`,
  `IconLookupTest::theRoutingEntrySendsCallersThereForBackendWorkOnly`
- **R-KNW-9** "How do I register this so the core finds it" is a covered
  question. Registering a content type, and registering a class the container
  resolves by name, both fail at request time and neither is a convention of a
  subsystem or a piece of backend markup — the two places an answer was looked
  for.
  *From:* a content element registered with a call signature from the previous
  major, and a page title provider that was not public and therefore not found
  (2026-07-29).
  *Held by:* `HintsTest::registeringSomethingSoTheCoreFindsItIsCovered`
- **R-KNW-8** Putting records of an own table on a page is covered as its own
  subject: the TCA-only table, the data processor chain that reads it, the
  single view fed by a request argument, and the routing a site set ships for
  it. It is what a site is built out of and it belongs to no single domain, so
  it is reachable from a task that names none of them.
  *From:* a product list, detail view and teaser element built for a
  sitepackage, answered with backend-form and shipping-content hints while the
  mechanism the whole task consists of was written down nowhere (2026-07-29).
  *Held by:* `HintsTest::aProductSectionInASitepackageIsAnsweredWithHowItIsBuilt`
- **R-KNW-7** A hint that ends in an instruction says how the instruction is
  carried out. Naming the way in and stopping where the work starts leaves the
  caller with the traps the sentence just sent them into.
  *From:* "seed with DataHandler, then export" — with no way to get a
  DataHandler, three steps of a hand-written boot each of which fails on its
  own, and an export that silently omits every table nobody named (2026-07-29).
  *Held by:* `HintsTest::theSeedingAdviceCarriesTheStepsItAsksFor`
- **R-KNW-6** Where the core itself ships the worked example of a convention,
  the answer names it instead of describing one. A theme extension is part of
  the core and is the reference a sitepackage is compared against, so it is
  covered here — the frontend-theming boundary excludes a project's own CSS,
  not the core's own extension.
  *From:* a sitepackage built with an invented directory layout, rejected with
  "die ordner passen nicht zu den best practices, bitte prüfe camino"
  (2026-07-29).
  *Held by:* `HintsTest::aSitepackageIsAnsweredWithTheLayoutTheCoreItselfShips`
- **R-KNW-5** Where a mechanism fails silently, the hint names the failure, not
  only the rule. A caller whose page comes back wrong with a 200 and an empty
  log has nothing to search for, so the sentence worth writing down is the one
  that says what it looks like when it goes wrong.
  *From:* a variable assigned outside `<f:section>` in a template that declares
  a layout, never executed and never reported; an HTML comment whose
  `{placeholders}` were resolved into the response; a layout root that put the
  page frame inside every content element; and `excludeDoktypes` replacing the
  default list so that every storage folder appeared in the menu (2026-07-29).
  *Held by:* `HintsTest::theTemplateTrapsThatFailWithoutAnErrorAreNamed`,
  `HintsTest::aNavigationIsAnsweredWhereMenusAreActuallyConfigured`
- **R-KNW-4** Where an answer covers one side of the authoring-versus-reading
  split, it names the side it is on and points at the other in one line. This is
  R-KNW-3 for the prose, and it is a pointer rather than site documentation, so
  it stays inside the stated scope.
  *From:* `typo3_rule_lookup` answering "deprecation" with how to write one, to a
  caller asking what a version had deprecated (2026-07-29).
  *Held by:* `KnowledgeTest::anAnswerAboutAuthoringPointsAtTheReadingSideOfTheSameThing`
- **R-KNW-3** A hint says how a subsystem is used, not only what a patch to it
  has to satisfy. Both audiences read the same entry: "DataHandler changes are
  high-impact and usually need functional tests" is true and answers nobody's
  question about how to write a datamap. Where a mechanism has a shape that is
  easy to get wrong — an order, a naming rule, a step that happens at install
  time — the hint states it.
  *From:* a session that built a site with this server as its only reference and
  found the catalog organised around "what must a patch satisfy to be merged"
  while the questions were "how do I do X with this API" (2026-07-29).
  *Held by:* `HintsTest::theFrontendRenderingPathIsAnsweredAsWellAsTheBackendOne`,
  and the shape of `datahandler-persistence`, `sitepackage-initial-content`,
  `public-assets`, `frontend-page-rendering` — not guarded beyond that.
- **R-KNW-2** A hint carries the words its subject is asked about in, not only
  its file extensions and its internal vocabulary. `appliesTo` is what the
  matcher scores, so a subject nobody can phrase their way to is a subject the
  server does not have.
  *From:* `language-files` matching `.xlf` and `trans-unit` but neither "xlf"
  nor "label" nor "language file" (2026-07-29).
  *Held by:* `HintsTest::aQueryAboutLanguageFilesReachesTheLanguageFilesHint`
- **R-KNW-1** Upgrade wizards and frontend DataProcessors have architecture
  hints, reachable from the path alone.
  *From:* an extension maintenance task that got generic TCA and Fluid hints and
  nothing for `Classes/Updates/` or `Classes/DataProcessing/` (2026-07-29).
  *Held by:* `HintsTest::aPathAloneReachesTheHintForTheSubsystemItIsIn`
