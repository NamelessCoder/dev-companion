# TYPO3 Core Commit Message Rules

Source:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

TYPO3 core commit messages are part of the contribution workflow and are checked
by tooling. Keep this document aligned with the official TYPO3 Core Contribution
Guide.

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

## Breaking Changes

- Breaking changes must use `[!!!]` before the keyword.
- Breaking changes must be documented with a changelog RST file.
- Breaking changes should usually target `main`.
- A removed or narrowed PHP API gets an extension scanner matcher entry in the
  same patch, below
  `typo3/sysext/install/Configuration/ExtensionScanner/Php/`. How the removed
  member is written where it is used decides the file:
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

- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`, in
  the directory of the minor version the change is released in. A backport goes
  into the `<lts>.x` directory of the oldest branch it reaches, in every branch
  that carries it.
- The file is named `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`.
- The type is the first of four that describes the change: `Breaking` where it
  moves or removes core functionality third-party code may use, `Deprecation`
  where it marks core functionality for a planned removal, `Feature` where it
  adds functionality, and `Important` for anything else that may require manual
  action. `Important` is the last resort, and the only one of the four an LTS
  release may carry.
- A casual bug fix owes no entry, because its commit message carries the
  information.
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
