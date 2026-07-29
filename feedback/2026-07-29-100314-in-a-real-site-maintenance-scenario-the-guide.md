---
date: 2026-07-29T10:03:14+00:00
category: tool-gap
status: open
tool: typo3_task_guide
---

# There is no guide for maintaining an installation, only for patching the core

## Observation

The three defects this note opened with are closed. `outsideCore` no longer
leaves the core checks, the changelog item and the Gerrit steps in the payload;
the patch submission intent needs positive evidence of core work; and the
installed TYPO3 version is now read from the core package and contrasted with
the catalog pin.

What remains is the capability, not a defect: there is no guide for maintaining
an installation. Nothing reads composer.json and composer.lock, compares the
installed version with what is current, ranks the changelog entries between two
versions against the code in the project's own extensions, or recommends the
project-level commands — `fluid:analyze`, `upgrade:list`, the extension scanner,
`composer validate`, `audit`, `outdated`.

The note about the site-maintenance scenario asks for the same thing from the
other end.

## Query

Maintain and further develop a TYPO3 v14 Composer site project; review project structure, updates, changelog, deprecations, features and best practices

## Suggestion

A maintenance guide that reads the discovered installation, states the TYPO3 and
PHP versions it found, and recommends only commands that exist in that
repository. It is a new tool surface rather than a correction, and the scope
statement currently says installation maintenance is out of scope — so this is a
decision about what this server is, not a bug to fix.
