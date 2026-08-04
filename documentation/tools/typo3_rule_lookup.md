# `typo3_rule_lookup`

Search the local TYPO3 core contribution rules and script notes by topic.
Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# Topic to look up, in English, for example testing, review, deprecation, or
# code style.
query: string
# The TYPO3 version the answer has to hold on, for example "13.4" or "14". A
# section bound to another major is left out. Defaults to every major this
# repository declares typo3/cms-core for, or to the installation this server was
# started in; where there is neither, every section comes back with the range it
# holds for.
targetVersion: string  # optional
```

## Answers with

```yaml
query: string
# The exact XLF resource the result was restricted to. Null means the caller did
# not yet provide the usage context.
resource: string or null  # optional
matchCount: integer
matches:
  - documentId: string
    # Title of the knowledge document.
    title: string
    # typo3://guides resource holding the full document.
    uri: string
    # Heading of the matched section.
    heading: string
    # The section as written, formatting included.
    body: string
    # The TYPO3 majors this section holds for, in words. Empty means every
    # covered major, which is what a section that declares nothing says.
    versions: string  # optional
    # Share of the query terms the section covers, 0 to 1.
    coverage: number
    # Weighted match score; headings weigh more than body text.
    score: integer
    # Whether the body was cut; read the resource for the rest.
    truncated: boolean
# Documents in the knowledge base with the topics they cover. Returned when
# nothing matched.
documents:  # optional
  - id: string
    title: string
    topics: [string]
# Documents outside the searched ones that do match the query.
elsewhere: [string]  # optional
# Hints matching the same query. They are a second corpus, searched by
# typo3_hint_lookup, which takes one of these ids.
alsoInHints:  # optional
  - id: string
    title: string
# One of: core, uncertain, project, extension. Which kind of work this answer is
# for: core, a patch to the TYPO3 core itself; project, the site repository
# around an installation; extension, a package in it, whether a sitepackage or a
# third-party one; or uncertain, which means nothing in the call placed the work
# and what came back is the core's own.
scope: string
# Documents that matched and were left out because they answer for the core
# repository alone. Empty inside the core. Each is still readable in full as its
# typo3://guides resource, which is the way to get one deliberately rather than
# by accident.
withheldDocuments:  # optional
  - id: string
    title: string
```

## Answered

Derived by `bin/cli tools:index`, and `bin/cli tools:check` holds it — the
same as everything above this heading. This tool reads nothing an installation
contains: what reaches its answer is the bundled knowledge and which TYPO3
major the caller is on, so what comes back is written down rather than recorded
from one machine's checkout. Answered against the core checkout this repository
writes below .fixtures/, declaring TYPO3 14.3.0.

### rules: hit

Called with:

```json
{
    "query": "deprecation"
}
```

Text:

```
A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Deprecations
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

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

## Breaking Changes
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

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

## Changelog Files
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

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

## Review Readiness
Source: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules) — matches 100% of the query terms

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.

The hints also cover this — call typo3_hint_lookup with the id:
- documentation-changelog — Documentation and Changelog
```

Data:

```json
{
    "query": "deprecation",
    "matchCount": 4,
    "matches": [
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
            "versions": "",
            "coverage": 1,
            "score": 98,
            "truncated": false
        },
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Breaking Changes",
            "body": "- Breaking changes must use `[!!!]` before the keyword.\n- Breaking changes must be documented with a changelog RST file.\n- Breaking changes should usually target `main`.\n- A removed or narrowed PHP API gets an extension scanner matcher entry in the\n  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.\n  How the removed member is written where it is used decides the file:\n  - `MethodCallMatcher.php` — an instance method.\n  - `MethodCallStaticMatcher.php` — a static method.\n  - `PropertyPublicMatcher.php` — a removed public property.\n  - `PropertyProtectedMatcher.php` — a public property that became protected.\n  - `ClassNameMatcher.php` — a whole class or interface.\n- Visibility routes a property and never a method. The method matchers are a\n  weak match on the method name where it is used, and they do not resolve the\n  class, so they cannot see one. A method that is protected, or that has become\n  protected, is entered where a public one is.\n  `RendererRegistry->getRendererInstances` went from public to protected in\n  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above\n  has no row for a protected method because none is needed, and that absence\n  says nothing about whether an entry is owed.\n- An entry is keyed by the fully qualified name with `->` or `::` and carries\n  `restFiles`, naming the changelog file that removed it. The method matchers\n  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member\n  deprecated before it was removed lists both changelog files.\n- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,\n  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag\n  is the claim those entries have to back: `FullyScanned` says every item the\n  changelog entry names can be found. The scanner reads PHP, so what an entry\n  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially\n  scanned.\n- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the\n  changelog files the matchers name exist, and nothing checks the other\n  direction. A missing entry surfaces when somebody audits the matcher files\n  against the changelog.",
            "versions": "",
            "coverage": 1,
            "score": 24,
            "truncated": false
        },
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Changelog Files",
            "body": "- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`, in\n  the directory of the minor version the change is released in. A backport goes\n  into the `<lts>.x` directory of the oldest branch it reaches, in every branch\n  that carries it.\n- The file is named `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`.\n- The type is the first of four that describes the change: `Breaking` where it\n  moves or removes core functionality third-party code may use, `Deprecation`\n  where it marks core functionality for a planned removal, `Feature` where it\n  adds functionality, and `Important` for anything else that may require manual\n  action. `Important` is the last resort, and the only one of the four an LTS\n  release may carry.\n- A casual bug fix owes no entry, because its commit message carries the\n  information.\n- `Task` is a commit message keyword and not a changelog type. Those four are\n  the whole list, and `checkRst` fails a title opening with anything else.\n- `Documentation/Changelog/Howto.rst` in the core checkout is the authority on\n  all of this, and `Build/Scripts/validateRstFiles.php` is what reports the\n  piece a file is missing.\n- The skeleton the file has to have, down to the tags it ends on, is\n  `typo3_hint_lookup` with the id `documentation-changelog`.\n- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.\n- These rules are for writing an entry. An installation reads them instead: the\n  same files ship with the core package, and `typo3 upgrade:list` and\n  `typo3 upgrade:run` are what acts on the migrations behind them.",
            "versions": "",
            "coverage": 1,
            "score": 24,
            "truncated": false
        },
        {
            "documentId": "core/contribution/rules",
            "title": "TYPO3 Core Contribution Rules",
            "uri": "typo3://guides/core/contribution/rules",
            "heading": "Review Readiness",
            "body": "- The change should be reproducible from the issue or task description.\n- The patch should include a concise explanation of the problem and the chosen\n  fix.\n- Breaking changes, migrations, and deprecations need clear notes.\n- Security-sensitive behavior needs extra care and focused tests.",
            "versions": "",
            "coverage": 1,
            "score": 24,
            "truncated": false
        }
    ],
    "scope": "core",
    "withheldDocuments": [],
    "alsoInHints": [
        {
            "id": "documentation-changelog",
            "title": "Documentation and Changelog"
        }
    ]
}
```

### rules: miss

Called with:

```json
{
    "query": "quantum entanglement pineapple"
}
```

Text:

```
No knowledge section matched "quantum entanglement pineapple".

This knowledge base covers:
- TYPO3 Core Commit Message Rules: Summary Line, Work in Progress, Body, Relationships, Breaking Changes, Deprecations, Changelog Files
- TYPO3 Gerrit Workflow: One-Time Setup, Where This Checkout Pushes, Push a Patch for Review, Push a Private or Work in Progress Change, Pushing From a Git Worktree, Update an Existing Patch, The Forge Issue a Change Hangs Off, Release Branches and Backports
- TYPO3 Core Contribution Rules: Contribution Flow, Code Style, Testing, Review Readiness
- TYPO3 Contribution Sources: Core Contribution Guide, Local Policy
- TYPO3 Core Script Help: Invoking runTests.sh, Common Commands, Script Notes
- Setting Up PHPUnit in a TYPO3 Extension: Build/UnitTests.xml, Build/FunctionalTests.xml, What was changed in the copied files, Running the suites, Database credentials for the functional suite, What is left after a run
- Setting Up Playwright in a TYPO3 Project: Build/playwright.config.ts, Build/tests/browser/helper/login.setup.ts, Build/tests/browser/frontend/pages.spec.ts, Build/tests/browser/e2e/backend.spec.ts, Reaching into a module, The environment the suite reads, What the login setup asserts, and why it differs by version, When the extension itself is the Composer root, What is not committed

For backend UI components use typo3_component_lookup, and call typo3_server_scope for what this server covers at all. If the topic should be covered here, leave a feedback with typo3_feedback_record.
```

Data:

```json
{
    "query": "quantum entanglement pineapple",
    "matchCount": 0,
    "matches": [],
    "scope": "core",
    "withheldDocuments": [],
    "alsoInHints": [],
    "documents": [
        {
            "id": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "topics": [
                "Summary Line",
                "Work in Progress",
                "Body",
                "Relationships",
                "Breaking Changes",
                "Deprecations",
                "Changelog Files"
            ]
        },
        {
            "id": "core/contribution/gerrit-workflow",
            "title": "TYPO3 Gerrit Workflow",
            "topics": [
                "One-Time Setup",
                "Where This Checkout Pushes",
                "Push a Patch for Review",
                "Push a Private or Work in Progress Change",
                "Pushing From a Git Worktree",
                "Update an Existing Patch",
                "The Forge Issue a Change Hangs Off",
                "Release Branches and Backports"
            ]
        },
        {
            "id": "core/contribution/rules",
            "title": "TYPO3 Core Contribution Rules",
            "topics": [
                "Contribution Flow",
                "Code Style",
                "Testing",
                "Review Readiness"
            ]
        },
        {
            "id": "core/contribution/sources",
            "title": "TYPO3 Contribution Sources",
            "topics": [
                "Core Contribution Guide",
                "Local Policy"
            ]
        },
        {
            "id": "core/testing/scripts",
            "title": "TYPO3 Core Script Help",
            "topics": [
                "Invoking runTests.sh",
                "Common Commands",
                "Script Notes"
            ]
        },
        {
            "id": "extension/testing/phpunit",
            "title": "Setting Up PHPUnit in a TYPO3 Extension",
            "topics": [
                "Build/UnitTests.xml",
                "Build/FunctionalTests.xml",
                "What was changed in the copied files",
                "Running the suites",
                "Database credentials for the functional suite",
                "What is left after a run"
            ]
        },
        {
            "id": "project/testing/playwright",
            "title": "Setting Up Playwright in a TYPO3 Project",
            "topics": [
                "Build/playwright.config.ts",
                "Build/tests/browser/helper/login.setup.ts",
                "Build/tests/browser/frontend/pages.spec.ts",
                "Build/tests/browser/e2e/backend.spec.ts",
                "Reaching into a module",
                "The environment the suite reads",
                "What the login setup asserts, and why it differs by version",
                "When the extension itself is the Composer root",
                "What is not committed"
            ]
        }
    ]
}
```
