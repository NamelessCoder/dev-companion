# TYPO3 Core Script Help

Default local checkout: `/home/benji/projects/typo3-cms`.

Override with `TYPO3_CORE_PATH` if the checkout moves. The MCP tools expose these entries so an assistant can suggest commands during core work.

## Common Commands

### Install Dependencies

```bash
composer install
```

Use this after cloning TYPO3 core or changing PHP dependencies.

### Run PHP Unit Tests

```bash
Build/Scripts/runTests.sh -s unit
```

Runs the TYPO3 core unit test suite. Add filters or paths when working on a narrow area.

### Run Functional Tests

```bash
Build/Scripts/runTests.sh -s functional
```

Runs functional tests. Use these for changes that touch TYPO3 services, persistence, configuration, or integrations.

### Run Coding Standards

```bash
Build/Scripts/runTests.sh -s cgl
```

Checks coding guidelines. Run before submitting style-sensitive PHP changes.

### Run TypeScript/Frontend Checks

```bash
Build/Scripts/runTests.sh -s build
```

Useful for backend UI, JavaScript, TypeScript, Sass, contrib, and frontend asset changes.

### Run SCSS Linting

```bash
Build/Scripts/runTests.sh -s lintScss
```

Runs the TYPO3 Core stylelint setup for Sass sources. Internally this runs `grunt stylelint` in the `Build` directory.

### Run CSS Build Only

```bash
Build/Scripts/runTests.sh -s npm -- run build-css
```

Runs the focused CSS build from `Build/package.json`, which maps to `grunt css`.

### Run PHPStan

```bash
Build/Scripts/runTests.sh -s phpstan
```

Useful for type-sensitive PHP changes and API contract changes.

### Dispatch Composer Commands

```bash
Build/Scripts/runTests.sh -s composer -- dumpautoload
```

Runs Composer commands inside the TYPO3 core test environment.

### Dispatch NPM Commands

```bash
Build/Scripts/runTests.sh -s npm -- run build
```

Runs npm commands inside the TYPO3 core test environment.

Useful package scripts for frontend and CSS work include `run build`, `run build-css`, `run lint`, and `run watch:build`.

## Script Notes

- Prefer running a targeted test while iterating.
- Run broader checks before marking a task as ready for review.
- For Sass changes, run `lintScss` for stylelint and `build` or `npm -- run build-css` for generated CSS.
- Keep command output snippets short in summaries; include the command and pass/fail result.
