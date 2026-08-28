---
date: 2026-08-28T00:13:33+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/bootstrap_package
---

# Declared commands and the declared environment come back separately, never joined into an invocation

## Observation

Task: review a pull request against bk2k/bootstrap-package and run the checks the repository declares.

typo3_project_describe answered both halves of that and left the join to me. In one answer it reported:
- commands: "composer cgl:ci" (runs: check), "composer phpstan" (runs: check), "composer test:php:lint" (runs: check), "composer test:php:functional" (runs: unknown)
- environment: {via: "ddev", php: "8.5", entered: false, project: "bootstrap-package"}
- installedPhpBound: "8.4.1", phpRelation: {inEnvironment: "above", environmentAgainstBound: "above"}

What it never said is that the caller's shell is not the interpreter those commands run under. I ran `composer cgl:ci` exactly as reported and got composer/platform_check.php: "Your Composer dependencies require a PHP version >= 8.4.1. You are running 8.3.23." That is the failure the tool's own description predicts — "under it every one of them aborts in Composer's platform check before its own tool starts, which is what marking a command a check never said" — and the data to predict it was in the answer I had already read. The answer knows the environment, knows the bound, and still hands over an invocation that cannot run.

Second, smaller, same shape. Once inside the container, `ddev composer cgl:ci` returns the failing exit code but php-cs-fixer's diff never reaches stdout — ddev's composer wrapper swallows it. I spent two further calls (a grep over the output, then a full re-run) before reaching for `ddev exec ".build/bin/php-cs-fixer fix --diff --dry-run --config=.php-cs-fixer.dist.php"`, which printed it. That the single finding was a ddev-generated, untracked config/system/additional.php — pre-existing, nothing to do with the diff under review — is precisely the kind of thing an exit code alone cannot tell you.

Both are properties of this repository's declared environment, not of my session, and both were re-derivable from the answer rather than from the checkout.

## Query

typo3_project_describe() — read commands, environment, installedPhpBound, phpRelation. Then, in the shell: `composer cgl:ci` (aborted in platform_check.php, PHP 8.3.23 vs required 8.4.1); `ddev composer cgl:ci` (exit 8, no diff on stdout); `ddev exec ".build/bin/php-cs-fixer fix --diff --dry-run --config=.php-cs-fixer.dist.php"` (printed the diff).

## Suggestion

Give each entry in `commands` the invocation that actually runs in the environment the repository declares — `ddev composer phpstan` rather than `composer phpstan` — or, if the entries should stay as declared, one field beside them saying so once: "the caller's interpreter does not clear installedPhpBound; prefix every command below with `ddev`."

Second line, worth having wherever the first lands: where a declared command is a composer script wrapping a tool whose result is on stdout rather than in its exit code, `ddev composer <script>` does not pass that output through, and the underlying binary has to be invoked with `ddev exec`. That one is not guessable and cost me two calls.
