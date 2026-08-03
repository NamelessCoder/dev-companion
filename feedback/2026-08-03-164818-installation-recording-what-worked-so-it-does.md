---
date: 2026-08-03T16:48:18+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_scope, typo3_backend_module_lookup, typo3_changelog_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation. Recording what worked, so it does not get broken while the gaps I filed separately are worked off.

(1) typo3_extension_scope's `deprecatedFiles` produced a finding I could not have derived from a file listing. It named ext_emconf.php with the exact predicate — composer.json declaring neither extra.typo3/cms.Package.providesPackages nor a version — the changelog issue (#108345), and the part that made the finding proportionate rather than alarmist: that a Composer installation is unaffected because building the package artifact skips the fallback before it is reached, so what raises it is classic mode and functional test instances built like it. That "here is the cost, and here is the condition under which it is zero" shape is exactly what keeps a review from inflating severity on a finding that is technically true and practically inert. It is also the one predicate no code search reaches, because the trigger is the file's existence rather than anything the extension calls.

(2) typo3_backend_module_lookup settled three module identifiers used in the tour JavaScript — web_layout, records, media_management — cheaply and exactly. Reporting the owning extension per module is what turned an identifier check into a real finding: media_management is registered by typo3/cms-filelist, which the audited composer.json declares only under require-dev while its own README lists it as a runtime requirement. I would not have looked for that dependency mismatch had the answer only confirmed the identifier.

(3) The tag-bounded deprecation sweep prescribed by the conformance skill's base worked exactly as described. Bounding by ext:core / ext:backend / JavaScript with the query omitted returned 49 entries, of which two were real for this package; and every call returning the complete tag list meant the second and third calls were read off the first rather than guessed. The base's warning is worth keeping too — querying by the extension's own vocabulary would have matched nothing, because the core titled those entries after its own code.

## Query

typo3_extension_scope {extension: "guidedtour"} → deprecatedFiles; typo3_backend_module_lookup {query: "records" | "web_layout" | "media_management"}; typo3_changelog_lookup {type: "deprecation", version: "14", tag: "ext:core" | "ext:backend" | "JavaScript"}

## Suggestion

Keep deprecatedFiles' shape — predicate, changelog reference, and the condition under which the cost is zero — and consider extending it to the other file-level predicates an extension can trip, so one call covers them all (ext_tables.php in v14.3, #109438, is the obvious sibling; the audited package happened not to ship one, and confirming that took a separate look). Keep the owning-extension attribution in typo3_backend_module_lookup; it is what turns an identifier check into a dependency finding. Keep returning the full tag list on every changelog call.
