---
date: 2026-08-04T05:54:20+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3-extension-testing
directory: /home/benji/projects/ext-guidedtour
---

# Task: add a code style fixer (php-cs-fixer) to a standalone TYPO3 extension repository (t3g/guide...

## Observation

Task: add a code style fixer (php-cs-fixer) to a standalone TYPO3 extension repository (t3g/guidedtour, TYPO3 14.3, DDEV, no test or static-quality infrastructure at all).

The skill typo3-extension-testing routed correctly and its references/static-quality.md named the right packages (friendsofphp/php-cs-fixer driven by typo3/coding-standards, editorconfig-checker where an .editorconfig is shipped). But the server itself has no hint covering this. The topical lookup above returned three hint groups — extension-manifest, extension-repository-layout, extension-boot-files — none of which mention php-cs-fixer, a rule set, a configuration file or .editorconfig. There is an id extension-static-analysis, and it is entirely about PHPStan: configuration location, tmpDir, bootstrapFiles for LF/CR, the ext_emconf.php exclusion, level 5, baselines. Nothing equivalent exists for the coding-standards side.

Everything I needed I read out of the installed package after requiring it:
- .build/vendor/typo3/coding-standards/templates/extension_php-cs-fixer.dist.php — the shipped template is `$config->getFinder()->in(__DIR__)`.
- .build/vendor/typo3/coding-standards/src/CsFixerConfig.php — create() excludes .build, typo3temp, var, vendor and notPath config/system/settings.php.
- .build/vendor/typo3/coding-standards/src/Setup.php and the CLI `typo3-coding-standards setup [extension|project] --rule-set=editorconfig|php-cs-fixer`, which I discovered by running `list --raw` and `setup --help`.

That reading mattered: this repository sets bin-dir to .build/bin and vendor-dir to .build/vendor, and the template's bare `->in(__DIR__)` combined with a `vendor` exclude that does not match `.build/vendor` is a trap a hint should warn about. I wrote an explicit finder over Classes/, Configuration/, ext_localconf.php, ext_emconf.php and the config file itself instead. That decision came from the skill's rule "keep automatic formatting inside the first-party paths", not from the server.

## Query

typo3_hint_lookup(task="coding standards php-cs-fixer setup for an extension", paths=["composer.json","Classes/","ext_localconf.php"], targetVersion="14.3")

## Suggestion

Add a hint id beside extension-static-analysis — extension-coding-standards or similar — answering the same questions for the fixer side that extension-static-analysis answers for PHPStan: that typo3/coding-standards is the rule set and ships both the .php-cs-fixer.dist.php and the .editorconfig template; that its CLI is `typo3-coding-standards setup <type> --rule-set=<set>` with the two valid types and two valid rule sets; what CsFixerConfig::create() already excludes and, crucially, that those excludes are literal directory names, so a package whose composer config moves vendor-dir or web-dir under a build directory must name its first-party paths explicitly rather than use the template's `->in(__DIR__)`; that check and fix belong in two separate commands; and that the check is version-independent, so one run proves as much as a matrix cell.
