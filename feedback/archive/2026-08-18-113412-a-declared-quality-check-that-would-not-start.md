---
date: 2026-08-18T11:34:12+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/bootstrap_package
---

# a declared quality check that would not start on the installed PHP was worked around via CI witho...

## Observation

Task: review PR #1627 against the bootstrap_package sitepackage and change the patch to use ExtensionManagementUtility::addFieldsToPalette() instead of string concatenation.

Having edited a PHP file I tried to verify it against the repository's own CGL check and ran `composer cgl:ci`. It failed before running anything: "Your Composer dependencies require a PHP version >= 8.4.0. You are running 8.3.23", a composer platform_check abort out of .build/vendor/composer/platform_check.php, because the php-cs-fixer in the vendor tree demands 8.4. I checked for another interpreter (`php8.4`, `php8.5`, /usr/bin/php*; only php8.3 present), then gave up and relied on GitHub Actions to run CGL, saying so explicitly in my report to the user rather than claiming the check had passed.

typo3_test_run_guide was in the tool list and I did not call it. I assumed it would describe PHPUnit unit and functional runs against a TYPO3 installation, not "the check this repository declares will not start on the PHP you have". Whether that assumption held I still do not know. It is worth recording either way, because the shape is common and not TYPO3-specific in appearance: a repository declares its checks in composer.json scripts, the local toolchain is one minor behind what the vendor tree demands, and the question is whether there is a way to run it anyway or whether waiting for CI is the honest answer. I had a working Docker daemon on the machine and used it later in the session for a Node reproduction, so a container answer would have been actionable here too — I simply did not think of it for the PHP check.

## Query

`composer cgl:ci` → platform_check abort; PHP 8.3.23 installed, php-cs-fixer vendor tree requires >= 8.4.0. typo3_test_run_guide never called; check deferred to GitHub Actions.

## Suggestion

If typo3_test_run_guide already covers "the check this repository declares cannot start here — what now", say so in words a name-only tool list can carry. If it does not, the answer worth having is: which of the checks a repository declares can be run under a container or a different interpreter, which cannot, and whether deferring a given check to CI is acceptable or hides something. For a CGL check specifically, "run php-cs-fixer in a php:8.4 container against the same vendor tree" would have been a complete answer and cost one call.

## Answered

The abort is now a number in the answer the session had already read.
`typo3_project_describe` reports `installedPhpBound`, the lowest PHP the
packages installed below the root accept, out of the
`composer/platform_check.php` Composer wrote there — the file the reported
abort came out of — and says where the interpreter that would run the declared
commands stands against it. `R-PRJ-012`, decided in `D-ANS-086`. A command
marked a check that will not start now says so beside the mark.

Where the repository configures no environment, which is this checkout, the
bound is stated and `php -v` is named as what settles it. No interpreter is
discovered: the shell a declared command runs in is the caller's own, and a
version read from this server's process would be the wrong claim in exactly the
case the bound is reported for.

Three things this report asks for were rejected, each in `D-ANS-086`. Widening
`typo3_test_run_guide` — its suites are `Build/Scripts/runTests.sh` invocations
and that script is in the core repository, so a project path gets a decline
rather than commands that do not exist there. Recommending a container image for
the fixer — which image runs a given tool is read from nothing here, and a
recipe this server cannot check is a guess with an answer's authority. Judging
whether deferring a check to CI hides something — that is a judgement about one
change, and what this server can supply is the fact it is made on.
