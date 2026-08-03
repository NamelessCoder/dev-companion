# Say that the manual has no page for a PHP identifier

**Serves:** feedback/2026-08-03-164805-installation-there-is-no-cheap-way-to-ask-is.md
**Priority:** normal

`skills/base.md` routes "does this still work in version N" to
`typo3_documentation_lookup`, and `skills/typo3-extension-conformance/SKILL.md`
repeats it for every surface a review raises. The manual matches page titles and
section paths and never the text of a page, so a PHP identifier reaches nothing:
at `targetVersion: "14"` the queries `inline language labels`,
`JavaScript labels backend` and `addInlineLanguageLabelFile` return no page
naming `PageRenderer::addInlineLanguageLabelFile()`, while
`Infobox ViewHelper state` returns the reference page carrying the deprecation
first. Write the distinction where the call is ordered — a documented surface
goes to the manual, an identifier goes to `typo3_changelog_lookup` by its own
name and then to the class `## When the lookups run out` already names. Which of
the two files carries it is the choice: `skills/base.md` reaches every skill and
its word count is what `D-SKL-001` watches, the conformance skill is where the
narrower condition already stands. `D-ANS-010` carries the readings.
