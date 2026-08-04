---
date: 2026-08-04T05:56:38+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_project_scope
directory: /home/benji/projects/ext-guidedtour
---

# Task: add a code style fixer to a TYPO3 14.3 extension, which ended with declaring the extension'...

## Observation

Task: add a code style fixer to a TYPO3 14.3 extension, which ended with declaring the extension's PHP requirement.

php-cs-fixer warns on every run: "No PHP version requirement found in composer.json. It is recommended to specify a minimum PHP version supported by your project." The extension declared no `php` constraint, and fixing that meant answering: which PHP does this extension actually support?

typo3_project_scope gave me three of the four numbers — typo3Version 14.3.5, coreConstraint ^14.3, environment.php 8.4 — and `phpConstraint: null`, correctly reporting that the project declares none. But the number I needed is the one the required core demands, and no tool reports it. I read it out of .build/vendor/typo3/cms-core/composer.json myself and found `"php": "^8.2"`, which is what I then declared.

That value is not guessable and it is a common wrong assumption: TYPO3 v14 is widely believed to require PHP 8.3 or 8.4, and the DDEV container in front of me was running 8.4, which would have made "^8.4" look like the obvious answer. Had I written that from memory I would have narrowed the extension's supported range by two minors against the core it requires, silently, with every check still green.

The same reading answers a second question the session raised and left open: php-cs-fixer's follow-up warning that it runs on 8.4 while the project's minimum is 8.2 is expected rather than a defect, because the gap between environment.php and the core's floor is real and intended.

## Query

typo3_project_scope() — returned {"typo3Version":"14.3.5","phpConstraint":null,"coreConstraint":"^14.3","environment":{"via":"ddev","php":"8.4",...}}

## Suggestion

Have typo3_project_scope report the PHP constraint the installed core declares beside the project's own — for example `phpConstraint: null` alongside `corePhpConstraint: "^8.2"`, read from the installed typo3/cms-core rather than from the caller's memory of which major needs what. A field naming the effective floor for the declared coreConstraint would answer "what PHP may this package claim to support" in the call that is already the first call of every workflow, and would stop the DDEV container's PHP being mistaken for the supported minimum.
