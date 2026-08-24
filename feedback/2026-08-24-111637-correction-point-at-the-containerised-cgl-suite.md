---
date: 2026-08-24T11:16:37+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Correction: point at the containerised cgl suite instead of prefixing the direct fixer with ddev ...

## Observation

Task: same session as the earlier feedback "cglFixMyCommit.sh needs ddev exec where the core checkout declares a DDEV environment". This corrects the suggestion in it, because the user rejected both forms and the resolution was a third one.

Two facts I established afterwards in the checkout:

- PHP_CS_FIXER_IGNORE_ENV=1, which the core repository's AGENTS.md prescribes for the fixer, exists to make php-cs-fixer run on a PHP version it does not support. It silences the interpreter check rather than using the right interpreter. The user's words: "wir sollten das ignore env nicht nehmen", and then "diese option ist nicht gut".
- .ddev/ is untracked and gitignored in this checkout (/.ddev/* in .gitignore). So DDEV is this developer's local environment rather than something the repository ships, and a `ddev exec` prefix in a tracked, repository-wide document would be exactly the machine-specific preference AGENTS.md itself forbids.

What both forms have in common is that they invoke php-cs-fixer directly, which the same AGENTS.md rules out two sections further down: "Do not invoke phpunit, phpstan, php-cs-fixer, npm or grunt directly — the wrapper supplies the PHP version, database service and bootstrap." The resolution was therefore to drop the direct invocation entirely: `./Build/Scripts/runTests.sh -s cglGit` checks the CGL of the last commit, starts its own container, needs no environment prefix and no env override, and it is what I had already run in the review. Both PHP_CS_FIXER_IGNORE_ENV lines were removed from AGENTS.md.

So the earlier suggestion — teach the workflows to prefix cglFixMyCommit.sh with `ddev exec` — would have a maintainer document a command that should not be recommended in the first place.

## Query

Follow-up to feedback/2026-08-24-104546-cglfixmycommit-sh-needs-ddev-exec-where-the.md. Established with: grep -n "IGNORE_ENV|cglFixMyCommit" AGENTS.md (lines 29 and 74), git ls-files .ddev (0 tracked), grep -n ddev .gitignore (line 38 /.ddev/*)

## Suggestion

Where a workflow tells an author to fix coding guidelines after committing, name `./Build/Scripts/runTests.sh -s cglGit` (report) and `-s cgl` (fix, `-n` for dry run) rather than Build/Scripts/cglFixMyCommit.sh. That is environment-independent by construction: the dispatcher brings its own container, so neither a DDEV prefix nor PHP_CS_FIXER_IGNORE_ENV is needed, and the answer stays the same whether or not the checkout has a local environment.

The general point of the earlier feedback still stands and is worth keeping separately: where typo3_project_describe reports an environment it found and did not enter, saying which of the listed commands run through it is useful. It just should not be demonstrated on this command.
