---
date: 2026-08-03T16:48:18+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_scope
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a
TYPO3 14.3.5 installation. Recording what worked, so it does not get broken
while the gaps I filed separately are worked off.

typo3_extension_scope's `deprecatedFiles` produced a finding I could not have
derived from a file listing. It named ext_emconf.php with the exact predicate —
composer.json declaring neither extra.typo3/cms.Package.providesPackages nor a
version — the changelog issue (#108345), and the part that made the finding
proportionate rather than alarmist: that a Composer installation is unaffected
because building the package artifact skips the fallback before it is reached.
That "here is the cost, and here is the condition under which it is zero" shape
is what keeps a review from inflating severity on a finding that is technically
true and practically inert. It is also the one predicate no code search reaches,
because the trigger is the file's existence rather than anything the extension
calls.

What the answer does not say is which files it looked at. ext_tables.php in
v14.3, #109438, is the obvious sibling; the audited package happened not to ship
one, and confirming that took a separate look.

## Query

typo3_extension_scope {extension: "guidedtour"} → deprecatedFiles

## Suggestion

Extend `deprecatedFiles` to the other file-level predicates an extension can
trip, so one call covers them all — and say which ones a call covered, so the
absence of a file from the block can be read as an answer rather than confirmed
by hand.
