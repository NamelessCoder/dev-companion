---
date: 2026-08-19T09:43:15+00:00
category: idea
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_hint_lookup, typo3-extension-health
directory: /home/benji/projects/blog
---

# What kept me from four wrong findings: deprecatedFiles, the conditions hint, and the sinks rule

## Observation

Task: full audit of the blog extension before its official v14 release. Recording what worked, because it is what must not be broken later. Four concrete saves, each of which would otherwise have cost a wrong finding or a wrong fix.

1. typo3_extension_describe -> deprecatedFiles. It reported ext_tables.php (#109438) and ext_emconf.php (#108345) with the predicate and the cost, before I had opened a single file. Both survived to the final report as findings, and the ext_emconf one carried the exact condition — "composer.json declares neither providesPackages nor a version" — which is what made it actionable rather than a note. A file listing cannot produce either. This field is the highest-value thing the server gave me in the whole session.

## Query

typo3_extension_describe extension=blog

## Suggestion

Keep deprecatedFiles on typo3_extension_describe exactly as it is — predicate plus cost, and reported for files that are present rather than only when asked. If the same treatment can be extended to other whole-file predicates (a Configuration/ file a version now expects, one it no longer reads), that is the same value again.

## Answered

The three other saves and the two failure modes it would not change. Every
quotation reproduces, and the two keep-requests that rested on nobody rewriting
the file are held now: `security-sinks` was in no assertion at all, and the
clause refusing `$GLOBALS['TYPO3_REQUEST']` was covered only for its verdict.

The plugin-registration item was the lever rather than a save. The hint stated
the constraint on the literal `"CType"`, the extension passes
`ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT`, and reading the core is what
told the session those are one thing. The hint names the constant now, and the
removal of `PLUGIN_TYPE_PLUGIN` beside it — `D-FBK-018`.

The ask above is answered and the field is unchanged in the way it was asked to
be kept: predicate plus cost, per file that is present. The set is four.
`ext_icon.svg`, `.png` and `.gif` join it as `#98093`, and
`ext_typoscript_setup.txt` beside `ext_typoscript_constants.txt` as `#96518` —
the second being the case this report named in its own words, a file a version
no longer reads. Each carries the half a listing cannot see: what core reads
first, and what that leaves the file costing. `D-ANS-009` has the sweep, both
boundaries and what was read and left out.
