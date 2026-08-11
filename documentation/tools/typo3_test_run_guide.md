# `typo3_test_run_guide`

Say what this core checkout needs before a test can run at all, and which
Build/Scripts/runTests.sh commands to run once it can. Ask it before checking
for vendor/bin/phpunit by hand: the suites run in containers, so the shell's PHP
is not the interpreter they run under and a missing vendor directory means
considerably less than it looks like. Pass the changed paths and the answer is
narrowed to the suites that can actually fail on them — a Sass-only change gets
the CSS suites, not the PHP ones. Which suites the script offers changes between
majors, so a suite that branch does not have is left out rather than handed over
as a command. The script belongs to the core repository, so paths that read as a
project or third-party extension get no suite at all rather than commands that
cannot run there. Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# Test or script topic, for example functional, phpstan, TypeScript, composer,
# or CGL.
query: string  # optional
# The changed file paths, as they are in the repository they belong to. Given,
# only suites touching their domains are returned. Each path is placed on its
# own: one outside the core narrows nothing and is named in the answer, because
# runTests.sh is not in its repository.
paths: [string]  # optional
# The TYPO3 version the commands have to run on, for example "13.4" or "14".
# Suites that branch's runTests.sh does not have are left out. Defaults to the
# version of the installation this server was started in; where there is none,
# every suite is listed.
targetVersion: string  # optional
```

## Answers with

```yaml
query: string or null  # optional
# The paths the answer was narrowed by, given ones and ones named in the query.
paths: [string]  # optional
# Which kind of work each path is. Only core paths can run a suite: runTests.sh
# is not in a project or an extension repository, so the others are named in the
# answer and narrow nothing.
scopes:
  - path: string
    # One of: core, uncertain, project, extension. Which kind of work this
    # answer is for: core, a patch to the TYPO3 core itself; project, the site
    # repository around an installation; extension, a package in it, whether a
    # sitepackage or a third-party one; or uncertain, which means nothing in the
    # call placed the work and what came back is the core's own.
    scope: string
# Domains those paths touch. Empty means nothing was narrowed.
domains: [string]  # optional
suites:
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
invocation:
  # What has to be true before any suite runs: the container the script starts,
  # and the vendor/ and bin/ the checkout may not have. This is the question a
  # caller holds at the moment it starts checking for vendor/bin/phpunit by
  # hand, and the shell's PHP is not the interpreter the answer is about.
  preconditions: [string]
  notes: [string]
  options:
    - option: string
      description: string
  examples:
    - purpose: string
      command: string
```

## Answered

Derived by `bin/cli tools:index`, and `bin/cli tools:check` holds it — the
same as everything above this heading. This tool reads nothing an installation
contains: what reaches its answer is the bundled knowledge and which TYPO3
major the caller is on, so what comes back is written down rather than recorded
from one machine's checkout. Answered against the core checkout this repository
writes below .fixtures/, declaring TYPO3 14.3.0.

### runTests: all

Called with:

```json
{}
```

Text:

```
## Before a suite can run
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.

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

PHP functional tests, sqlite by default.
Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.

## cgl
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s cgl`
Targeted run while iterating:
`CI=true ./Build/Scripts/runTests.sh -s cgl -n`

Checks and fixes coding guideline issues for all core PHP files.
Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.

## cglGit
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s cglGit`
Targeted run while iterating:
`CI=true ./Build/Scripts/runTests.sh -s cgl -n`

Checks and fixes coding guideline issues in the latest committed patch.
Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.

## lintPhp
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintPhp`

PHP syntax linting.
Use for broad PHP syntax confidence after touching many PHP files.

## phpstan
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s phpstan`

Static analysis with phpstan.
Use for type-sensitive PHP changes and API contract changes.

## lintServicesYaml
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintServicesYaml`

Lints Services.yaml files with tag parsing enabled.
Use after changing dependency injection wiring in a Configuration/Services.yaml.

## lintYaml
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintYaml`

YAML linting for every YAML file below typo3/ except Services.yaml.
Use after changing any Configuration YAML: site set config.yaml and settings definitions, route enhancers, form setups, RTE presets. Services.yaml has its own suite, lintServicesYaml, because it needs tag parsing.

## checkIntegrityPhp
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`

Checks core PHP files against the registered integrity rules.
Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.

## checkComposer
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkComposer`

Checks the composer.json files of the system extensions for version integrity.
Use after editing any composer.json, for example when adding a dependency between system extensions.

## checkIntegritySetLabels
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkIntegritySetLabels`

Checks the labels.xlf integrity of the site sets.
Use after adding or changing a Configuration/Sets/<Set>/labels.xlf. It is the purpose-built check for site set labels; checkIntegrityXliff does not replace it.

## lintHtml
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintHtml`

Whitespace and EditorConfig linting of the templates below typo3/sysext/*/Resources/Private.
Use after changing a Fluid template, partial, or layout.

## checkIntegrityXliff
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkIntegrityXliff`

Checks all .xlf files for validity and deprecated usages.
Use after adding, changing, or retiring XLF labels.

## normalizeXliff
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s normalizeXliff`

Normalizes .xlf files (formatting, attribute order).
Use after editing XLF files, so the diff carries no formatting noise. Add `-n` to only report.

## checkRst
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkRst`

Checks .rst files for integrity.
Use for every changelog entry and other ReST documentation change.

## checkExtensionScannerRst
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`

Verifies that all .rst files referenced by the extension scanner exist.
Use when a deprecation or breaking change adds extension scanner matchers.

## build
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s build`

Frontend build for TypeScript, Sass, Contrib, and assets.
Use when backend UI assets, TypeScript, Sass, or generated assets change.

## lintScss
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintScss`

SCSS linting with TYPO3's stylelint setup.
Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.

## build-css
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`

Focused CSS build from Build/package.json.
Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.

## lintTypescript
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintTypescript`

TypeScript linting.
Use when TypeScript or JavaScript code changes.

## unitJavascript
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s unitJavascript`

JavaScript unit tests for the built backend modules.
Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output. `typo3_hint_lookup` for `javascript-unit-tests` says where the file goes, what discovers it and what it imports.

## e2e
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e`

End-to-end tests driving a real backend with Playwright.
Use for editor or administrator workflows that only break in the assembled backend.

## e2e-prepare
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e-prepare`

Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.
Use to look at a backend change in a real browser, and to run Playwright yourself against the instance. It prints the URL to open and the two commands to run the specs locally, headless and in the UI, with PLAYWRIGHT_BASE_URL already set — which is how a single spec or project is selected, since the containerised suites pass no arguments through. Enter re-runs the specs in the container, Control-C ends it.

## e2e-browser
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e-browser`

The e2e suite in Playwright's own UI, served from the container.
Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it.

## composerInstall
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s composerInstall`

Installs the PHP dependencies of the checkout it is run in, into its own vendor/ and bin/, inside the container.
Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. It needs no PHP on the host, unlike `composer install` run there.

## npm
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s npm -- <npm command>`

Dispatcher for npm commands inside the TYPO3 core build environment.
Use for npm install, audit, build, watch, and package-script tasks.

## composer
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s composer -- <composer command>`

Dispatcher for composer commands inside the TYPO3 core build environment.
Use for composer dumpautoload, require, info, and dependency tasks.

## Looking at it rather than asserting it
The suites above start a browser and stop there. The rest is one call away — typo3_rule_lookup with documentId "any/testing/browser-check", which needs no resource list.
- The instance `-s e2e-prepare` installs is a styleguide and carries no content beyond the components it demonstrates. Where the defect needs content, the installation that has it is the one to look at, and a browser in a container reaches a running DDEV site over `ddev_default` rather than over `host.docker.internal`.
- Where the harness and its screenshots go: `Build/typo3temp/` is not ignored, so one written there lands in the next commit.

## Invoking runTests.sh
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.
- `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.

Options:
- `-- <phpunit arguments>` — Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method.
- `-d sqlite|mariadb|mysql|postgres` — Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour.
- `-a mysqli|pdo_mysql` — Database driver, only with `-s functional` on mysql or mariadb.
- `-i <version>` — Specific database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` — PHP minor version for the container. Only the versions the branch supports are available; check `-h`.
- `-n` — Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are.
- `-c <chunk>/<total>` — Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them.
- `-x [-y <port>]` — Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one.
- `-b docker|podman` — Container runtime. podman is the default and docker the fallback.

Examples:
- One unit test file:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One unit test method:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One functional test file on the default sqlite database:
  `CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php`
- Functional tests on mariadb, to reproduce a DBMS-specific bug:
  `CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/`
- Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`

## The whole procedure
This is the suites and how to invoke them. The rest is one call away — typo3_rule_lookup with documentId "core/testing/scripts", which needs no resource list.
- Why a suite runs against the `vendor/` and `bin/` of the directory it was started from, and what `exec: line 9: bin/phpunit: not found` means — it names phpunit rather than the directory.
- Why `-s cglGit` reports SUCCESS having read no file from a git worktree, and that `-s cgl` is the one that works from either.
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
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "lintPhp",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintPhp",
            "targeted": null,
            "description": "PHP syntax linting.",
            "whenToUse": "Use for broad PHP syntax confidence after touching many PHP files.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
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
        },
        {
            "suite": "lintServicesYaml",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintServicesYaml",
            "targeted": null,
            "description": "Lints Services.yaml files with tag parsing enabled.",
            "whenToUse": "Use after changing dependency injection wiring in a Configuration/Services.yaml.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "lintYaml",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintYaml",
            "targeted": null,
            "description": "YAML linting for every YAML file below typo3/ except Services.yaml.",
            "whenToUse": "Use after changing any Configuration YAML: site set config.yaml and settings definitions, route enhancers, form setups, RTE presets. Services.yaml has its own suite, lintServicesYaml, because it needs tag parsing.",
            "domains": [
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
            "suite": "checkComposer",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkComposer",
            "targeted": null,
            "description": "Checks the composer.json files of the system extensions for version integrity.",
            "whenToUse": "Use after editing any composer.json, for example when adding a dependency between system extensions.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "checkIntegritySetLabels",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegritySetLabels",
            "targeted": null,
            "description": "Checks the labels.xlf integrity of the site sets.",
            "whenToUse": "Use after adding or changing a Configuration/Sets/<Set>/labels.xlf. It is the purpose-built check for site set labels; checkIntegrityXliff does not replace it.",
            "domains": [
                "typoscript"
            ],
            "versions": "TYPO3 v13 and newer"
        },
        {
            "suite": "lintHtml",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintHtml",
            "targeted": null,
            "description": "Whitespace and EditorConfig linting of the templates below typo3/sysext/*/Resources/Private.",
            "whenToUse": "Use after changing a Fluid template, partial, or layout.",
            "domains": [
                "fluid"
            ],
            "versions": ""
        },
        {
            "suite": "checkIntegrityXliff",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityXliff",
            "targeted": null,
            "description": "Checks all .xlf files for validity and deprecated usages.",
            "whenToUse": "Use after adding, changing, or retiring XLF labels.",
            "domains": [
                "xliff"
            ],
            "versions": "TYPO3 v14 and newer"
        },
        {
            "suite": "normalizeXliff",
            "command": "CI=true ./Build/Scripts/runTests.sh -s normalizeXliff",
            "targeted": null,
            "description": "Normalizes .xlf files (formatting, attribute order).",
            "whenToUse": "Use after editing XLF files, so the diff carries no formatting noise. Add `-n` to only report.",
            "domains": [
                "xliff"
            ],
            "versions": "TYPO3 v14 and newer"
        },
        {
            "suite": "checkRst",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkRst",
            "targeted": null,
            "description": "Checks .rst files for integrity.",
            "whenToUse": "Use for every changelog entry and other ReST documentation change.",
            "domains": [
                "docs"
            ],
            "versions": ""
        },
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
            "suite": "build",
            "command": "CI=true ./Build/Scripts/runTests.sh -s build",
            "targeted": null,
            "description": "Frontend build for TypeScript, Sass, Contrib, and assets.",
            "whenToUse": "Use when backend UI assets, TypeScript, Sass, or generated assets change.",
            "domains": [
                "typescript",
                "css"
            ],
            "versions": "TYPO3 v13 and newer"
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
        },
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
            "suite": "lintTypescript",
            "command": "CI=true ./Build/Scripts/runTests.sh -s lintTypescript",
            "targeted": null,
            "description": "TypeScript linting.",
            "whenToUse": "Use when TypeScript or JavaScript code changes.",
            "domains": [
                "typescript"
            ],
            "versions": ""
        },
        {
            "suite": "unitJavascript",
            "command": "CI=true ./Build/Scripts/runTests.sh -s unitJavascript",
            "targeted": null,
            "description": "JavaScript unit tests for the built backend modules.",
            "whenToUse": "Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output. `typo3_hint_lookup` for `javascript-unit-tests` says where the file goes, what discovers it and what it imports.",
            "domains": [
                "typescript"
            ],
            "versions": ""
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
        },
        {
            "suite": "e2e-prepare",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-prepare",
            "targeted": null,
            "description": "Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.",
            "whenToUse": "Use to look at a backend change in a real browser, and to run Playwright yourself against the instance. It prints the URL to open and the two commands to run the specs locally, headless and in the UI, with PLAYWRIGHT_BASE_URL already set — which is how a single spec or project is selected, since the containerised suites pass no arguments through. Enter re-runs the specs in the container, Control-C ends it.",
            "domains": [
                "php",
                "typescript",
                "fluid",
                "css"
            ],
            "versions": "TYPO3 v13 and newer"
        },
        {
            "suite": "e2e-browser",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-browser",
            "targeted": null,
            "description": "The e2e suite in Playwright's own UI, served from the container.",
            "whenToUse": "Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it.",
            "domains": [
                "php",
                "typescript",
                "fluid",
                "css"
            ],
            "versions": "TYPO3 v14 and newer"
        },
        {
            "suite": "composerInstall",
            "command": "CI=true ./Build/Scripts/runTests.sh -s composerInstall",
            "targeted": null,
            "description": "Installs the PHP dependencies of the checkout it is run in, into its own vendor/ and bin/, inside the container.",
            "whenToUse": "Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. It needs no PHP on the host, unlike `composer install` run there.",
            "domains": [
                "php",
                "fluid",
                "typoscript"
            ],
            "versions": ""
        },
        {
            "suite": "npm",
            "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- <npm command>",
            "targeted": null,
            "description": "Dispatcher for npm commands inside the TYPO3 core build environment.",
            "whenToUse": "Use for npm install, audit, build, watch, and package-script tasks.",
            "domains": [
                "typescript",
                "css"
            ],
            "versions": ""
        },
        {
            "suite": "composer",
            "command": "CI=true ./Build/Scripts/runTests.sh -s composer -- <composer command>",
            "targeted": null,
            "description": "Dispatcher for composer commands inside the TYPO3 core build environment.",
            "whenToUse": "Use for composer dumpautoload, require, info, and dependency tasks.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "invocation": {
        "preconditions": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container."
        ],
        "notes": [
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.",
            "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it."
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
            {
                "option": "-a mysqli|pdo_mysql",
                "description": "Database driver, only with `-s functional` on mysql or mariadb."
            },
            {
                "option": "-i <version>",
                "description": "Specific database version, for example `-d mariadb -i 11.4`."
            },
            {
                "option": "-p <php minor>",
                "description": "PHP minor version for the container. Only the versions the branch supports are available; check `-h`."
            },
            {
                "option": "-n",
                "description": "Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are."
            },
            {
                "option": "-c <chunk>/<total>",
                "description": "Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them."
            },
            {
                "option": "-x [-y <port>]",
                "description": "Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one."
            },
            {
                "option": "-b docker|podman",
                "description": "Container runtime. podman is the default and docker the fallback."
            }
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
            {
                "purpose": "One functional test file on the default sqlite database",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php"
            },
            {
                "purpose": "Functional tests on mariadb, to reproduce a DBMS-specific bug",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/"
            },
            {
                "purpose": "Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit"
            },
            {
                "purpose": "A single npm package script",
                "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css"
            },
            {
                "purpose": "A composer command inside the core test environment",
                "command": "CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload"
            }
        ]
    }
}
```

### runTests: hit

Called with:

```json
{
    "query": "phpstan"
}
```

Text:

```
## Before a suite can run
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.

## phpstan
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s phpstan`

Static analysis with phpstan.
Use for type-sensitive PHP changes and API contract changes.

## Invoking runTests.sh
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.
- `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.

Options:
- `-- <phpunit arguments>` — Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method.
- `-d sqlite|mariadb|mysql|postgres` — Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour.
- `-a mysqli|pdo_mysql` — Database driver, only with `-s functional` on mysql or mariadb.
- `-i <version>` — Specific database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` — PHP minor version for the container. Only the versions the branch supports are available; check `-h`.
- `-n` — Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are.
- `-c <chunk>/<total>` — Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them.
- `-x [-y <port>]` — Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one.
- `-b docker|podman` — Container runtime. podman is the default and docker the fallback.

Examples:
- One unit test file:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One unit test method:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One functional test file on the default sqlite database:
  `CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php`
- Functional tests on mariadb, to reproduce a DBMS-specific bug:
  `CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/`
- Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`

## The whole procedure
This is the suites and how to invoke them. The rest is one call away — typo3_rule_lookup with documentId "core/testing/scripts", which needs no resource list.
- Why a suite runs against the `vendor/` and `bin/` of the directory it was started from, and what `exec: line 9: bin/phpunit: not found` means — it names phpunit rather than the directory.
- Why `-s cglGit` reports SUCCESS having read no file from a git worktree, and that `-s cgl` is the one that works from either.
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
        "preconditions": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container."
        ],
        "notes": [
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.",
            "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it."
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
            {
                "option": "-a mysqli|pdo_mysql",
                "description": "Database driver, only with `-s functional` on mysql or mariadb."
            },
            {
                "option": "-i <version>",
                "description": "Specific database version, for example `-d mariadb -i 11.4`."
            },
            {
                "option": "-p <php minor>",
                "description": "PHP minor version for the container. Only the versions the branch supports are available; check `-h`."
            },
            {
                "option": "-n",
                "description": "Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are."
            },
            {
                "option": "-c <chunk>/<total>",
                "description": "Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them."
            },
            {
                "option": "-x [-y <port>]",
                "description": "Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one."
            },
            {
                "option": "-b docker|podman",
                "description": "Container runtime. podman is the default and docker the fallback."
            }
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
            {
                "purpose": "One functional test file on the default sqlite database",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php"
            },
            {
                "purpose": "Functional tests on mariadb, to reproduce a DBMS-specific bug",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/"
            },
            {
                "purpose": "Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit"
            },
            {
                "purpose": "A single npm package script",
                "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css"
            },
            {
                "purpose": "A composer command inside the core test environment",
                "command": "CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload"
            }
        ]
    }
}
```

### runTests: miss

Called with:

```json
{
    "query": "quantumflux"
}
```

Text:

```
## Before a suite can run
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.

No runTests.sh suite matched "quantumflux". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".

## Invoking runTests.sh
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.
- `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.

Options:
- `-- <phpunit arguments>` — Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method.
- `-d sqlite|mariadb|mysql|postgres` — Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour.
- `-a mysqli|pdo_mysql` — Database driver, only with `-s functional` on mysql or mariadb.
- `-i <version>` — Specific database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` — PHP minor version for the container. Only the versions the branch supports are available; check `-h`.
- `-n` — Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are.
- `-c <chunk>/<total>` — Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them.
- `-x [-y <port>]` — Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one.
- `-b docker|podman` — Container runtime. podman is the default and docker the fallback.

Examples:
- One unit test file:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One unit test method:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One functional test file on the default sqlite database:
  `CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php`
- Functional tests on mariadb, to reproduce a DBMS-specific bug:
  `CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/`
- Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`

## The whole procedure
This is the suites and how to invoke them. The rest is one call away — typo3_rule_lookup with documentId "core/testing/scripts", which needs no resource list.
- Why a suite runs against the `vendor/` and `bin/` of the directory it was started from, and what `exec: line 9: bin/phpunit: not found` means — it names phpunit rather than the directory.
- Why `-s cglGit` reports SUCCESS having read no file from a git worktree, and that `-s cgl` is the one that works from either.
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
        "preconditions": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container."
        ],
        "notes": [
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.",
            "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it."
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
            {
                "option": "-a mysqli|pdo_mysql",
                "description": "Database driver, only with `-s functional` on mysql or mariadb."
            },
            {
                "option": "-i <version>",
                "description": "Specific database version, for example `-d mariadb -i 11.4`."
            },
            {
                "option": "-p <php minor>",
                "description": "PHP minor version for the container. Only the versions the branch supports are available; check `-h`."
            },
            {
                "option": "-n",
                "description": "Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are."
            },
            {
                "option": "-c <chunk>/<total>",
                "description": "Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them."
            },
            {
                "option": "-x [-y <port>]",
                "description": "Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one."
            },
            {
                "option": "-b docker|podman",
                "description": "Container runtime. podman is the default and docker the fallback."
            }
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
            {
                "purpose": "One functional test file on the default sqlite database",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php"
            },
            {
                "purpose": "Functional tests on mariadb, to reproduce a DBMS-specific bug",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/"
            },
            {
                "purpose": "Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit"
            },
            {
                "purpose": "A single npm package script",
                "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css"
            },
            {
                "purpose": "A composer command inside the core test environment",
                "command": "CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload"
            }
        ]
    }
}
```

### runTests: narrowed by paths

Called with:

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
## Before a suite can run
- Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
- A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.

Narrowed to the css domain(s) the given paths touch. Suites outside them cannot fail on this change; call again without paths to see all of them.

## build-css
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`

Focused CSS build from Build/package.json.
Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.

## e2e-browser
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e-browser`

The e2e suite in Playwright's own UI, served from the container.
Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it.

## e2e-prepare
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e-prepare`

Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.
Use to look at a backend change in a real browser, and to run Playwright yourself against the instance. It prints the URL to open and the two commands to run the specs locally, headless and in the UI, with PLAYWRIGHT_BASE_URL already set — which is how a single spec or project is selected, since the containerised suites pass no arguments through. Enter re-runs the specs in the container, Control-C ends it.

## lintScss
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s lintScss`

SCSS linting with TYPO3's stylelint setup.
Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.

## Looking at it rather than asserting it
The suites above start a browser and stop there. The rest is one call away — typo3_rule_lookup with documentId "any/testing/browser-check", which needs no resource list.
- The instance `-s e2e-prepare` installs is a styleguide and carries no content beyond the components it demonstrates. Where the defect needs content, the installation that has it is the one to look at, and a browser in a container reaches a running DDEV site over `ddev_default` rather than over `host.docker.internal`.
- Where the harness and its screenshots go: `Build/typo3temp/` is not ignored, so one written there lands in the next commit.

## Invoking runTests.sh
- Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.
- Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
- While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
- The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.
- `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.

Options:
- `-- <phpunit arguments>` — Passthrough to phpunit. A path limits the run to one test file, `--filter <methodName>` to one test method.
- `-d sqlite|mariadb|mysql|postgres` — Database for `-s functional` and for whichever installer suite the branch carries. sqlite is the default and the fastest; use mariadb or postgres to reproduce DBMS-specific behaviour.
- `-a mysqli|pdo_mysql` — Database driver, only with `-s functional` on mysql or mariadb.
- `-i <version>` — Specific database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` — PHP minor version for the container. Only the versions the branch supports are available; check `-h`.
- `-n` — Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are.
- `-c <chunk>/<total>` — Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them.
- `-x [-y <port>]` — Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one.
- `-b docker|podman` — Container runtime. podman is the default and docker the fallback.

Examples:
- One unit test file:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One unit test method:
  `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php`
- One functional test file on the default sqlite database:
  `CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php`
- Functional tests on mariadb, to reproduce a DBMS-specific bug:
  `CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/`
- Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`

## The whole procedure
This is the suites and how to invoke them. The rest is one call away — typo3_rule_lookup with documentId "core/testing/scripts", which needs no resource list.
- Why a suite runs against the `vendor/` and `bin/` of the directory it was started from, and what `exec: line 9: bin/phpunit: not found` means — it names phpunit rather than the directory.
- Why `-s cglGit` reports SUCCESS having read no file from a git worktree, and that `-s cgl` is the one that works from either.
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
            "suite": "e2e-browser",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-browser",
            "targeted": null,
            "description": "The e2e suite in Playwright's own UI, served from the container.",
            "whenToUse": "Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it.",
            "domains": [
                "php",
                "typescript",
                "fluid",
                "css"
            ],
            "versions": "TYPO3 v14 and newer"
        },
        {
            "suite": "e2e-prepare",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-prepare",
            "targeted": null,
            "description": "Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.",
            "whenToUse": "Use to look at a backend change in a real browser, and to run Playwright yourself against the instance. It prints the URL to open and the two commands to run the specs locally, headless and in the UI, with PLAYWRIGHT_BASE_URL already set — which is how a single spec or project is selected, since the containerised suites pass no arguments through. Enter re-runs the specs in the container, Control-C ends it.",
            "domains": [
                "php",
                "typescript",
                "fluid",
                "css"
            ],
            "versions": "TYPO3 v13 and newer"
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
        "preconditions": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container."
        ],
        "notes": [
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports.",
            "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it."
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
            {
                "option": "-a mysqli|pdo_mysql",
                "description": "Database driver, only with `-s functional` on mysql or mariadb."
            },
            {
                "option": "-i <version>",
                "description": "Specific database version, for example `-d mariadb -i 11.4`."
            },
            {
                "option": "-p <php minor>",
                "description": "PHP minor version for the container. Only the versions the branch supports are available; check `-h`."
            },
            {
                "option": "-n",
                "description": "Dry run for the `cgl` suites, and for any other suite the branch lists under it: report issues without modifying files. `-h` says which ones those are."
            },
            {
                "option": "-c <chunk>/<total>",
                "description": "Split `-s functional`, and the browser suite where the branch has one, into chunks and run one of them."
            },
            {
                "option": "-x [-y <port>]",
                "description": "Enable xdebug towards a listening IDE (default port 9003) for `-s unit`, `-s functional`, and the installer suite where the branch has one."
            },
            {
                "option": "-b docker|podman",
                "description": "Container runtime. podman is the default and docker the fallback."
            }
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
            {
                "purpose": "One functional test file on the default sqlite database",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php"
            },
            {
                "purpose": "Functional tests on mariadb, to reproduce a DBMS-specific bug",
                "command": "CI=true ./Build/Scripts/runTests.sh -s functional -d mariadb -- typo3/sysext/impexp/Tests/Functional/"
            },
            {
                "purpose": "Coding guidelines of the latest commit only, from a normal checkout — from a git worktree it reads no file and still reports SUCCESS",
                "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit"
            },
            {
                "purpose": "A single npm package script",
                "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css"
            },
            {
                "purpose": "A composer command inside the core test environment",
                "command": "CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload"
            }
        ]
    }
}
```
