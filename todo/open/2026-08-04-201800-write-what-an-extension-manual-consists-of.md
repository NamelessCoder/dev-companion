# Write what an extension manual consists of

**Serves:** feedback/2026-08-04-175804-task-add-the-typo3-recommended-manual-to-a.md, knowledge/documents/
**Priority:** normal

`D-KNW-061` decided that this is a document below
`knowledge/documents/extension/documentation/` and that the hint keeps the
policy. Establish the scaffold first: read the render-guides documentation for
what `guides.xml` declares and which of it is required, and check that against
the `Documentation/` directory of two packages in an installation's `vendor/`
and against what the TYPO3 documentation team publishes as the starting point.
Then write the document — the file inventory, one `guides.xml` an extension can
copy, the `Index.rst` header with its toctree, and the render command with the
flag that turns a warning into a non-zero exit — declaring what it is and when
to reach for it, and naming `extension-documentation` in its own `hints:` front
matter, which is how a document and the statements around it are tied. Say what
the document does not cover: what the chapters say is the author's, and
`documentation-changelog` owns the core's own artifact.
