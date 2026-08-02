# What the default preview renderer already draws around a Fluid preview

**Serves:** feedback/2026-08-01-002930-debrief-of-a-typo3-14-backend-preview-task-the.md
**Priority:** normal

Step 1a of the ladder, on the evidence in
[`D-KNW-015`](../../decisions/knowledge/knw-015-a-fluid-preview-template-replaces-the-content-half-and-nothing-here-says-so.md):
the corpus registers a preview template and says nothing about what surrounds
its output, and the manual page it points a session at does not either. Read
`StandardContentPreviewRenderer::renderPageModulePreviewHeader()`,
`renderPageModulePreviewFooter()` and `wrapPageModulePreview()` together with
`GridColumnItem::getPreview()` on `.checkouts/13.4` and `.checkouts/14.3` for
which parts each major draws around the template, and `RecordFieldPreviewProcessor`
on 14.3 for what each header field is passed through — then write it as one
statement beside the existing preview statement on the `content-elements` hint in
`knowledge/architecture-hints/general.json`, naming the header parts by field
rather than as "the header", with a requirement for what has to keep holding. It
carries no `since`: the split holds on both majors. `D-KNW-014`'s todo rewrites
the same statement, so whichever lands second rewrites in place.
