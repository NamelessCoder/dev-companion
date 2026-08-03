# `typo3_task_guide`

Build a task checklist enriched with matching hints and relevant core checks.
Built from bundled conventions only: it does not read your checkout, so it also
names what you have to establish there yourself and routes to the lookups that
fit the task. Work that reads as a project or third-party extension is answered
with what transfers only — the core checks, checklist items and steps that name
something only the core repository has are left out rather than handed over.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# Short description of the TYPO3 core task, in English.
task: string
# Affected subsystem or extension, if known.
area: string  # optional
# The files the task is about, as they are in the repository they belong to.
# Pass them where the work touches more than one place: each is placed on its
# own, so a core path and an extension path in one call are not answered with
# one verdict. The area counts as one of them.
paths: [string]  # optional
# The TYPO3 version this task is for, for example "13.4" or "14". Conventions
# that do not hold there are left out, including those the repository needs for
# another major it declares. Defaults to every major this repository declares
# typo3/cms-core for, or to the installation this server was started in where
# there is no declaration.
targetVersion: string  # optional
# One of: bugfix, feature, cleanup, test, documentation, unknown.
changeType: string  # optional
```

## Answers with

```yaml
task: string
# Affected subsystem or path, if one was given.
area: string or null  # optional
# The paths this brief was composed for, the area among them. Empty where the
# call named none.
paths: [string]  # optional
# Which kind of work each path is. Where every path is outside the core, the
# core checks, the core-only checklist items and the submission route are left
# out of the whole brief.
scopes:  # optional
  - path: string
    # One of: core, uncertain, project, extension. Which kind of work this
    # answer is for: core, a patch to the TYPO3 core itself; project, the site
    # repository around an installation; extension, a package in it, whether a
    # sitepackage or a third-party one; or uncertain, which means nothing in the
    # call placed the work and what came back is the core's own.
    scope: string
changeType: string
# The TYPO3 major this repository runs — stated by the caller, or read from
# the installation. Null means nothing was filtered by version. Where the
# repository serves several majors, targetVersions is what the answer holds for.
targetVersion: integer or null  # optional
# Every TYPO3 major the answer holds for. One entry is the ordinary case.
# Several mean this repository declares typo3/cms-core for more than one of
# them, so a statement was kept when it holds on any — and where two
# statements about the same subject differ, the difference is the constraint the
# code lives under rather than drift. Empty when nothing was filtered by
# version.
targetVersions: [integer]  # optional
domains: [string]
# One of: core, uncertain, project, extension. Which kind of work the call as a
# whole reads as. Anything but core means the answer holds core conventions that
# may transfer, not a checklist for the task. Where the paths disagree, scopes
# is the answer and this is what the task text and the area alone say.
scope: string  # optional
# The kinds of core work recognized in the task text.
intents:  # optional
  - id: string
    title: string
    # One of: strong, weak. weak: a word named the subject without naming the
    # work, or the intent is a core-only one and nothing in the task says this
    # is core work. Either way it applies only under its condition.
    confidence: string
    # When a weakly matched intent applies. Empty for a strong match.
    condition: string
hints:
  - id: string
    title: string
    # PHP, TypeScript, JavaScript, CSS, or General.
    category: string
    # One of: core, project, extension, null. Which kind of work the whole hint
    # obliges. "core" means it is a condition of a patch to the TYPO3 core and a
    # convention anywhere else — the backend's own design system, the
    # changelog artifact, the paths of the mono repository. "project" and
    # "extension" are the mirror: what the repository around an installation, or
    # a package distributed on its own, has to do, and what is context rather
    # than a condition inside the core. Null, the ordinary case, means it holds
    # wherever TYPO3 is written: an API that throws throws in a sitepackage too.
    scope: string or null
    hints:
      - # The statement itself. It reads the same on every version it holds for;
        # the range is beside it, never inside it.
        text: string
        # First TYPO3 major this holds on. Null means as far back as this
        # knowledge base reaches.
        since: integer or null
        # Last TYPO3 major this holds on. Null means it still holds.
        until: integer or null
        # The same range as a sentence, empty when the statement is bound to
        # nothing.
        versions: string
        # One of: core, project, extension, null. Which kind of work this
        # statement obliges. "core" means it is a condition of a patch to the
        # TYPO3 core and a convention anywhere else — the backend's own design
        # system, the changelog artifact, the paths of the mono repository.
        # "project" and "extension" are the mirror: what the repository around
        # an installation, or a package distributed on its own, has to do, and
        # what is context rather than a condition inside the core. Null, the
        # ordinary case, means it holds wherever TYPO3 is written: an API that
        # throws throws in a sitepackage too.
        scope: string or null
# Rule sections that apply to this task.
rules:  # optional
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
# Commands to run, ready to execute from the core root.
checks: [string]
# Checks that only apply if the task really is the kind of work a weakly matched
# intent suggests.
conditionalChecks:  # optional
  - title: string
    condition: string
    checks: [string]
testSuites:  # optional
  - suite: string
    # Full command, run from the core root.
    command: string
    # Narrowed form for iterating on a single file or test.
    targeted: string or null
    description: string  # optional
    whenToUse: string  # optional
    domains: [string]  # optional
    # The TYPO3 majors whose runTests.sh has this suite, where that is not all
    # of them. Null means every covered version.
    versions: string or null
checklist: [string]
# What this server cannot see and the agent has to establish itself.
checkoutDiscovery:  # optional
  - establish: string
    how: string
nextTools:
  - tool: string
    when: string
```

## Answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/, whose
console could not be reached: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
`bin/cli tools:check` holds it.

### brief: with area

Called with:

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
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s unit
- CI=true ./Build/Scripts/runTests.sh -s functional

## Events and Extension Points
Hints:
- A listener is registered with the #[AsEventListener] attribute from TYPO3\CMS\Core\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way. [TYPO3 v13 and newer]
- Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.
- Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.
- The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.
- A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s unit
- CI=true ./Build/Scripts/runTests.sh -s functional

### General

## Deprecated APIs
Hints:
- Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole, and this server does not know your branch. Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one.
- What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.
- A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.
- Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.

Rules that apply to this task:

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

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s checkRst`
- `CI=true ./Build/Scripts/runTests.sh -s unit`
- `CI=true ./Build/Scripts/runTests.sh -s functional`
## unit
`CI=true ./Build/Scripts/runTests.sh -s unit`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>`
Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.
## functional
`CI=true ./Build/Scripts/runTests.sh -s functional`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`
Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.
## cgl
`CI=true ./Build/Scripts/runTests.sh -s cgl`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.
## cglGit
`CI=true ./Build/Scripts/runTests.sh -s cglGit`
Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.

Suggested checklist:
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.
- Add a changelog file below typo3/sysext/core/Documentation/Changelog/<version>/ named Deprecation-<issue>-<Title>.rst.
- Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.
- Keep the deprecated API working and let it trigger E_USER_DEPRECATED; removal happens in a later major.
- Document the migration path in the changelog and consider an extension scanner matcher.
- Migrate all core usages of the deprecated API in the same patch.
- Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_commit_message_guide — with isDeprecation=true, to get the keyword and prefix rules checked
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_architecture_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_feedback_record — when one of these answers was wrong or incomplete
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
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
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
                {
                    "text": "A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": []
        },
        {
            "id": "events-extension-points",
            "title": "Events and Extension Points",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "A listener is registered with the #[AsEventListener] attribute from TYPO3\\CMS\\Core\\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way.",
                    "since": 13,
                    "until": null,
                    "versions": "TYPO3 v13 and newer",
                    "scope": null
                },
                {
                    "text": "Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        }
    ],
    "rules": [
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
            "coverage": 1,
            "score": 72,
            "truncated": false
        },
        {
            "documentId": "typo3-commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://core/typo3-commit-messages",
            "heading": "Changelog Files",
            "body": "- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.\n- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`,\n  `Important-`, and `Task-`.\n- Include the Forge issue number in changelog filenames when possible.\n- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.\n- These rules are for writing an entry. An installation reads them instead: the\n  same files ship with the core package, and `typo3 upgrade:list` and\n  `typo3 upgrade:run` are what acts on the migrations behind them.",
            "coverage": 1,
            "score": 28,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s checkRst",
        "CI=true ./Build/Scripts/runTests.sh -s unit",
        "CI=true ./Build/Scripts/runTests.sh -s functional"
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
                "typoscript"
            ],
            "versions": ""
        },
        {
            "suite": "cgl",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cgl",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues for all core PHP files.",
            "whenToUse": "Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "cglGit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
            "targeted": null,
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.",
        "Add a changelog file below typo3/sysext/core/Documentation/Changelog/<version>/ named Deprecation-<issue>-<Title>.rst.",
        "Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.",
        "Keep the deprecated API working and let it trigger E_USER_DEPRECATED; removal happens in a later major.",
        "Document the migration path in the changelog and consider an extension scanner matcher.",
        "Migrate all core usages of the deprecated API in the same patch.",
        "Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
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
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
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
        {
            "tool": "typo3_architecture_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```

### brief: task only

Called with:

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
- labels is a translation domain, not a file path. typo3_translation_domain_lookup computes it for the XLF file the module's labels live in. [TYPO3 v14 and newer]
- Configure a module shortcut with $moduleTemplate->getDocHeaderComponent()->setShortcutContext($routeIdentifier, $displayName, $arguments). The doc header creates and positions the ShortcutButton; do not add one to its button bar manually. [TYPO3 v14 and newer]
- After a module POST changes state, return a RedirectResponse with HTTP 303 status. The browser then follows with GET; HTTP 302 does not state that method change and can repeat the POST.
- Configuration/Backend/Routes.php and AjaxRoutes.php declare backend routes outside a module, in the same declarative style.
- These are declarative files with no schema check behind them: a wrong key does not fail at boot, it fails when a user opens the module. Take the shape from a neighbouring extension.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s functional

Rules that apply to this task:

These sections are prose and are not filtered by version. Where a subsystem changed inside the covered range, the statement that changed carries the range elsewhere: call typo3_architecture_lookup with targetVersion for the convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Testing
Source: TYPO3 Core Contribution Rules (typo3://core/typo3-core-rules) — matches 67% of the query terms

- Unit tests are expected for isolated behavior.
- Functional tests are expected for persistence, configuration, routing, backend
  behavior, or integration with TYPO3 services.
- End-to-end tests, the `e2e` suite, are useful when the change affects editor or
  administrator workflows and only breaks in the assembled backend. They replaced
  the former acceptance suites.
- Document tests that could not be executed and why.

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s lintScss`
- `CI=true ./Build/Scripts/runTests.sh -s build`
- `CI=true ./Build/Scripts/runTests.sh -s functional`
## unit
`CI=true ./Build/Scripts/runTests.sh -s unit`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>`
Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.
## functional
`CI=true ./Build/Scripts/runTests.sh -s functional`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`
Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.
## cgl
`CI=true ./Build/Scripts/runTests.sh -s cgl`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.
## cglGit
`CI=true ./Build/Scripts/runTests.sh -s cglGit`
Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.

Suggested checklist:
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Use the existing backend component classes and their documented markup instead of new ad-hoc classes.
- Check the styleguide demo of the component for the canonical structure.
- Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_component_lookup — before writing backend markup or CSS classes
- typo3_backend_module_lookup — to compare the declaration with modules registered by the active installation
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_architecture_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_commit_message_guide — before committing
- typo3_feedback_record — when one of these answers was wrong or incomplete
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
                {
                    "text": "routes maps a route name to a target of the form Controller::class . '::method'; the entry named _default is what the module opens with.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "labels is a translation domain, not a file path. typo3_translation_domain_lookup computes it for the XLF file the module's labels live in.",
                    "since": 14,
                    "until": null,
                    "versions": "TYPO3 v14 and newer",
                    "scope": null
                },
                {
                    "text": "Configure a module shortcut with $moduleTemplate->getDocHeaderComponent()->setShortcutContext($routeIdentifier, $displayName, $arguments). The doc header creates and positions the ShortcutButton; do not add one to its button bar manually.",
                    "since": 14,
                    "until": null,
                    "versions": "TYPO3 v14 and newer",
                    "scope": null
                },
                {
                    "text": "After a module POST changes state, return a RedirectResponse with HTTP 303 status. The browser then follows with GET; HTTP 302 does not state that method change and can repeat the POST.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Configuration/Backend/Routes.php and AjaxRoutes.php declare backend routes outside a module, in the same declarative style.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "These are declarative files with no schema check behind them: a wrong key does not fail at boot, it fails when a user opens the module. Take the shape from a neighbouring extension.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
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
            "body": "- Unit tests are expected for isolated behavior.\n- Functional tests are expected for persistence, configuration, routing, backend\n  behavior, or integration with TYPO3 services.\n- End-to-end tests, the `e2e` suite, are useful when the change affects editor or\n  administrator workflows and only breaks in the assembled backend. They replaced\n  the former acceptance suites.\n- Document tests that could not be executed and why.",
            "coverage": 0.667,
            "score": 28,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s lintScss",
        "CI=true ./Build/Scripts/runTests.sh -s build",
        "CI=true ./Build/Scripts/runTests.sh -s functional"
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
                "typoscript"
            ],
            "versions": ""
        },
        {
            "suite": "cgl",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cgl",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues for all core PHP files.",
            "whenToUse": "Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "cglGit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
            "targeted": null,
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Use the existing backend component classes and their documented markup instead of new ad-hoc classes.",
        "Check the styleguide demo of the component for the canonical structure.",
        "Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
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
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
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
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        {
            "tool": "typo3_architecture_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_commit_message_guide",
            "when": "before committing"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```

### brief: paths of two kinds

Called with:

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

## DataHandler and Persistence
Hints:
- DataHandler and persistence changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.
- Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.
- Writing records goes through a datamap: $dataMap[<table>][<uid or "NEW" plus a unique suffix>][<field>], handed to start($dataMap, $cmdMap) and then process_datamap(). Moving, copying and deleting go through the command map instead. A new record's real uid comes back in substNEWwithIDs, keyed by the placeholder.
- A new record is placed at the TOP of its page: the pid field is the positioning pid, and a page uid there means "first record on that page". A datamap written in reading order therefore comes out reversed — pages in a menu, content elements in a column.
- To place records in order, use the negative form: a pid of -<uid> means "directly after that record". A "NEW" placeholder may be used there as well, as -NEW..., and resolves once the record it names has been created in the same run.
- DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s functional
- CI=true ./Build/Scripts/runTests.sh -s phpstan

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s unit
- CI=true ./Build/Scripts/runTests.sh -s functional

# For packages/acme_events/Classes/Domain/Repository/EventRepository.php — extension

### PHP

## Extbase Plugins
Hints:
- Extbase is what a frontend needs beyond reading records: pagination, a search with arguments, validation, forms. Rendering records read-only needs none of it — see the records hint — and mixing the two questions is where most of the wrong answers here come from.
- A plugin is registered by two calls that do different things. ExtensionUtility::registerPlugin() belongs in Configuration/TCA/Overrides/tt_content.php and adds the item to the CType selector, optionally with a FlexForm; ExtensionUtility::configurePlugin() belongs in ext_localconf.php and declares which controller actions exist and which of them are not cacheable. Neither one replaces the other.
- The plugin signature both calls derive — the extension name and the plugin name, lowercased and joined by an underscore — is four things at once: the value in the CType column, the TypoScript key below tt_content, the key the FlexForm is registered under, and the namespace the plugin's own arguments arrive in. Renaming a plugin renames all four, and existing content keeps the old value.
- configurePlugin() takes a plugin type as its last argument that has to be omitted or given as "CType": a plugin is a content type of its own and nothing else, and anything else throws. [TYPO3 v14 and newer]
- A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.
- Which paginator to use follows from what is being paginated: QueryResultPaginator for an Extbase query result, QueryBuilderPaginator for a Doctrine query builder, ArrayPaginator for an array that is already in memory. The last one paginates in PHP, so choosing it for a query means every record is fetched in order to show one page. SlidingWindowPagination on top of any of them produces the page numbers.
- A search form submitted by GET on a cacheable action answers with a page-not-found, and putting the action into the non-cacheable list does not fix it: the cache hash is validated by a middleware, long before Extbase knows which action was called. The plugin's arguments have to be excluded from the hash as well, in $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters']. A name there carries an indicator: ^ matches the beginning of a parameter name, ~ any part of it, and no indicator at all is an exact match — the one entry that covers a whole plugin is the prefix form. The hidden fields a Fluid form adds need the same treatment.
- An object argument maps nothing until its properties are allowed. A request that hands a controller an object other than a persisted entity fails with "It is not allowed to map property" until the matching initialize<Action>Action() calls allowProperties() on that argument's property mapping configuration. That is the secure default rather than a defect.
- An object that is not persisted is dropped from a generated link without a word: the URI builder serialises an entity by its uid, and something built for the request has none — so the filter of a search is gone on page two while the link looks correct. Pass such state to a link as plain arguments.
- A paginator clamps a page number outside its range, so a page beyond the last one answers with the first page rather than with a not-found. Compare the current page against the number of pages in the controller and answer with the not-found response yourself, or the same list is served under an unbounded number of URLs.
- A paginated plugin needs a route for the list as well as one for the paginated form. A single route with the page number defaulted only omits the variable when a link is built, and the literal part of the segment stays behind — a link to the first page then points at a path that ends in the prefix of a number that is not there. Routes are tried in the order they are declared, so the paginated one goes before a route whose slug would otherwise swallow it.
- Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.
Worked example: typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example — typo3_reference_list for what it demonstrates and where an installation has it.

## DataHandler and Persistence
Hints:
- DataHandler and persistence changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.
- Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.
- Writing records goes through a datamap: $dataMap[<table>][<uid or "NEW" plus a unique suffix>][<field>], handed to start($dataMap, $cmdMap) and then process_datamap(). Moving, copying and deleting go through the command map instead. A new record's real uid comes back in substNEWwithIDs, keyed by the placeholder.
- A new record is placed at the TOP of its page: the pid field is the positioning pid, and a page uid there means "first record on that page". A datamap written in reading order therefore comes out reversed — pages in a menu, content elements in a column.
- To place records in order, use the negative form: a pid of -<uid> means "directly after that record". A "NEW" placeholder may be used there as well, as -NEW..., and resolves once the record it names has been created in the same run.
- DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s functional`
- `CI=true ./Build/Scripts/runTests.sh -s phpstan`
- `CI=true ./Build/Scripts/runTests.sh -s unit`
## checkExtensionScannerRst
`CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`
Use when a deprecation or breaking change adds extension scanner matchers.
## checkIntegrityPhp
`CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.
## e2e
`CI=true ./Build/Scripts/runTests.sh -s e2e`
Use for editor or administrator workflows that only break in the assembled backend.

Suggested checklist:
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Reproduce the bug first, ideally with a failing test that the fix turns green.
- Check whether the bug also affects maintained older release branches.
- Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_architecture_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_architecture_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_commit_message_guide — before committing
- typo3_feedback_record — when one of these answers was wrong or incomplete
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
                {
                    "text": "Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Writing records goes through a datamap: $dataMap[<table>][<uid or \"NEW\" plus a unique suffix>][<field>], handed to start($dataMap, $cmdMap) and then process_datamap(). Moving, copying and deleting go through the command map instead. A new record's real uid comes back in substNEWwithIDs, keyed by the placeholder.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A new record is placed at the TOP of its page: the pid field is the positioning pid, and a page uid there means \"first record on that page\". A datamap written in reading order therefore comes out reversed — pages in a menu, content elements in a column.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "To place records in order, use the negative form: a pid of -<uid> means \"directly after that record\". A \"NEW\" placeholder may be used there as well, as -NEW..., and resolves once the record it names has been created in the same run.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
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
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        {
            "id": "extbase",
            "title": "Extbase Plugins",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Extbase is what a frontend needs beyond reading records: pagination, a search with arguments, validation, forms. Rendering records read-only needs none of it — see the records hint — and mixing the two questions is where most of the wrong answers here come from.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A plugin is registered by two calls that do different things. ExtensionUtility::registerPlugin() belongs in Configuration/TCA/Overrides/tt_content.php and adds the item to the CType selector, optionally with a FlexForm; ExtensionUtility::configurePlugin() belongs in ext_localconf.php and declares which controller actions exist and which of them are not cacheable. Neither one replaces the other.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The plugin signature both calls derive — the extension name and the plugin name, lowercased and joined by an underscore — is four things at once: the value in the CType column, the TypoScript key below tt_content, the key the FlexForm is registered under, and the namespace the plugin's own arguments arrive in. Renaming a plugin renames all four, and existing content keeps the old value.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "configurePlugin() takes a plugin type as its last argument that has to be omitted or given as \"CType\": a plugin is a content type of its own and nothing else, and anything else throws.",
                    "since": 14,
                    "until": null,
                    "versions": "TYPO3 v14 and newer",
                    "scope": null
                },
                {
                    "text": "A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Which paginator to use follows from what is being paginated: QueryResultPaginator for an Extbase query result, QueryBuilderPaginator for a Doctrine query builder, ArrayPaginator for an array that is already in memory. The last one paginates in PHP, so choosing it for a query means every record is fetched in order to show one page. SlidingWindowPagination on top of any of them produces the page numbers.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A search form submitted by GET on a cacheable action answers with a page-not-found, and putting the action into the non-cacheable list does not fix it: the cache hash is validated by a middleware, long before Extbase knows which action was called. The plugin's arguments have to be excluded from the hash as well, in $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters']. A name there carries an indicator: ^ matches the beginning of a parameter name, ~ any part of it, and no indicator at all is an exact match — the one entry that covers a whole plugin is the prefix form. The hidden fields a Fluid form adds need the same treatment.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "An object argument maps nothing until its properties are allowed. A request that hands a controller an object other than a persisted entity fails with \"It is not allowed to map property\" until the matching initialize<Action>Action() calls allowProperties() on that argument's property mapping configuration. That is the secure default rather than a defect.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "An object that is not persisted is dropped from a generated link without a word: the URI builder serialises an entity by its uid, and something built for the request has none — so the filter of a search is gone on page two while the link looks correct. Pass such state to a link as plain arguments.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A paginator clamps a page number outside its range, so a page beyond the last one answers with the first page rather than with a not-found. Compare the current page against the number of pages in the controller and answer with the not-found response yourself, or the same list is served under an unbounded number of URLs.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A paginated plugin needs a route for the list as well as one for the paginated form. A single route with the page number defaulted only omits the variable when a link is built, and the literal part of the segment stays behind — a link to the first page then points at a path that ends in the prefix of a number that is not there. Routes are tried in the order they are declared, so the paginated one goes before a route whose slug would otherwise swallow it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": []
        }
    ],
    "rules": [],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s functional",
        "CI=true ./Build/Scripts/runTests.sh -s phpstan",
        "CI=true ./Build/Scripts/runTests.sh -s unit"
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
        {
            "suite": "e2e",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e",
            "targeted": null,
            "description": "End-to-end tests driving a real backend with Playwright.",
            "whenToUse": "Use for editor or administrator workflows that only break in the assembled backend.",
            "domains": [
                "php",
                "typescript",
                "fluid"
            ],
            "versions": "TYPO3 v13 and newer"
        }
    ],
    "checklist": [
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Reproduce the bug first, ideally with a failing test that the fix turns green.",
        "Check whether the bug also affects maintained older release branches.",
        "Write the commit message with typo3_commit_message_guide: summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
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
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
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
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_commit_message_guide",
            "when": "before committing"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```
