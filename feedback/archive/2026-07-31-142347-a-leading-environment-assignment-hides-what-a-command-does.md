---
date: 2026-07-31T14:23:47+02:00
category: bug
status: closed
closed: 2026-07-31
commit: 419b622
subject: "[BUGFIX] Read the tool behind a leading environment assignment"
tool: typo3_project_scope
---

# A leading environment assignment hides what a command does

## Observation

`typo3_project_scope` in `/home/benji/projects/news` returned all six declared
commands with `"runs": "unknown"`, including this one:

```
PHP_CS_FIXER_IGNORE_ENV=1 php ./.Build/bin/php-cs-fixer fix --dry-run -v --config ./Build/php-cs-fixer/php-cs-fixer.php ./
```

That is a `--dry-run` php-cs-fixer and it should read as `check`. The cause is
in `Project::runsLine()`: `@php`, `@composer` and `@putenv` are stripped from
the front of a declaration, but a plain shell assignment is not, so
`self::tool()` is handed `PHP_CS_FIXER_IGNORE_ENV=1` and every flag after it is
never examined.

The consequence is not the missing `check` — it is that `cs` and `csfix` come
back **identical**. The scope cannot tell the reporter from the rewriter, so an
agent told not to change files gets `unknown` for both and has no answer from
this server about which one is safe. The `REVIEW-02` run this came from decided
for itself, correctly, and ran `cs`; nothing in the answer it was given made
that the safe choice rather than a lucky one.

`PHP_CS_FIXER_IGNORE_ENV=1` in front of the fixer is the documented way to run
it on a PHP the fixer does not yet claim support for, so this shape is common
in exactly the repositories that span two majors — the CI of the same project
carries it too.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

Strip leading `NAME=value` assignments in `Project::runsLine()` the way the
`@`-prefixes are already stripped, before the first token is read as the tool,
and keep the assignments out of the tool name rather than out of the
declaration the answer shows. A test with `PHP_CS_FIXER_IGNORE_ENV=1` in front
of a `--dry-run` fixer and in front of a fixing one holds the half that matters:
the two must not classify the same.
