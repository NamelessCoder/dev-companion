# `typo3_script_lookup`

Find notes for TYPO3 core scripts and commands. They are the core checkout's
own: a query that reads as a project or third-party extension is answered with
the boundary instead of with commands that do not exist there.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# The TYPO3 core task, in English, for example unit tests, functional tests,
# CGL, npm, or dependency install.
task: string
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
```

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### scripts: hit

Called with:

```json
{
    "task": "functional tests"
}
```

Text:

````
These sections are prose and are not filtered by version. Where a subsystem changed inside the covered range, the statement that changed carries the range elsewhere: call typo3_hint_lookup with targetVersion for the convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Invoking runTests.sh
Source: TYPO3 Core Script Help (typo3://core/typo3-core-scripts) — matches 100% of the query terms

`Build/Scripts/runTests.sh` runs every suite inside a container and is started
from the core checkout root.

- Prefix scripted or non-interactive runs with `CI=true`. It drops the
  interactive container flags, skips the SIGINT trap, and picks the CI phpstan
  configuration. Without a TTY the script removes the interactive flags on its
  own, but `CI=true` is the explicit form.
- Everything after `--` is passed through unchanged: to phpunit for the test
  suites, to npm for `-s npm`, to composer for `-s composer`.
- Run one file or one method while iterating; a full suite costs minutes per
  round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the
  checked-out branch supports.

```bash
# one unit test file
CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

# one unit test method
CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

# one functional test file, sqlite (default)
CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/impexp/Tests/Functional/Export/ExportTest.php
```

Frequently needed options:

- `-d sqlite|mariadb|mysql|postgres` selects the database for `-s functional`
  and for whichever installer suite the branch carries. sqlite is the default
  and the fastest.
- `-a mysqli|pdo_mysql` selects the driver for mysql and mariadb.
- `-i <version>` pins a database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` selects the PHP version of the container.
- `-n` turns the `cgl` suites, and any other suite the branch lists under it,
  into a dry run that only reports.
- `-c <chunk>/<total>` splits `-s functional`, and the browser suite where the
  branch has one, into chunks.
- `-x` (with optional `-y <port>`) enables xdebug towards a listening IDE.
- `-b docker|podman` selects the container runtime; podman is the default.

## Common Commands
Source: TYPO3 Core Script Help (typo3://core/typo3-core-scripts) — matches 88% of the query terms

### Install Dependencies

```bash
composer install
```

Use this after cloning TYPO3 core or changing PHP dependencies.

### Run PHP Unit Tests

```bash
CI=true ./Build/Scripts/runTests.sh -s unit
```

Runs the TYPO3 core unit test suite. Add a path or `--filter` after `--` when
working on a narrow area.

### Run Functional Tests

```bash
CI=true ./Build/Scripts/runTests.sh -s functional
```

Runs functional tests. Use these for changes that touch TYPO3 services,
persistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`
to reproduce DBMS-specific behaviour.

### Run Coding Standards

```bash
CI=true ./Build/Scripts/runTests.sh -s cgl -n
```

Checks coding guidelines for all core PHP files and reports without changing
them; drop `-n` to have them fixed. `-s cglGit` runs
`Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,
but only from a normal checkout: that script asks git for its file list inside
the container, and a git worktree keeps its gitdir outside the mounted
directory, so git fails, the list comes back empty and the suite reports SUCCESS
having read no file. `-s cgl` asks git nothing and works from either.

### Run PHPStan

```bash
CI=true ./Build/Scripts/runTests.sh -s phpstan
```

Useful for type-sensitive PHP changes and API contract changes.

### Check ReST Documentation

```bash
CI=true ./Build/Scripts/runTests.sh -s checkRst
```

Required for every changelog entry below
`typo3/sysext/core/Documentation/Changelog/`.

### Check and Normalize XLIFF

Editing language files calls for a check that the XLIFF is valid and a run that
normalizes its formatting, so the diff carries no noise. Which suites those are
is a property of the branch, and some branches have neither: ask
`typo3_test_run_guide` with the `targetVersion` for the commands that exist on
yours.

### Run TypeScript/Frontend Checks

The frontend build covers backend UI, JavaScript, TypeScript, Sass, contrib and
generated assets. Whether it is one suite or split in two changed inside the
covered range, so ask `typo3_test_run_guide` with the `targetVersion` rather
than copying a command from here.

### Run SCSS Linting

```bash
CI=true ./Build/Scripts/runTests.sh -s lintScss
```

Runs the TYPO3 Core stylelint setup for Sass sources. Internally this runs
`grunt stylelint` in the `Build` directory.

### Run CSS Build Only

(section truncated — read typo3://core/typo3-core-scripts for the rest)

These commands run in a TYPO3 core checkout. In any other repository, what to run is declared in its own composer.json, package.json and CI configuration.
````

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
            "heading": "Invoking runTests.sh",
            "body": "`Build/Scripts/runTests.sh` runs every suite inside a container and is started\nfrom the core checkout root.\n\n- Prefix scripted or non-interactive runs with `CI=true`. It drops the\n  interactive container flags, skips the SIGINT trap, and picks the CI phpstan\n  configuration. Without a TTY the script removes the interactive flags on its\n  own, but `CI=true` is the explicit form.\n- Everything after `--` is passed through unchanged: to phpunit for the test\n  suites, to npm for `-s npm`, to composer for `-s composer`.\n- Run one file or one method while iterating; a full suite costs minutes per\n  round.\n- `./Build/Scripts/runTests.sh -h` lists the suites and option values the\n  checked-out branch supports.\n\n```bash\n# one unit test file\nCI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php\n\n# one unit test method\nCI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php\n\n# one functional test file, sqlite (default)\nCI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/impexp/Tests/Functional/Export/ExportTest.php\n```\n\nFrequently needed options:\n\n- `-d sqlite|mariadb|mysql|postgres` selects the database for `-s functional`\n  and for whichever installer suite the branch carries. sqlite is the default\n  and the fastest.\n- `-a mysqli|pdo_mysql` selects the driver for mysql and mariadb.\n- `-i <version>` pins a database version, for example `-d mariadb -i 11.4`.\n- `-p <php minor>` selects the PHP version of the container.\n- `-n` turns the `cgl` suites, and any other suite the branch lists under it,\n  into a dry run that only reports.\n- `-c <chunk>/<total>` splits `-s functional`, and the browser suite where the\n  branch has one, into chunks.\n- `-x` (with optional `-y <port>`) enables xdebug towards a listening IDE.\n- `-b docker|podman` selects the container runtime; podman is the default.",
            "coverage": 1,
            "score": 10,
            "truncated": false
        },
        {
            "documentId": "typo3-core-scripts",
            "title": "TYPO3 Core Script Help",
            "uri": "typo3://core/typo3-core-scripts",
            "heading": "Common Commands",
            "body": "### Install Dependencies\n\n```bash\ncomposer install\n```\n\nUse this after cloning TYPO3 core or changing PHP dependencies.\n\n### Run PHP Unit Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s unit\n```\n\nRuns the TYPO3 core unit test suite. Add a path or `--filter` after `--` when\nworking on a narrow area.\n\n### Run Functional Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s functional\n```\n\nRuns functional tests. Use these for changes that touch TYPO3 services,\npersistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`\nto reproduce DBMS-specific behaviour.\n\n### Run Coding Standards\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s cgl -n\n```\n\nChecks coding guidelines for all core PHP files and reports without changing\nthem; drop `-n` to have them fixed. `-s cglGit` runs\n`Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,\nbut only from a normal checkout: that script asks git for its file list inside\nthe container, and a git worktree keeps its gitdir outside the mounted\ndirectory, so git fails, the list comes back empty and the suite reports SUCCESS\nhaving read no file. `-s cgl` asks git nothing and works from either.\n\n### Run PHPStan\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s phpstan\n```\n\nUseful for type-sensitive PHP changes and API contract changes.\n\n### Check ReST Documentation\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s checkRst\n```\n\nRequired for every changelog entry below\n`typo3/sysext/core/Documentation/Changelog/`.\n\n### Check and Normalize XLIFF\n\nEditing language files calls for a check that the XLIFF is valid and a run that\nnormalizes its formatting, so the diff carries no noise. Which suites those are\nis a property of the branch, and some branches have neither: ask\n`typo3_test_run_guide` with the `targetVersion` for the commands that exist on\nyours.\n\n### Run TypeScript/Frontend Checks\n\nThe frontend build covers backend UI, JavaScript, TypeScript, Sass, contrib and\ngenerated assets. Whether it is one suite or split in two changed inside the\ncovered range, so ask `typo3_test_run_guide` with the `targetVersion` rather\nthan copying a command from here.\n\n### Run SCSS Linting\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s lintScss\n```\n\nRuns the TYPO3 Core stylelint setup for Sass sources. Internally this runs\n`grunt stylelint` in the `Build` directory.\n\n### Run CSS Build Only",
            "coverage": 0.876,
            "score": 9,
            "truncated": true
        }
    ],
    "scope": "core"
}
```

### scripts: miss

Called with:

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
