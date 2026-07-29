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

- **R-AUD-1** **open** — Three audiences are served, not one: the core
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
- **R-AUD-4** **open** — More than one TYPO3 version is supported. An answer
  either holds across the versions covered, or names the version it holds for.
  This is already the rule for architecture hints (see `AGENTS.md`), and for the
  catalogs, which now carry the version they describe and contrast it with the
  installation being read. What is still open is the caller who has no
  installation for this server to read, and the answer that is not merely
  qualified but wrong below a version — `typo3_translation_domain_lookup` is
  handled, and nothing generalises it yet.
  *Held so far by:* `CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead`,
  `CatalogTest::anInstallationWithoutTranslationDomainsIsGivenTheFileReference`,
  `CatalogTest::anInstallationThatResolvesDomainsIsGivenTheDomain`,
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
  boot where the files hold it anyway. `typo3_label_lookup` falls back to the
  XLF files of the same packages and reports `answeredBy: "packages"`, naming
  what the weaker source leaves out. Where nothing can answer, the failure is
  diagnosed rather than passed through: a query against a missing table means
  the database has no schema, not that the installation is broken.
  *From:* an installed TYPO3 13.4.33 before the dump was imported, where the
  labels sat in the files and both console-backed lookups returned a raw SQL
  stack trace (2026-07-29).
  *Held by:* `LabelSearchTest::aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead`,
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
