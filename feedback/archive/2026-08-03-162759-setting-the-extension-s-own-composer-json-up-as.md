---
date: 2026-08-03T16:27:59+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: give a standalone TYPO3 extension repository a local development environment. Setting the e...

## Observation

Task: give a standalone TYPO3 extension repository a local development environment. Setting the extension's own composer.json up as the Composer root package that installs TYPO3 beneath itself produced three surprises, all found by trial against TYPO3 14.3.5 and typo3/cms-composer-installers v5.

1. extra.typo3/cms.app-dir is dead. Setting it to ".build" to keep config/ and var/ out of the repository root is accepted silently at write time, but composer install prints "Changing app-dir is not supported any more. TYPO3 application dir will always be set to Composer root directory" and ignores it. Consequence: with the extension repository as the Composer root, TYPO3 writes config/ (settings.php, additional.php, sites/) and var/ (cache, log, session, the SQLite file) straight into the versioned repository. Both must go into .gitignore. Only web-dir is still honoured, so .build/public works while .build/config does not.

2. typo3/cms-cli must not be required explicitly. Adding "typo3/cms-cli": "^5.0" to require-dev — a plausible-looking constraint — makes the resolver fail with "typo3/cms-core[v14.3.3, ..., v14.3.5] require typo3/cms-cli ^3.1.3 but it conflicts with your root composer.json require (^5.0)". The package is at v3.1.3 while the core is at 14.3.5; its version line has nothing to do with the TYPO3 version. It arrives as a dependency of typo3/cms-core anyway, and .build/bin/typo3 exists without naming it.

3. The extension being the Composer root package changes where it lands. It is not symlinked into <web-dir>/typo3conf/ext/ — that directory stays empty — and TYPO3 loads it from the repository root via the package artifact. What is created is <web-dir>/_assets/<md5>/ pointing at ../../../Resources/Public, so edits to CSS and JavaScript under Resources/Public are live in the browser after a reload with no composer or build step. Worth stating positively, because the empty typo3conf/ext/ otherwise reads as a broken installation.

## Query

Making a TYPO3 extension's own composer.json install a full TYPO3 14.3 into .build/ so the extension can be run locally: which config/extra keys apply, and which packages may be required.

## Suggestion

Carry the Composer root-package layout for a local extension installation as explicit knowledge: which extra.typo3/cms keys still have an effect in the current major (web-dir yes, app-dir no, with the message the installer prints), that config/ and var/ therefore land in the Composer root and belong in .gitignore, that typo3/cms-cli is version-independent of the core and must be left to the core's own dependency, and that a root-package extension is loaded from the repository root rather than from typo3conf/ext/.

The _assets half of the third point is answered and is struck. `public-assets` states that Resources/Public/ of an installed package is published into the document root below _assets/<hash>/, that the hash is the package rather than the file, and that the path is an implementation detail to be reached through an EXT: reference. It is reachable on a query shaped like this one's third point. What stays open beside it is the placement: that the extension being the root package is loaded from the repository root, so an empty typo3conf/ext/ is not a broken installation. `D-KNW-047` has the readings.
