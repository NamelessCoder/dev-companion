# Say what a translation file with no `target-language` renders

**Serves:** R-KNW-061, feedback/2026-08-03-164659-installation-the-highest-impact-finding-of-the.md
**Priority:** normal
**Branch:** todo/say-what-a-translation-file-with-no-target-language-renders
**Claimed:** 2026-08-03

[`D-KNW-050`](../../decisions/knowledge/knw-050-what-a-missing-target-language-does-to-a-translation-file-is-a-gap-this-server-owns.md),
step 1a: `language-files` names `target-language` only inside the correction of a
source file that is not English, so nothing in it fires on a translation file
that is already missing the attribute. Add a statement of its own to the hint in
`knowledge/hints/labels.json`, written as the defect rather than as the rule — a
locale-prefixed XLF whose `<file>` element declares no `target-language` is read
as the default language, `<target>` is never taken, the labels render in the
source wording, and nothing is raised, logged or deprecated. It carries
`since: 14`: `XliffLoader::parseXliff1()` decides it on
`!isset($fileTag['target-language'])` in `.checkouts/14.3`, while `XliffParser`
in `.checkouts/13.4` and `.checkouts/12.4` decides on `$this->languageKey`, so
the file was read correctly before v14 and this is an upgrade finding too.
Establish before writing whether `original` and `datatype` belong beside it — the
feedback claims XLIFF 1.2 requires all three on `<file>` and that was not
checked. Then settle the reach, which is the half no wording change answers on
its own: whether `skills/typo3-extension-upgrade` and
`skills/typo3-extension-conformance` reach the statement at all, with
`bin/cli hints:coverage` as the measure, and whether the XLF schema check the
feedback asks for belongs in the check layer `typo3-extension-testing` describes
or nowhere. The case goes into `HintsTest` beside
`aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes`, which is what turns
`R-KNW-061` from `open` to `held`. Archive the feedback in that same commit.
