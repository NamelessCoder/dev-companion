# What the tools answered

Recorded on 2026-08-02 by `bin/cli tools:record`, over the calls
`Upkeep\ToolCalls` holds — the same ones `ToolContractTest` validates. It is
one run on one machine and it may be older than the code: nothing checks it,
and `documentation/clients/tools.md` is where the current shape of an answer
is.

Answered against core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below
.checkouts/, whose console could not be reached: <installation> has no TYPO3
console — none of bin/typo3, vendor/bin/typo3 exists. Half of these answers
belong to it rather than to this server — which labels and icons exist, what
the project consists of — and the other half would read the same anywhere.

Every answer is cut to fit: the text at 14 lines, a list at 2 entries with the
rest counted, a string at 320 characters. The data stays valid JSON, so what a
cut leaves behind is a string saying how much is missing rather than a broken
document. Absolute paths are written as `<repository>`, `<installation>` and
`<home>`, because where a machine keeps its checkouts is not what these answers
are showing.

## `typo3_server_scope` — scope

```json
{}
```

Text:

```
A curated knowledge base for working with TYPO3, for the three audiences that do: the core contributor, the extension author, and the site developer. It holds the conventions each subsystem is built on and how its mechanisms are used, the core's own contribution process — the rules, the Gerrit workflow, the scripts and test suites — and a searchable index of backend UI components whose contract is read from the active installation where possible. It also answers what is registered in the installation it was started in, which no bundled snapshot could get right. Every answer says which TYPO3 versions it holds for and which of the three kinds of work it belongs to.

Covered, and how deeply. Each topic says which kind of work its answers are for: core is the contribution process and the scripts that belong to that repository, any is a convention that holds wherever TYPO3 is written. Where the source names the installation, the answer is read from the one this server was started in rather than from any snapshot.
## Contribution rules and review readiness
Curated prose. The rules a patch is judged by, not a full style guide.
Tools: typo3_rule_lookup, typo3_task_guide
Source: typo3://core/typo3-core-rules (core)
## Gerrit workflow: setup, pushing, amending, backports
Curated prose, command level. Covers the local git side; it cannot talk to the Gerrit server.
Tools: typo3_rule_lookup
Source: typo3://core/typo3-gerrit-workflow (core)
## Commit messages
Rules plus a working draft and check, including 72-character body wrapping. The subject and body conventions are also served without the core workflow, for a commit in a repository that has no Forge issue and no release branches: workflow="project". The same guide is exposed as the user-invoked prompt commit_message.
Tools: typo3_commit_message_guide, typo3_rule_lookup
… 121 more lines
```

Data:

```json
{
    "purpose": "A curated knowledge base for working with TYPO3, for the three audiences that do: the core contributor, the extension author, and the site developer. It holds the conventions each subsystem is built on and how its mechanisms are used, the core's own contribution process — the rules, the Gerrit workflow, the scripts and… (671 characters)",
    "instructions": "Start every task with typo3_project_scope: the installation's TYPO3 version, the extensions that are the project's own, the sites it configures, and the commands the repository actually declares — a check you recommend that the repository does not declare is a wrong answer however sensible it sounds. typo3_task_guide t… (1620 characters)",
    "covers": [
        {
            "topic": "Contribution rules and review readiness",
            "depth": "Curated prose. The rules a patch is judged by, not a full style guide.",
            "tools": [
                "typo3_rule_lookup",
                "typo3_task_guide"
            ],
            "source": "typo3://core/typo3-core-rules",
            "scope": "core"
        },
        {
            "topic": "Gerrit workflow: setup, pushing, amending, backports",
            "depth": "Curated prose, command level. Covers the local git side; it cannot talk to the Gerrit server.",
            "tools": [
                "typo3_rule_lookup"
            ],
            "source": "typo3://core/typo3-gerrit-workflow",
            "scope": "core"
        },
        "… 14 more"
    ],
    "doesNotCover": [
        {
            "topic": "Your checkout: changed files, branch, working tree, local git state",
            "why": "This server answers from its own bundled knowledge and never reads, inspects, or runs anything against a TYPO3 core checkout. It cannot be pointed at one.",
            "instead": "Determine the changed paths yourself (git status, git diff --name-only), then pass them to typo3_architecture_lookup and typo3_task_guide to get the conventions and checks that apply to them."
        },
        {
            "topic": "The core source itself: API signatures, TCA of a table, existing implementations",
            "why": "The knowledge base describes conventions, not code. No core sources ship with it.",
            "instead": "Read the checkout. Use this server for how the subsystem is built and what a patch has to satisfy."
        },
        "… 7 more"
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        "… 4 more"
    ],
    "routing": [
        {
            "when": "Asked to review, audit or assess a project, a site package or an extension — before opening the first file, because what a finding is worth depends on the version and the commands this repository has",
            "call": "typo3_project_scope, then typo3_task_guide for the workflow and typo3_extension_scope for each extension in scope"
        },
        {
            "when": "Starting a core task and looking for the applicable conventions and checks",
            "call": "typo3_task_guide"
        },
        "… 20 more"
    ],
    "versions": [
        {
            "major": 12,
            "branch": "12.4",
            "status": "lts"
        },
        {
            "major": 13,
            "branch": "13.4",
            "status": "lts"
        },
        "… 2 more"
    ],
    "excludedTools": {
        "names": [],
        "variable": "TYPO3_MCP_EXCLUDE_TOOLS"
    },
    "installation": {
        "found": true,
        "root": "<installation>",
        "kind": "core-checkout",
        "via": "discovery",
        "startedFrom": "<installation>",
        "searched": [
            "<installation>"
        ],
        "packageCount": 36,
        "misconfiguration": null,
        "console": {
            "reachable": false,
            "via": null,
            "php": null,
            "command": null,
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
            "caveat": null
        },
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## `typo3_rule_lookup` — rules: hit

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
… 29 more lines
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
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  dep… (708 characters)",
            "coverage": 1,
            "score": 83,
            "truncated": false
        },
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Changelog Files",
            "body": "- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.\n- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`,\n  `Important-`, and `Task-`.\n- Include the Forge issue number in changelog filenames when possible.\n- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.\n-… (535 characters)",
            "coverage": 1,
            "score": 21,
            "truncated": false
        },
        "… 1 more"
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
        "… 1 more"
    ]
}
```

## `typo3_rule_lookup` — rules: miss

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
                "… 4 more"
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
        "… 3 more"
    ]
}
```

## `typo3_script_lookup` — scripts: hit

```json
{
    "task": "functional tests"
}
```

Text:

```
These sections are prose and are not filtered by version. Where a subsystem changed inside the covered range, the statement that changed carries the range elsewhere: call typo3_architecture_lookup with targetVersion for the convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Common Commands
Source: TYPO3 Core Script Help (typo3://core/typo3-core-scripts) — matches 100% of the query terms

### Install Dependencies

```bash
composer install
```

Use this after cloning TYPO3 core or changing PHP dependencies.

### Run PHP Unit Tests
… 132 more lines
```

Data:

```json
{
    "query": "functional tests",
    "matchCount": 2,
    "matches": [
        {
            "documentId": "typo3-core-scripts",
            "title": "TYPO3 Core Script Help",
            "uri": "typo3://core/typo3-core-scripts",
            "heading": "Common Commands",
            "body": "### Install Dependencies\n\n```bash\ncomposer install\n```\n\nUse this after cloning TYPO3 core or changing PHP dependencies.\n\n### Run PHP Unit Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s unit\n```\n\nRuns the TYPO3 core unit test suite. Add a path or `--filter` after `--` when\nworking on a narrow area.\n\n### Run Funct… (2375 characters)",
            "coverage": 1,
            "score": 10,
            "truncated": true
        },
        {
            "documentId": "typo3-core-scripts",
            "title": "TYPO3 Core Script Help",
            "uri": "typo3://core/typo3-core-scripts",
            "heading": "Invoking runTests.sh",
            "body": "`Build/Scripts/runTests.sh` runs every suite inside a container and is started\nfrom the core checkout root.\n\n- Prefix scripted or non-interactive runs with `CI=true`. It drops the\n  interactive container flags, skips the SIGINT trap, and picks the CI phpstan\n  configuration. Without a TTY the script removes the interac… (1954 characters)",
            "coverage": 1,
            "score": 10,
            "truncated": false
        }
    ],
    "scope": "core"
}
```

## `typo3_script_lookup` — scripts: miss

```json
{
    "task": "quantum entanglement pineapple"
}
```

Text:

```
No section of the TYPO3 core script notes matched "quantum entanglement pineapple". They cover: Invoking runTests.sh, Common Commands, Script Notes.
```

Data:

```json
{
    "query": "quantum entanglement pineapple",
    "matchCount": 0,
    "matches": [],
    "elsewhere": [],
    "scope": "core"
}
```

## `typo3_task_guide` — brief: with area

```json
{
    "task": "Deprecate a public method",
    "area": "typo3/sysext/core/Classes/Utility/GeneralUtility.php",
    "changeType": "cleanup"
}
```

Text:

```
Task: Deprecate a public method
Area: typo3/sysext/core/Classes/Utility/GeneralUtility.php
Change type: cleanup
Domains: php
Recognized as: Deprecation

Architecture hints:
### PHP

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.
… 110 more lines
```

Data:

```json
{
    "task": "Deprecate a public method",
    "area": "typo3/sysext/core/Classes/Utility/GeneralUtility.php",
    "paths": [
        "typo3/sysext/core/Classes/Utility/GeneralUtility.php"
    ],
    "scopes": [
        {
            "path": "typo3/sysext/core/Classes/Utility/GeneralUtility.php",
            "scope": "core"
        }
    ],
    "changeType": "cleanup",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [
        {
            "id": "deprecation",
            "title": "Deprecation",
            "confidence": "strong",
            "condition": ""
        }
    ],
    "architectureHints": [
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 1 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs",
            "category": "General",
            "scope": null,
            "hints": [
                {
                    "text": "Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole, and this server does not know your branch. Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 2 more"
            ],
            "checks": []
        },
        "… 1 more"
    ],
    "rules": [
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  dep… (708 characters)",
            "coverage": 1,
            "score": 72,
            "truncated": false
        },
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Changelog Files",
            "body": "- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.\n- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`,\n  `Important-`, and `Task-`.\n- Include the Forge issue number in changelog filenames when possible.\n- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.\n-… (535 characters)",
            "coverage": 1,
            "score": 28,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s checkRst",
        "CI=true ./Build/Scripts/runTests.sh -s unit",
        "… 1 more"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "unit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s unit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>",
            "description": "PHP unit tests.",
            "whenToUse": "Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "functional",
            "command": "CI=true ./Build/Scripts/runTests.sh -s functional",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>",
            "description": "PHP functional tests, sqlite by default.",
            "whenToUse": "Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.",
            "domains": [
                "php",
                "fluid",
                "… 1 more"
            ],
            "versions": ""
        },
        "… 2 more"
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "… 10 more"
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        "… 4 more"
    ],
    "nextTools": [
        {
            "tool": "typo3_commit_message_guide",
            "when": "with isDeprecation=true, to get the keyword and prefix rules checked"
        },
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        "… 3 more"
    ]
}
```

## `typo3_task_guide` — brief: task only

```json
{
    "task": "Add a badge to the list module"
}
```

Text:

```
Task: Add a badge to the list module
Area: unknown
Change type: unknown
Domains: php
Recognized as: Backend UI markup

Architecture hints:
### PHP

## Backend Module and Route Registration
Hints:
- A backend module is declared in the owning extension's Configuration/Backend/Modules.php, which returns an array keyed by module identifier. There is no registration call.
- The keys of an entry are parent (the identifier of the module it sits under), access ("user", "admin", ...), path (the backend route it answers on), iconIdentifier, labels, aliases (former identifiers kept working), routes, and moduleData for the module's persisted per-user state.
- routes maps a route name to a target of the form Controller::class . '::method'; the entry named _default is what the module opens with.
… 75 more lines
```

Data:

```json
{
    "task": "Add a badge to the list module",
    "area": null,
    "paths": [],
    "scopes": [],
    "changeType": "unknown",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [
        {
            "id": "backend-ui",
            "title": "Backend UI markup",
            "confidence": "strong",
            "condition": "only if the change adds or alters backend component markup or CSS classes"
        }
    ],
    "architectureHints": [
        {
            "id": "backend-modules",
            "title": "Backend Module and Route Registration",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "A backend module is declared in the owning extension's Configuration/Backend/Modules.php, which returns an array keyed by module identifier. There is no registration call.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The keys of an entry are parent (the identifier of the module it sits under), access (\"user\", \"admin\", ...), path (the backend route it answers on), iconIdentifier, labels, aliases (former identifiers kept working), routes, and moduleData for the module's persisted per-user state.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 6 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        }
    ],
    "rules": [
        {
            "documentId": "typo3-core-rules",
            "title": "TYPO3 Core Contribution Rules",
            "uri": "typo3://core/typo3-core-rules",
            "heading": "Testing",
            "body": "- Unit tests are expected for isolated behavior.\n- Functional tests are expected for persistence, configuration, routing, backend\n  behavior, or integration with TYPO3 services.\n- End-to-end tests, the `e2e` suite, are useful when the change affects editor or\n  administrator workflows and only breaks in the assembled b… (426 characters)",
            "coverage": 0.667,
            "score": 28,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s lintScss",
        "CI=true ./Build/Scripts/runTests.sh -s build",
        "… 1 more"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "unit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s unit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>",
            "description": "PHP unit tests.",
            "whenToUse": "Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "functional",
            "command": "CI=true ./Build/Scripts/runTests.sh -s functional",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>",
            "description": "PHP functional tests, sqlite by default.",
            "whenToUse": "Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.",
            "domains": [
                "php",
                "fluid",
                "… 1 more"
            ],
            "versions": ""
        },
        "… 2 more"
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "… 6 more"
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        "… 4 more"
    ],
    "nextTools": [
        {
            "tool": "typo3_component_lookup",
            "when": "before writing backend markup or CSS classes"
        },
        {
            "tool": "typo3_backend_module_lookup",
            "when": "to compare the declaration with modules registered by the active installation"
        },
        "… 5 more"
    ]
}
```

## `typo3_task_guide` — brief: paths of two kinds

```json
{
    "task": "Fix the query that reads the events",
    "paths": [
        "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
        "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
    ],
    "changeType": "bugfix"
}
```

Text:

```
Of the paths given, packages/acme_events/Classes/Domain/Repository/EventRepository.php is outside the TYPO3 core — a project or third-party extension. What follows is split accordingly: what only the core repository has is left out of the half that is about it. The checks below, the changelog and the submission route belong to the core repository, so they are steps for the paths that are in it and for none of the others.

Task: Fix the query that reads the events
Area: unknown
Change type: bugfix
Domains: php
Paths:
- packages/acme_events/Classes/Domain/Repository/EventRepository.php (extension)
- typo3/sysext/core/Classes/Database/Query/QueryBuilder.php

Architecture hints:
# For typo3/sysext/core/Classes/Database/Query/QueryBuilder.php

### PHP
… 97 more lines
```

Data:

```json
{
    "task": "Fix the query that reads the events",
    "area": null,
    "paths": [
        "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
        "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
    ],
    "scopes": [
        {
            "path": "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
            "scope": "extension"
        },
        {
            "path": "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php",
            "scope": "core"
        }
    ],
    "changeType": "bugfix",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [],
    "architectureHints": [
        {
            "id": "datahandler-persistence",
            "title": "DataHandler and Persistence",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "DataHandler and persistence changes are high-impact and usually need functional tests.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 5 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s functional",
                "CI=true ./Build/Scripts/runTests.sh -s phpstan"
            ]
        },
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 1 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        "… 1 more"
    ],
    "rules": [],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s functional",
        "CI=true ./Build/Scripts/runTests.sh -s phpstan",
        "… 1 more"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "checkExtensionScannerRst",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst",
            "targeted": null,
            "description": "Verifies that all .rst files referenced by the extension scanner exist.",
            "whenToUse": "Use when a deprecation or breaking change adds extension scanner matchers.",
            "domains": [
                "docs",
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "checkIntegrityPhp",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
            "targeted": null,
            "description": "Checks core PHP files against the registered integrity rules.",
            "whenToUse": "Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.",
            "domains": [
                "php"
            ],
            "versions": "TYPO3 v13 and newer"
        },
        "… 1 more"
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "… 6 more"
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        "… 4 more"
    ],
    "nextTools": [
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        {
            "tool": "typo3_architecture_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        "… 3 more"
    ]
}
```

## `typo3_test_run_guide` — runTests: all

```json
{}
```

Text:

```
## unit
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s unit`
Targeted run while iterating:
`CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>`

PHP unit tests.
Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.

## functional
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s functional`
Targeted run while iterating:
`CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`
… 193 more lines
```

Data:

```json
{
    "query": null,
    "paths": [],
    "scopes": [],
    "domains": [],
    "suites": [
        {
            "suite": "unit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s unit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>",
            "description": "PHP unit tests.",
            "whenToUse": "Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "functional",
            "command": "CI=true ./Build/Scripts/runTests.sh -s functional",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>",
            "description": "PHP functional tests, sqlite by default.",
            "whenToUse": "Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.",
            "domains": [
                "php",
                "fluid",
                "… 1 more"
            ],
            "versions": ""
        },
        "… 22 more"
    ],
    "invocation": {
        "notes": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "… 3 more"
        ],
        "options": [
            {
                "option": "-- <phpunit arguments>",
                "description": "Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method."
            },
            {
                "option": "-d sqlite|mariadb|mysql|postgres",
                "description": "Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour."
            },
            "… 7 more"
        ],
        "examples": [
            {
                "purpose": "One unit test file",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            {
                "purpose": "One unit test method",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            "… 5 more"
        ]
    }
}
```

## `typo3_test_run_guide` — runTests: hit

```json
{
    "query": "phpstan"
}
```

Text:

```
## phpstan
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s phpstan`

Static analysis with phpstan.
Use for type-sensitive PHP changes and API contract changes.

## Invoking runTests.sh
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.

… 26 more lines
```

Data:

```json
{
    "query": "phpstan",
    "paths": [],
    "scopes": [],
    "domains": [],
    "suites": [
        {
            "suite": "phpstan",
            "command": "CI=true ./Build/Scripts/runTests.sh -s phpstan",
            "targeted": null,
            "description": "Static analysis with phpstan.",
            "whenToUse": "Use for type-sensitive PHP changes and API contract changes.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "invocation": {
        "notes": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "… 3 more"
        ],
        "options": [
            {
                "option": "-- <phpunit arguments>",
                "description": "Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method."
            },
            {
                "option": "-d sqlite|mariadb|mysql|postgres",
                "description": "Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour."
            },
            "… 7 more"
        ],
        "examples": [
            {
                "purpose": "One unit test file",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            {
                "purpose": "One unit test method",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            "… 5 more"
        ]
    }
}
```

## `typo3_test_run_guide` — runTests: miss

```json
{
    "query": "quantumflux"
}
```

Text:

```
No runTests.sh suite matched "quantumflux". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".

## Invoking runTests.sh
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.

Options:
- `-- <phpunit arguments>` — Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method.
- `-d sqlite|mariadb|mysql|postgres` — Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour.
- `-a mysqli|pdo_mysql` — Database driver, only with `-s functional` on mysql or mariadb.
- `-i <version>` — Specific database version, for example `-d mariadb -i 11.4`.
… 21 more lines
```

Data:

```json
{
    "query": "quantumflux",
    "paths": [],
    "scopes": [],
    "domains": [],
    "suites": [],
    "invocation": {
        "notes": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "… 3 more"
        ],
        "options": [
            {
                "option": "-- <phpunit arguments>",
                "description": "Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method."
            },
            {
                "option": "-d sqlite|mariadb|mysql|postgres",
                "description": "Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour."
            },
            "… 7 more"
        ],
        "examples": [
            {
                "purpose": "One unit test file",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            {
                "purpose": "One unit test method",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            "… 5 more"
        ]
    }
}
```

## `typo3_test_run_guide` — runTests: narrowed by paths

```json
{
    "query": "what do I have to run",
    "paths": [
        "Build/Sources/Sass/component/_card.scss"
    ]
}
```

Text:

```
Narrowed to the css domain(s) the given paths touch. Suites outside them cannot fail on this change; call again without paths to see all of them.

## build-css
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`

Focused CSS build from Build/package.json.
Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.

## lintScss
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintScss`

SCSS linting with TYPO3's stylelint setup.
… 35 more lines
```

Data:

```json
{
    "query": "what do I have to run",
    "paths": [
        "Build/Sources/Sass/component/_card.scss"
    ],
    "scopes": [
        {
            "path": "Build/Sources/Sass/component/_card.scss",
            "scope": "core"
        }
    ],
    "domains": [
        "css"
    ],
    "suites": [
        {
            "suite": "build-css",
            "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css",
            "targeted": null,
            "description": "Focused CSS build from Build/package.json.",
            "whenToUse": "Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.",
            "domains": [
                "css"
            ],
            "versions": ""
        },
        {
            "suite": "lintScss",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintScss",
            "targeted": null,
            "description": "SCSS linting with TYPO3's stylelint setup.",
            "whenToUse": "Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.",
            "domains": [
                "css"
            ],
            "versions": ""
        }
    ],
    "invocation": {
        "notes": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "… 3 more"
        ],
        "options": [
            {
                "option": "-- <phpunit arguments>",
                "description": "Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method."
            },
            {
                "option": "-d sqlite|mariadb|mysql|postgres",
                "description": "Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour."
            },
            "… 7 more"
        ],
        "examples": [
            {
                "purpose": "One unit test file",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            {
                "purpose": "One unit test method",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php"
            },
            "… 5 more"
        ]
    }
}
```

## `typo3_architecture_lookup` — architecture: path

```json
{
    "paths": [
        "typo3/sysext/core/Classes/DataHandling/DataHandler.php"
    ]
}
```

Text:

```
Paths:
- typo3/sysext/core/Classes/DataHandling/DataHandler.php
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Architecture hints:
### PHP

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.
Relevant checks:
… 15 more lines
```

Data:

```json
{
    "task": null,
    "paths": [
        "typo3/sysext/core/Classes/DataHandling/DataHandler.php"
    ],
    "scopes": [
        {
            "path": "typo3/sysext/core/Classes/DataHandling/DataHandler.php",
            "scope": "core"
        }
    ],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "withheldCategories": [],
    "hints": [
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 1 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        {
            "id": "datahandler-persistence",
            "title": "DataHandler and Persistence",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "DataHandler and persistence changes are high-impact and usually need functional tests.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 5 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s functional",
                "CI=true ./Build/Scripts/runTests.sh -s phpstan"
            ]
        }
    ],
    "availableHints": []
}
```

## `typo3_architecture_lookup` — architecture: topic

```json
{
    "task": "sass build"
}
```

Text:

```
Task: sass build
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: css (hints outside these domains are not shown)

Architecture hints:
### Backend CSS

## CSS Source and Build Boundaries
Hints:
- Treat Build/Sources/Sass/ as the source of truth when a Sass source exists.
- This is the core's asset pipeline. A project extension owns a separate build; see extension-asset-build.
- Do not hand-edit generated public CSS as the only change.
- Not every asset comes out of the Sass build. The CKEditor CSS is built through Build/rollup/ckeditor.js, so a change there is not picked up by a CSS build and looks like nothing happened.
- Verify generated assets are in sync when public assets are committed.
… 69 more lines
```

Data:

```json
{
    "task": "sass build",
    "paths": [],
    "scopes": [],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "css"
    ],
    "withheldCategories": [],
    "hints": [
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "General",
            "scope": null,
            "hints": [
                {
                    "text": "An extension owns its asset source, build tool and generated output; installing it into TYPO3 does not attach its Sass or TypeScript to the core's Build/Sources pipelines. Put only browser-consumable output below Resources/Public/ and keep the source where the extension's own package scripts name it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Decide whether generated assets are committed. If they are, source and output change together; if they are not, the project deployment has to run the build. The extension's package.json and CI are the executable record of that decision.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 2 more"
            ],
            "checks": []
        },
        {
            "id": "css-source-build-boundaries",
            "title": "CSS Source and Build Boundaries",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Treat Build/Sources/Sass/ as the source of truth when a Sass source exists.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "This is the core's asset pipeline. A project extension owns a separate build; see extension-asset-build.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                "… 4 more"
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss",
                "… 1 more"
            ]
        },
        "… 4 more"
    ],
    "availableHints": []
}
```

## `typo3_architecture_lookup` — architecture: miss

```json
{
    "task": "quantumflux"
}
```

Text:

```
Task: quantumflux
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Architecture hints:
No architecture hint matched. Name a path or a more specific topic, or ask for one of the ids below.

Hints that exist in these domains, requestable by id:
- language-files — Language Files (General)
- site-label-language — Core Labels on a Non-English Site (General)
- configuration-reach — Configuration Belongs to Its Reach (General)
- icon-usage — Rendering and Registering Icons (General)
- sitepackage-layout — How a Sitepackage Is Laid Out (General)
- project-repository-layout — How a TYPO3 Project Repository Is Laid Out (General)
… 33 more lines
```

Data:

```json
{
    "task": "quantumflux",
    "paths": [],
    "scopes": [],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "withheldCategories": [],
    "hints": [],
    "availableHints": [
        {
            "id": "language-files",
            "title": "Language Files",
            "category": "General"
        },
        {
            "id": "site-label-language",
            "title": "Core Labels on a Non-English Site",
            "category": "General"
        },
        "… 37 more"
    ]
}
```

## `typo3_documentation_lookup` — documentation: unsupported version

```json
{
    "queries": [
        "page title event"
    ],
    "targetVersion": "999"
}
```

Text:

```
Official TYPO3 documentation for 999.
Source: https://docs.typo3.org
Could not answer: TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main.
```

Data:

```json
{
    "mode": "search",
    "status": "unavailable",
    "targetVersion": "999",
    "source": "https://docs.typo3.org",
    "queries": [
        "page title event"
    ],
    "results": [],
    "unavailable": {
        "cause": "version-not-covered",
        "reason": "TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main."
    }
}
```

## `typo3_component_lookup` — components: list

```json
{}
```

Text:

```
TYPO3 backend component catalog:
- alert — Flash Messages / Alerts
- avatar — Avatar
- badge — Badges
- breadcrumb — Breadcrumbs
- button — Buttons
- button-group — Button Groups
- callout — Callout
- card — Cards
- dropdown — Dropdown
- dropzone — Dropzone
- form-check — Checkboxes, Radios, Switches
- infobox — Infobox (ViewHelper)
- input — Form Inputs
… 14 more lines
```

Data:

```json
{
    "query": null,
    "targetVersion": 14,
    "matchCount": 25,
    "components": [
        {
            "name": "alert",
            "title": "Flash Messages / Alerts",
            "summary": "Dismissible status message, used for backend flash messages. Variants map to TYPO3 state tokens.",
            "rootClass": "alert",
            "sassPath": "Build/Sources/Sass/component/_alert.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_alert.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/FlashMessages.fluid.html",
            "classes": [
                "alert",
                "alert-actions",
                "… 15 more"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_alert.scss",
                "… 1 more"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "avatar",
            "title": "Avatar",
            "summary": "User/record avatar image with an optional icon overlay. Rendered via the backend avatar ViewHelper; sizes are modifier classes.",
            "rootClass": "avatar",
            "sassPath": "Build/Sources/Sass/component/_avatar.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_avatar.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Avatar.fluid.html",
            "classes": [
                "avatar",
                "avatar-icon",
                "… 5 more"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_avatar.scss",
                "… 1 more"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": null,
            "until": null,
            "verifiedOn": ""
        },
        "… 23 more"
    ],
    "withheld": [],
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

## `typo3_component_lookup` — components: hit

```json
{
    "query": "badge"
}
```

Text:

```
## Badges (`badge`)
Matched in: name
Small inline status, label, or count indicator. Variants map to TYPO3 state tokens.

Markup:
```html
<span class="badge badge-default">default badge</span>
```
Variants: badge-primary, badge-secondary, badge-info, badge-success, badge-warning, badge-danger, badge-notice, badge-default
Modifiers: badge-pill, badge-space-start, badge-space-end, badge-stable, badge-experimental, badge-beta, badge-alpha, badge-deprecated
Sub-components: badge-list
Custom properties: --typo3-badge-bg, --typo3-badge-border-color, --typo3-badge-border-radius, --typo3-badge-color, --typo3-badge-font-size, --typo3-badge-link-focus-bg, --typo3-badge-link-focus-border-color, --typo3-badge-link-focus-color, --typo3-badge-link-hover-bg, --typo3-badge-link-hover-border-color, --typo3-badge-link-hover-color, --typo3-badge-padding-x, --typo3-badge-padding-y
Curated Sass source path: Build/Sources/Sass/component/_badges.scss
Styleguide demo: typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html
… 46 more lines
```

Data:

```json
{
    "query": "badge",
    "targetVersion": 14,
    "matchCount": 1,
    "components": [
        {
            "name": "badge",
            "title": "Badges",
            "summary": "Small inline status, label, or count indicator. Variants map to TYPO3 state tokens.",
            "rootClass": "badge",
            "variants": [
                "badge-primary",
                "badge-secondary",
                "… 6 more"
            ],
            "modifiers": [
                "badge-pill",
                "badge-space-start",
                "… 6 more"
            ],
            "subComponents": [
                "badge-list"
            ],
            "customProperties": [
                "--typo3-badge-bg",
                "--typo3-badge-border-color",
                "… 11 more"
            ],
            "markup": "<span class=\"badge badge-default\">default badge</span>",
            "examples": [
                "<span class=\"badge badge-pill badge-default\">pill shaped badge</span>\n<span class=\"badge badge-pill badge-default\">1</span>",
                "<div class=\"example-container\">\n    <f:for each=\"{variants}\" as=\"variant\">\n        <div class=\"example-item\">\n            <span class=\"badge badge-{variant}\">{variant}</span>\n        </div>\n    </f:for>\n</div>",
                "… 1 more"
            ],
            "sassPath": "Build/Sources/Sass/component/_badges.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_badges.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html",
            "matchedIn": [
                "name"
            ],
            "classes": [
                "badge",
                "badge-alpha",
                "… 16 more"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_badges.scss",
                "… 1 more"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        }
    ],
    "withheld": [],
    "checklist": {
        "title": "Component Definition of Done",
        "intro": "Applies to every backend component. Verify each item before a component change is complete.",
        "items": [
            "All relevant variants, sizes, and modifiers are implemented and demoed.",
            "All applicable states are covered: hover, focus, active, disabled, loading, empty, error, selected, expanded, collapsed.",
            "… 11 more"
        ]
    },
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

## `typo3_component_lookup` — components: miss

```json
{
    "query": "quantumflux"
}
```

Text:

```
No TYPO3 component matched "quantumflux". Try a component name (badge, card), a class (input-group), or a topic (search box). The installed packages were checked, but the searchable component index remains curated; inspect the installed backend CSS for an uncatalogued class.
Component contract: installed TYPO3 14.3.6-dev packages. Names, summaries, keywords, and fallback markup come from the curated catalog; classes and custom properties come from EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces the fallback markup where available.
```

Data:

```json
{
    "query": "quantumflux",
    "targetVersion": 14,
    "matchCount": 0,
    "components": [],
    "withheld": [],
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

## `typo3_system_extension_lookup` — system extensions: hit

```json
{
    "query": "impexp"
}
```

Text:

```
The core ships these on TYPO3 v14.

- impexp (typo3/cms-impexp)
  Import/Export - Tool for importing and exporting records using XML or the custom T3D format.
```

Data:

```json
{
    "query": "impexp",
    "targetVersion": 14,
    "matchCount": 1,
    "extensions": [
        {
            "key": "impexp",
            "package": "typo3/cms-impexp",
            "description": "Import/Export - Tool for importing and exporting records using XML or the custom T3D format.",
            "since": null,
            "until": null,
            "shippedOn": ""
        }
    ],
    "coveredVersions": [
        12,
        13,
        "… 2 more"
    ]
}
```

## `typo3_system_extension_lookup` — system extensions: miss

```json
{
    "query": "typo3/cms-content-blocks"
}
```

Text:

```
"typo3/cms-content-blocks" is not a system extension on TYPO3 v14. That is an answer about the core, not about the package: it may well exist on Packagist or in the TER, where it is a third-party extension with its own release cycle. Call this without a query for everything the core does ship.
```

Data:

```json
{
    "query": "typo3/cms-content-blocks",
    "targetVersion": 14,
    "matchCount": 0,
    "extensions": [],
    "coveredVersions": [
        12,
        13,
        "… 2 more"
    ]
}
```

## `typo3_system_extension_lookup` — system extensions: everything

```json
{}
```

Text:

```
The core ships these on TYPO3 v14.

- adminpanel (typo3/cms-adminpanel)
  Admin Panel - The Admin Panel displays information about your site in the frontend and contains a range of metrics including debug and caching information.
- backend (typo3/cms-backend)
  Backend
- belog (typo3/cms-belog)
  Log - View logs from the sys_log table in the TYPO3 backend modules System>Log
- beuser (typo3/cms-beuser)
  Backend User - TYPO3 backend module Administration > Users for managing backend users and groups.
- core (typo3/cms-core)
  Core
- dashboard (typo3/cms-dashboard)
  Dashboard - TYPO3 backend module used to configure and create backend widgets.
… 61 more lines
```

Data:

```json
{
    "query": "",
    "targetVersion": 14,
    "matchCount": 36,
    "extensions": [
        {
            "key": "adminpanel",
            "package": "typo3/cms-adminpanel",
            "description": "Admin Panel - The Admin Panel displays information about your site in the frontend and contains a range of metrics including debug and caching information.",
            "since": null,
            "until": null,
            "shippedOn": ""
        },
        {
            "key": "backend",
            "package": "typo3/cms-backend",
            "description": "Backend",
            "since": null,
            "until": null,
            "shippedOn": ""
        },
        "… 34 more"
    ],
    "coveredVersions": [
        12,
        13,
        "… 2 more"
    ]
}
```

## `typo3_reference_list` — references

```json
{}
```

Text:

```
Worked examples in the TYPO3 core, as TYPO3 v14 has them.
Paths are relative to a core checkout. Where none is at hand, they are also the paths in github.com/TYPO3/typo3 on the matching branch.

- typo3/sysext/theme_camino — TYPO3 v14 and newer
  A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.
  Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.
  In an installation: vendor/typo3/theme-camino/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="sitepackage-layout"
- typo3/sysext/styleguide — TYPO3 v13 and newer
  Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.
  In an installation: vendor/typo3/cms-styleguide/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="css-styleguide-demos"
- typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example
  An Extbase extension the core keeps green: domain models with their TCA, repositories, controllers and validators — and, in the functional tests around it, how a repository is exercised at all, request and configuration manager included.
… 17 more lines
```

Data:

```json
{
    "targetVersion": 14,
    "matchCount": 7,
    "references": [
        {
            "id": "sitepackage",
            "path": "typo3/sysext/theme_camino",
            "package": "typo3/theme-camino",
            "reference": "A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.",
            "caveat": "Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.",
            "hint": "sitepackage-layout",
            "since": 14,
            "until": null,
            "existsOn": "TYPO3 v14 and newer"
        },
        {
            "id": "backend-components",
            "path": "typo3/sysext/styleguide",
            "package": "typo3/cms-styleguide",
            "reference": "Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.",
            "caveat": null,
            "hint": "css-styleguide-demos",
            "since": 13,
            "until": null,
            "existsOn": "TYPO3 v13 and newer"
        },
        "… 5 more"
    ],
    "coveredVersions": [
        12,
        13,
        "… 2 more"
    ]
}
```

## `typo3_translation_domain_lookup` — domain: EXT reference

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
}
```

Text:

```
EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf resolves to the translation domain:

  backend.alt_doc

Reference a label in it as "backend.alt_doc:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
Composed for the installation here, TYPO3 14.3.6-dev. State targetVersion where the label is being written for another branch.
Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.
```

Data:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": 14,
    "domain": "backend.alt_doc",
    "domainOnNewerVersions": null
}
```

## `typo3_translation_domain_lookup` — domain: checkout path

```json
{
    "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf"
}
```

Text:

```
typo3/sysext/core/Resources/Private/Language/locallang.xlf resolves to the translation domain:

  core.messages

Reference a label in it as "core.messages:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
Composed for the installation here, TYPO3 14.3.6-dev. State targetVersion where the label is being written for another branch.
Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.
```

Data:

```json
{
    "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf",
    "targetVersion": 14,
    "domain": "core.messages",
    "domainOnNewerVersions": null
}
```

## `typo3_translation_domain_lookup` — domain: on an older target

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": "13.4"
}
```

Text:

```
TYPO3 13, which you asked about, has no translation domains: the API that resolves them arrived after it. Reference the file itself instead:

  LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:<trans-unit id>

For the record, the domain this path would resolve to on a version that has them is "backend.alt_doc". Writing it into a label there renders nothing, and fails at runtime rather than at build time.
```

Data:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": 13,
    "domain": null,
    "domainOnNewerVersions": "backend.alt_doc"
}
```

## `typo3_translation_domain_lookup` — domain: miss

```json
{
    "path": "somewhere/else.xlf"
}
```

Text:

```
"somewhere/else.xlf" names no extension, so no translation domain follows from it.
Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").
```

Data:

```json
{
    "path": "somewhere/else.xlf",
    "targetVersion": 14,
    "domain": null
}
```

## `typo3_label_lookup` — labels: hit

```json
{
    "query": "save"
}
```

Text:

```
106 label(s) in <installation> match "save" — showing the first 25:
- backend.alt_doc:buttons.confirm.duplicate_record_changed.yes
  "Yes, save and duplicate this record"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.yes
  "Discard changes"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.no
  "Keep editing"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.save_and_close
  "Save and close"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.duplicate_record_changed.title
… 68 more lines
```

Data:

```json
{
    "query": "save",
    "resource": null,
    "matchCount": 106,
    "labels": [
        {
            "ref": "backend.alt_doc:buttons.confirm.duplicate_record_changed.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.duplicate_record_changed.yes",
            "source": "Yes, save and duplicate this record",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.close_without_save.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.close_without_save.yes",
            "source": "Discard changes",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        "… 23 more"
    ],
    "terms": [
        {
            "term": "save",
            "matchCount": 106
        }
    ],
    "answeredBy": "packages"
}
```

## `typo3_label_lookup` — labels: miss

```json
{
    "query": "quantumflux"
}
```

Text:

```
No label in <installation> matches "quantumflux". This is an answer about your installation rather than about TYPO3 in general.

A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

Read from the XLF files of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists). What that leaves out is the assembled runtime state — a label an installation replaces through LANG/resourceOverrides is shown here as its package ships it.
```

Data:

```json
{
    "query": "quantumflux",
    "resource": null,
    "matchCount": 0,
    "labels": [],
    "terms": [
        {
            "term": "quantumflux",
            "matchCount": 0
        }
    ],
    "answeredBy": "packages"
}
```

## `typo3_icon_lookup` — icons: hit

```json
{
    "query": "actions-open"
}
```

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

"actions-open" is registered in <installation>; 22 related identifier(s) follow as suggestions:
- actions-open
  matched: name part "open", exact identifier
- actions-document-history-open
  alias of actions-history
  matched: name part "open"
- actions-document-open
  alias of actions-document-edit
  matched: name part "open"
- actions-document-open-read-only
  alias of actions-document-readonly
  matched: name part "open"
… 46 more lines
```

Data:

```json
{
    "query": "actions-open",
    "matchCount": 1,
    "suggestionCount": 22,
    "exactMatch": true,
    "icons": [
        {
            "identifier": "actions-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 1004,
            "why": [
                "name part \"open\"",
                "exact identifier"
            ]
        },
        {
            "identifier": "actions-document-history-open",
            "category": "actions",
            "aliasOf": "actions-history",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        "… 21 more"
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has… (511 characters)",
    "answeredBy": "packages"
}
```

## `typo3_icon_lookup` — icons: everything

```json
{}
```

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

Icon categories in this installation: actions, apps, avatar, content, default, empty, files, flags, form, information, install, mimetypes, miscellaneous, module, modulegroup, overlay, provider, share, spinner, status, sysnote, tcarecords, theme.

Concept words that map to a shape: warning, caution, error, danger, info, notice, help, success, confirm, add, new, create, edit, delete, remove, save, search, filter, settings, configuration, user, permission, lock, hidden, visibility, preview, view, upload, download, refresh, reload, sort, close, cancel, copy, duplicate, move, link, translation, localization, language, folder, page, record, history, undo, import, export, message, notification, mail, calendar, time, list, menu, workspace, cache, bookmark, extension.
```

Data:

```json
{
    "query": "",
    "matchCount": 0,
    "suggestionCount": 0,
    "exactMatch": false,
    "icons": [],
    "categories": [
        "actions",
        "apps",
        "… 21 more"
    ],
    "concepts": [
        "warning",
        "caution",
        "… 57 more"
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has… (511 characters)",
    "answeredBy": "packages"
}
```

## `typo3_backend_module_lookup` — modules

```json
{}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "query": "",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## `typo3_fluid_namespace_list` — namespaces

```json
{}
```

Text:

```
3 globally registered Fluid namespace(s):
- core: TYPO3\CMS\Core\ViewHelpers
- f: TYPO3\CMS\Adminpanel\ViewHelpers\Fluid, TYPO3Fluid\Fluid\ViewHelpers, TYPO3\CMS\Fluid\ViewHelpers
- formvh: TYPO3\CMS\Form\ViewHelpers

These prefixes work in any template without being declared. Every other namespace is declared in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.

Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists). That is what the packages declare, not what the container assembled from them.
```

Data:

```json
{
    "matchCount": 3,
    "namespaces": [
        {
            "prefix": "core",
            "phpNamespaces": [
                "TYPO3\\CMS\\Core\\ViewHelpers"
            ]
        },
        {
            "prefix": "f",
            "phpNamespaces": [
                "TYPO3\\CMS\\Adminpanel\\ViewHelpers\\Fluid",
                "TYPO3Fluid\\Fluid\\ViewHelpers",
                "… 1 more"
            ]
        },
        "… 1 more"
    ],
    "answeredBy": "packages"
}
```

## `typo3_configuration_lookup` — configuration

```json
{
    "path": "SYS/fluid"
}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "path": "SYS/fluid",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## `typo3_schema_lookup` — schema: one table

```json
{
    "table": "tt_content"
}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": "tt_content",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## `typo3_schema_lookup` — schema: every table

```json
{}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": null,
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## `typo3_changelog_lookup` — changelog: hit

```json
{
    "query": "ext_tables.php"
}
```

Text:

```
1 changelog entry carrying "ext_tables.php":
- 14.3 Deprecation: ext_tables.php in extensions (#109438)
  EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst — PHP-API, NotScanned, ext:core

Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
```

Data:

```json
{
    "query": "ext_tables.php",
    "matchCount": 1,
    "entries": [
        {
            "type": "Deprecation",
            "version": "14.3",
            "issue": "109438",
            "title": "ext_tables.php in extensions",
            "tags": [
                "PHP-API",
                "NotScanned",
                "… 1 more"
            ],
            "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst"
        }
    ],
    "versions": [
        "14.3",
        "14.3.x",
        "… 52 more"
    ],
    "answeredBy": "packages"
}
```

## `typo3_changelog_lookup` — changelog: miss

```json
{
    "query": "quantumflux"
}
```

Text:

```
No changelog entry in this installation carries all of "quantumflux".
The changelog here covers 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3 and older. A version this installation does not ship is not in it — read that one in the core repository or at https://docs.typo3.org.
```

Data:

```json
{
    "query": "quantumflux",
    "matchCount": 0,
    "entries": [],
    "versions": [
        "14.3",
        "14.3.x",
        "… 52 more"
    ],
    "answeredBy": "packages"
}
```

## `typo3_project_scope` — project

```json
{}
```

Text:

```
<installation> — core-checkout, TYPO3 14.3.6-dev, PHP ^8.2

Extensions: none beyond TYPO3's own.

Sites: none configured below config/sites/.

Commands this repository declares — these exist here, the core's testing suites do not. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
- composer gerrit:setup (composer.json) — unknown: @gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable
- composer gerrit:setup:commitMessageHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enableCommitMessageHook
- composer gerrit:setup:preCommitHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enablePreCommitHook
- composer gerrit:setup:preCommitHook:disable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::disablePreCommitHook
```

Data:

```json
{
    "root": "<installation>",
    "kind": "core-checkout",
    "typo3Version": "14.3.6-dev",
    "phpConstraint": "^8.2",
    "coreConstraint": null,
    "extensions": [],
    "sites": [],
    "commands": [
        {
            "command": "composer gerrit:setup",
            "source": "composer.json",
            "declares": "@gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable",
            "runs": "unknown"
        },
        {
            "command": "composer gerrit:setup:commitMessageHook:enable",
            "source": "composer.json",
            "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::enableCommitMessageHook",
            "runs": "unknown"
        },
        "… 2 more"
    ],
    "patches": [],
    "answeredBy": "packages"
}
```

## `typo3_extension_scope` — extension

```json
{
    "extension": "backend"
}
```

Text:

```
backend (system) — <installation>/typo3/sysext/backend
TYPO3 CMS Backend

TCA tables it extends: be_users

Backend modules: web_layout, records, content_status, site_configuration, link_management, about, pagetsconfig, pagetsconfig_pages, pagetsconfig_active, pagetsconfig_includes, content_security_policy, user_setup

Backend routes: login, main, state-tracker, logout, password_forget, password_forget_initiate_reset, password_reset_validate, password_reset_finish, sudo_mode_module, sudo_mode_apply, sudo_mode_error, login_frameset, login_request_token, auth_mfa, setup_mfa, mfa, wizard_add, wizard_list, wizard_edit, wizard_element_browser, wizard_link, online_media, record_download, record_history, db_new, db_new_pages, pages_sort, pages_new, new_content_element_wizard, move_page, move_element, show_item, dummy, tce_db, tce_file, record_edit, record_edit_contextual, image_processing, clipboard_process, resource_request_thumbnail, language_domain, resource_rename, resource_gather, resource_replace, link_resource, file_process, file_exists, file_reference_details, file_reference_create, file_reference_synchronizelocalize, file_reference_expandcollapse, record_inline_details, record_inline_create, record_inline_synchronizelocalize, record_inline_expandcollapse, site_configuration_inline_create, record_slug_suggest, site_configuration_inline_details, record_flex_container_add, record_suggest, record_tree_data, page_tree_data, page_tree_rootline, page_tree_filter, page_tree_configuration, page_tree_browser_configuration, page_tree_set_temporary_mount_point, filestorage_tree_data, filestorage_tree_rootline, filestorage_tree_filter, bookmark_list, bookmark_create, bookmark_update, bookmark_delete, bookmark_reorder, bookmark_delete_multiple, bookmark_move, bookmark_group_create, bookmark_group_update, bookmark_group_delete, bookmark_group_reorder, clearcache_group_pages, clearcache_group_all, clearcache_page, systeminformation_render, modulemenu, topbar, login, logout, login_preflight, login_refresh, login_timedout, switch_user, switch_user_exit, mfa, contextmenu, contextmenu_clipboard, record_process, usersettings_process, wizard_image_manipulation, livesearch, livesearch_form, online_media_create, icons, link_browser_encodetypolink, wizard_localization_get_record, wizard_localization_get_targets, wizard_localization_get_sources, wizard_localization_get_modes, wizard_localization_get_handlers, wizard_localization_get_content, wizard_localization_localize, show_columns, show_columns_selector, record_download_settings, record_toggle_visibility, password_generate, security_csp_control, sudo_mode_control, codeeditor_tsref, codeeditor_codecompletion_loadtemplates, color_scheme_update, qrcode_generator, qrcode_download, wizard_page_get_doktypes, wizard_page_get_page_detail, wizard_page_get_processed_value, wizard_config, wizard_submit

Middlewares: typo3/cms-core/normalized-params-attribute, typo3/cms-backend/locked-backend, typo3/cms-backend/https-redirector, typo3/cms-backend/csp-report, typo3/cms-backend/backend-routing, typo3/cms-core/request-token-middleware, typo3/cms-backend/authentication, typo3/cms-backend/backend-module-validator, typo3/cms-backend/sudo-mode-interceptor, typo3/cms-backend/site-resolver, typo3/cms-backend/page-context, typo3/cms-backend/csp-headers, typo3/cms-backend/js-label-importmap-resolver, typo3/cms-backend/response-headers, typo3/cms-core/response-propagation

Fluid roots: Resources/Private/Templates/, Resources/Private/Partials/, Resources/Private/Layouts/

Registration files: ext_localconf.php, ext_tables.sql, Configuration/page.tsconfig, Configuration/user.tsconfig, Configuration/RequestMiddlewares.php, Configuration/Services.yaml, Configuration/JavaScriptModules.php
… 58 more lines
```

Data:

```json
{
    "key": "backend",
    "path": "<installation>/typo3/sysext/backend",
    "origin": "system",
    "composerName": "typo3/cms-backend",
    "description": "TYPO3 CMS Backend",
    "requires": [
        {
            "package": "ext-intl",
            "constraint": "*"
        },
        {
            "package": "ext-libxml",
            "constraint": "*"
        },
        "… 2 more"
    ],
    "tcaTables": [],
    "tcaOverrides": [
        "be_users"
    ],
    "contentElements": [],
    "backendModules": [
        "web_layout",
        "records",
        "… 10 more"
    ],
    "backendRoutes": [
        "login",
        "main",
        "… 127 more"
    ],
    "icons": [],
    "siteSets": [],
    "middlewares": [
        "typo3/cms-core/normalized-params-attribute",
        "typo3/cms-backend/locked-backend",
        "… 13 more"
    ],
    "serviceTags": [],
    "fluidRoots": [
        "Resources/Private/Templates/",
        "Resources/Private/Partials/",
        "… 1 more"
    ],
    "fluidNamespaces": [],
    "typoScript": [],
    "classes": [
        {
            "kind": "Command",
            "files": 8
        },
        {
            "kind": "Controller",
            "files": 90
        },
        "… 9 more"
    ],
    "files": [
        "ext_localconf.php",
        "ext_tables.sql",
        "… 5 more"
    ],
    "notReadStatically": [],
    "artifacts": {
        "manual": null,
        "readme": "README.rst",
        "tests": [
            "Functional",
            "Unit"
        ],
        "languageFiles": [
            {
                "path": "Resources/Private/Language/Modules/about.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/content-security-policy.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            "… 47 more"
        ]
    },
    "answeredBy": "packages"
}
```

## `typo3_catalog_scope` — catalog scope

```json
{}
```

Text:

```
Installed component contract
For TYPO3 v14, 25 of the 25 curated component entries were found in the installed backend CSS or JavaScript. Their class and custom-property contracts were read from those packages.
The bundled catalog remains the curated search index and markup fallback; it does not override installed classes.

Bundled fallback source checkout
- Source: https://github.com/TYPO3/typo3
- Checkout branch: main (TYPO3 15.0)
- Commit: 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0
- Verified: 2026-07-28
- Re-check with: `bin/cli catalog:paths /path/to/typo3-core-checkout`

Scope
- components: The bundled fallback and curated search index for backend UI components, with markup, Sass source paths, and the TYPO3 majors each entry was verified on. When the target is the active installation, its backend CSS and JavaScript replace the class and custom-property contract, and an installed styleguide example replaces fallback markup where available. The index remains a subset, not every CSS class in the core.
- systemExtensions: Every system extension of every covered TYPO3 line, read off one checkout per version: the extension key, the Composer package name to require it by, what it is for, and the majors that ship it. Complete rather than curated — `bin/cli catalog:check` re-derives it, so a release that adds or drops one is reported.
… 6 more lines
```

Data:

```json
{
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    },
    "verifyCommand": "bin/cli catalog:paths /path/to/typo3-core-checkout",
    "scope": {
        "components": "The bundled fallback and curated search index for backend UI components, with markup, Sass source paths, and the TYPO3 majors each entry was verified on. When the target is the active installation, its backend CSS and JavaScript replace the class and custom-property contract, and an installed styleguide example replace… (415 characters)",
        "systemExtensions": "Every system extension of every covered TYPO3 line, read off one checkout per version: the extension key, the Composer package name to require it by, what it is for, and the majors that ship it. Complete rather than curated — `bin/cli catalog:check` re-derives it, so a release that adds or drops one is reported."
    },
    "counts": {
        "components": 25,
        "systemExtensions": 38
    },
    "targetVersion": 14,
    "verifiedCount": 25,
    "componentSource": "installation",
    "withheld": []
}
```

## `typo3_commit_message_guide` — commit: from parts

```json
{
    "changeType": "BUGFIX",
    "summary": "Show hidden records in the import preview",
    "issue": "106123"
}
```

Text:

```
Commit message draft:
```text
[BUGFIX] Show hidden records in the import preview

Resolves: #106123
Releases: RELEASE_TARGET
```

Checks:
- WARNING: The draft carries "Releases: RELEASE_TARGET". Replace it with the target versions, for example "Releases: main, 13.4".

Checked against the core contribution rules, trailers included. workflow="project" applies the same subject and body rules without the Forge issue and the Releases: trailer.
```

Data:

```json
{
    "message": "[BUGFIX] Show hidden records in the import preview\n\nResolves: #106123\nReleases: RELEASE_TARGET",
    "checks": [
        {
            "level": "warning",
            "code": "missing-releases",
            "message": "The draft carries \"Releases: RELEASE_TARGET\". Replace it with the target versions, for example \"Releases: main, 13.4\"."
        }
    ],
    "workflow": "core"
}
```

## `typo3_commit_message_guide` — commit: from a message

```json
{
    "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main"
}
```

Text:

```
Commit message, corrected:
```text
[TASK] Do a thing

Body.

Resolves: #1
Releases: main
```

Checks:
- INFO: No commit message readiness issues found by the local checks.

Checked against the core contribution rules, trailers included. workflow="project" applies the same subject and body rules without the Forge issue and the Releases: trailer.
```

Data:

```json
{
    "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
    "checks": [
        {
            "level": "info",
            "code": "no-issues-found",
            "message": "No commit message readiness issues found by the local checks."
        }
    ],
    "workflow": "core"
}
```
