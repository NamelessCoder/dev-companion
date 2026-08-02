# What a v14 preview template is handed, and what a field on it resolves to

**Serves:** feedback/2026-08-01-002745-task-show-assigned-related-groups-in-a-typo3-14.md
**Priority:** normal
**Branch:** todo/what-a-v14-preview-template-is-handed-and-what-a-field-on-it-resolves-to
**Claimed:** 2026-08-02

Step 1a of the ladder, on the evidence in
[`D-KNW-014`](../../decisions/knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md):
nothing below `knowledge/` or `skills/` names the Record API, and the one
preview statement there registers a template without saying what it receives.
Establish it from the checkouts — `FluidBasedContentPreviewRenderer` on
`.checkouts/13.4` and `.checkouts/14.3` for which variables each major assigns,
`Core\Domain\Record` with typo3fluid/fluid 5.3.1's
`StandardVariableProvider::getByPath()` for how `{record.header}` resolves at
all, and `Core\DataHandling\RecordFieldTransformer` with the worked templates
under `theme_camino/Resources/Private/Templates/ContentPreviews/` for what a
relational field comes back as and which field types reach that branch — then
write it as `since`/`until` statements on the `content-elements` hint in
`knowledge/architecture-hints/general.json`, with a requirement for what has to
keep holding.
