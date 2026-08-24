---
description: >-
  Who a core commit message is written for, and the subject line, the body and the trailers it carries, Gerrit's own among them.
whenToUse: >-
  When writing or amending the message of a patch to the core, which is the only repository these rules describe.
hints: []
---

# TYPO3 Core Commit Message Rules

Source:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

TYPO3 core commit messages are part of the contribution workflow and are checked
by tooling. Keep this document aligned with the official TYPO3 Core Contribution
Guide.

## Who Reads It

- A commit message is read by a person who wants to know what the commit did —
  in `git log`, in a blame, in a review.
- Write it in plain English, and only as long as that answer needs.
- The diff carries the detail, so the message does not repeat it. Nothing here
  asks for a full account of the change.

## Summary Line

- Start with one of `[BUGFIX]`, `[FEATURE]`, `[TASK]`, or `[DOCS]`.
- Add `[!!!]` before the keyword for breaking changes.
- `[!!!]` is the only prefix a merge-ready subject carries.
- Do not use `[SECURITY]` unless this is handled by the TYPO3 Security Team.
- Keep the summary below 52 characters if possible and below 72 characters in
  any case.
- Use imperative present tense, for example `Fix`, `Add`, `Improve`, or
  `Remove`.
- Describe what the patch changes, not what used to be broken.
- Start the summary text after the keyword with a capital letter.
- Avoid `EXT:some_extension` in the subject when the changed files already make
  the extension context clear.

## Work in Progress

- `[WIP]` and `[POC]` go before the keyword, where `[!!!]` goes:
  `[WIP][BUGFIX] Parse User TSConfig for user settings`. They mark a state, not
  a kind of change — work in progress, and proof of concept.
- A change carrying one is not offered for merge. Both come off before it is
  merged, and no merged commit carries either.
- `[POC]` is written `[PoC]` as often as not, and the two are the same marker.
- Gerrit says the same thing to the review server rather than to a reader:
  pushing with `%wip` opens the change as work in progress.

## Body

- Separate the summary and body with a blank line.
- Keep the body brief and focused on what changed and why.
- Do not repeat full reproduction instructions from the Forge issue.
- Wrap body lines manually after 72 characters.

## Relationships

- `Resolves: #12345` is required.
- Multiple resolved issues need one `Resolves:` line per issue.
- `Related: #12345` is optional and cannot replace `Resolves:`.
- `Releases: main, 13.4` lists target versions.
- Do not create or change `Change-Id:` manually. The commit hook creates it.
  Keep it when amending an existing Gerrit patch set.

## Release Targets

- `Releases:` names branches: `main` and the maintained release lines, comma
  separated.
- Which lines those are changes with every LTS release and every support window
  that closes, so it is a lookup and not a rule to remember.
  `typo3_commit_message_guide` names them where the trailer is left out, and
  reports a branch that is out of regular support as an error.
- A line out of regular support still has releases, and the ELTS partners make
  them. A patch pushed to Gerrit is not one of them.
- The branch list in a checkout does not answer this. `git branch -r` reaches
  back to `TYPO3_3-6`, and counting `Releases:` trailers on recent commits
  samples what other changes needed rather than what this one does.
- Which of the maintained lines a change reaches is your reading of where the
  defect is, and the trailer is the claim you verified it there — by reading the
  changed file on each branch you name.
- A feature, a deprecation and a breaking change go to `main`. A backport of one
  happens and is the release managers' call: `origin/main..origin/13.4` carries
  three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and
  `origin/main..origin/14.3` carries none at all.
- A bug fix and a task go to `main` and to the one release line back from it.
  That the defect is present on an older maintained line does not put that line
  in the trailer: the older lines take priority bug fixes and grave or
  security-relevant defects, and naming one for an ordinary fix asks a merger to
  cherry-pick onto a line the change was never meant for.
- So the trailer is two readings rather than one. Where the defect is, on each
  line, is the first; whether its severity earns an older line is the second,
  and it is a judgement you state rather than something that follows from the
  first.
- What a release branch carries since it was cut is `origin/main..origin/14.3`.
  A plain log on that branch, or a `--since` window over it, answers about the
  history shared with `main` and reports every change made before the branch
  existed as if the branch had taken it: the same count that is 0 one way is 188
  the other. The two differ by one operator and give opposite answers about
  whether features reach a release line.

## Breaking Changes

- Breaking changes must use `[!!!]` before the keyword.
- Breaking changes must be documented with a changelog RST file.
- Breaking changes should usually target `main`.
- A removed or narrowed PHP API gets an extension scanner matcher entry in the
  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
  How the removed member is written where it is used decides the file:
  - `MethodCallMatcher.php` — an instance method.
  - `MethodCallStaticMatcher.php` — a static method.
  - `PropertyPublicMatcher.php` — a removed public property.
  - `PropertyProtectedMatcher.php` — a public property that became protected.
  - `ClassNameMatcher.php` — a whole class or interface.
- Visibility routes a property and never a method. The method matchers are a
  weak match on the method name where it is used, and they do not resolve the
  class, so they cannot see one. A method that is protected, or that has become
  protected, is entered where a public one is.
  `RendererRegistry->getRendererInstances` went from public to protected in
  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above
  has no row for a protected method because none is needed, and that absence
  says nothing about whether an entry is owed.
- An entry is keyed by the fully qualified name with `->` or `::` and carries
  `restFiles`, naming the changelog file that removed it. The method matchers
  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member
  deprecated before it was removed lists both changelog files.
- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag
  is the claim those entries have to back: `FullyScanned` says every item the
  changelog entry names can be found. The scanner reads PHP, so what an entry
  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially
  scanned.
- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
  changelog files the matchers name exist, and nothing checks the other
  direction. A missing entry surfaces when somebody audits the matcher files
  against the changelog.

## Changed Signatures

A signature change is the third breaking move beside removing and narrowing, and
adding a parameter is one — an optional parameter included. A public or
protected method on a class that is not final is an override point, and every
subclass declaring the old signature fatals as it loads.

- The obligation follows from the member being overridable rather than from an
  override anybody found. `Breaking-101133` files a changed parameter of
  `IconFactory->getIcon()` against "custom extensions extending the method", and
  `Breaking-110218` declares `LogRecord` final while calling the affected
  installations very unlikely.
- A member marked `@internal` takes an `Important` instead. `Important-107342`
  extended `FormPersistenceManagerInterface::listForms()` by two optional
  arguments and reached `13.4.x` on that ground. An entry is still owed; only
  its type changes, and that is what lets such a change reach a release line.
- Neither owes a matcher, and both are `NotScanned`. A matcher is keyed on where
  a member is called, an override is not a call, and an added optional parameter
  leaves every existing call site valid.
- So it decides the target branch before anything else. A maintained release
  line takes no breaking change, so a fix owed to one cannot carry the signature
  change at all, and the shape that reaches it is the additive one: a method of
  its own, or the state handed over on something the callee already receives.
  Declaring the class or the method final first is no cheaper, because that is
  itself a breaking change.
- Nothing in a core checkout reports any of this. No core class has to override
  the method, so the unit, functional, coding-guidelines and static-analysis
  runs are all green on the change.

## Deprecations

- Deprecations must not use `[!!!]`.
- Deprecations may only use `[TASK]` or `[FEATURE]`.
- Deprecations must be documented with a changelog RST file.
- Deprecations need migration guidance and may need extension scanner
  considerations.
- All of the above is the authoring side. Reading it — what a given version
  deprecated, and what that means for code that uses it — works the other way
  round: the changelog files below `Documentation/Changelog/` of the core
  package and the matchers below the install package's
  `Configuration/ExtensionScanner/Php/` are what an installation is checked
  against, by the Extension Scanner in the Install Tool. Both directories ship
  with a Composer installation.

## Changelog Files

This is the changelog obligation per change type: which change types owe a
changelog entry, which owe none, and what the entry a review asks for has to be.
A `BUGFIX` owes none, a `TASK` owes none, and the four types below are the whole
list.

- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`, in
  the directory of the minor version the change is released in. A backport goes
  into the `<lts>.x` directory of the oldest branch it reaches, in every branch
  that carries it.
- The file is named `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`.
- The type is the first of four that describes the change: `Breaking` where it
  moves or removes core functionality that may break or affect third-party code,
  `Deprecation` where it marks core functionality for a planned removal,
  `Feature` where it adds functionality, and `Important` for anything else that
  may require manual action. `Important` is the last resort, and the only one of
  the four an LTS release may carry.
- `Breaking` reaches past a moved PHP member. `affect` in that definition covers
  a change in what an installation renders or is configured by, and which of
  those the core files as breaking, and where the boundary against `Important`
  runs, is `typo3_hint_lookup` with the id `breaking-without-a-moved-member`.
- A casual bug fix owes no entry, because its commit message carries the
  information. Casual is the fix that changes nothing an installation renders,
  is configured by, or has documented. Demanding one of a `BUGFIX` that changes
  none of the three is a review defect of its own.
- `Task` is a commit message keyword and not a changelog type. Those four are
  the whole list, and `checkRst` fails a title opening with anything else.
- `Documentation/Changelog/Howto.rst` in the core checkout is the authority on
  all of this, and `Build/Scripts/validateRstFiles.php` is what reports the
  piece a file is missing.
- The skeleton the file has to have, down to the tags it ends on, is
  `typo3_hint_lookup` with the id `documentation-changelog`.
- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.
- These rules are for writing an entry. An installation reads them instead: the
  same files ship with the core package, and `typo3 upgrade:list` and
  `typo3 upgrade:run` are what acts on the migrations behind them.
