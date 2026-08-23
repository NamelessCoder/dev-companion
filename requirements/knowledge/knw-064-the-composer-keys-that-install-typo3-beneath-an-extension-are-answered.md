---
id: R-KNW-064
title: 'The Composer keys that install TYPO3 beneath an extension are answered'
status: held
restsOn: [D-KNW-053]
heldBy:
  - HintsTest::installingTypo3BeneathTheExtensionNamesTheInertKey
---

# R-KNW-064 — The Composer keys that install TYPO3 beneath an extension are answered

**An extension repository that installs TYPO3 beneath its own `composer.json` is
answered with the keys that move the installation, the key that only warns, and
the root package's placement.**

Two of the three are traps rather than absences. `extra.typo3/cms.app-dir` is
taken at write time and reported at install time, so `config/` and `var/` land
in the versioned tree of a session that believed it had moved them; and a root
require of `typo3/cms-cli` fails with a message naming `typo3/cms-core`, so the
line that looks wrong is not the line that is. The placement is the third: the
root package is loaded from the Composer root and nothing is installed into
`typo3conf/ext/`, which otherwise reads as a broken installation.

## From

A session giving a standalone extension repository a local environment, which
found all three by trial and reported them as missing knowledge
(`feedback/2026-08-03-162759`). The corpus named `extra.typo3/cms.web-dir` only
to say which directory is served, and `app-dir`, `vendor-dir`, `bin-dir` and
`cms-cli` occurred nowhere below `knowledge/` (2026-08-03).
