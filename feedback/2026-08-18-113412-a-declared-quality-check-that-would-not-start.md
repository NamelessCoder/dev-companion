---
date: 2026-08-18T11:34:12+00:00
category: idea
status: open
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
