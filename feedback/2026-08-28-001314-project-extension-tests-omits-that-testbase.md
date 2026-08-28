---
date: 2026-08-28T00:13:14+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/bootstrap_package
---

# project-extension-tests omits that Testbase chdir's into the instance path

## Observation

Task: write functional regression tests proving that a TYPO3 extension's SCSS cache files are still found when the current working directory is not the public path (bk2k/bootstrap-package, PR #1621 — the fix resolves relative cache paths through GeneralUtility::getFileAbsFileName() before file_exists() and filemtime()).

typo3_hint_lookup id=project-extension-tests is unusually thorough about the environment around a functional test: the five database environment variables and the error text when they are absent, the per-class database and its _ft<7 hex> suffix, that PHPUnit checks the interpreter before composer/platform_check.php, that .ddev/config.yaml being tracked makes ddev exec fail from a git worktree, the typo3temp/var/tests/functional-<7 hex> instance directory, $initializeDatabase = false, pdo_sqlite cleanup.

It does not say that the framework changes the working directory. typo3/testing-framework Classes/Core/Testbase.php:215 calls chdir($instancePath), so a functional test always runs with the working directory equal to the public path.

What that cost: I wrote the obvious regression test — compile twice through the service, assert the cache file was not rewritten — and it passed both with and without the fix. For one round I concluded the reported defect did not reproduce, which is the wrong answer to a pull request. `grep -rn chdir` over the installed testing framework explained it; the fix was to move the working directory explicitly inside the test and restore it in a finally block. Two full functional runs of roughly 25 seconds each, plus the wrong intermediate conclusion.

This is not a corner case. Any test about path resolution, about Environment::getPublicPath() versus getProjectPath(), or about behaviour that differs between a web request and a CLI request lands on it — and the hint already owns exactly that subject, "what the harness does around your test". It is the one line in it I needed and the one that was not there.

## Query

typo3_hint_lookup(id="project-extension-tests") — read whole, then had to establish the missing fact by grepping the installed typo3/testing-framework for chdir.

## Suggestion

One line in project-extension-tests, beside the instance-directory statements it already carries:

"Testbase changes the working directory into the instance path before the first test runs (Testbase.php, chdir($instancePath)), so a functional test never observes a working directory other than the public path. A test about path resolution — anything that would behave differently under CLI, where the working directory is the project root — has to move it itself and put it back."

Naming Testbase and the chdir call makes it greppable from the symptom, which is how I eventually found it.
