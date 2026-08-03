---
date: 2026-08-03T16:47:49+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_extension_scope
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation. The extension ships Resources/Private/Layouts/Login.html, a fork of the core backend login layout, registered by appending a layout root path at runtime in an event listener.

Two gaps, one subject: the server cannot help with a forked core file, which is a common and fragile extension pattern.

(1) In v14 the core renamed its Fluid templates to *.fluid.html. My first `diff` against .build/vendor/typo3/cms-backend/Resources/Private/Layouts/Login.html failed with "No such file or directory", and it took a `find` to discover Login.fluid.html before any comparison was possible. That rename appears in no hint returned for Fluid or template paths, and typo3_documentation_lookup did not surface it either (filed separately). It also has a direct bearing on the verdict: the audited file is named Login.html, which no longer follows the current convention — but it still resolves, and saying so required reading typo3fluid/fluid TemplatePaths::resolveFileInPaths() to establish the fallback order (`.fluid.<format>`, then `.<format>`, then bare, per path, over `array_reverse($paths)`). Three shell roundtrips plus a vendor read to answer "does this file still get picked up, and is its name still right".

(2) Nothing maps "this extension file is a fork of that core file". Once I had the diff by hand it was the single most productive artifact of the audit: it proved the fork was taken from a v13 layout despite its own comment and the README both claiming v14.3, because it still uses f:constant on InfoboxViewHelper::STATE_ERROR (deprecated in v14, removed in v15) where 14.3 uses ContextualFeedbackSeverity::ERROR — and because it had silently dropped the `t3js-login-warning-password-whitespace` block that the core's own @typo3/backend/login.js, which the fork itself loads, still queries. Neither is reachable from the extension's files alone; both fall straight out of the diff. Which core file an extension file shadows, and what that file contains in the installed version, are installation facts.

## Query

Shell roundtrip after typo3_extension_scope reported fluidRoots for guidedtour: diff .build/vendor/typo3/cms-backend/Resources/Private/Layouts/Login.html (failed: No such file or directory) → find -name "Login*.html" → diff against Login.fluid.html

## Suggestion

Add the fallback order TemplatePaths::resolveFileInPaths() actually applies to the Fluid/template hints, so a reviewer can judge which of two files that both exist is the one rendered without reading the resolver.

The naming half of this ask is answered and is struck. `fluid-templates` states that template files carry the .fluid.html extension on v14 and that the bare .html form still resolves, so a directory of .html templates is the predecessor rather than a mistake to fix on sight. It was written on 2026-08-02, the day before this was filed, and it is the first hint returned for `Resources/Private/Layouts/Login.html`.

The lookup half is judged and is not built: with the order stated it is the Fluid roots `typo3_extension_scope` already reports plus that chain, after which the diff is one command in a tree the auditor has open. The reading is in `D-ANS-003`, and `D-KNW-052` carries what is left open here.
