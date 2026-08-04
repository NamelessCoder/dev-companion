# `typo3_hint_lookup`

Return hints for TYPO3 core paths or task topics, grouped by section. Where the
paths read as a project or third-party extension the hints still come back,
because the conventions transfer. The "Backend CSS" and "Backend TypeScript and
JavaScript" sections describe the TYPO3 backend interface and are withheld, with
the reason, where the task names the frontend. Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# File paths related to the task, as they are in the repository they belong to.
# Each is placed on its own, so a core path and an extension path in one call
# are matched separately, and a statement is labelled where it obliges the other
# one.
paths: [string]  # optional
# Short task description or topic, in English. Matching is lexical against
# English text, so another language reaches only the loanwords.
task: string  # optional
# Ask for one hint by its id, for example language-files, instead of matching.
# Every answer lists the ids it did not return, so a subject a query missed can
# be requested by name rather than guessed at in other words.
id: string  # optional
# The TYPO3 version the answer has to hold for, for example "13.4" or "14".
# Statements that do not hold there are left out, including those the repository
# needs for another major it declares. Defaults to every major this repository
# declares typo3/cms-core for, or to the installation this server was started in
# where there is no declaration; where there is neither, nothing is filtered and
# every statement carries the versions it holds for.
targetVersion: string  # optional
# Maximum number of hints.
limit: integer  # optional
```

## Answers with

```yaml
task: string or null  # optional
paths: [string]
# Which kind of work each path is. Paths of different scope are matched
# separately, so a hint that came back for one of them is about that path.
scopes:
  - path: string
    # One of: core, uncertain, project, extension. Which kind of work this
    # answer is for: core, a patch to the TYPO3 core itself; project, the site
    # repository around an installation; extension, a package in it, whether a
    # sitepackage or a third-party one; or uncertain, which means nothing in the
    # call placed the work and what came back is the core's own.
    scope: string
# The TYPO3 major this repository runs — stated by the caller, or read from
# the installation. Null means nothing was filtered and every statement carries
# its own range. Where the repository serves several majors, targetVersions is
# what the answer holds for.
targetVersion: integer or null  # optional
# Every TYPO3 major the answer holds for. One entry is the ordinary case.
# Several mean this repository declares typo3/cms-core for more than one of
# them, so a statement was kept when it holds on any — and where two
# statements about the same subject differ, the difference is the constraint the
# code lives under rather than drift. Empty when nothing was filtered by
# version.
targetVersions: [integer]  # optional
# Hints outside these domains are not returned.
domains: [string]
# Categories that matched the domains but were left out because the task names
# the frontend. "Backend CSS" and "Backend TypeScript and JavaScript" describe
# the TYPO3 backend interface and are wrong advice for what a website renders;
# see docs.typo3.org for frontend theming.
withheldCategories: [string]
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
# The hints that exist in the searched domains, minus the ones returned above.
# Carried on every answer rather than on an empty one: a query that matched
# three hints about something else is where naming an id is worth most. An id
# lookup lists what stands beside the hint it returned.
availableHints:
  - # Ask for this hint outright by passing it as id.
    id: string
    title: string
    # PHP, TypeScript, JavaScript, CSS, or General.
    category: string
```

## Answered

Recorded on 2026-08-04 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: the installation requires PHP 8.5.0 and no
interpreter on this machine provides it (running 8.3.23). Nothing checks what
is below this heading; everything above it is derived from the class that
answers the call, and `bin/cli tools:check` holds it.

### hints: path

Called with:

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
Answered for TYPO3 v15: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Hints:
### PHP

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.

## DataHandler Is the Write Path for Records
Hints:
- Every record change the backend makes goes through DataHandler. It evaluates the TCA of each field, checks the editor's permissions, keeps workspaces and localizations consistent and updates the reference index. A direct INSERT writes the row and none of that, so the record exists and nothing else about it is true.
- DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.
- The rest of the subject is its own hint, because each is asked as its own question: datahandler-writing for the datamap, datahandler-relations for a relation field, datahandler-placement for which page may hold a record and where it lands on it, datahandler-seeding for building content that exists nowhere yet, datahandler-testing for covering any of it. Reading records back is persistence-reading and works nothing like this.

## Testing DataHandler Behaviour
Hints:
- DataHandler and persistence changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.
- Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.
- DataHandler scenarios extend AbstractDataHandlerActionTestCase instead of FunctionalTestCase; it carries the scenario setup those tests share. It lives in typo3/sysext/core/Tests/Functional/DataScenarios/, which is the core's own test tree — an extension writes an ordinary FunctionalTestCase and primes its records with CSV fixtures.

What matched above is a guess at your words. The rest of these domains, requestable by id:
- public-assets — Public Assets and the Publish Step (PHP)
- extension-asset-build — Building Assets in a Project Extension (PHP)
- authentication-permissions — Authentication and Permissions (PHP)
- backend-modules — Backend Module and Route Registration (PHP)
- caching — Caches (PHP)
- configuration-reach — Configuration Belongs to Its Reach (PHP)
- environment-variables — What TYPO3 Reads From the Environment (PHP)
- environment-placeholders — %env() in a YAML Configuration (PHP)
- environment-runtime-readers — What Reads the Environment While It Runs (PHP)
- installation-setup — What typo3 setup Takes and What It Refuses (PHP)
- console-commands — Console Commands (PHP)
- content-elements — Registering a Content Element (PHP)
- content-element-shape — What a Content Element Owns (PHP)
- content-element-preview — The Backend Preview of a Content Element (Fluid)
- datahandler-writing — Writing Records with a Datamap (PHP)
- datahandler-relations — What a Datamap Does to a Relation Field (PHP)
- datahandler-placement — Which Page May Hold a New Record, and Where It Lands on It (PHP)
- datahandler-seeding — Seeding Records with a Script (PHP)
- frontend-dataprocessors — Frontend DataProcessors (PHP)
- dependency-injection — Wiring a Service (PHP)
- di-service-not-found — A Service the Container Cannot Find at Runtime (PHP)
- sitepackage-initial-content — Shipping Initial Content with an Extension (PHP)
- initial-content-import-once — Why a Changed Data File Does Not Arrive (PHP)
- initial-content-references — What Survives the Import, and What Points at a Stranger (PHP)
- impexp-artifact — Writing the Export a Distribution Ships (PHP)
- extension-documentation — Documenting a Project Extension (Documentation)
- events-extension-points — Events and Extension Points (PHP)
- extbase — What Extbase Is For, and When It Is Not Needed (PHP)
- extbase-plugin-registration — The Two Calls That Register a Plugin (PHP)
- extbase-domain-mapping — Models, Repositories and the Table Behind Them (PHP)
- extbase-arguments — What Arrives From a Request, and What Silently Does Not (PHP)
- extbase-pagination — Paginating a List (PHP)
- extension-manifest — What Makes a Directory an Extension (PHP)
- extension-schema-sql — Declaring Tables and Columns (PHP)
- extension-declarative-files — The Files an Extension Is Configured By (PHP)
- extension-boot-files — What Still Runs at Boot, and What No Longer Does (PHP)
- content-rendering-templates — contentRenderingTemplates and Where Plugin TypoScript Lands (PHP)
- extension-repository-layout — How a Distributed Extension Repository Is Laid Out (PHP)
- extension-repository-dependencies — What Such a Repository Commits, and What It Vendors (PHP)
- extension-repository-tests — The Instance an Extension Suite Builds Itself (PHP)
- extension-repository-installation — Installing TYPO3 Beneath the Extension Repository (PHP)
- fal-basics — Files Are Addressed Through FAL, Not by Path (PHP)
- fal-storages-drivers — Storages and the Drivers Behind Them (PHP)
- fal-reading — Getting a File Object, and Its Metadata (PHP)
- fal-writing — Putting a File Into a Storage (PHP)
- fal-testing — Covering FAL Behaviour (PHP)
- fal-processing — Which Processor Claims a File, and What Runs Below It (PHP)
- form-framework — EXT:form Configuration and Runtime (PHP)
- icon-usage — Rendering and Registering Icons (PHP)
- site-label-language — Core Labels on a Non-English Site (Labels)
- page-cache-flushing — Which Caches a Change Invalidates, and What Clears the Rest (Fluid)
- persistence-reading — Reading Records, and What Is Hidden From the Query (PHP)
- installation-boot — Booting the Installation a Project Repository Declares (PHP)
- project-repository-layout — How a TYPO3 Project Repository Is Laid Out (PHP)
- project-build-and-scripts — Build/, the Scripts, and What Is Not Deployed (PHP)
- project-configuration-files — What the Installation Is Configured By (PHP)
- frontend-records — Records in the Frontend Without Extbase (TypoScript)
- record-routing — Routing a Record Detail View (PHP)
- record-page-title — The Title of a Record Detail Page (PHP)
- routing-request-handling — Routing, Middleware, and Request Handling (PHP)
- security-sinks — Following a Value to Its Sink (PHP)
- tca-formengine — TCA, FormEngine, and Backend Forms (PHP)
- tca-schema-api — TCA Schema API (PHP)
- formdata-providers — FormEngine Data Providers (PHP)
- core-tests — Writing Core Tests (PHP)
- project-extension-tests — Setting a Test Suite Up in an Extension (PHP)
- extension-test-extensions — Which Extensions a Functional Test Loads (PHP)
- extension-test-site — Writing a Site Configuration in a Test (PHP)
- extension-test-frontend-request — Asserting a Frontend Response in a Test (PHP)
- browser-tests — Browser Tests with Playwright (PHP)
- browser-test-accessibility — Checking Accessibility and Contrast From the Same Spec (PHP)
- browser-tests-outside-core — The Site a Project Suite Runs Against (PHP)
- extension-static-analysis — Setting Up PHPStan for an Extension (PHP)
- unit-test-doubles — Unit Tests, Test Doubles and Data Providers in PHPUnit (PHP)
- installation-upgrade — Upgrading an Installation (PHP)
- upgrade-own-code — What No Wizard Touches (PHP)
- deprecated-apis — Deprecated APIs (PHP)
- upgrade-wizards — Upgrade Wizards (PHP)
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
    "targetVersion": 15,
    "targetVersions": [
        15
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
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "datahandler-basics",
            "title": "DataHandler Is the Write Path for Records",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Every record change the backend makes goes through DataHandler. It evaluates the TCA of each field, checks the editor's permissions, keeps workspaces and localizations consistent and updates the reference index. A direct INSERT writes the row and none of that, so the record exists and nothing else about it is true.",
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
                },
                {
                    "text": "The rest of the subject is its own hint, because each is asked as its own question: datahandler-writing for the datamap, datahandler-relations for a relation field, datahandler-placement for which page may hold a record and where it lands on it, datahandler-seeding for building content that exists nowhere yet, datahandler-testing for covering any of it. Reading records back is persistence-reading and works nothing like this.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "datahandler-testing",
            "title": "Testing DataHandler Behaviour",
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
                    "text": "DataHandler scenarios extend AbstractDataHandlerActionTestCase instead of FunctionalTestCase; it carries the scenario setup those tests share. It lives in typo3/sysext/core/Tests/Functional/DataScenarios/, which is the core's own test tree — an extension writes an ordinary FunctionalTestCase and primes its records with CSV fixtures.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": "core"
                }
            ]
        }
    ],
    "availableHints": [
        {
            "id": "public-assets",
            "title": "Public Assets and the Publish Step",
            "category": "PHP"
        },
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "PHP"
        },
        {
            "id": "authentication-permissions",
            "title": "Authentication and Permissions",
            "category": "PHP"
        },
        {
            "id": "backend-modules",
            "title": "Backend Module and Route Registration",
            "category": "PHP"
        },
        {
            "id": "caching",
            "title": "Caches",
            "category": "PHP"
        },
        {
            "id": "configuration-reach",
            "title": "Configuration Belongs to Its Reach",
            "category": "PHP"
        },
        {
            "id": "environment-variables",
            "title": "What TYPO3 Reads From the Environment",
            "category": "PHP"
        },
        {
            "id": "environment-placeholders",
            "title": "%env() in a YAML Configuration",
            "category": "PHP"
        },
        {
            "id": "environment-runtime-readers",
            "title": "What Reads the Environment While It Runs",
            "category": "PHP"
        },
        {
            "id": "installation-setup",
            "title": "What typo3 setup Takes and What It Refuses",
            "category": "PHP"
        },
        {
            "id": "console-commands",
            "title": "Console Commands",
            "category": "PHP"
        },
        {
            "id": "content-elements",
            "title": "Registering a Content Element",
            "category": "PHP"
        },
        {
            "id": "content-element-shape",
            "title": "What a Content Element Owns",
            "category": "PHP"
        },
        {
            "id": "content-element-preview",
            "title": "The Backend Preview of a Content Element",
            "category": "Fluid"
        },
        {
            "id": "datahandler-writing",
            "title": "Writing Records with a Datamap",
            "category": "PHP"
        },
        {
            "id": "datahandler-relations",
            "title": "What a Datamap Does to a Relation Field",
            "category": "PHP"
        },
        {
            "id": "datahandler-placement",
            "title": "Which Page May Hold a New Record, and Where It Lands on It",
            "category": "PHP"
        },
        {
            "id": "datahandler-seeding",
            "title": "Seeding Records with a Script",
            "category": "PHP"
        },
        {
            "id": "frontend-dataprocessors",
            "title": "Frontend DataProcessors",
            "category": "PHP"
        },
        {
            "id": "dependency-injection",
            "title": "Wiring a Service",
            "category": "PHP"
        },
        {
            "id": "di-service-not-found",
            "title": "A Service the Container Cannot Find at Runtime",
            "category": "PHP"
        },
        {
            "id": "sitepackage-initial-content",
            "title": "Shipping Initial Content with an Extension",
            "category": "PHP"
        },
        {
            "id": "initial-content-import-once",
            "title": "Why a Changed Data File Does Not Arrive",
            "category": "PHP"
        },
        {
            "id": "initial-content-references",
            "title": "What Survives the Import, and What Points at a Stranger",
            "category": "PHP"
        },
        {
            "id": "impexp-artifact",
            "title": "Writing the Export a Distribution Ships",
            "category": "PHP"
        },
        {
            "id": "extension-documentation",
            "title": "Documenting a Project Extension",
            "category": "Documentation"
        },
        {
            "id": "events-extension-points",
            "title": "Events and Extension Points",
            "category": "PHP"
        },
        {
            "id": "extbase",
            "title": "What Extbase Is For, and When It Is Not Needed",
            "category": "PHP"
        },
        {
            "id": "extbase-plugin-registration",
            "title": "The Two Calls That Register a Plugin",
            "category": "PHP"
        },
        {
            "id": "extbase-domain-mapping",
            "title": "Models, Repositories and the Table Behind Them",
            "category": "PHP"
        },
        {
            "id": "extbase-arguments",
            "title": "What Arrives From a Request, and What Silently Does Not",
            "category": "PHP"
        },
        {
            "id": "extbase-pagination",
            "title": "Paginating a List",
            "category": "PHP"
        },
        {
            "id": "extension-manifest",
            "title": "What Makes a Directory an Extension",
            "category": "PHP"
        },
        {
            "id": "extension-schema-sql",
            "title": "Declaring Tables and Columns",
            "category": "PHP"
        },
        {
            "id": "extension-declarative-files",
            "title": "The Files an Extension Is Configured By",
            "category": "PHP"
        },
        {
            "id": "extension-boot-files",
            "title": "What Still Runs at Boot, and What No Longer Does",
            "category": "PHP"
        },
        {
            "id": "content-rendering-templates",
            "title": "contentRenderingTemplates and Where Plugin TypoScript Lands",
            "category": "PHP"
        },
        {
            "id": "extension-repository-layout",
            "title": "How a Distributed Extension Repository Is Laid Out",
            "category": "PHP"
        },
        {
            "id": "extension-repository-dependencies",
            "title": "What Such a Repository Commits, and What It Vendors",
            "category": "PHP"
        },
        {
            "id": "extension-repository-tests",
            "title": "The Instance an Extension Suite Builds Itself",
            "category": "PHP"
        },
        {
            "id": "extension-repository-installation",
            "title": "Installing TYPO3 Beneath the Extension Repository",
            "category": "PHP"
        },
        {
            "id": "fal-basics",
            "title": "Files Are Addressed Through FAL, Not by Path",
            "category": "PHP"
        },
        {
            "id": "fal-storages-drivers",
            "title": "Storages and the Drivers Behind Them",
            "category": "PHP"
        },
        {
            "id": "fal-reading",
            "title": "Getting a File Object, and Its Metadata",
            "category": "PHP"
        },
        {
            "id": "fal-writing",
            "title": "Putting a File Into a Storage",
            "category": "PHP"
        },
        {
            "id": "fal-testing",
            "title": "Covering FAL Behaviour",
            "category": "PHP"
        },
        {
            "id": "fal-processing",
            "title": "Which Processor Claims a File, and What Runs Below It",
            "category": "PHP"
        },
        {
            "id": "form-framework",
            "title": "EXT:form Configuration and Runtime",
            "category": "PHP"
        },
        {
            "id": "icon-usage",
            "title": "Rendering and Registering Icons",
            "category": "PHP"
        },
        {
            "id": "site-label-language",
            "title": "Core Labels on a Non-English Site",
            "category": "Labels"
        },
        {
            "id": "page-cache-flushing",
            "title": "Which Caches a Change Invalidates, and What Clears the Rest",
            "category": "Fluid"
        },
        {
            "id": "persistence-reading",
            "title": "Reading Records, and What Is Hidden From the Query",
            "category": "PHP"
        },
        {
            "id": "installation-boot",
            "title": "Booting the Installation a Project Repository Declares",
            "category": "PHP"
        },
        {
            "id": "project-repository-layout",
            "title": "How a TYPO3 Project Repository Is Laid Out",
            "category": "PHP"
        },
        {
            "id": "project-build-and-scripts",
            "title": "Build/, the Scripts, and What Is Not Deployed",
            "category": "PHP"
        },
        {
            "id": "project-configuration-files",
            "title": "What the Installation Is Configured By",
            "category": "PHP"
        },
        {
            "id": "frontend-records",
            "title": "Records in the Frontend Without Extbase",
            "category": "TypoScript"
        },
        {
            "id": "record-routing",
            "title": "Routing a Record Detail View",
            "category": "PHP"
        },
        {
            "id": "record-page-title",
            "title": "The Title of a Record Detail Page",
            "category": "PHP"
        },
        {
            "id": "routing-request-handling",
            "title": "Routing, Middleware, and Request Handling",
            "category": "PHP"
        },
        {
            "id": "security-sinks",
            "title": "Following a Value to Its Sink",
            "category": "PHP"
        },
        {
            "id": "tca-formengine",
            "title": "TCA, FormEngine, and Backend Forms",
            "category": "PHP"
        },
        {
            "id": "tca-schema-api",
            "title": "TCA Schema API",
            "category": "PHP"
        },
        {
            "id": "formdata-providers",
            "title": "FormEngine Data Providers",
            "category": "PHP"
        },
        {
            "id": "core-tests",
            "title": "Writing Core Tests",
            "category": "PHP"
        },
        {
            "id": "project-extension-tests",
            "title": "Setting a Test Suite Up in an Extension",
            "category": "PHP"
        },
        {
            "id": "extension-test-extensions",
            "title": "Which Extensions a Functional Test Loads",
            "category": "PHP"
        },
        {
            "id": "extension-test-site",
            "title": "Writing a Site Configuration in a Test",
            "category": "PHP"
        },
        {
            "id": "extension-test-frontend-request",
            "title": "Asserting a Frontend Response in a Test",
            "category": "PHP"
        },
        {
            "id": "browser-tests",
            "title": "Browser Tests with Playwright",
            "category": "PHP"
        },
        {
            "id": "browser-test-accessibility",
            "title": "Checking Accessibility and Contrast From the Same Spec",
            "category": "PHP"
        },
        {
            "id": "browser-tests-outside-core",
            "title": "The Site a Project Suite Runs Against",
            "category": "PHP"
        },
        {
            "id": "extension-static-analysis",
            "title": "Setting Up PHPStan for an Extension",
            "category": "PHP"
        },
        {
            "id": "unit-test-doubles",
            "title": "Unit Tests, Test Doubles and Data Providers in PHPUnit",
            "category": "PHP"
        },
        {
            "id": "installation-upgrade",
            "title": "Upgrading an Installation",
            "category": "PHP"
        },
        {
            "id": "upgrade-own-code",
            "title": "What No Wizard Touches",
            "category": "PHP"
        },
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs",
            "category": "PHP"
        },
        {
            "id": "upgrade-wizards",
            "title": "Upgrade Wizards",
            "category": "PHP"
        }
    ]
}
```

### hints: topic

Called with:

```json
{
    "task": "sass build"
}
```

Text:

```
Task: sass build
Answered for TYPO3 v15: statements that do not hold there are left out.
Domains: css (hints outside these domains are not shown)

Hints:
### PHP

## Building Assets in a Project Extension
Binding for work outside the TYPO3 core — a project repository or a distributed extension. In the core it is context for what such a repository has to do, and no condition of a patch.
Hints:
- An extension owns its asset source, build tool and generated output; installing it into TYPO3 does not attach its Sass or TypeScript to the core's Build/Sources pipelines. Put only browser-consumable output below Resources/Public/ and keep the source where the extension's own package scripts name it.
- Decide whether generated assets are committed. If they are, source and output change together; if they are not, the project deployment has to run the build. The extension's package.json and CI are the executable record of that decision.
- The public-assets hint covers how Resources/Public files are published and referenced. The extension-declarative-files hint covers Configuration/JavaScriptModules.php for backend JavaScript import maps; neither implies a particular bundler.
- For a patch to the TYPO3 backend itself, css-source-build-boundaries and backend-typescript describe the core's source trees and generated pairs; those paths and commands do not transfer to an extension.

### Backend CSS

## CSS Source and Build Boundaries
Hints:
- Treat Build/Sources/Sass/ as the source of truth when a Sass source exists.
- This is the core's asset pipeline. A project extension owns a separate build; see extension-asset-build.
- Do not hand-edit generated public CSS as the only change.
- Not every asset comes out of the Sass build. The CKEditor CSS is built through Build/rollup/ckeditor.js, so a change there is not picked up by a CSS build and looks like nothing happened.
- Verify generated assets are in sync when public assets are committed.
- Use lintScss for TYPO3's stylelint setup and npm -- run build-css for a focused CSS build while iterating.

## CSS Component Structure
Hints:
- The backend stylesheet is a set of bundles. Each top-level entry file under Build/Sources/Sass/ without a leading underscore — backend.scss, dashboard.scss, adminpanel.scss, form.scss, workspace.scss and a few more — compiles to one generated public CSS file. Everything else is a partial named _name.scss and reaches a bundle only through an @import.
- A partial is compiled only once something imports it: there is no glob and no index file. That is the step a new partial is forgotten at — the file exists, the Sass build passes, and none of it is in the output. Wire a foundational, reusable component into _minimal.scss and an app-specific one into backend.scss or whichever bundle owns the feature.
- The backend bundle has two layers. _minimal.scss is the base — the Bootstrap foundation plus TYPO3's own foundational partials, component/buttons, badges, panel, table, nav, modal and the scaffold/* layout. backend.scss is the application layer: it imports _minimal, then the backend-specific partials, the element/* custom-element styles and the typo3/* glue.
- The folders under Build/Sources/Sass/ each own a concern: component/ holds one partial per component, component/forms/ the form controls, component/scaffold/ the topbar, toolbar, module menu and sidebar, element/ the custom elements named after their host element, dashboard/ and module/ the area styles, variables/ and mixins/ the tokens and helpers. libs/ and typo3/ are third-party and legacy glue — no new component styles go there.
- Prefer focused component partials in the existing Sass structure.
- Name a partial after the class root it owns, _badges.scss for .badge. One partial owns one component; a component spread across partials is one nobody can find.
- Keep selectors close to the owning UI component.
- Use forms, scaffold, dashboard, and element folders for their owning concerns instead of creating broad global styles.
- Document a component's canonical markup in a // Markup: block at the top of its partial, and let the styleguide demo mirror that markup.
- The Sass layer uses @import, not the Dart Sass @use/@forward module system. Follow the existing import style and ordering rather than introducing the newer one in one file.

## Styleguide Demos for CSS Components
Hints:
- All CSS components must be represented with demos in the styleguide extension.
- New CSS components need a matching styleguide demo.
- Changed CSS components should update an existing styleguide demo or add one when no demo exists.
- Backend component demos usually live below typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/.
- A demo covers what a reviewer would otherwise have to build themselves: the variants, the states, the sizes, with and without an icon, empty and disabled and mid-interaction, in both color schemes, and in RTL where the layout is direction-sensitive. A demo of the default state only shows the case nobody was worried about.
Worked example: typo3/sysext/styleguide — typo3_reference_list for what it demonstrates and where an installation has it.

## Web Components and Element Styles
Hints:
- Styles for TYPO3 custom elements should start at the custom element host selector.
- Keep custom element Sass below Build/Sources/Sass/element/ when the style belongs to a web component.
- Use CSS custom properties, ::part(...), slots, and explicit host attributes as stable styling boundaries.
- Do not style arbitrary internal DOM depth when a host selector, part, slot, or custom property can express the contract.

## CSS Class Naming
Hints:
- Use existing component naming conventions from nearby Sass files.
- TYPO3 backend component classes usually use a short component root plus hyphenated elements or variants, for example .panel-heading, .toolbar-item, or .module-docheader.
- Variants should customize the base component through custom properties whenever possible.
- Do not introduce a new naming system such as BEM-style block__element--modifier unless the surrounding component already uses it.
- Variants and sizes append a suffix to the root — .btn-sm, .card-size-large, .table-fit — and should mainly set the custom properties the base component consumes rather than duplicating a full rule set per variant.
- State classes are explicit and consistent: .active, .disabled, .selected, and the .is-* and .has-* forms.
- Avoid a generic name that can collide globally. There is one stylesheet and no scoping, so a .header or a .content in a component partial is a name taken from everybody.
- Use t3js-* classes only as JavaScript hooks and keep them separate from visual styling selectors.

What matched above is a guess at your words. The rest of these domains, requestable by id:
- public-assets — Public Assets and the Publish Step (PHP)
- css-browser-target — CSS Browser Target (Backend CSS)
- css-minimal-reusable-components — Minimal CSS and Reusable Components (Backend CSS)
- css-tokens-specificity — CSS Tokens and Specificity (Backend CSS)
- css-state-attributes — State Attributes and Semantic Selectors (Backend CSS)
- css-color-surface-tokens — Color and Surface Tokens (Backend CSS)
- css-shadow-layering — Shadow Tokens and Layering (Backend CSS)
- css-z-index-layering — Z-Index and Overlay Layering (Backend CSS)
- css-motion-transitions — Motion and Transitions (Backend CSS)
- css-container-queries — Container Queries and Responsive Components (Backend CSS)
- css-light-dark-mode — Light and Dark Mode CSS (Backend CSS)
- css-rtl-logical-properties — RTL and Logical CSS Properties (Backend CSS)
- css-bootstrap-transition — Bootstrap Transition in Backend CSS (Backend CSS)
- css-accessibility-states — CSS Accessibility, Contrast, and States (Backend CSS)
- css-icon-text-layout-stability — Icons, Text, and Layout Stability (Backend CSS)
- browser-test-accessibility — Checking Accessibility and Contrast From the Same Spec (PHP)
```

Data:

```json
{
    "task": "sass build",
    "paths": [],
    "scopes": [],
    "targetVersion": 15,
    "targetVersions": [
        15
    ],
    "domains": [
        "css"
    ],
    "withheldCategories": [],
    "hints": [
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "PHP",
            "scope": "extension",
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
                {
                    "text": "The public-assets hint covers how Resources/Public files are published and referenced. The extension-declarative-files hint covers Configuration/JavaScriptModules.php for backend JavaScript import maps; neither implies a particular bundler.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "For a patch to the TYPO3 backend itself, css-source-build-boundaries and backend-typescript describe the core's source trees and generated pairs; those paths and commands do not transfer to an extension.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
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
                {
                    "text": "Do not hand-edit generated public CSS as the only change.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Not every asset comes out of the Sass build. The CKEditor CSS is built through Build/rollup/ckeditor.js, so a change there is not picked up by a CSS build and looks like nothing happened.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Verify generated assets are in sync when public assets are committed.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use lintScss for TYPO3's stylelint setup and npm -- run build-css for a focused CSS build while iterating.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "css-components",
            "title": "CSS Component Structure",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "The backend stylesheet is a set of bundles. Each top-level entry file under Build/Sources/Sass/ without a leading underscore — backend.scss, dashboard.scss, adminpanel.scss, form.scss, workspace.scss and a few more — compiles to one generated public CSS file. Everything else is a partial named _name.scss and reaches a bundle only through an @import.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A partial is compiled only once something imports it: there is no glob and no index file. That is the step a new partial is forgotten at — the file exists, the Sass build passes, and none of it is in the output. Wire a foundational, reusable component into _minimal.scss and an app-specific one into backend.scss or whichever bundle owns the feature.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The backend bundle has two layers. _minimal.scss is the base — the Bootstrap foundation plus TYPO3's own foundational partials, component/buttons, badges, panel, table, nav, modal and the scaffold/* layout. backend.scss is the application layer: it imports _minimal, then the backend-specific partials, the element/* custom-element styles and the typo3/* glue.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The folders under Build/Sources/Sass/ each own a concern: component/ holds one partial per component, component/forms/ the form controls, component/scaffold/ the topbar, toolbar, module menu and sidebar, element/ the custom elements named after their host element, dashboard/ and module/ the area styles, variables/ and mixins/ the tokens and helpers. libs/ and typo3/ are third-party and legacy glue — no new component styles go there.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Prefer focused component partials in the existing Sass structure.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Name a partial after the class root it owns, _badges.scss for .badge. One partial owns one component; a component spread across partials is one nobody can find.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep selectors close to the owning UI component.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use forms, scaffold, dashboard, and element folders for their owning concerns instead of creating broad global styles.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Document a component's canonical markup in a // Markup: block at the top of its partial, and let the styleguide demo mirror that markup.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The Sass layer uses @import, not the Dart Sass @use/@forward module system. Follow the existing import style and ordering rather than introducing the newer one in one file.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "css-styleguide-demos",
            "title": "Styleguide Demos for CSS Components",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "All CSS components must be represented with demos in the styleguide extension.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "New CSS components need a matching styleguide demo.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Changed CSS components should update an existing styleguide demo or add one when no demo exists.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Backend component demos usually live below typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A demo covers what a reviewer would otherwise have to build themselves: the variants, the states, the sizes, with and without an icon, empty and disabled and mid-interaction, in both color schemes, and in RTL where the layout is direction-sensitive. A demo of the default state only shows the case nobody was worried about.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "css-web-components",
            "title": "Web Components and Element Styles",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Styles for TYPO3 custom elements should start at the custom element host selector.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep custom element Sass below Build/Sources/Sass/element/ when the style belongs to a web component.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use CSS custom properties, ::part(...), slots, and explicit host attributes as stable styling boundaries.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Do not style arbitrary internal DOM depth when a host selector, part, slot, or custom property can express the contract.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "css-class-naming",
            "title": "CSS Class Naming",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Use existing component naming conventions from nearby Sass files.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "TYPO3 backend component classes usually use a short component root plus hyphenated elements or variants, for example .panel-heading, .toolbar-item, or .module-docheader.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Variants should customize the base component through custom properties whenever possible.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Do not introduce a new naming system such as BEM-style block__element--modifier unless the surrounding component already uses it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Variants and sizes append a suffix to the root — .btn-sm, .card-size-large, .table-fit — and should mainly set the custom properties the base component consumes rather than duplicating a full rule set per variant.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "State classes are explicit and consistent: .active, .disabled, .selected, and the .is-* and .has-* forms.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Avoid a generic name that can collide globally. There is one stylesheet and no scoping, so a .header or a .content in a component partial is a name taken from everybody.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use t3js-* classes only as JavaScript hooks and keep them separate from visual styling selectors.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        }
    ],
    "availableHints": [
        {
            "id": "public-assets",
            "title": "Public Assets and the Publish Step",
            "category": "PHP"
        },
        {
            "id": "css-browser-target",
            "title": "CSS Browser Target",
            "category": "Backend CSS"
        },
        {
            "id": "css-minimal-reusable-components",
            "title": "Minimal CSS and Reusable Components",
            "category": "Backend CSS"
        },
        {
            "id": "css-tokens-specificity",
            "title": "CSS Tokens and Specificity",
            "category": "Backend CSS"
        },
        {
            "id": "css-state-attributes",
            "title": "State Attributes and Semantic Selectors",
            "category": "Backend CSS"
        },
        {
            "id": "css-color-surface-tokens",
            "title": "Color and Surface Tokens",
            "category": "Backend CSS"
        },
        {
            "id": "css-shadow-layering",
            "title": "Shadow Tokens and Layering",
            "category": "Backend CSS"
        },
        {
            "id": "css-z-index-layering",
            "title": "Z-Index and Overlay Layering",
            "category": "Backend CSS"
        },
        {
            "id": "css-motion-transitions",
            "title": "Motion and Transitions",
            "category": "Backend CSS"
        },
        {
            "id": "css-container-queries",
            "title": "Container Queries and Responsive Components",
            "category": "Backend CSS"
        },
        {
            "id": "css-light-dark-mode",
            "title": "Light and Dark Mode CSS",
            "category": "Backend CSS"
        },
        {
            "id": "css-rtl-logical-properties",
            "title": "RTL and Logical CSS Properties",
            "category": "Backend CSS"
        },
        {
            "id": "css-bootstrap-transition",
            "title": "Bootstrap Transition in Backend CSS",
            "category": "Backend CSS"
        },
        {
            "id": "css-accessibility-states",
            "title": "CSS Accessibility, Contrast, and States",
            "category": "Backend CSS"
        },
        {
            "id": "css-icon-text-layout-stability",
            "title": "Icons, Text, and Layout Stability",
            "category": "Backend CSS"
        },
        {
            "id": "browser-test-accessibility",
            "title": "Checking Accessibility and Contrast From the Same Spec",
            "category": "PHP"
        }
    ]
}
```

### hints: miss

Called with:

```json
{
    "task": "quantumflux"
}
```

Text:

```
Task: quantumflux
Answered for TYPO3 v15: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Hints:
No hint matched. Name a path or a more specific topic, or ask for one of the ids below.

Hints that exist in these domains, requestable by id:
- public-assets — Public Assets and the Publish Step (PHP)
- extension-asset-build — Building Assets in a Project Extension (PHP)
- authentication-permissions — Authentication and Permissions (PHP)
- backend-modules — Backend Module and Route Registration (PHP)
- caching — Caches (PHP)
- configuration-reach — Configuration Belongs to Its Reach (PHP)
- environment-variables — What TYPO3 Reads From the Environment (PHP)
- environment-placeholders — %env() in a YAML Configuration (PHP)
- environment-runtime-readers — What Reads the Environment While It Runs (PHP)
- installation-setup — What typo3 setup Takes and What It Refuses (PHP)
- console-commands — Console Commands (PHP)
- content-elements — Registering a Content Element (PHP)
- content-element-shape — What a Content Element Owns (PHP)
- content-element-preview — The Backend Preview of a Content Element (Fluid)
- datahandler-basics — DataHandler Is the Write Path for Records (PHP)
- datahandler-writing — Writing Records with a Datamap (PHP)
- datahandler-relations — What a Datamap Does to a Relation Field (PHP)
- datahandler-placement — Which Page May Hold a New Record, and Where It Lands on It (PHP)
- datahandler-seeding — Seeding Records with a Script (PHP)
- datahandler-testing — Testing DataHandler Behaviour (PHP)
- frontend-dataprocessors — Frontend DataProcessors (PHP)
- dependency-injection — Wiring a Service (PHP)
- di-service-not-found — A Service the Container Cannot Find at Runtime (PHP)
- sitepackage-initial-content — Shipping Initial Content with an Extension (PHP)
- initial-content-import-once — Why a Changed Data File Does Not Arrive (PHP)
- initial-content-references — What Survives the Import, and What Points at a Stranger (PHP)
- impexp-artifact — Writing the Export a Distribution Ships (PHP)
- extension-documentation — Documenting a Project Extension (Documentation)
- events-extension-points — Events and Extension Points (PHP)
- extbase — What Extbase Is For, and When It Is Not Needed (PHP)
- extbase-plugin-registration — The Two Calls That Register a Plugin (PHP)
- extbase-domain-mapping — Models, Repositories and the Table Behind Them (PHP)
- extbase-arguments — What Arrives From a Request, and What Silently Does Not (PHP)
- extbase-pagination — Paginating a List (PHP)
- extension-manifest — What Makes a Directory an Extension (PHP)
- extension-schema-sql — Declaring Tables and Columns (PHP)
- extension-declarative-files — The Files an Extension Is Configured By (PHP)
- extension-boot-files — What Still Runs at Boot, and What No Longer Does (PHP)
- content-rendering-templates — contentRenderingTemplates and Where Plugin TypoScript Lands (PHP)
- extension-repository-layout — How a Distributed Extension Repository Is Laid Out (PHP)
- extension-repository-dependencies — What Such a Repository Commits, and What It Vendors (PHP)
- extension-repository-tests — The Instance an Extension Suite Builds Itself (PHP)
- extension-repository-installation — Installing TYPO3 Beneath the Extension Repository (PHP)
- system-extension-boundaries — System Extension Boundaries (PHP)
- fal-basics — Files Are Addressed Through FAL, Not by Path (PHP)
- fal-storages-drivers — Storages and the Drivers Behind Them (PHP)
- fal-reading — Getting a File Object, and Its Metadata (PHP)
- fal-writing — Putting a File Into a Storage (PHP)
- fal-testing — Covering FAL Behaviour (PHP)
- fal-processing — Which Processor Claims a File, and What Runs Below It (PHP)
- form-framework — EXT:form Configuration and Runtime (PHP)
- icon-usage — Rendering and Registering Icons (PHP)
- site-label-language — Core Labels on a Non-English Site (Labels)
- page-cache-flushing — Which Caches a Change Invalidates, and What Clears the Rest (Fluid)
- persistence-reading — Reading Records, and What Is Hidden From the Query (PHP)
- installation-boot — Booting the Installation a Project Repository Declares (PHP)
- project-repository-layout — How a TYPO3 Project Repository Is Laid Out (PHP)
- project-build-and-scripts — Build/, the Scripts, and What Is Not Deployed (PHP)
- project-configuration-files — What the Installation Is Configured By (PHP)
- frontend-records — Records in the Frontend Without Extbase (TypoScript)
- record-routing — Routing a Record Detail View (PHP)
- record-page-title — The Title of a Record Detail Page (PHP)
- routing-request-handling — Routing, Middleware, and Request Handling (PHP)
- security-sinks — Following a Value to Its Sink (PHP)
- tca-formengine — TCA, FormEngine, and Backend Forms (PHP)
- tca-schema-api — TCA Schema API (PHP)
- formdata-providers — FormEngine Data Providers (PHP)
- core-tests — Writing Core Tests (PHP)
- project-extension-tests — Setting a Test Suite Up in an Extension (PHP)
- extension-test-extensions — Which Extensions a Functional Test Loads (PHP)
- extension-test-site — Writing a Site Configuration in a Test (PHP)
- extension-test-frontend-request — Asserting a Frontend Response in a Test (PHP)
- browser-tests — Browser Tests with Playwright (PHP)
- browser-test-accessibility — Checking Accessibility and Contrast From the Same Spec (PHP)
- browser-tests-outside-core — The Site a Project Suite Runs Against (PHP)
- extension-static-analysis — Setting Up PHPStan for an Extension (PHP)
- unit-test-doubles — Unit Tests, Test Doubles and Data Providers in PHPUnit (PHP)
- installation-upgrade — Upgrading an Installation (PHP)
- upgrade-own-code — What No Wizard Touches (PHP)
- deprecated-apis — Deprecated APIs (PHP)
- upgrade-wizards — Upgrade Wizards (PHP)
```

Data:

```json
{
    "task": "quantumflux",
    "paths": [],
    "scopes": [],
    "targetVersion": 15,
    "targetVersions": [
        15
    ],
    "domains": [
        "php"
    ],
    "withheldCategories": [],
    "hints": [],
    "availableHints": [
        {
            "id": "public-assets",
            "title": "Public Assets and the Publish Step",
            "category": "PHP"
        },
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "PHP"
        },
        {
            "id": "authentication-permissions",
            "title": "Authentication and Permissions",
            "category": "PHP"
        },
        {
            "id": "backend-modules",
            "title": "Backend Module and Route Registration",
            "category": "PHP"
        },
        {
            "id": "caching",
            "title": "Caches",
            "category": "PHP"
        },
        {
            "id": "configuration-reach",
            "title": "Configuration Belongs to Its Reach",
            "category": "PHP"
        },
        {
            "id": "environment-variables",
            "title": "What TYPO3 Reads From the Environment",
            "category": "PHP"
        },
        {
            "id": "environment-placeholders",
            "title": "%env() in a YAML Configuration",
            "category": "PHP"
        },
        {
            "id": "environment-runtime-readers",
            "title": "What Reads the Environment While It Runs",
            "category": "PHP"
        },
        {
            "id": "installation-setup",
            "title": "What typo3 setup Takes and What It Refuses",
            "category": "PHP"
        },
        {
            "id": "console-commands",
            "title": "Console Commands",
            "category": "PHP"
        },
        {
            "id": "content-elements",
            "title": "Registering a Content Element",
            "category": "PHP"
        },
        {
            "id": "content-element-shape",
            "title": "What a Content Element Owns",
            "category": "PHP"
        },
        {
            "id": "content-element-preview",
            "title": "The Backend Preview of a Content Element",
            "category": "Fluid"
        },
        {
            "id": "datahandler-basics",
            "title": "DataHandler Is the Write Path for Records",
            "category": "PHP"
        },
        {
            "id": "datahandler-writing",
            "title": "Writing Records with a Datamap",
            "category": "PHP"
        },
        {
            "id": "datahandler-relations",
            "title": "What a Datamap Does to a Relation Field",
            "category": "PHP"
        },
        {
            "id": "datahandler-placement",
            "title": "Which Page May Hold a New Record, and Where It Lands on It",
            "category": "PHP"
        },
        {
            "id": "datahandler-seeding",
            "title": "Seeding Records with a Script",
            "category": "PHP"
        },
        {
            "id": "datahandler-testing",
            "title": "Testing DataHandler Behaviour",
            "category": "PHP"
        },
        {
            "id": "frontend-dataprocessors",
            "title": "Frontend DataProcessors",
            "category": "PHP"
        },
        {
            "id": "dependency-injection",
            "title": "Wiring a Service",
            "category": "PHP"
        },
        {
            "id": "di-service-not-found",
            "title": "A Service the Container Cannot Find at Runtime",
            "category": "PHP"
        },
        {
            "id": "sitepackage-initial-content",
            "title": "Shipping Initial Content with an Extension",
            "category": "PHP"
        },
        {
            "id": "initial-content-import-once",
            "title": "Why a Changed Data File Does Not Arrive",
            "category": "PHP"
        },
        {
            "id": "initial-content-references",
            "title": "What Survives the Import, and What Points at a Stranger",
            "category": "PHP"
        },
        {
            "id": "impexp-artifact",
            "title": "Writing the Export a Distribution Ships",
            "category": "PHP"
        },
        {
            "id": "extension-documentation",
            "title": "Documenting a Project Extension",
            "category": "Documentation"
        },
        {
            "id": "events-extension-points",
            "title": "Events and Extension Points",
            "category": "PHP"
        },
        {
            "id": "extbase",
            "title": "What Extbase Is For, and When It Is Not Needed",
            "category": "PHP"
        },
        {
            "id": "extbase-plugin-registration",
            "title": "The Two Calls That Register a Plugin",
            "category": "PHP"
        },
        {
            "id": "extbase-domain-mapping",
            "title": "Models, Repositories and the Table Behind Them",
            "category": "PHP"
        },
        {
            "id": "extbase-arguments",
            "title": "What Arrives From a Request, and What Silently Does Not",
            "category": "PHP"
        },
        {
            "id": "extbase-pagination",
            "title": "Paginating a List",
            "category": "PHP"
        },
        {
            "id": "extension-manifest",
            "title": "What Makes a Directory an Extension",
            "category": "PHP"
        },
        {
            "id": "extension-schema-sql",
            "title": "Declaring Tables and Columns",
            "category": "PHP"
        },
        {
            "id": "extension-declarative-files",
            "title": "The Files an Extension Is Configured By",
            "category": "PHP"
        },
        {
            "id": "extension-boot-files",
            "title": "What Still Runs at Boot, and What No Longer Does",
            "category": "PHP"
        },
        {
            "id": "content-rendering-templates",
            "title": "contentRenderingTemplates and Where Plugin TypoScript Lands",
            "category": "PHP"
        },
        {
            "id": "extension-repository-layout",
            "title": "How a Distributed Extension Repository Is Laid Out",
            "category": "PHP"
        },
        {
            "id": "extension-repository-dependencies",
            "title": "What Such a Repository Commits, and What It Vendors",
            "category": "PHP"
        },
        {
            "id": "extension-repository-tests",
            "title": "The Instance an Extension Suite Builds Itself",
            "category": "PHP"
        },
        {
            "id": "extension-repository-installation",
            "title": "Installing TYPO3 Beneath the Extension Repository",
            "category": "PHP"
        },
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP"
        },
        {
            "id": "fal-basics",
            "title": "Files Are Addressed Through FAL, Not by Path",
            "category": "PHP"
        },
        {
            "id": "fal-storages-drivers",
            "title": "Storages and the Drivers Behind Them",
            "category": "PHP"
        },
        {
            "id": "fal-reading",
            "title": "Getting a File Object, and Its Metadata",
            "category": "PHP"
        },
        {
            "id": "fal-writing",
            "title": "Putting a File Into a Storage",
            "category": "PHP"
        },
        {
            "id": "fal-testing",
            "title": "Covering FAL Behaviour",
            "category": "PHP"
        },
        {
            "id": "fal-processing",
            "title": "Which Processor Claims a File, and What Runs Below It",
            "category": "PHP"
        },
        {
            "id": "form-framework",
            "title": "EXT:form Configuration and Runtime",
            "category": "PHP"
        },
        {
            "id": "icon-usage",
            "title": "Rendering and Registering Icons",
            "category": "PHP"
        },
        {
            "id": "site-label-language",
            "title": "Core Labels on a Non-English Site",
            "category": "Labels"
        },
        {
            "id": "page-cache-flushing",
            "title": "Which Caches a Change Invalidates, and What Clears the Rest",
            "category": "Fluid"
        },
        {
            "id": "persistence-reading",
            "title": "Reading Records, and What Is Hidden From the Query",
            "category": "PHP"
        },
        {
            "id": "installation-boot",
            "title": "Booting the Installation a Project Repository Declares",
            "category": "PHP"
        },
        {
            "id": "project-repository-layout",
            "title": "How a TYPO3 Project Repository Is Laid Out",
            "category": "PHP"
        },
        {
            "id": "project-build-and-scripts",
            "title": "Build/, the Scripts, and What Is Not Deployed",
            "category": "PHP"
        },
        {
            "id": "project-configuration-files",
            "title": "What the Installation Is Configured By",
            "category": "PHP"
        },
        {
            "id": "frontend-records",
            "title": "Records in the Frontend Without Extbase",
            "category": "TypoScript"
        },
        {
            "id": "record-routing",
            "title": "Routing a Record Detail View",
            "category": "PHP"
        },
        {
            "id": "record-page-title",
            "title": "The Title of a Record Detail Page",
            "category": "PHP"
        },
        {
            "id": "routing-request-handling",
            "title": "Routing, Middleware, and Request Handling",
            "category": "PHP"
        },
        {
            "id": "security-sinks",
            "title": "Following a Value to Its Sink",
            "category": "PHP"
        },
        {
            "id": "tca-formengine",
            "title": "TCA, FormEngine, and Backend Forms",
            "category": "PHP"
        },
        {
            "id": "tca-schema-api",
            "title": "TCA Schema API",
            "category": "PHP"
        },
        {
            "id": "formdata-providers",
            "title": "FormEngine Data Providers",
            "category": "PHP"
        },
        {
            "id": "core-tests",
            "title": "Writing Core Tests",
            "category": "PHP"
        },
        {
            "id": "project-extension-tests",
            "title": "Setting a Test Suite Up in an Extension",
            "category": "PHP"
        },
        {
            "id": "extension-test-extensions",
            "title": "Which Extensions a Functional Test Loads",
            "category": "PHP"
        },
        {
            "id": "extension-test-site",
            "title": "Writing a Site Configuration in a Test",
            "category": "PHP"
        },
        {
            "id": "extension-test-frontend-request",
            "title": "Asserting a Frontend Response in a Test",
            "category": "PHP"
        },
        {
            "id": "browser-tests",
            "title": "Browser Tests with Playwright",
            "category": "PHP"
        },
        {
            "id": "browser-test-accessibility",
            "title": "Checking Accessibility and Contrast From the Same Spec",
            "category": "PHP"
        },
        {
            "id": "browser-tests-outside-core",
            "title": "The Site a Project Suite Runs Against",
            "category": "PHP"
        },
        {
            "id": "extension-static-analysis",
            "title": "Setting Up PHPStan for an Extension",
            "category": "PHP"
        },
        {
            "id": "unit-test-doubles",
            "title": "Unit Tests, Test Doubles and Data Providers in PHPUnit",
            "category": "PHP"
        },
        {
            "id": "installation-upgrade",
            "title": "Upgrading an Installation",
            "category": "PHP"
        },
        {
            "id": "upgrade-own-code",
            "title": "What No Wizard Touches",
            "category": "PHP"
        },
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs",
            "category": "PHP"
        },
        {
            "id": "upgrade-wizards",
            "title": "Upgrade Wizards",
            "category": "PHP"
        }
    ]
}
```
