# What `typo3_test_run_guide` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## runTests: all

Called with:

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

Checks and fixes coding guideline issues in the latest committed patch.
Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.

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
Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output.

## e2e
Command from the TYPO3 core root:
`CI=true ./Build/Scripts/runTests.sh -s e2e`

End-to-end tests driving a real backend with Playwright.
Use for editor or administrator workflows that only break in the assembled backend.

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
- Coding guidelines of the latest commit only:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`
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
            "targeted": null,
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit. Much faster than a full cgl run.",
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
            "whenToUse": "Use for TypeScript modules with real logic or state transitions. Run the branch's frontend build first so the tests see the current output.",
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
        "notes": [
            "Run runTests.sh from the TYPO3 core checkout root. It starts a container (podman by default, `-b docker` to switch) and runs the suite inside it.",
            "Prefix scripted and non-interactive runs with `CI=true`. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but `CI=true` is the explicit form and the one to use from an agent.",
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports."
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
                "purpose": "Coding guidelines of the latest commit only",
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

## runTests: hit

Called with:

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
- Coding guidelines of the latest commit only:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`
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
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports."
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
                "purpose": "Coding guidelines of the latest commit only",
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

## runTests: miss

Called with:

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
- Coding guidelines of the latest commit only:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`
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
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports."
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
                "purpose": "Coding guidelines of the latest commit only",
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

## runTests: narrowed by paths

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
Use when Sass or CSS sources change. Internally this runs grunt stylelint in the Build directory.

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
- Coding guidelines of the latest commit only:
  `CI=true ./Build/Scripts/runTests.sh -s cglGit`
- A single npm package script:
  `CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css`
- A composer command inside the core test environment:
  `CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload`
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
            "Everything after `--` is handed to the underlying tool unchanged: phpunit for the test suites, npm for `-s npm`, composer for `-s composer`.",
            "While iterating, run a single test file or a single test method instead of a whole suite; a full functional run costs minutes per round.",
            "`./Build/Scripts/runTests.sh -h` lists the suites and option values the checked-out branch actually supports."
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
                "purpose": "Coding guidelines of the latest commit only",
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
