# Carry the definition body through the manual reader

**Serves:** feedback/2026-08-07-132457-documentation-page-read-returns-tca-property.md
**Priority:** high

`Documentation::content()` selects `.//dt` and no `.//dd`
(`src/Manual/Documentation.php:490`), so every definition term is emitted as
`**label**` and its body is dropped. On the TCA reference that is the whole
machine-readable half of each property: a caller reading the `nullable` page
gets `**Type**`, `**Default**`, `**Path**` and `**Scope**` each named and each
empty, and the prose beside them survives only because it sits in a `<p>`, which
the same query does select. One review needed the default of `nullable` per
`dbType`, found the cell blank, and read it in the core checkout instead. Add
`dd` to the query and give it a block form that pairs it with the term above.
Take the feedback's fallback seriously where the pairing cannot be made
reliable: emitting neither is better than emitting the label alone, because an
empty cell reads as the property genuinely having no documented default rather
than as the reader having dropped it. There is a recorded case beside it — `dt`
bodies over 300 characters already fall back to the `sig-name` span — so check
what the new `dd` does to those pages before and after. `bin/cli tools:record`
is what writes the answers back down.
