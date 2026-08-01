---
date: 2026-07-29T10:06:19+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: b6952ac
subject: "Name the side an answer is on, and point at the other one"
tool: typo3_rule_lookup
---

# Evaluated this server as the developer of a TYPO3 v14 site (composer project, sitepackage under p...

## Observation

Evaluated this server as the developer of a TYPO3 v14 site (composer project, sitepackage under packages/), asking the questions that actually come up in maintenance: what did v14 deprecate that affects my code, what is in the changelog, how do I upgrade. typo3_rule_lookup "deprecation" answers with how to AUTHOR a deprecation when contributing to core -- do not use [!!!], write a Deprecation-<issue>.rst, run checkRst -- and "changelog" with the RST file format and its index tags. Both are correct for a core contributor and inverted for a site developer, who consumes changelog entries rather than writes them. Unlike typo3_task_guide, typo3_rule_lookup gives no out-of-scope hint at all, so the answer reads as authoritative. I accept this is the declared scope -- typo3_server_scope names "upgrading an installation" as deliberately not covered -- but recording it because the server is started in a site installation, detects it, reads it for icons/labels/modules/config, and then answers its most common maintenance question as if the caller were patching core.

## Query

{"query":"deprecation"} and {"query":"changelog"} asked while maintaining a TYPO3 v14 site project

## Suggestion

Where an answer is scoped to the consuming-vs-authoring split, say which side it is on and point the other way in one line: for "deprecation", note that what a given version deprecated is in typo3/sysext/core/Documentation/Changelog/<version>/ and in the extension scanner matchers under EXT:install/Configuration/ExtensionScanner/Php/, both of which are present in a composer installation as vendor/typo3/cms-*, and that `typo3 upgrade:run` and the Extension Scanner in the Install Tool are the site-side counterparts. That stays inside the stated scope -- it is a pointer, not site documentation -- and turns a confidently misdirected answer into a useful one.
