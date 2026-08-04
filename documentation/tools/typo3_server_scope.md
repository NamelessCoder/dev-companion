# `typo3_server_scope`

Orientation for this server: what it covers and at which depth, what it
deliberately does not cover, and which tool to call when. Start here when it is
unclear whether this server can answer a question at all, or which of the
lookups is the right one. Answers from: knowledge, installation.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge),
[`installation`](answer-sources.md#installation).

## Takes

Nothing.

## Answers with

```yaml
# What this server is for.
purpose: string
# The boundary statement clients receive at initialize time.
instructions: string  # optional
covers:
  - topic: string
    # How deeply the topic is covered.
    depth: string
    tools: [string]
    # Knowledge file or typo3:// resource behind the topic.
    source: string
    # One of: core, project, extension, any. Which kind of work the answers are
    # for. core: the contribution process and the scripts of that repository.
    # any: a convention that holds wherever TYPO3 is written.
    scope: string
doesNotCover:
  - topic: string
    why: string
    # What to do instead of asking this server.
    instead: string
checkoutDiscovery:  # optional
  - establish: string
    how: string
routing:
  - when: string
    call: string
# The TYPO3 versions the knowledge is bound to. A statement outside a range is
# left out when a target version is known.
versions:
  - major: integer
    # The branch that line is verified against.
    branch: string
    # lts, stable, or development.
    status: string
# Which tools are worth calling in the state this machine is in — nothing
# running answers from knowledge and packages alone. Every tool states its own
# sources at the foot of its description; this groups them the other way round.
answersFrom:
  - # installation, packages, knowledge, network or checkout.
    source: string
    # What that source is, and what it cannot answer.
    meaning: string
    # The offered tools it can answer. A tool with two sources stands under
    # both.
    tools: [string]
excludedTools:
  # The tools that are really gone, and the only reason the list is ever shorter
  # than the documented one. Empty unless the variable is set.
  names: [string]
  # Names in the variable that took nothing away: no tool answers to the name,
  # or it is one of the three this server offers whatever the variable says.
  # Each of them is in the tool list. Absent means nothing to report, which is
  # the ordinary case.
  ignored: [string]  # optional
  # Environment variable that names them.
  variable: string
installation:
  # Whether there is an installation to read at all.
  found: boolean
  # Absolute path of the installation.
  root: string or null  # optional
  # core-checkout or composer-project.
  kind: string or null  # optional
  # How it was determined: discovery (walked up from the start directory) or
  # environment (named by TYPO3_MCP_ROOT).
  via: string or null  # optional
  # Where the search started, or the configured value.
  startedFrom: string or null  # optional
  # The directories the search walked. A failure here means a layout that cannot
  # be read or a server started in the wrong place — this says which.
  searched: [string]
  # TYPO3 packages found in it.
  packageCount: integer
  # Set when a configured value could not be followed. Nothing falls back to a
  # discovered installation.
  misconfiguration: string or null  # optional
  console:
    # False means every installation-backed tool answers with unsupported in
    # place of its result.
    reachable: boolean
    # ddev, php, or override.
    via: string or null  # optional
    # The PHP version it runs on, where that is known.
    php: string or null  # optional
    # The invocation, as it is run.
    command: string or null  # optional
    # Why it cannot be run. Null when it can.
    reason: string or null  # optional
    # What limits the console that was found — a project whose containers are
    # stopped is answered by an interpreter of this machine, which reaches what
    # TYPO3 assembles from its own files and not the services the project's own
    # runtime brings. Null when nothing limits it.
    caveat: string or null  # optional
  settings:  # optional
    # Environment variable that names the installation root.
    root: string
    # Environment variable that names the console command.
    console: string
```

## Answered

`typo3_project_describe` and `typo3_extension_describe` were called
`typo3_project_scope` and `typo3_extension_scope` when this was recorded, and
the routing below names them by those spellings.
Recorded on 2026-08-04 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### scope

Called with:

```json
{}
```

Text:

```
A development companion for coding agents working with TYPO3, for the three audiences that do: the core contributor, the extension author, and the site developer. It establishes the project and installation the agent is working in, supplies current, version-bound TYPO3 knowledge, and hands task-specific workflows to the skills that own them so the agent can implement, review, and verify the work. Scope answers describe what is present without treating it as correct; the knowledge and skills supply the conventions that apply, so code found in one installation is not repeated as a pattern merely because it runs. They cover how TYPO3's subsystems are used, the core's own contribution process — the rules, the Gerrit workflow, the scripts and test suites — and a searchable index of backend UI components whose contract is read from the active installation where possible. The server also answers what is registered in the installation it was started in, which no bundled snapshot could get right. Where that installation does not boot, or does not exist yet, the bundled knowledge and the installed packages answer instead, and the answer says which of them it came from and what that leaves out. Every answer says which TYPO3 versions it holds for and which of the three kinds of work it belongs to.

Covered, and how deeply. Each topic says which kind of work its answers are for: core is the contribution process and the scripts that belong to that repository, any is a convention that holds wherever TYPO3 is written. Where the source names the installation, the answer is read from the one this server was started in rather than from any snapshot.
## Contribution rules and review readiness
Curated prose. The rules a patch is judged by, not a full style guide.
Tools: typo3_rule_lookup, typo3_task_guide
Source: typo3://core/typo3-core-rules (core)
## Gerrit workflow: setup, pushing, amending, backports
Curated prose, command level. Covers the local git side; it cannot talk to the Gerrit server.
Tools: typo3_rule_lookup
Source: typo3://core/typo3-gerrit-workflow (core)
## What a Forge issue says and what was decided about it, and which other issues describe the same thing
The tracker's own API, read live. By number: subject, tracker, status, target version, the TYPO3 and PHP versions it was reported against, related issues, and the comments — the description is what the reporter saw, the comments are where a maintainer said what will happen. By words: the issues whose text matches them, each with its number, subject, tracker, status and URL, in the tracker's own order and unranked. An issue worded differently is invisible to a word search, so words that match nothing are an answer about the words and not about whether the thing was reported.
Tools: typo3_forge_lookup
Source: https://forge.typo3.org (network) (core)
## Whether a patch for an issue already exists on the review server
The anonymous Gerrit REST API, read live: change number, subject, status, target branch, review URL, and the patch set that is current with the commit it is — which is what says whether a checkout is the revision under review. A change pushed as private is not visible to it.
Tools: typo3_gerrit_lookup
Source: https://review.typo3.org (network) (core)
## Commit messages
Rules plus a working draft and check, including 72-character body wrapping. The subject and body conventions are also served without the core workflow, for a commit in a repository that has no Forge issue and no release branches: workflow="project". The same guide is exposed as the user-invoked prompt commit_message.
Tools: typo3_commit_message_guide, typo3_rule_lookup
Source: typo3://core/typo3-commit-messages (any)
## Core testing suites: runTests.sh options and targeted invocation
Every suite this knowledge base knows, with the command, the targeted form, and when to use it. The script is part of the core repository, so paths that read as a project or third-party extension get the boundary stated instead of a command.
Tools: typo3_test_run_guide, typo3_script_lookup
Source: knowledge/test-suite-hints.json, typo3://core/typo3-core-scripts (core)
## Where the upstream contribution documentation is
The official contribution workflow guide is not bundled: this names its entry points, so a question past what the local documents answer is sent to the source rather than guessed at. The local policy that sits beside it is this repository's own.
Tools: typo3_rule_lookup
Source: typo3://core/typo3-contribution-sources (core)
## Core conventions: DI, events and hooks, TCA and FormEngine, FormEngine data providers, DataHandler, routing, Fluid templates and ViewHelpers, frontend page rendering with PAGEVIEW, how a sitepackage is laid out, records in the frontend and the routing for them, registering a content element, Extbase plugins, TypoScript site sets and TSconfig, language files, upgrade wizards, frontend DataProcessors, upgrading an installation, testing strategy, assembling a test suite for a project extension, setting up static analysis for an extension, browser and accessibility tests with Playwright
Conventions per subsystem, matched by path or topic, from both angles: what a change to the subsystem has to satisfy, and how the mechanism is used. It describes how the core is built, never what your checkout contains.
Tools: typo3_hint_lookup, typo3_task_guide
Source: knowledge/hints/ (any)
## Official TYPO3 API, reference and tutorial documentation
Live lookup in the public tables of contents of TYPO3 Explained, TypoScript Explained, the TCA Reference and the Fluid ViewHelper Reference, bound to one covered documentation release. Every result keeps its canonical URL, document, version and section; pass that URL back with the same target version to read the page as text, including headings and code examples. An unreachable service is not an empty search.
Tools: typo3_documentation_lookup
Source: https://docs.typo3.org (any)
## Backend CSS architecture, design tokens, class naming
Curated prose covering the Sass sources, the token contract, and the component structure.
Tools: typo3_hint_lookup, typo3_rule_lookup
Source: knowledge/hints/backend-css.json (any)
## Backend UI components: markup, variants, custom properties
A curated searchable subset, not every CSS class in the core. When targetVersion is the active installation, installed backend CSS and JavaScript supply presence, classes and custom properties, and a matching installed styleguide example supplies markup. The bundled version-bound snapshot is the fallback and still supplies names, summaries and keywords.
Tools: typo3_component_lookup
Source: installed EXT:backend and EXT:styleguide files; knowledge/catalog/components.json as index and fallback — read from the installation being worked in, not from a bundled snapshot. (any)
## Which extensions the TYPO3 core ships, and on which versions
Every system extension of every covered TYPO3 line, by extension key and Composer package name, with what it is for and the range it is shipped on. Derived from one checkout per covered version, so it answers for an extension that is not installed — which is the case the question is asked in. A miss says the name is not a system extension there, never that it does not exist.
Tools: typo3_system_extension_lookup
Source: knowledge/catalog/system-extensions.json (any)
## Where the core keeps its own worked examples
An index rather than the examples themselves: one line per directory on what it is a reference for, the Composer package an installation can read it from, and the versions it exists on. It is what a hint is a summary of, so it is the better answer where a hint is thin or where a subject has none yet.
Tools: typo3_reference_list
Source: knowledge/catalog/references.json (any)
## Translation domains: how the domain of an XLF file is derived
Complete and computed, not a snapshot: the core's own path-to-domain rules ported here — the class that holds them is TranslationDomainMapper on one branch and TranslationDomainResolver on the next, so compare against whichever your checkout has, so any XLF path in any extension resolves — including a file that does not exist yet. Where the installation being read is older than translation domains, the full LLL:EXT: reference is the answer instead: the domain form renders nothing there.
Tools: typo3_translation_domain_lookup
Source: src/Knowledge/Catalog/TranslationDomain.php, knowledge/hints/labels.json (any)
## How current the catalogs are
Whether component contracts came from the active installation or the bundled fallback, plus the core revision, branch, and verification date behind that fallback.
Tools: typo3_catalog_scope
Source: knowledge/catalog/meta.json (any)
## What the installation you are working in supplies: backend component contracts, labels, icons, backend modules, Fluid namespaces, effective configuration, the columns TYPO3 derives for a table
Answered by that installation rather than from a snapshot, across the packages it has active — project extensions included. Component presence, classes and custom properties come from the installed backend CSS and JavaScript, with installed styleguide markup where available. Label reuse is restricted to the XLF resource used at the consuming code; an instance-wide match from another resource is discovery, not a reuse candidate. Its console does the answering wherever a command exists, and where none does — the icon registry — TYPO3 is booted in a subprocess and its container asked; package files answer where neither can be reached, or where the installation has no configuration yet and boots into a core-only failsafe container. The columns a table gets from its TCA are read the same way and have no file to fall back on: they are what the core would create, so only the core can say them. Every answer identifies its source, so a fallback stays visible.
Tools: typo3_component_lookup, typo3_label_lookup, typo3_icon_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_schema_lookup
Source: the discovered TYPO3 installation — read from the installation being worked in, not from a bundled snapshot. (any)
## The project around the installation: its extensions, its sites and their sets, its own commands, the environment it declares and what that environment runs by itself, and what one of those extensions registers
Read from the repository's files — composer.json, package.json, config/sites, and the .ddev configuration where there is one — so it answers on a fresh clone, before anything is installed or migrated. The declared commands are what a caller may run; a DDEV project also states the PHP its container runs, the hooks that fire at a stage of their own with the command each runs, and the pull recipes its database and files come from. It describes what is there and does not rate it; the one thing it volunteers is a core deprecation whose predicate is a registration file the extension ships, because nothing a caller would think to search for reaches that. Per extension: the tables its TCA defines and extends, the content elements it adds to tt_content with the Fluid template each renders through and the FlexForm each binds, its backend modules and routes, its icons, its site sets and the files core reads each set for, the form configurations it registers and the definitions they store, its service tags, its middlewares, which of the Fluid root directories it ships and the namespaces it registers globally, the shape of its Classes/ directory, and which of the registration files it ships a deprecation names. Its tables, content elements and icons come from the booted installation where there is one; the rest is read from files, and where the installation could not be booted the answer says what a file-read list leaves out.
Tools: typo3_project_scope, typo3_extension_scope
Source: the discovered project — read from the installation being worked in, not from a bundled snapshot. (any)
## What a TYPO3 version broke, deprecated, added or noted
The changelog the installed core ships, one entry per change, searched by words, type and version. It covers exactly the versions that installation has — a newer one is not in it.
Tools: typo3_changelog_lookup
Source: the discovered installation — read from the installation being worked in, not from a bundled snapshot. (any)

Query this server in English, whatever language you are speaking with the user. Its knowledge is written in English and its matching is lexical, so a query in another language reaches only the words the two happen to share and otherwise comes back empty.

Versions this knowledge is bound to:
- TYPO3 v12 (12.4, lts)
- TYPO3 v13 (13.4, lts)
- TYPO3 v14 (14.3, stable)
- TYPO3 v15 (main, development)
A statement that does not hold on all of them carries the range it holds on. Pass targetVersion to have the ones that do not apply left out; without it, the version of the installation being read decides, and where there is none nothing is filtered.

Deliberately not covered — and this list is the boundary: a subject that is not on it is in scope, so a thin answer to it is a gap in the knowledge base rather than a limit of it. Record one with typo3_feedback_record instead of going elsewhere.
## Your git state: changed files, branch, working tree, commit message, history
This server reads the directory it was started in — a core checkout as readily as an installed project — but it never runs git. What it reads are the files a package ships and, where a console answers, the container behind them. Nothing here knows what you changed, what you committed, or which branch you are on.
Instead: Ask git yourself. One "git show --name-only --format=%B HEAD" carries both things this server's own guides ask for: the changed paths typo3_test_run_guide narrows suites by, and the message typo3_commit_message_guide checks. Pass the paths to typo3_hint_lookup and typo3_task_guide for the conventions and checks that apply to them.
## PHP source as code: a method signature, whether a class or member is @internal or public API, an implementation to copy
No core sources ship here, and nothing reads a class for what it declares. Where the files a package ships are read at all it is for what they register — an icon, a namespace, an extension's own metadata — never for a signature or an annotation. Whether a removed method was public API is therefore still a question for the checkout, and it is the one that decides whether a removal is breaking.
Instead: Read the class. TCA is a different question and is answered here: typo3_schema_lookup returns the table as the installation's container assembles it, which is not what any single file says. typo3_changelog_lookup says what a version broke, deprecated or added, and typo3_hint_lookup says what the subsystem is built to.
## Which test covers a given file
Test coverage is a property of the checkout, and no index of it is bundled.
Instead: Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/, so typo3/sysext/core/Classes/DataHandling/DataHandler.php is covered by typo3/sysext/core/Tests/Unit/DataHandling/DataHandlerTest.php. Look the file up there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
## Frontend theming: the CSS and the JavaScript a website renders
Everything here about CSS and TypeScript describes the TYPO3 backend interface — its Sass sources, its --typo3-* token contract, its light and dark color schemes, its move away from Bootstrap. For a theme extension those are not a milder version of the right answer, they are the opposite of it. How a theme extension is laid out is a different question and is not excluded here: the core ships its own theme as a system extension, and the conventions it establishes — the template tree, the layout names, the backend layout files — are in the hints.
Instead: typo3_hint_lookup withholds the Backend CSS and Backend TypeScript and JavaScript hints where a task names the frontend, and returns what does transfer — Fluid, TypoScript, PHP, and the sitepackage layout. The styling itself is documented at https://docs.typo3.org.
## Whether an uncatalogued component or CSS class exists on your branch
The active installation supplies the contract for curated component entries, but the searchable index is deliberately a subset rather than every class in backend.css. Without an installation, even those entries fall back to one pinned core snapshot.
Instead: Call typo3_catalog_scope to see which source answered, then inspect the installed backend CSS or the target checkout when the class is outside the curated index.
## Gerrit review state beyond what an anonymous read answers: votes, CI results, the comments and the diff of a patch set, and anything a private change carries
The review API is read without a credential, so what a reviewer sees and what a private change contains are outside it. Nothing here writes to Gerrit at all.
Instead: typo3_gerrit_lookup answers whether a change exists, what it is called, which branch it targets, whether it is still open, and which patch set is current with the commit it is — hold that commit against your own HEAD. Votes, comments, CI and the diff of a patch set belong in the web UI; pushing and amending are git, and typo3://core/typo3-gerrit-workflow carries those.
## What someone else's extension does: the API, the options and the documentation of a package the core does not ship
Writing an extension is covered — the extension author is one of the three audiences this exists for, and the registration files, the subsystem conventions, the sitepackage layout and the test suite are all here. What is not here is the inside of somebody else's package: it has its own API, its own release cycle and its own documentation, and none of them is read by this server. Whether a name belongs to the core at all is a different question, and typo3_system_extension_lookup answers it.
Instead: Read that extension's own documentation. What an installed extension registers — yours or a third party's — is answered by typo3_extension_scope from the files it ships.
## Deciding one site's configuration: its languages, its base and the variants per environment, its error handling, and the steps that install it
A site set is a convention and is covered; what one installation writes into config/sites/<identifier>/config.yaml is that site's decision, and nothing here would make one answer to it more right than another. The file is read rather than composed — typo3_project_scope reports the sites a repository configures and the sets they depend on.
Instead: Read what the project already has with typo3_project_scope, ask typo3_hint_lookup with id=site-sets for what a set ships and how its settings resolve against a site's own, and take the configuration format and the language setup from https://docs.typo3.org/.
## Running an installation: server and container setup, deployment, backups, the editorial use of the backend
The knowledge base is about what is written into a TYPO3 project — its extensions, its configuration, its templates and its tests. Operating the installation around it is a different subject with different sources.
Instead: Use the TYPO3 documentation at https://docs.typo3.org/.

Which tool to call when:
- Asked to review, audit or assess a project, a site package or an extension — before opening the first file, because what a finding is worth depends on the version and the commands this repository has → typo3_project_scope, then typo3_task_guide for the workflow and typo3_extension_scope for each extension in scope
- Taking a Forge issue on, before believing what it describes — an issue can be stale, already fixed, or closed on a decision that is not in its description → typo3_forge_lookup for the issue and its comments, then typo3_forge_lookup again with query in the words the issue uses for the other issues describing the same thing — its relations carry only what somebody linked by hand — then typo3_gerrit_lookup with the same number for whether a patch exists already
- Reviewing a TYPO3 core patch — the current changes in a core checkout, a commit, or a change fetched from Gerrit — rather than a project or an extension → typo3_rule_lookup per obligation the diff raises, typo3_changelog_lookup for the precedent, typo3_test_run_guide with the changed paths, then typo3_commit_message_guide
- Writing a core patch: taking a Forge issue on, fixing a core bug, deprecating or removing core API, or amending a patch after review → typo3_task_guide with the paths you are changing, then typo3_hint_lookup with the same ones, typo3_test_run_guide for the suites that can fail, and typo3_rule_lookup for the Gerrit workflow
- Starting a core task and looking for the applicable conventions and checks → typo3_task_guide
- About to invent a layout, a directory structure or a test harness — the core has probably worked one out already → typo3_reference_list
- About to write backend markup or invent a CSS class name → typo3_component_lookup
- About to run tests or any other core check → typo3_test_run_guide
- Working in a concrete file and unsure about the subsystem's conventions → typo3_hint_lookup
- Needing the official API, reference or tutorial documentation for a covered TYPO3 version → typo3_documentation_lookup with several short English queries and targetVersion, then with page and the same targetVersion to read a selected result
- Writing or amending the commit message → typo3_commit_message_guide
- Pushing for review, amending a patch set, or backporting → typo3_rule_lookup with a Gerrit query
- A catalog lookup found nothing that should exist → typo3_catalog_scope, then typo3_feedback_record
- Needing the translation domain an XLF file resolves to → typo3_translation_domain_lookup with the path
- About to write user-facing text or invent a label key → typo3_label_lookup with words from the wording and the XLF resource used at the consuming code
- About to reference an icon identifier in the backend — TCA, a module, a content element wizard, a backend template. The registry is the backend's; frontend rendering has no access to it. → typo3_icon_lookup
- Needing a configuration value as it really is at runtime, not as the core ships it → typo3_configuration_lookup with the TYPO3_CONF_VARS path
- Working on a backend module and needing its registration → typo3_backend_module_lookup
- Unsure which Fluid namespace prefixes a template may use undeclared → typo3_fluid_namespace_list
- About to recommend a command, or asking what this project consists of → typo3_project_scope
- Starting work on a TYPO3 major you have not built on recently — before asking what a version changed → typo3_changelog_lookup
- Asking whether a pattern still works in a version — what nothing changed has no changelog entry, so the changelog's silence is not an answer → typo3_documentation_lookup with targetVersion and short English queries for the reference that documents the pattern, then with page to read it
- Upgrading or maintaining an installation, and needing the order of operations → typo3_task_guide, then typo3_project_scope and typo3_changelog_lookup for what this one is and what the target version changed
- Working in one extension and needing what it registers → typo3_extension_scope with its key
- About to claim that an extension is (or is not) part of the core, or to require one → typo3_system_extension_lookup
- About to write or review an ext_tables.sql, and needing to know which columns TYPO3 creates by itself → typo3_schema_lookup with the table

Found the TYPO3 installation at <installation> (core-checkout, found by walking up, from <installation>), which holds 36 packages. If that is not the installation you are working on, this server was started in the wrong directory — or set TYPO3_MCP_ROOT to the one you mean.
Its console cannot be run right now, so questions that only the installation can answer — which labels exist, which backend modules are registered — have no answer here: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Where the command that would work is known, TYPO3_MCP_CONSOLE states it, for example "ddev exec .build/bin/typo3".

Every lookup and guide is read-only. typo3_documentation_lookup reads the official, versioned manuals at docs.typo3.org; apart from that and the installation named above, nothing is fetched, executed, or looked up online.
The one exception is typo3_feedback_record, this server's only write: it creates a new markdown feedback under feedback/ and touches nothing else. Missing something that belongs here? Leave feedback about it.

Where the answers come from, which is what says whether a question can be asked at all right now. Every tool states the same thing at the foot of its own description.
## Answers from installation
The installation this server was started in, booted or asked through its console: its assembled state after every extension has had its say, and nothing at all where it cannot be reached.
Tools: typo3_server_scope, typo3_label_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_schema_lookup, typo3_backend_module_lookup, typo3_icon_lookup, typo3_extension_scope
## Answers from packages
The files the installed packages ship, read rather than executed. Answers on a fresh clone and with the containers down; what a package registers by running is not in it.
Tools: typo3_component_lookup, typo3_label_lookup, typo3_fluid_namespace_list, typo3_icon_lookup, typo3_changelog_lookup, typo3_project_scope, typo3_extension_scope, typo3_catalog_scope
## Answers from knowledge
The knowledge base inside this package. Needs nothing running, and is bound to TYPO3 versions rather than to an installation.
Tools: typo3_server_scope, typo3_rule_lookup, typo3_script_lookup, typo3_task_guide, typo3_test_run_guide, typo3_hint_lookup, typo3_component_lookup, typo3_system_extension_lookup, typo3_reference_list, typo3_translation_domain_lookup, typo3_catalog_scope, typo3_commit_message_guide
## Answers from network
A service outside this machine. An unreachable one is said out loud rather than answered as empty.
Tools: typo3_documentation_lookup, typo3_forge_lookup, typo3_gerrit_lookup
## Answers from checkout
This server's own checkout, which is why the tool offering it exists only in a standalone one.
Tools: typo3_feedback_record, typo3_feedback_list
```

Data:

```json
{
    "purpose": "A development companion for coding agents working with TYPO3, for the three audiences that do: the core contributor, the extension author, and the site developer. It establishes the project and installation the agent is working in, supplies current, version-bound TYPO3 knowledge, and hands task-specific workflows to the skills that own them so the agent can implement, review, and verify the work. Scope answers describe what is present without treating it as correct; the knowledge and skills supply the conventions that apply, so code found in one installation is not repeated as a pattern merely because it runs. They cover how TYPO3's subsystems are used, the core's own contribution process — the rules, the Gerrit workflow, the scripts and test suites — and a searchable index of backend UI components whose contract is read from the active installation where possible. The server also answers what is registered in the installation it was started in, which no bundled snapshot could get right. Where that installation does not boot, or does not exist yet, the bundled knowledge and the installed packages answer instead, and the answer says which of them it came from and what that leaves out. Every answer says which TYPO3 versions it holds for and which of the three kinds of work it belongs to.",
    "instructions": "Start every task with typo3_project_scope: the installation's TYPO3 version, the extensions that are the project's own, the sites it configures, and the commands the repository actually declares — a check you recommend that the repository does not declare is a wrong answer however sensible it sounds. typo3_task_guide then gives the workflow the task belongs to, and hands the parts that have their own workflow to the skill that owns them. The coding agent writes the patch; this server supplies the task knowledge and workflows around it. What changed, which branch you are on, and whether a path still exists are yours to read in the checkout.\n\nBefore writing backend markup or CSS classes, call typo3_component_lookup with the targetVersion. Before choosing or emitting a backend icon identifier, call typo3_icon_lookup against the installation. Before adding or rewording a label, pass the XLF resource used at the consuming code to typo3_label_lookup — a match in another resource is not reusable there. Guessed, these three fail only at runtime.\n\nWhat is a property of an installation rather than of TYPO3 is asked of that installation instead of answered from the catalogue. Answers are filtered by targetVersion or by the version read, and a statement that does not hold on every covered line carries the range it does.\n\nQuery this server in English whatever language you speak with the user: its knowledge is English and its matching lexical, so a query in another language reaches only the loanwords. Translate the subject before you call, and the answer back.\n\ntypo3_server_scope says what is covered, at which depth, by which tool, and which installation is read.",
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
        {
            "topic": "What a Forge issue says and what was decided about it, and which other issues describe the same thing",
            "depth": "The tracker's own API, read live. By number: subject, tracker, status, target version, the TYPO3 and PHP versions it was reported against, related issues, and the comments — the description is what the reporter saw, the comments are where a maintainer said what will happen. By words: the issues whose text matches them, each with its number, subject, tracker, status and URL, in the tracker's own order and unranked. An issue worded differently is invisible to a word search, so words that match nothing are an answer about the words and not about whether the thing was reported.",
            "tools": [
                "typo3_forge_lookup"
            ],
            "source": "https://forge.typo3.org (network)",
            "scope": "core"
        },
        {
            "topic": "Whether a patch for an issue already exists on the review server",
            "depth": "The anonymous Gerrit REST API, read live: change number, subject, status, target branch, review URL, and the patch set that is current with the commit it is — which is what says whether a checkout is the revision under review. A change pushed as private is not visible to it.",
            "tools": [
                "typo3_gerrit_lookup"
            ],
            "source": "https://review.typo3.org (network)",
            "scope": "core"
        },
        {
            "topic": "Commit messages",
            "depth": "Rules plus a working draft and check, including 72-character body wrapping. The subject and body conventions are also served without the core workflow, for a commit in a repository that has no Forge issue and no release branches: workflow=\"project\". The same guide is exposed as the user-invoked prompt commit_message.",
            "tools": [
                "typo3_commit_message_guide",
                "typo3_rule_lookup"
            ],
            "source": "typo3://core/typo3-commit-messages",
            "scope": "any"
        },
        {
            "topic": "Core testing suites: runTests.sh options and targeted invocation",
            "depth": "Every suite this knowledge base knows, with the command, the targeted form, and when to use it. The script is part of the core repository, so paths that read as a project or third-party extension get the boundary stated instead of a command.",
            "tools": [
                "typo3_test_run_guide",
                "typo3_script_lookup"
            ],
            "source": "knowledge/test-suite-hints.json, typo3://core/typo3-core-scripts",
            "scope": "core"
        },
        {
            "topic": "Where the upstream contribution documentation is",
            "depth": "The official contribution workflow guide is not bundled: this names its entry points, so a question past what the local documents answer is sent to the source rather than guessed at. The local policy that sits beside it is this repository's own.",
            "tools": [
                "typo3_rule_lookup"
            ],
            "source": "typo3://core/typo3-contribution-sources",
            "scope": "core"
        },
        {
            "topic": "Core conventions: DI, events and hooks, TCA and FormEngine, FormEngine data providers, DataHandler, routing, Fluid templates and ViewHelpers, frontend page rendering with PAGEVIEW, how a sitepackage is laid out, records in the frontend and the routing for them, registering a content element, Extbase plugins, TypoScript site sets and TSconfig, language files, upgrade wizards, frontend DataProcessors, upgrading an installation, testing strategy, assembling a test suite for a project extension, setting up static analysis for an extension, browser and accessibility tests with Playwright",
            "depth": "Conventions per subsystem, matched by path or topic, from both angles: what a change to the subsystem has to satisfy, and how the mechanism is used. It describes how the core is built, never what your checkout contains.",
            "tools": [
                "typo3_hint_lookup",
                "typo3_task_guide"
            ],
            "source": "knowledge/hints/",
            "scope": "any"
        },
        {
            "topic": "Official TYPO3 API, reference and tutorial documentation",
            "depth": "Live lookup in the public tables of contents of TYPO3 Explained, TypoScript Explained, the TCA Reference and the Fluid ViewHelper Reference, bound to one covered documentation release. Every result keeps its canonical URL, document, version and section; pass that URL back with the same target version to read the page as text, including headings and code examples. An unreachable service is not an empty search.",
            "tools": [
                "typo3_documentation_lookup"
            ],
            "source": "https://docs.typo3.org",
            "scope": "any"
        },
        {
            "topic": "Backend CSS architecture, design tokens, class naming",
            "depth": "Curated prose covering the Sass sources, the token contract, and the component structure.",
            "tools": [
                "typo3_hint_lookup",
                "typo3_rule_lookup"
            ],
            "source": "knowledge/hints/backend-css.json",
            "scope": "any"
        },
        {
            "topic": "Backend UI components: markup, variants, custom properties",
            "depth": "A curated searchable subset, not every CSS class in the core. When targetVersion is the active installation, installed backend CSS and JavaScript supply presence, classes and custom properties, and a matching installed styleguide example supplies markup. The bundled version-bound snapshot is the fallback and still supplies names, summaries and keywords.",
            "tools": [
                "typo3_component_lookup"
            ],
            "source": "installed EXT:backend and EXT:styleguide files; knowledge/catalog/components.json as index and fallback — read from the installation being worked in, not from a bundled snapshot.",
            "scope": "any"
        },
        {
            "topic": "Which extensions the TYPO3 core ships, and on which versions",
            "depth": "Every system extension of every covered TYPO3 line, by extension key and Composer package name, with what it is for and the range it is shipped on. Derived from one checkout per covered version, so it answers for an extension that is not installed — which is the case the question is asked in. A miss says the name is not a system extension there, never that it does not exist.",
            "tools": [
                "typo3_system_extension_lookup"
            ],
            "source": "knowledge/catalog/system-extensions.json",
            "scope": "any"
        },
        {
            "topic": "Where the core keeps its own worked examples",
            "depth": "An index rather than the examples themselves: one line per directory on what it is a reference for, the Composer package an installation can read it from, and the versions it exists on. It is what a hint is a summary of, so it is the better answer where a hint is thin or where a subject has none yet.",
            "tools": [
                "typo3_reference_list"
            ],
            "source": "knowledge/catalog/references.json",
            "scope": "any"
        },
        {
            "topic": "Translation domains: how the domain of an XLF file is derived",
            "depth": "Complete and computed, not a snapshot: the core's own path-to-domain rules ported here — the class that holds them is TranslationDomainMapper on one branch and TranslationDomainResolver on the next, so compare against whichever your checkout has, so any XLF path in any extension resolves — including a file that does not exist yet. Where the installation being read is older than translation domains, the full LLL:EXT: reference is the answer instead: the domain form renders nothing there.",
            "tools": [
                "typo3_translation_domain_lookup"
            ],
            "source": "src/Knowledge/Catalog/TranslationDomain.php, knowledge/hints/labels.json",
            "scope": "any"
        },
        {
            "topic": "How current the catalogs are",
            "depth": "Whether component contracts came from the active installation or the bundled fallback, plus the core revision, branch, and verification date behind that fallback.",
            "tools": [
                "typo3_catalog_scope"
            ],
            "source": "knowledge/catalog/meta.json",
            "scope": "any"
        },
        {
            "topic": "What the installation you are working in supplies: backend component contracts, labels, icons, backend modules, Fluid namespaces, effective configuration, the columns TYPO3 derives for a table",
            "depth": "Answered by that installation rather than from a snapshot, across the packages it has active — project extensions included. Component presence, classes and custom properties come from the installed backend CSS and JavaScript, with installed styleguide markup where available. Label reuse is restricted to the XLF resource used at the consuming code; an instance-wide match from another resource is discovery, not a reuse candidate. Its console does the answering wherever a command exists, and where none does — the icon registry — TYPO3 is booted in a subprocess and its container asked; package files answer where neither can be reached, or where the installation has no configuration yet and boots into a core-only failsafe container. The columns a table gets from its TCA are read the same way and have no file to fall back on: they are what the core would create, so only the core can say them. Every answer identifies its source, so a fallback stays visible.",
            "tools": [
                "typo3_component_lookup",
                "typo3_label_lookup",
                "typo3_icon_lookup",
                "typo3_backend_module_lookup",
                "typo3_fluid_namespace_list",
                "typo3_configuration_lookup",
                "typo3_schema_lookup"
            ],
            "source": "the discovered TYPO3 installation — read from the installation being worked in, not from a bundled snapshot.",
            "scope": "any"
        },
        {
            "topic": "The project around the installation: its extensions, its sites and their sets, its own commands, the environment it declares and what that environment runs by itself, and what one of those extensions registers",
            "depth": "Read from the repository's files — composer.json, package.json, config/sites, and the .ddev configuration where there is one — so it answers on a fresh clone, before anything is installed or migrated. The declared commands are what a caller may run; a DDEV project also states the PHP its container runs, the hooks that fire at a stage of their own with the command each runs, and the pull recipes its database and files come from. It describes what is there and does not rate it; the one thing it volunteers is a core deprecation whose predicate is a registration file the extension ships, because nothing a caller would think to search for reaches that. Per extension: the tables its TCA defines and extends, the content elements it adds to tt_content with the Fluid template each renders through and the FlexForm each binds, its backend modules and routes, its icons, its site sets and the files core reads each set for, the form configurations it registers and the definitions they store, its service tags, its middlewares, which of the Fluid root directories it ships and the namespaces it registers globally, the shape of its Classes/ directory, and which of the registration files it ships a deprecation names. Its tables, content elements and icons come from the booted installation where there is one; the rest is read from files, and where the installation could not be booted the answer says what a file-read list leaves out.",
            "tools": [
                "typo3_project_scope",
                "typo3_extension_scope"
            ],
            "source": "the discovered project — read from the installation being worked in, not from a bundled snapshot.",
            "scope": "any"
        },
        {
            "topic": "What a TYPO3 version broke, deprecated, added or noted",
            "depth": "The changelog the installed core ships, one entry per change, searched by words, type and version. It covers exactly the versions that installation has — a newer one is not in it.",
            "tools": [
                "typo3_changelog_lookup"
            ],
            "source": "the discovered installation — read from the installation being worked in, not from a bundled snapshot.",
            "scope": "any"
        }
    ],
    "doesNotCover": [
        {
            "topic": "Your git state: changed files, branch, working tree, commit message, history",
            "why": "This server reads the directory it was started in — a core checkout as readily as an installed project — but it never runs git. What it reads are the files a package ships and, where a console answers, the container behind them. Nothing here knows what you changed, what you committed, or which branch you are on.",
            "instead": "Ask git yourself. One \"git show --name-only --format=%B HEAD\" carries both things this server's own guides ask for: the changed paths typo3_test_run_guide narrows suites by, and the message typo3_commit_message_guide checks. Pass the paths to typo3_hint_lookup and typo3_task_guide for the conventions and checks that apply to them."
        },
        {
            "topic": "PHP source as code: a method signature, whether a class or member is @internal or public API, an implementation to copy",
            "why": "No core sources ship here, and nothing reads a class for what it declares. Where the files a package ships are read at all it is for what they register — an icon, a namespace, an extension's own metadata — never for a signature or an annotation. Whether a removed method was public API is therefore still a question for the checkout, and it is the one that decides whether a removal is breaking.",
            "instead": "Read the class. TCA is a different question and is answered here: typo3_schema_lookup returns the table as the installation's container assembles it, which is not what any single file says. typo3_changelog_lookup says what a version broke, deprecated or added, and typo3_hint_lookup says what the subsystem is built to."
        },
        {
            "topic": "Which test covers a given file",
            "why": "Test coverage is a property of the checkout, and no index of it is bundled.",
            "instead": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/, so typo3/sysext/core/Classes/DataHandling/DataHandler.php is covered by typo3/sysext/core/Tests/Unit/DataHandling/DataHandlerTest.php. Look the file up there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        {
            "topic": "Frontend theming: the CSS and the JavaScript a website renders",
            "why": "Everything here about CSS and TypeScript describes the TYPO3 backend interface — its Sass sources, its --typo3-* token contract, its light and dark color schemes, its move away from Bootstrap. For a theme extension those are not a milder version of the right answer, they are the opposite of it. How a theme extension is laid out is a different question and is not excluded here: the core ships its own theme as a system extension, and the conventions it establishes — the template tree, the layout names, the backend layout files — are in the hints.",
            "instead": "typo3_hint_lookup withholds the Backend CSS and Backend TypeScript and JavaScript hints where a task names the frontend, and returns what does transfer — Fluid, TypoScript, PHP, and the sitepackage layout. The styling itself is documented at https://docs.typo3.org."
        },
        {
            "topic": "Whether an uncatalogued component or CSS class exists on your branch",
            "why": "The active installation supplies the contract for curated component entries, but the searchable index is deliberately a subset rather than every class in backend.css. Without an installation, even those entries fall back to one pinned core snapshot.",
            "instead": "Call typo3_catalog_scope to see which source answered, then inspect the installed backend CSS or the target checkout when the class is outside the curated index."
        },
        {
            "topic": "Gerrit review state beyond what an anonymous read answers: votes, CI results, the comments and the diff of a patch set, and anything a private change carries",
            "why": "The review API is read without a credential, so what a reviewer sees and what a private change contains are outside it. Nothing here writes to Gerrit at all.",
            "instead": "typo3_gerrit_lookup answers whether a change exists, what it is called, which branch it targets, whether it is still open, and which patch set is current with the commit it is — hold that commit against your own HEAD. Votes, comments, CI and the diff of a patch set belong in the web UI; pushing and amending are git, and typo3://core/typo3-gerrit-workflow carries those."
        },
        {
            "topic": "What someone else's extension does: the API, the options and the documentation of a package the core does not ship",
            "why": "Writing an extension is covered — the extension author is one of the three audiences this exists for, and the registration files, the subsystem conventions, the sitepackage layout and the test suite are all here. What is not here is the inside of somebody else's package: it has its own API, its own release cycle and its own documentation, and none of them is read by this server. Whether a name belongs to the core at all is a different question, and typo3_system_extension_lookup answers it.",
            "instead": "Read that extension's own documentation. What an installed extension registers — yours or a third party's — is answered by typo3_extension_scope from the files it ships."
        },
        {
            "topic": "Deciding one site's configuration: its languages, its base and the variants per environment, its error handling, and the steps that install it",
            "why": "A site set is a convention and is covered; what one installation writes into config/sites/<identifier>/config.yaml is that site's decision, and nothing here would make one answer to it more right than another. The file is read rather than composed — typo3_project_scope reports the sites a repository configures and the sets they depend on.",
            "instead": "Read what the project already has with typo3_project_scope, ask typo3_hint_lookup with id=site-sets for what a set ships and how its settings resolve against a site's own, and take the configuration format and the language setup from https://docs.typo3.org/."
        },
        {
            "topic": "Running an installation: server and container setup, deployment, backups, the editorial use of the backend",
            "why": "The knowledge base is about what is written into a TYPO3 project — its extensions, its configuration, its templates and its tests. Operating the installation around it is a different subject with different sources.",
            "instead": "Use the TYPO3 documentation at https://docs.typo3.org/."
        }
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
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
    "routing": [
        {
            "when": "Asked to review, audit or assess a project, a site package or an extension — before opening the first file, because what a finding is worth depends on the version and the commands this repository has",
            "call": "typo3_project_scope, then typo3_task_guide for the workflow and typo3_extension_scope for each extension in scope"
        },
        {
            "when": "Taking a Forge issue on, before believing what it describes — an issue can be stale, already fixed, or closed on a decision that is not in its description",
            "call": "typo3_forge_lookup for the issue and its comments, then typo3_forge_lookup again with query in the words the issue uses for the other issues describing the same thing — its relations carry only what somebody linked by hand — then typo3_gerrit_lookup with the same number for whether a patch exists already"
        },
        {
            "when": "Reviewing a TYPO3 core patch — the current changes in a core checkout, a commit, or a change fetched from Gerrit — rather than a project or an extension",
            "call": "typo3_rule_lookup per obligation the diff raises, typo3_changelog_lookup for the precedent, typo3_test_run_guide with the changed paths, then typo3_commit_message_guide"
        },
        {
            "when": "Writing a core patch: taking a Forge issue on, fixing a core bug, deprecating or removing core API, or amending a patch after review",
            "call": "typo3_task_guide with the paths you are changing, then typo3_hint_lookup with the same ones, typo3_test_run_guide for the suites that can fail, and typo3_rule_lookup for the Gerrit workflow"
        },
        {
            "when": "Starting a core task and looking for the applicable conventions and checks",
            "call": "typo3_task_guide"
        },
        {
            "when": "About to invent a layout, a directory structure or a test harness — the core has probably worked one out already",
            "call": "typo3_reference_list"
        },
        {
            "when": "About to write backend markup or invent a CSS class name",
            "call": "typo3_component_lookup"
        },
        {
            "when": "About to run tests or any other core check",
            "call": "typo3_test_run_guide"
        },
        {
            "when": "Working in a concrete file and unsure about the subsystem's conventions",
            "call": "typo3_hint_lookup"
        },
        {
            "when": "Needing the official API, reference or tutorial documentation for a covered TYPO3 version",
            "call": "typo3_documentation_lookup with several short English queries and targetVersion, then with page and the same targetVersion to read a selected result"
        },
        {
            "when": "Writing or amending the commit message",
            "call": "typo3_commit_message_guide"
        },
        {
            "when": "Pushing for review, amending a patch set, or backporting",
            "call": "typo3_rule_lookup with a Gerrit query"
        },
        {
            "when": "A catalog lookup found nothing that should exist",
            "call": "typo3_catalog_scope, then typo3_feedback_record"
        },
        {
            "when": "Needing the translation domain an XLF file resolves to",
            "call": "typo3_translation_domain_lookup with the path"
        },
        {
            "when": "About to write user-facing text or invent a label key",
            "call": "typo3_label_lookup with words from the wording and the XLF resource used at the consuming code"
        },
        {
            "when": "About to reference an icon identifier in the backend — TCA, a module, a content element wizard, a backend template. The registry is the backend's; frontend rendering has no access to it.",
            "call": "typo3_icon_lookup"
        },
        {
            "when": "Needing a configuration value as it really is at runtime, not as the core ships it",
            "call": "typo3_configuration_lookup with the TYPO3_CONF_VARS path"
        },
        {
            "when": "Working on a backend module and needing its registration",
            "call": "typo3_backend_module_lookup"
        },
        {
            "when": "Unsure which Fluid namespace prefixes a template may use undeclared",
            "call": "typo3_fluid_namespace_list"
        },
        {
            "when": "About to recommend a command, or asking what this project consists of",
            "call": "typo3_project_scope"
        },
        {
            "when": "Starting work on a TYPO3 major you have not built on recently — before asking what a version changed",
            "call": "typo3_changelog_lookup"
        },
        {
            "when": "Asking whether a pattern still works in a version — what nothing changed has no changelog entry, so the changelog's silence is not an answer",
            "call": "typo3_documentation_lookup with targetVersion and short English queries for the reference that documents the pattern, then with page to read it"
        },
        {
            "when": "Upgrading or maintaining an installation, and needing the order of operations",
            "call": "typo3_task_guide, then typo3_project_scope and typo3_changelog_lookup for what this one is and what the target version changed"
        },
        {
            "when": "Working in one extension and needing what it registers",
            "call": "typo3_extension_scope with its key"
        },
        {
            "when": "About to claim that an extension is (or is not) part of the core, or to require one",
            "call": "typo3_system_extension_lookup"
        },
        {
            "when": "About to write or review an ext_tables.sql, and needing to know which columns TYPO3 creates by itself",
            "call": "typo3_schema_lookup with the table"
        }
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
        {
            "major": 14,
            "branch": "14.3",
            "status": "stable"
        },
        {
            "major": 15,
            "branch": "main",
            "status": "development"
        }
    ],
    "excludedTools": {
        "names": [],
        "variable": "TYPO3_MCP_EXCLUDE_TOOLS"
    },
    "answersFrom": [
        {
            "source": "installation",
            "meaning": "The installation this server was started in, booted or asked through its console: its assembled state after every extension has had its say, and nothing at all where it cannot be reached.",
            "tools": [
                "typo3_server_scope",
                "typo3_label_lookup",
                "typo3_fluid_namespace_list",
                "typo3_configuration_lookup",
                "typo3_schema_lookup",
                "typo3_backend_module_lookup",
                "typo3_icon_lookup",
                "typo3_extension_scope"
            ]
        },
        {
            "source": "packages",
            "meaning": "The files the installed packages ship, read rather than executed. Answers on a fresh clone and with the containers down; what a package registers by running is not in it.",
            "tools": [
                "typo3_component_lookup",
                "typo3_label_lookup",
                "typo3_fluid_namespace_list",
                "typo3_icon_lookup",
                "typo3_changelog_lookup",
                "typo3_project_scope",
                "typo3_extension_scope",
                "typo3_catalog_scope"
            ]
        },
        {
            "source": "knowledge",
            "meaning": "The knowledge base inside this package. Needs nothing running, and is bound to TYPO3 versions rather than to an installation.",
            "tools": [
                "typo3_server_scope",
                "typo3_rule_lookup",
                "typo3_script_lookup",
                "typo3_task_guide",
                "typo3_test_run_guide",
                "typo3_hint_lookup",
                "typo3_component_lookup",
                "typo3_system_extension_lookup",
                "typo3_reference_list",
                "typo3_translation_domain_lookup",
                "typo3_catalog_scope",
                "typo3_commit_message_guide"
            ]
        },
        {
            "source": "network",
            "meaning": "A service outside this machine. An unreachable one is said out loud rather than answered as empty.",
            "tools": [
                "typo3_documentation_lookup",
                "typo3_forge_lookup",
                "typo3_gerrit_lookup"
            ]
        },
        {
            "source": "checkout",
            "meaning": "This server's own checkout, which is why the tool offering it exists only in a standalone one.",
            "tools": [
                "typo3_feedback_record",
                "typo3_feedback_list"
            ]
        }
    ],
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
