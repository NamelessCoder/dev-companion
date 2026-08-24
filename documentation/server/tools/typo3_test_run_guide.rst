.. _typo3_test_run_guide:

``typo3_test_run_guide``
========================

Say what this core checkout needs before a test can run at all, and which
Build/Scripts/runTests.sh commands to run once it can. Ask it before checking
for vendor/bin/phpunit by hand: the suites run in containers, so the shell's PHP
is not the interpreter they run under and a missing vendor directory means
considerably less than it looks like. Pass the changed paths and the answer is
narrowed to the suites that can actually fail on them — a Sass-only change gets
the CSS suites, not the PHP ones. Every suite comes back marked by what running
it does to the checkout: a check that hands it back as it was, a change that
rewrites files, git where the suite runs `git add *` over the working tree, or
unknown where the body does not say. A task told not to change files reads that
before it pastes a command, and the frontend build is a change rather than a
check. Which suites the script offers changes between majors, so a suite that
branch does not have is left out rather than handed over as a command. The
script belongs to the core repository, so paths that read as a project or
third-party extension get no suite at all rather than commands that cannot run
there. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

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

Answers with
------------

.. code-block:: yaml

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
    # What the narrowing left out. Both empty where nothing was narrowed.
    withheld:
      # Domains no given path reached. A path landing in one of them means calling
      # again, because this answer holds for the path set it was given.
      domains: [string]
      # How many suites those domains hold on the target version. Counted rather
      # than listed: the list is what the narrowing exists to avoid.
      suites: integer
    suites:
      - suite: string
        # Full command, run from the core root.
        command: string
        # One of: check, change, git, unknown. What running the command does to the
        # checkout, read off the suite's body in Build/Scripts/runTests.sh rather
        # than by running it. The values typo3_project_describe gives a declared
        # command, plus one for the suites that run git. check: it reports and hands
        # the files back as they were, so a task told not to change files can run it
        # — installing its own node_modules or writing a cache is not a change.
        # change: it rewrites files, generated or installed. git: it runs git over
        # the working tree, so `git add *` stages what it finds, untracked files
        # included, and a suite of this kind may discard uncommitted edits first.
        # unknown: the body does not say, which is what a test suite is, because it
        # runs the core's own code.
        runs: string
        # Narrowed form for iterating on a single file or test. It can run
        # differently from command — `-s cgl -n` reports where `-s cgl` rewrites
        # — and runs above answers for command.
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

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

runTests: all
~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {}

Text:

.. code-block:: text

    ## Before a suite can run
    - Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
    - A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.
    - The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run.

    ## unit
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s unit`
    Running it: unknown — the body does not say what it does to the checkout.
    Targeted run while iterating:
    `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>`

    PHP unit tests.
    Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.

    ## functional
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s functional`
    Running it: unknown — the body does not say what it does to the checkout.
    Targeted run while iterating:
    `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`

    PHP functional tests, sqlite by default.
    Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.

    ## cgl
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s cgl`
    Running it: change — it rewrites files in the checkout.
    Targeted run while iterating:
    `CI=true ./Build/Scripts/runTests.sh -s cgl -n`

    Checks and fixes coding guideline issues for all core PHP files.
    Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.

    ## cglGit
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s cglGit`
    Running it: change — it rewrites files in the checkout.
    Targeted run while iterating:
    `CI=true ./Build/Scripts/runTests.sh -s cgl -n`

    Checks and fixes coding guideline issues in the latest committed patch.
    Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.

    ## lintPhp
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintPhp`
    Running it: check — it reports and hands the checkout back as it was.

    PHP syntax linting.
    Use for broad PHP syntax confidence after touching many PHP files.

    ## phpstan
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s phpstan`
    Running it: check — it reports and hands the checkout back as it was.

    Static analysis with phpstan.
    Use for type-sensitive PHP changes and API contract changes.

    ## lintServicesYaml
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintServicesYaml`
    Running it: check — it reports and hands the checkout back as it was.

    Lints Services.yaml files with tag parsing enabled.
    Use after changing dependency injection wiring in a Configuration/Services.yaml.

    ## lintYaml
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintYaml`
    Running it: check — it reports and hands the checkout back as it was.

    YAML linting for every YAML file below typo3/ except Services.yaml.
    Use after changing any Configuration YAML: site set config.yaml and settings definitions, route enhancers, form setups, RTE presets. Services.yaml has its own suite, lintServicesYaml, because it needs tag parsing.

    ## checkIntegrityPhp
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
    Running it: check — it reports and hands the checkout back as it was.

    Checks core PHP files against the registered integrity rules.
    Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.

    ## checkComposer
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkComposer`
    Running it: check — it reports and hands the checkout back as it was.

    Checks the composer.json files of the system extensions for version integrity.
    Use after editing any composer.json, for example when adding a dependency between system extensions.

    ## checkIntegritySetLabels
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkIntegritySetLabels`
    Running it: check — it reports and hands the checkout back as it was.

    Checks the labels.xlf integrity of the site sets.
    Use after adding or changing a Configuration/Sets/<Set>/labels.xlf. It is the purpose-built check for site set labels; checkIntegrityXliff does not replace it.

    ## lintHtml
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintHtml`
    Running it: check — it reports and hands the checkout back as it was.

    Whitespace and EditorConfig linting of the templates below typo3/sysext/*/Resources/Private.
    Use after changing a Fluid template, partial, or layout.

    ## checkIntegrityXliff
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkIntegrityXliff`
    Running it: check — it reports and hands the checkout back as it was.

    Checks all .xlf files for validity and deprecated usages.
    Use after adding, changing, or retiring XLF labels.

    ## normalizeXliff
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s normalizeXliff`
    Running it: change — it rewrites files in the checkout.

    Normalizes .xlf files (formatting, attribute order).
    Use after editing XLF files, so the diff carries no formatting noise. Add `-n` to only report.

    ## checkRst
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkRst`
    Running it: check — it reports and hands the checkout back as it was.

    Checks .rst files for integrity.
    Use for every changelog entry and other ReST documentation change.

    ## checkExtensionScannerRst
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`
    Running it: check — it reports and hands the checkout back as it was.

    Verifies that all .rst files referenced by the extension scanner exist.
    Use when a deprecation or breaking change adds extension scanner matchers.

    ## build
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s build`
    Running it: change — it rewrites files in the checkout.

    Frontend build for TypeScript, Sass, Contrib, and assets. It rewrites the committed JavaScript and CSS below typo3/sysext/*/Resources/Public/, and its npm install can rewrite Build/package-lock.json.
    Use when backend UI assets, TypeScript, Sass, or generated assets change.

    ## lintScss
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintScss`
    Running it: check — it reports and hands the checkout back as it was.

    SCSS linting with TYPO3's stylelint setup.
    Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.

    ## build-css
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
    Running it: change — it rewrites files in the checkout.

    Focused CSS build from Build/package.json.
    Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.

    ## lintTypescript
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintTypescript`
    Running it: check — it reports and hands the checkout back as it was.

    TypeScript linting.
    Use when TypeScript or JavaScript code changes.

    ## unitJavascript
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s unitJavascript`
    Running it: unknown — the body does not say what it does to the checkout.

    JavaScript unit tests for the built backend modules.
    Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output. `typo3_hint_lookup` for `javascript-unit-tests` says where the file goes, what discovers it and what it imports.

    ## checkGruntClean
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s checkGruntClean`
    Running it: git — it runs git over the working tree, so the index and uncommitted edits are at stake.

    Rebuilds the committed JavaScript and reports whether it still matches its TypeScript sources.
    Use for a change below Build/Sources/TypeScript, where the committed JavaScript has to stay in sync with its source. Its body deletes every generated .js below typo3/sysext and runs the frontend build. Then it runs `git add *` over the whole working tree and asks git status whether anything is left, so it stages every file the checkout holds, untracked ones included. Run it in a checkout whose index you can throw away, and not in one holding work of your own. A git worktree is not the way out: its gitdir sits outside the mounted directory, so the git calls fail inside the container and the suite reports failure rather than an answer.

    ## e2e
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s e2e`
    Running it: unknown — the body does not say what it does to the checkout.

    End-to-end tests driving a real backend with Playwright.
    Use for editor or administrator workflows that only break in the assembled backend. Nothing passes through to Playwright — no test path, no filter, whatever follows `--` — so the run is every spec of the project. A change to a single spec file costs the whole suite, and that run is the one a review reports.

    ## e2e-prepare
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s e2e-prepare`
    Running it: unknown — the body does not say what it does to the checkout.

    Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.
    Use to look at a backend change in a real browser. It prints the instance URL and then waits: Enter re-runs the specs in the container, Control-C ends it. That wait is a read from /dev/tty, so the suite needs a controlling terminal — CI=true does not stand in for one, and a run that has none exits at the prompt, removes the instance it just installed and still reports SUCCESS. The two local Playwright commands it prints, headless and in the UI with PLAYWRIGHT_BASE_URL already set, are for iterating by eye rather than for reporting: they run on the host, where the browsers are an `npm --prefix=Build run playwright:install` of their own that the containerised suites never need. What a review reports is the `-s e2e` run.

    ## e2e-browser
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s e2e-browser`
    Running it: unknown — the body does not say what it does to the checkout.

    The e2e suite in Playwright's own UI, served from the container.
    Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal; a run that has none removes both containers and still reports SUCCESS.

    ## composerInstall
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s composerInstall`
    Running it: change — it rewrites files in the checkout.

    Installs the PHP dependencies of the checkout it is run in, into its own vendor/ and bin/, inside the container.
    Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. Whether the lock changed since the install is what `typo3_project_describe` says, package by package; a vendor/ behind it fails the suite in classes the patch never touched. It needs no PHP on the host, unlike `composer install` run there.

    ## npm
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s npm -- <npm command>`
    Running it: unknown — the body does not say what it does to the checkout.

    Dispatcher for npm commands inside the TYPO3 core build environment.
    Use for npm install, audit, build, watch, and package-script tasks.

    ## composer
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s composer -- <composer command>`
    Running it: unknown — the body does not say what it does to the checkout.

    Dispatcher for composer commands inside the TYPO3 core build environment.
    Use for composer dumpautoload, require, info, and dependency tasks.

    ## Looking at it rather than asserting it
    The suites above start a browser and stop there. The rest is one call away — typo3_rule_lookup with documentId "any/testing/browser-check", which needs no resource list.
    - The instance `-s e2e-prepare` installs is a styleguide and carries no content beyond the components it demonstrates. Where the defect needs content, the installation that has it is the one to look at, and a browser in a container reaches a running DDEV site over `ddev_default` rather than over `host.docker.internal`.
    - Where the harness and its screenshots go: `Build/typo3temp/` is not ignored, so one written there lands in the next commit.

    ## Invoking runTests.sh
    - Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.
    - Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
    - The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file "--filter" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.
    - While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
    - The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
    - `./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.
    - A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.
    - `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.
    - A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes.

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

Data:

.. code-block:: json

    {
        "query": null,
        "paths": [],
        "scopes": [],
        "domains": [],
        "withheld": {
            "domains": [],
            "suites": 0
        },
        "suites": [
            {
                "suite": "unit",
                "command": "CI=true ./Build/Scripts/runTests.sh -s unit",
                "runs": "unknown",
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
                "runs": "unknown",
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
                "runs": "change",
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
                "runs": "change",
                "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
                "description": "Checks and fixes coding guideline issues in the latest committed patch.",
                "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. It is `Build/Scripts/cglFixMyCommit.sh` in the container, so running that script directly buys nothing and puts it on the host's PHP rather than on the one the branch pins. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
                "domains": [
                    "php"
                ],
                "versions": ""
            },
            {
                "suite": "lintPhp",
                "command": "CI=true ./Build/Scripts/runTests.sh -s lintPhp",
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "change",
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
                "runs": "check",
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
                "runs": "check",
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
                "runs": "change",
                "targeted": null,
                "description": "Frontend build for TypeScript, Sass, Contrib, and assets. It rewrites the committed JavaScript and CSS below typo3/sysext/*/Resources/Public/, and its npm install can rewrite Build/package-lock.json.",
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
                "runs": "check",
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
                "runs": "change",
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
                "runs": "check",
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
                "runs": "unknown",
                "targeted": null,
                "description": "JavaScript unit tests for the built backend modules.",
                "whenToUse": "Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output. `typo3_hint_lookup` for `javascript-unit-tests` says where the file goes, what discovers it and what it imports.",
                "domains": [
                    "typescript"
                ],
                "versions": ""
            },
            {
                "suite": "checkGruntClean",
                "command": "CI=true ./Build/Scripts/runTests.sh -s checkGruntClean",
                "runs": "git",
                "targeted": null,
                "description": "Rebuilds the committed JavaScript and reports whether it still matches its TypeScript sources.",
                "whenToUse": "Use for a change below Build/Sources/TypeScript, where the committed JavaScript has to stay in sync with its source. Its body deletes every generated .js below typo3/sysext and runs the frontend build. Then it runs `git add *` over the whole working tree and asks git status whether anything is left, so it stages every file the checkout holds, untracked ones included. Run it in a checkout whose index you can throw away, and not in one holding work of your own. A git worktree is not the way out: its gitdir sits outside the mounted directory, so the git calls fail inside the container and the suite reports failure rather than an answer.",
                "domains": [
                    "typescript"
                ],
                "versions": ""
            },
            {
                "suite": "e2e",
                "command": "CI=true ./Build/Scripts/runTests.sh -s e2e",
                "runs": "unknown",
                "targeted": null,
                "description": "End-to-end tests driving a real backend with Playwright.",
                "whenToUse": "Use for editor or administrator workflows that only break in the assembled backend. Nothing passes through to Playwright — no test path, no filter, whatever follows `--` — so the run is every spec of the project. A change to a single spec file costs the whole suite, and that run is the one a review reports.",
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
                "runs": "unknown",
                "targeted": null,
                "description": "Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.",
                "whenToUse": "Use to look at a backend change in a real browser. It prints the instance URL and then waits: Enter re-runs the specs in the container, Control-C ends it. That wait is a read from /dev/tty, so the suite needs a controlling terminal — CI=true does not stand in for one, and a run that has none exits at the prompt, removes the instance it just installed and still reports SUCCESS. The two local Playwright commands it prints, headless and in the UI with PLAYWRIGHT_BASE_URL already set, are for iterating by eye rather than for reporting: they run on the host, where the browsers are an `npm --prefix=Build run playwright:install` of their own that the containerised suites never need. What a review reports is the `-s e2e` run.",
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
                "runs": "unknown",
                "targeted": null,
                "description": "The e2e suite in Playwright's own UI, served from the container.",
                "whenToUse": "Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal; a run that has none removes both containers and still reports SUCCESS.",
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
                "runs": "change",
                "targeted": null,
                "description": "Installs the PHP dependencies of the checkout it is run in, into its own vendor/ and bin/, inside the container.",
                "whenToUse": "Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. Whether the lock changed since the install is what `typo3_project_describe` says, package by package; a vendor/ behind it fails the suite in classes the patch never touched. It needs no PHP on the host, unlike `composer install` run there.",
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
                "runs": "unknown",
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
                "runs": "unknown",
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
                "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.",
                "The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run."
            ],
            "notes": [
                "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.",
                "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
                "The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file \"--filter\" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.",
                "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
                "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
                "`./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.",
                "A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.",
                "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.",
                "A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes."
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

runTests: hit
~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "phpstan"
    }

Text:

.. code-block:: text

    ## Before a suite can run
    - Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
    - A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.
    - The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run.

    ## phpstan
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s phpstan`
    Running it: check — it reports and hands the checkout back as it was.

    Static analysis with phpstan.
    Use for type-sensitive PHP changes and API contract changes.

    ## Invoking runTests.sh
    - Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.
    - Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
    - The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file "--filter" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.
    - While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
    - The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
    - `./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.
    - A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.
    - `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.
    - A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes.

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

Data:

.. code-block:: json

    {
        "query": "phpstan",
        "paths": [],
        "scopes": [],
        "domains": [],
        "withheld": {
            "domains": [],
            "suites": 0
        },
        "suites": [
            {
                "suite": "phpstan",
                "command": "CI=true ./Build/Scripts/runTests.sh -s phpstan",
                "runs": "check",
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
                "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.",
                "The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run."
            ],
            "notes": [
                "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.",
                "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
                "The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file \"--filter\" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.",
                "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
                "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
                "`./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.",
                "A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.",
                "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.",
                "A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes."
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

runTests: miss
~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux"
    }

Text:

.. code-block:: text

    ## Before a suite can run
    - Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
    - A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.
    - The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run.

    No runTests.sh suite matched "quantumflux". Try "unit", "functional", "phpstan", "checkRst", "build", "composer", or "npm".

    ## Invoking runTests.sh
    - Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.
    - Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
    - The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file "--filter" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.
    - While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
    - The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
    - `./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.
    - A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.
    - `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.
    - A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes.

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

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "paths": [],
        "scopes": [],
        "domains": [],
        "withheld": {
            "domains": [],
            "suites": 0
        },
        "suites": [],
        "invocation": {
            "preconditions": [
                "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
                "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.",
                "The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run."
            ],
            "notes": [
                "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.",
                "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
                "The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file \"--filter\" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.",
                "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
                "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
                "`./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.",
                "A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.",
                "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.",
                "A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes."
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

runTests: narrowed by paths
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "what do I have to run",
        "paths": [
            "Build/Sources/Sass/component/_card.scss"
        ]
    }

Text:

.. code-block:: text

    ## Before a suite can run
    - Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.
    - A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.
    - The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run.

    Narrowed to the css domain(s) the given paths touch. Suites outside them cannot fail on this change; call again without paths to see all of them. No given path reached php, fluid, typoscript, xliff, docs and typescript, which leaves 22 suites out. A path landing in one of those domains means calling again.

    ## e2e-prepare
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s e2e-prepare`
    Running it: unknown — the body does not say what it does to the checkout.

    Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.
    Use to look at a backend change in a real browser. It prints the instance URL and then waits: Enter re-runs the specs in the container, Control-C ends it. That wait is a read from /dev/tty, so the suite needs a controlling terminal — CI=true does not stand in for one, and a run that has none exits at the prompt, removes the instance it just installed and still reports SUCCESS. The two local Playwright commands it prints, headless and in the UI with PLAYWRIGHT_BASE_URL already set, are for iterating by eye rather than for reporting: they run on the host, where the browsers are an `npm --prefix=Build run playwright:install` of their own that the containerised suites never need. What a review reports is the `-s e2e` run.

    ## build-css
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
    Running it: change — it rewrites files in the checkout.

    Focused CSS build from Build/package.json.
    Use while iterating on Sass/CSS changes when a full frontend build is not needed. This maps to grunt css.

    ## e2e-browser
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s e2e-browser`
    Running it: unknown — the body does not say what it does to the checkout.

    The e2e suite in Playwright's own UI, served from the container.
    Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal; a run that has none removes both containers and still reports SUCCESS.

    ## lintScss
    Command from the TYPO3 core root:
    `CI=true ./Build/Scripts/runTests.sh -s lintScss`
    Running it: check — it reports and hands the checkout back as it was.

    SCSS linting with TYPO3's stylelint setup.
    Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.

    ## Looking at it rather than asserting it
    The suites above start a browser and stop there. The rest is one call away — typo3_rule_lookup with documentId "any/testing/browser-check", which needs no resource list.
    - The instance `-s e2e-prepare` installs is a styleguide and carries no content beyond the components it demonstrates. Where the defect needs content, the installation that has it is the one to look at, and a browser in a container reaches a running DDEV site over `ddev_default` rather than over `host.docker.internal`.
    - Where the harness and its screenshots go: `Build/typo3temp/` is not ignored, so one written there lands in the next commit.

    ## Invoking runTests.sh
    - Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.
    - Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.
    - The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file "--filter" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.
    - While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.
    - The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.
    - `./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.
    - A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.
    - `PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.
    - A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes.

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

Data:

.. code-block:: json

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
        "withheld": {
            "domains": [
                "php",
                "fluid",
                "typoscript",
                "xliff",
                "docs",
                "typescript"
            ],
            "suites": 22
        },
        "suites": [
            {
                "suite": "e2e-prepare",
                "command": "CI=true ./Build/Scripts/runTests.sh -s e2e-prepare",
                "runs": "unknown",
                "targeted": null,
                "description": "Installs the same instance the e2e suite runs against, publishes it on a local port and leaves it up.",
                "whenToUse": "Use to look at a backend change in a real browser. It prints the instance URL and then waits: Enter re-runs the specs in the container, Control-C ends it. That wait is a read from /dev/tty, so the suite needs a controlling terminal — CI=true does not stand in for one, and a run that has none exits at the prompt, removes the instance it just installed and still reports SUCCESS. The two local Playwright commands it prints, headless and in the UI with PLAYWRIGHT_BASE_URL already set, are for iterating by eye rather than for reporting: they run on the host, where the browsers are an `npm --prefix=Build run playwright:install` of their own that the containerised suites never need. What a review reports is the `-s e2e` run.",
                "domains": [
                    "php",
                    "typescript",
                    "fluid",
                    "css"
                ],
                "versions": "TYPO3 v13 and newer"
            },
            {
                "suite": "build-css",
                "command": "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css",
                "runs": "change",
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
                "runs": "unknown",
                "targeted": null,
                "description": "The e2e suite in Playwright's own UI, served from the container.",
                "whenToUse": "Use to watch a spec run and step through it. It prints the UI URL and the instance URL beside it, then waits on a keypress it reads from /dev/tty. Like e2e-prepare it needs a controlling terminal; a run that has none removes both containers and still reports SUCCESS.",
                "domains": [
                    "php",
                    "typescript",
                    "fluid",
                    "css"
                ],
                "versions": "TYPO3 v14 and newer"
            },
            {
                "suite": "lintScss",
                "command": "CI=true ./Build/Scripts/runTests.sh -s lintScss",
                "runs": "check",
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
                "A suite runs against the `vendor/` and `bin/` of the directory it is started from, because the script mounts that directory and nothing else. A fresh clone has neither, and so does a git worktree of a checkout that has them — `/vendor/*` and `/bin/*` are gitignored, so git never brings them. The run then stops at `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`, which names phpunit rather than the directory. Run `CI=true ./Build/Scripts/runTests.sh -s composerInstall` once in that directory first. Symlinking `vendor/` and `bin/` from another checkout does not stand in for it: the target sits outside the one mount and does not resolve inside the container.",
                "The node suites need none of that. `-s build`, `-s lintTypescript`, `-s lintScss`, `-s lintHtml`, `-s unitJavascript` and `-s npm` run npm inside `Build/`, whose `package.json` and `package-lock.json` are tracked, and install the `node_modules` they need themselves. So a fresh clone and a bare git worktree run them without a composerInstall first, which is how a build is run for a checkout that has to stay as it is. That is read off the suite bodies rather than measured from a run."
            ],
            "notes": [
                "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent. What it does not do is stand in for a terminal: a suite that waits for a keypress reads /dev/tty itself, and its entry says what a run without one costs.",
                "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
                "The script stops reading its own options at the first word that is not one. `getopts` ends there and `shift $((OPTIND - 1))` hands that word and everything after it to phpunit, the `--` included. So `-s functional -d sqlite <path> -- --filter someTest` leaves `<path> -- --filter someTest` for phpunit, and the failure reads `Test file \"--filter\" not found`. An option written after a path is handed on the same way: `-d sqlite` behind the path never reaches the script. Every option the script owns stands before the `--`, and the path stands after it beside the filter.",
                "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
                "The exception is a change that alters rendered output — a URI, a tag, an attribute other tests assert verbatim. Narrowing then reports the blast radius one failing suite at a time, and each round costs a run. Find the expectations by searching the checkout first and fix them in one pass; `typo3_hint_lookup` for `core-tests` says where they hide, which is largely not in files named `*Test.php`. Run the full functional suite once to confirm, rather than widening the path set round after round.",
                "`./Build/Scripts/runTests.sh -h` is how a suite is confirmed to exist on the branch: it lists the suites and option values that branch supports. Grepping the case label in the script misses a glob — the label is `build*)` on 13.4 and up, and `buildCss)` and `buildJavascript)` on 12.4, so a search for `build)` finds nothing on any covered branch.",
                "A suite this answer does not list can still run git over the working tree: `checkIsoDatabase` and `checkCharsets` regenerate a table, discard uncommitted edits to composer.json and composer.lock, and stage everything with `git add *`.",
                "`PLAYWRIGHT_USE_EXISTING_INSTANCE=1` in the environment keeps the instance a previous `-s e2e-prepare` installed: the run skips the composer install of the test instance and starts in seconds instead of minutes. Only the branches that carry the e2e suites read it.",
                "A session with no terminal reaches the suites that wait by allocating one: `script -qec '<the runTests.sh command>' /dev/null` (util-linux), with stdin from something that stays open and never writes, such as a fifo. `/dev/null` as stdin ends the wait immediately and `/dev/zero` floods the terminal with NUL bytes."
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
