# `typo3_rule_lookup`

Search the TYPO3 rules and procedures this server carries, by topic. The core
contribution process is most of it: the commit message conventions, which
branches take a patch today, the changelog entry each change type owes, the
Gerrit push and amend workflow with both refspecs, and the notes beside
runTests.sh. It answers outside a core checkout too — setting up an extension
manual, PHPUnit in an extension, Playwright in a project — and there the
core-only documents are withheld and named rather than dropped in silence. What
comes back is the sections that matched, each naming the document it was cut
from. Pass that documentId back instead of a query to read the whole document —
the section answers what was asked and the rest of the page regularly answers
the next thing, and it needs no resource list. Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# Topic to look up, in English, for example testing, review, deprecation, or
# code style. A call carries query or documentId, never both.
query: string  # optional
# One document to read whole instead of searching, named by the documentId a
# match carries — for example "core/contribution/commit-messages". Use it when
# a matched section came out of a document whose other sections may answer what
# the query did not: the whole page comes back, no search, no version filter. A
# call carries query or documentId, never both.
documentId: string  # optional
# The TYPO3 version the answer has to hold on, for example "13.4" or "14". A
# section bound to another major is left out. Defaults to every major this
# repository declares typo3/cms-core for, or to the installation this server was
# started in; where there is neither, every section comes back with the range it
# holds for. Ignored for documentId, which returns the document as written.
targetVersion: string  # optional
```

The call carries exactly one of these sets of arguments: `query` — or
`documentId`.

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

## Release Targets
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

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
- A bug fix goes to every maintained line that carries the defect, which is what
  makes it the change type the trailer is worth checking on.
- What a release branch carries since it was cut is `origin/main..origin/14.3`.
  A plain log on that branch, or a `--since` window over it, answers about the
  history shared with `main` and reports every change made before the branch
  existed as if the branch had taken it: the same count that is 0 one way is 188
  the other. The two differ by one operator and give opposite answers about
  whether features reach a release line.

## Review Readiness
Source: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules) — matches 100% of the query terms

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.

Each excerpt above is one section of a longer document. Where the task is the whole procedure rather than the fact you searched for, read the page: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages), TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules). A client may render no resource list, so that address is how one is reached.

Each of these is a section. The whole document is one call away — typo3_rule_lookup with documentId, which needs no resource list:
- core/contribution/commit-messages — TYPO3 Core Commit Message Rules
- core/contribution/rules — TYPO3 Core Contribution Rules

The hints also cover this — call typo3_hint_lookup with the id:
- documentation-changelog — Documentation and Changelog
```

Data:

```json
{
    "query": "deprecation",
    "matchCount": 5,
    "matches": [
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
            "versions": "",
            "coverage": 1,
            "score": 95,
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
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Release Targets",
            "body": "- `Releases:` names branches: `main` and the maintained release lines, comma\n  separated.\n- Which lines those are changes with every LTS release and every support window\n  that closes, so it is a lookup and not a rule to remember.\n  `typo3_commit_message_guide` names them where the trailer is left out, and\n  reports a branch that is out of regular support as an error.\n- A line out of regular support still has releases, and the ELTS partners make\n  them. A patch pushed to Gerrit is not one of them.\n- The branch list in a checkout does not answer this. `git branch -r` reaches\n  back to `TYPO3_3-6`, and counting `Releases:` trailers on recent commits\n  samples what other changes needed rather than what this one does.\n- Which of the maintained lines a change reaches is your reading of where the\n  defect is, and the trailer is the claim you verified it there — by reading the\n  changed file on each branch you name.\n- A feature, a deprecation and a breaking change go to `main`. A backport of one\n  happens and is the release managers' call: `origin/main..origin/13.4` carries\n  three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and\n  `origin/main..origin/14.3` carries none at all.\n- A bug fix goes to every maintained line that carries the defect, which is what\n  makes it the change type the trailer is worth checking on.\n- What a release branch carries since it was cut is `origin/main..origin/14.3`.\n  A plain log on that branch, or a `--since` window over it, answers about the\n  history shared with `main` and reports every change made before the branch\n  existed as if the branch had taken it: the same count that is 0 one way is 188\n  the other. The two differ by one operator and give opposite answers about\n  whether features reach a release line.",
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
- TYPO3 Core Commit Message Rules: Who Reads It, Summary Line, Work in Progress, Body, Relationships, Release Targets, Breaking Changes, Deprecations, Changelog Files
- TYPO3 Gerrit Workflow: One-Time Setup, Where This Checkout Pushes, Fetch a Change Into This Checkout, Push a Patch for Review, Push a Private or Work in Progress Change, Pushing From a Git Worktree, Update an Existing Patch, The Forge Issue a Change Hangs Off, Release Branches and Backports
- TYPO3 Core Contribution Rules: Contribution Flow, Code Style, Testing, Review Readiness
- TYPO3 Contribution Sources: Core Contribution Guide, Local Policy
- TYPO3 Core Script Help: Invoking runTests.sh, Common Commands, Script Notes
- Setting Up an Extension Manual: Documentation/guides.xml, Documentation/Index.rst, The two conventional files, Rendering it before it is published
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
                "Who Reads It",
                "Summary Line",
                "Work in Progress",
                "Body",
                "Relationships",
                "Release Targets",
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
                "Fetch a Change Into This Checkout",
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
            "id": "extension/documentation/manual",
            "title": "Setting Up an Extension Manual",
            "topics": [
                "Documentation/guides.xml",
                "Documentation/Index.rst",
                "The two conventional files",
                "Rendering it before it is published"
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
