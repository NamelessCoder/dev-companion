---
date: 2026-08-04T17:59:53+00:00
category: missing-knowledge
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-new
---

# Task: fix an audit finding that readme.md documented TYPO3_DB_*, TYPO3_ENCRYPTION_KEY, TYPO3_TRUS...

## Observation

Task: fix an audit finding that readme.md documented TYPO3_DB_*, TYPO3_ENCRYPTION_KEY, TYPO3_TRUSTED_HOSTS and TYPO3_MAIL_DSN as applying on any non-DDEV host while config/system/additional.php read none of them.

The environment-variables hint is what made that finding precise, and its sentence "no running installation picks up TYPO3_ENCRYPTION_KEY or TYPO3_DB_HOST by itself ... a readme documenting the name without such a line documents nothing" is quotable enough that it went into the commit message. Excellent as far as it goes.

Where it stopped short: the same hint says "TYPO3 also ships no .env reader, so whatever puts a .env file into the process environment — DDEV, the container runtime, a dotenv component the project required — is the project's choice as well." That framing reads as out of scope. So I implemented getenv() reads, verified them, and closed the finding.

The user then pointed out that .gitignore had listed .env and .env.local since before any of this. The repository advertised a dotenv workflow that nothing implemented — the identical failure one layer down from the one I had just fixed, and I walked past it while holding the file open.

Implementing it surfaced a detail the hint could own, because getting it wrong reproduces the same silent-nothing: symfony/dotenv's usePutenv defaults to false. It populates $_ENV and $_SERVER only. Any project whose additional.php reads getenv() — which is the form this hint's own example uses — must call usePutenv(), or the .env parses and configures nothing at all.

## Query

typo3_hint_lookup id=environment-variables targetVersion=14.3, and id=environment-runtime-readers, while implementing the documented env-var contract in config/system/additional.php

## Suggestion

Add to environment-variables: where a repository's .gitignore lists .env, something has to load it, and symfony/dotenv is the usual choice; loadEnv() with overrideExistingVars left false gives real environment variables precedence over the file, which is what a deployment needs. State that usePutenv() is required whenever the project reads getenv(), since Dotenv otherwise populates only $_ENV/$_SERVER and the file silently does nothing — the same failure mode the hint already warns about for undocumented variable names.
