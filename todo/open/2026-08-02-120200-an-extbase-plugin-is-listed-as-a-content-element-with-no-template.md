# An Extbase plugin is listed as a content element with no template

**Serves:** feedback/2026-07-31-193109-task-typo3-extension-conformance-audit-what-i.md
**Priority:** normal

Step 1b, on `D-ANS-015`: `typo3_extension_scope` lists an `ExtensionUtility::registerPlugin()`
identifier under "Content elements it adds" and reports "no templateName in this
extension's TypoScript", which sent a real audit looking for two files nothing
was going to write. Establish first, against `.checkouts/14.3` and `.checkouts/13.4`,
what a CType-registered Extbase plugin renders through and whether
`tt_content.<identifier>.templateName` reaches it at all — the extension under
`/home/benji/projects/site-new` sets only `plugin.tx_<signature> < lib.…` — then
decide in `src/Installation/Extension.php` whether such an identifier is told
apart from a `templateName` element and what the answer says instead of an
absence. `R-ANS-012` is the requirement it stands under.
