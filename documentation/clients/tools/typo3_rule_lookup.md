# `typo3_rule_lookup`

Search the local TYPO3 core contribution rules and script notes by topic.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# Topic to look up, in English, for example testing, review, deprecation, or
# code style.
query: string
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
    # typo3://core resource holding the full document.
    uri: string
    # Heading of the matched section.
    heading: string
    # The section as written, formatting included.
    body: string
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
# typo3://core resource, which is the way to get one deliberately rather than by
# accident.
withheldDocuments:  # optional
  - id: string
    title: string
```

## Answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/, whose
console could not be reached: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
`bin/cli tools:check` holds it.

### rules: hit

Called with:

```json
{
    "query": "deprecation"
}
```

Text:

```
These sections are prose and are not filtered by version. Where a subsystem changed inside the covered range, the statement that changed carries the range elsewhere: call typo3_architecture_lookup with targetVersion for the convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Deprecations
Source: TYPO3 Core Commit Message Rules (typo3://core/typo3-commit-messages) — matches 100% of the query terms

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
Source: TYPO3 Core Commit Message Rules (typo3://core/typo3-commit-messages) — matches 100% of the query terms

- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.
- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`,
  `Important-`, and `Task-`.
- Include the Forge issue number in changelog filenames when possible.
- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.
- These rules are for writing an entry. An installation reads them instead: the
  same files ship with the core package, and `typo3 upgrade:list` and
  `typo3 upgrade:run` are what acts on the migrations behind them.

## Review Readiness
Source: TYPO3 Core Contribution Rules (typo3://core/typo3-core-rules) — matches 100% of the query terms

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.

The architecture hints also cover this — call typo3_architecture_lookup with the id:
- deprecated-apis — Deprecated APIs
- documentation-changelog — Documentation and Changelog
- form-framework — EXT:form Configuration and Runtime
```

Data:

```json
{
    "query": "deprecation",
    "matchCount": 3,
    "matches": [
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
            "coverage": 1,
            "score": 83,
            "truncated": false
        },
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Changelog Files",
            "body": "- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.\n- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`,\n  `Important-`, and `Task-`.\n- Include the Forge issue number in changelog filenames when possible.\n- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.\n- These rules are for writing an entry. An installation reads them instead: the\n  same files ship with the core package, and `typo3 upgrade:list` and\n  `typo3 upgrade:run` are what acts on the migrations behind them.",
            "coverage": 1,
            "score": 21,
            "truncated": false
        },
        {
            "documentId": "typo3-core-rules",
            "title": "TYPO3 Core Contribution Rules",
            "uri": "typo3://core/typo3-core-rules",
            "heading": "Review Readiness",
            "body": "- The change should be reproducible from the issue or task description.\n- The patch should include a concise explanation of the problem and the chosen\n  fix.\n- Breaking changes, migrations, and deprecations need clear notes.\n- Security-sensitive behavior needs extra care and focused tests.",
            "coverage": 1,
            "score": 21,
            "truncated": false
        }
    ],
    "scope": "core",
    "withheldDocuments": [],
    "alsoInHints": [
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs"
        },
        {
            "id": "documentation-changelog",
            "title": "Documentation and Changelog"
        },
        {
            "id": "form-framework",
            "title": "EXT:form Configuration and Runtime"
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
- TYPO3 Core Commit Message Rules: Summary Line, Body, Relationships, Breaking Changes, Deprecations, Changelog Files
- TYPO3 Contribution Sources: Core Contribution Guide, Local Policy
- TYPO3 Core Contribution Rules: Contribution Flow, Code Style, Testing, Review Readiness
- TYPO3 Core Script Help: Invoking runTests.sh, Common Commands, Script Notes
- TYPO3 Gerrit Workflow: One-Time Setup, Push a Patch for Review, Update an Existing Patch, Release Branches and Backports

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
            "id": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "topics": [
                "Summary Line",
                "Body",
                "Relationships",
                "Breaking Changes",
                "Deprecations",
                "Changelog Files"
            ]
        },
        {
            "id": "typo3-contribution-sources",
            "title": "TYPO3 Contribution Sources",
            "topics": [
                "Core Contribution Guide",
                "Local Policy"
            ]
        },
        {
            "id": "typo3-core-rules",
            "title": "TYPO3 Core Contribution Rules",
            "topics": [
                "Contribution Flow",
                "Code Style",
                "Testing",
                "Review Readiness"
            ]
        },
        {
            "id": "typo3-core-scripts",
            "title": "TYPO3 Core Script Help",
            "topics": [
                "Invoking runTests.sh",
                "Common Commands",
                "Script Notes"
            ]
        },
        {
            "id": "typo3-gerrit-workflow",
            "title": "TYPO3 Gerrit Workflow",
            "topics": [
                "One-Time Setup",
                "Push a Patch for Review",
                "Update an Existing Patch",
                "Release Branches and Backports"
            ]
        }
    ]
}
```
