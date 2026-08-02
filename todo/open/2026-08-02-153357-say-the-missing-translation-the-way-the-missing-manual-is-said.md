# Say the missing translation the way the missing manual is said

**Serves:** feedback/2026-07-31-194825-typo3-extension-scope-answered-absences-as.md
**Priority:** normal

Step 4 of the ladder, wording: the fact is computed and the primary answer drops
it. `ExtensionScope::text()` renders `manual`, `readme` and `tests` present or
absent in one `Ships:` line and renders `languageFiles` only where the list is
not empty, so an extension shipping no XLF gets `languageFiles: []` in the data
and no sentence at all in the text — checked against `rte_ckeditor` in
`.checkouts/14.3` on 2026-08-02. Start by reading what
`Extension::artifacts()` puts in that key, then decide what the text says for an
empty list against what `R-PRJ-006` requires and what `R-ANS-002` says about the
other direction, and extend
`ProjectTest::whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut`, which today
asserts the present case in both halves and the absent case in the data alone.
Whether an extension that ships no translations is a state worth a line on every
answer, or one worth a line only where a caller could mistake it for a gap, is
the part to settle before the wording — `D-FBK-018` has the readings and the
reason it is queued rather than closed.
