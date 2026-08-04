# `typo3_script_lookup`

Find notes for TYPO3 core scripts and commands. They are the core checkout's
own: a query that reads as a project or third-party extension is answered with
the boundary instead of with commands that do not exist there. Answers from:
knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

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

Derived by `bin/cli tools:index`, and `bin/cli tools:check` holds it — the
same as everything above this heading. This tool reads nothing an installation
contains: what reaches its answer is the bundled knowledge and which TYPO3
major the caller is on, so what comes back is written down rather than recorded
from one machine's checkout. Answered against the core checkout this repository
writes below .fixtures/, declaring TYPO3 14.3.0.

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
Source: TYPO3 Core Script Help (typo3://core/typo3-core-scripts) — matches 69% of the query terms

### Install Dependencies

```bash
CI=true ./Build/Scripts/runTests.sh -s composerInstall
```

A suite runs against the `vendor/` and `bin/` of the directory it is started
from, because `runTests.sh` mounts that directory and nothing else. A fresh
clone has neither, and so does a git worktree of a checkout that has them:
`/vendor/*` and `/bin/*` are gitignored, so git never brings them. The first
suite there stops at `exec: line 9: bin/phpunit: not found`, which names phpunit
rather than the directory, so the cause is not readable from the symptom. Run
the install once in that directory first.

Symlinking `vendor/` and `bin/` from another checkout does not stand in for it.
The target sits outside the one mount and does not resolve inside the container,
whether the link is absolute or relative.

`composer install` on the host installs the same dependencies, but it wants the
PHP the branch requires; the containerised form is why `runTests.sh` exists.
Either way this is a precondition and not a step: a checkout that already has
`vendor/` needs it again only after `composer.json` or `composer.lock` changed.

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
            "body": "### Install Dependencies\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s composerInstall\n```\n\nA suite runs against the `vendor/` and `bin/` of the directory it is started\nfrom, because `runTests.sh` mounts that directory and nothing else. A fresh\nclone has neither, and so does a git worktree of a checkout that has them:\n`/vendor/*` and `/bin/*` are gitignored, so git never brings them. The first\nsuite there stops at `exec: line 9: bin/phpunit: not found`, which names phpunit\nrather than the directory, so the cause is not readable from the symptom. Run\nthe install once in that directory first.\n\nSymlinking `vendor/` and `bin/` from another checkout does not stand in for it.\nThe target sits outside the one mount and does not resolve inside the container,\nwhether the link is absolute or relative.\n\n`composer install` on the host installs the same dependencies, but it wants the\nPHP the branch requires; the containerised form is why `runTests.sh` exists.\nEither way this is a precondition and not a step: a checkout that already has\n`vendor/` needs it again only after `composer.json` or `composer.lock` changed.\n\n### Run PHP Unit Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s unit\n```\n\nRuns the TYPO3 core unit test suite. Add a path or `--filter` after `--` when\nworking on a narrow area.\n\n### Run Functional Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s functional\n```\n\nRuns functional tests. Use these for changes that touch TYPO3 services,\npersistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`\nto reproduce DBMS-specific behaviour.\n\n### Run Coding Standards\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s cgl -n\n```\n\nChecks coding guidelines for all core PHP files and reports without changing\nthem; drop `-n` to have them fixed. `-s cglGit` runs\n`Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,\nbut only from a normal checkout: that script asks git for its file list inside\nthe container, and a git worktree keeps its gitdir outside the mounted\ndirectory, so git fails, the list comes back empty and the suite reports SUCCESS\nhaving read no file. `-s cgl` asks git nothing and works from either.\n\n### Run PHPStan\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s phpstan\n```\n\nUseful for type-sensitive PHP changes and API contract changes.\n\n### Check ReST Documentation",
            "coverage": 0.688,
            "score": 7,
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
