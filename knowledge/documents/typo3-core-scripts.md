# TYPO3 Core Script Help

Commands for working on a TYPO3 core checkout. All paths are relative to the
checkout root.

## Invoking runTests.sh

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
CI=true ./Build/Scripts/runTests.sh -s cglGit
```

Checks and fixes coding guidelines for the latest commit. `-s cgl` does the same
for all core PHP files and takes considerably longer; `-n` only reports.

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

```bash
CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css
```

Runs the focused CSS build from `Build/package.json`, which maps to `grunt css`.

### Dispatch Composer Commands

```bash
CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload
```

Runs Composer commands inside the TYPO3 core test environment.

### Dispatch NPM Commands

```bash
CI=true ./Build/Scripts/runTests.sh -s npm -- run build
```

Runs npm commands inside the TYPO3 core test environment.

Useful package scripts for frontend and CSS work include `run build`, `run
build-css`, `run lint`, and `run watch:build`.

## Script Notes

- Prefer running a targeted test while iterating.
- Run broader checks before marking a task as ready for review.
- For Sass changes, run `lintScss` for stylelint and `npm -- run build-css` for
  generated CSS.
- Keep command output snippets short in summaries; include the command and
  pass/fail result.
