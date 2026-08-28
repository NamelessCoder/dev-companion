---
date: 2026-08-28T00:13:33+00:00
category: tool-gap
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/bootstrap_package
---

# ddev composer does not pass a wrapped tool's stdout through

## Observation

Task: review a pull request against bk2k/bootstrap-package and run the checks the repository declares.

Once inside the container, `ddev composer cgl:ci` returns the failing exit code but php-cs-fixer's diff never reaches stdout — ddev's composer wrapper swallows it. I spent two further calls (a grep over the output, then a full re-run) before reaching for `ddev exec ".build/bin/php-cs-fixer fix --diff --dry-run --config=.php-cs-fixer.dist.php"`, which printed it. That the single finding was a ddev-generated, untracked config/system/additional.php — pre-existing, nothing to do with the diff under review — is precisely the kind of thing an exit code alone cannot tell you.

That is a property of this repository's declared environment rather than of my session.

## Query

In the shell: `ddev composer cgl:ci` (exit 8, no diff on stdout); `ddev exec ".build/bin/php-cs-fixer fix --diff --dry-run --config=.php-cs-fixer.dist.php"` (printed the diff).

## Suggestion

Where a declared command is a composer script wrapping a tool whose result is on stdout rather than in its exit code, `ddev composer <script>` does not pass that output through, and the underlying binary has to be invoked with `ddev exec`. That one is not guessable and cost me two calls.
