# Say what a contentRenderingTemplates entry is read by

**Serves:** feedback/2026-07-31-174526-stale-registration-detection-gap-ext-localconf.md
**Priority:** normal
**Branch:** todo/say-what-a-contentrenderingtemplates-entry-is-read-by
**Claimed:** 2026-08-02

Write into the `extension-files` hint in `knowledge/architecture-hints/php.json`
what `$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates']` is read by,
so a reviewer can tell a dead entry from a harmful one without opening the core.
The fact is established and holds unversioned: the key is a membership list, and
every use of it decides whether
`FE['defaultTypoScript_<constants|setup>.']['defaultContentRendering']` is
appended while a legacy `EXT:<key>/Configuration/TypoScript/…` static include is
resolved. It is read in `SysTemplateTreeBuilder::addStaticMagicFromGlobals()`
and `TreeFromLineStreamBuilder` on 13.4, 14.3 and `main`, and in those two plus
`TypoScriptParser` and `TemplateService` on 12.4; `fluid_styled_content`
registers itself the same way on every branch, so nothing about it is
deprecated and no changelog entry mentions it. What follows for a reader is the
half worth writing: an extension whose TypoScript has moved into
`Configuration/Sets/` has no such static include left, so its entries can never
be matched and the lines are a cleanup rather than a defect. Decide while
writing whether the `site-sets` hint in `typoscript.json` gets a sentence
pointing at it, since that is the migration these entries are left over from.
`D-ANS-003` carries the judgement and says why this is a hint rather than the
detector the feedback asked for.
