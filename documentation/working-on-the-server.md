# Working on the server itself

For someone changing this repository rather than using it. The conventions are
in [AGENTS.md](../AGENTS.md); these are the commands they rest on.

## Keeping the repository in order

Everything this repository is kept in order by is one command — the requirement
and decision files, the forward-run scenarios, the hint corpus, the bundled
catalogs, and the core checkouts below. Run it with nothing and it says what it
supports:

```bash
bin/cli          # every subject, and every command it carries
bin/cli next     # the one todo that is due now, and nothing else
bin/cli check    # requirements, decisions, scenarios and todo.md against their formats
```

`bin/typo3-cms-mcp` is the server itself and carries none of this.

## Core checkouts

The knowledge is bound to TYPO3 versions, so writing it means checking a
statement on both sides of the boundary it claims. `knowledge/versions.json`
declares the lines that are covered, and one command turns them into checkouts
this repository owns:

```bash
bin/cli checkouts update   # create what is missing, update what is there
bin/cli checkouts status   # what exists, at which revision
```

They land below `.checkouts/`, which is gitignored — one treeless clone plus a
worktree per version, so four lines share one object store (under a gigabyte in
total). Nothing at runtime reads them: they are how the knowledge is verified,
not where the answers come from.

## Tests

```bash
composer ci      # lint, static analysis, tests — what CI runs
composer test    # phpunit only
composer stan    # phpstan only
```

`composer ci` lints, runs the static analysis, and runs the test suite: the
search and ranking logic, every tool against its declared schemas and
annotations, and the stdio entrypoint driven as a real subprocess. CI runs the
same command on every supported PHP version.
