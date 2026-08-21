# A flex field is resolved to the data structure the installation would use

**Serves:** feedback/2026-08-21-074351-no-tool-resolves-a-tca-flex-field-to-its-data.md
**Priority:** normal

Build `typo3_flexform_lookup`: a table and a field go in, and what comes back is
the identifier `FlexFormTools::getDataStructureIdentifier()` produces, its
decoded form, and the structure `parseDataStructureByIdentifier()` returns for
it. The record is emulated from values the caller passes — `CType` is the one
every plugin needs — and no row is ever loaded, which is what keeps this on the
side of `knowledge/server-scope.json` the server declares it covers. Where the
resolution throws, the exception is the answer: an empty `ds`, a field that is
not `type=flex`, a record type nothing is registered for.

Three things are read before the schema is written. What the answer carries per
sheet and per field, since the prepared TCA of an element is larger than what a
caller writing a FlexForm needs. How a second topic is passed to `probe.php`,
where `$configurationPath` is the only parameterized one today and a substituted
literal per topic is what a second caller turns into a shape. And what differs
per covered major, verified on both sides of each boundary: `ds_pointerField`
and the keyed `ds` array on 12.4 and 13.4, `columnsOverrides` on 14.3 and
`main`, and the `TcaSchema` the v14 signature wants from `TcaSchemaFactory`.

`D-ANS-095` carries what was measured and what would show the boundary wrong.
The new tool is also a line in `covers` and in `routing` in
`knowledge/server-scope.json` and a page under `documentation/server/tools/`.
