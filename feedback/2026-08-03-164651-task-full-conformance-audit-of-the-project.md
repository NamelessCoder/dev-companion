---
date: 2026-08-03T16:46:51+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_extension_scope
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation (typo3-extension-conformance skill).

typo3_extension_scope reported `classes: [{"kind": "EventListener", "files": 2}]`. The extension actually ships three PHP classes in two directories:

- Classes/EventListener/BackendShellTourEventListener.php
- Classes/EventListener/LoginTourEventListener.php
- Classes/Utility/MascotResolver.php

Classes/Utility/ is missing from the answer entirely — not reported under another kind, just absent. Verified with `find Classes -name "*.php"` in the checkout: 3 files, 2 directories.

The tool description promises "the shape of its Classes/ directory", so an audit that trusts it for exactly that skips a whole directory. Here the omitted class was the consequential one: MascotResolver is the only class that touches the public-resource URL API, and it was directly relevant to a deprecation question raised by the sweep (PathUtility::getPublicResourceWebPath, #107537). I found it only because I also ran `git ls-files` for unrelated reasons. An audit that had taken extension_scope at its word would have reported the package as free of that concern without ever opening the file that decides it.

The same parser looks like it also produced `fluidRoots: ["Resources/Private/Layouts/"]`, which is a directory-derived guess rather than a registration: the extension registers no Fluid root declaratively, it appends the layout root path at runtime inside LoginTourEventListener::__invoke(). With `answeredBy: "installation"` on the response that reads as a runtime fact.

## Query

typo3_extension_scope {extension: "guidedtour"}

## Suggestion

Report every subdirectory under Classes/ with its file count, using a kind such as `Other` or `Unclassified` for directories matching no recognised pattern, rather than dropping them. A total PHP file count under Classes/ beside the per-kind breakdown would make any such omission self-evident to the caller. Separately, mark values that are derived from the directory layout rather than from a registration (fluidRoots here) as such, so `answeredBy: "installation"` is not read as "this is registered".
