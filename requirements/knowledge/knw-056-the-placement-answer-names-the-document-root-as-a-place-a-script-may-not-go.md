---
id: R-KNW-056
title: 'The placement answer names the document root as a place a script may not go'
status: held
restsOn: [D-KNW-026, D-KNW-045]
heldBy:
  - HintsTest::whereAOneOffScriptMayNotGoNamesTheDocumentRoot
---

# R-KNW-056 — The placement answer names the document root as a place a script may not go

**A caller placing a one-off script is told that the document root is a place it
may not go, because a file there is served and outlives the run.**

Which directory that is belongs in the same answer, and so does the project root
above it. Where such a script goes was already answered, and the only place it
named as wrong was `var/` — for the one reason that `var/` is ignored. That
leaves the directory a session actually reaches for unmentioned, and the reason
it gives does not carry: a file in the document root is not ignored, it is
served.

Naming the directory is half of it. `extra.typo3/cms.web-dir` in the root
`composer.json` is what decides it and `public/` is what a project has when the
key is absent, so an answer that names only a path is right about one project;
the container path DDEV serves from is what the same session sees in front of
it, and is named as the example it is.

The two reasons are both needed. Being served is what makes the document root
different from anywhere else a file could be dropped, and outliving the run is
what covers the project root above it — which is where the reported file went,
and which is in the tree that gets deployed.

Reaching it is the other half, and the words are the ones of a session that has
already decided to write the file rather than of one asking where files belong:
the query this came from named a webroot, a PHP script and debugging, and
reached nothing.

## From

`feedback/2026-08-01-003938` (2026-08-01), a session debugging the `Record`
class that wrote `/var/www/html/check_record.php` into a DDEV TYPO3 project and
executed it in the container; the user rejected the action, and nothing in this
corpus had said not to.
