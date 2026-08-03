# Say which registration files the deprecation check covered

**Serves:** feedback/2026-08-03-164818-installation-recording-what-worked-so-it-does.md
**Priority:** normal
**Branch:** todo/say-which-registration-files-the-deprecation-check-covered
**Claimed:** 2026-08-03

`ExtensionScope::answer()` at `src/Tool/ExtensionScope.php:206` closes the
deprecated-files block with *typo3_changelog_lookup is what answers that — these
two entries whole*, on an answer that renders one entry where one of the two
predicates fired, and it names neither file. `Extension::deprecatedFiles()` at
`src/Installation/Extension.php:1213` checks `ext_tables.php` and
`ext_emconf.php`, and the covered set stands only in the tool description and in
the `deprecatedFiles` schema description — so a caller reading the text cannot
tell a file that was checked and not found from one that was never looked at,
and the reporting session confirmed the absent sibling by hand. Settle against
the tool which shape says it: the closing sentence naming both files, or a line
per file that was checked and did not fire. Leave the empty-list case as it is —
`D-ANS-009` decided that nothing is rendered where nothing fired, and this
report does not bear on it, because its block rendered. The rendered block is in
no assertion today; the tests that drive the rendered answer are in `ProjectTest`
beside `whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut`. `normal` because
one session reported it and the sentence is wrong as it stands rather than only
thin.
