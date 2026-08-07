# Name what schema_lookup answers in the words a caller holds

**Serves:** feedback/2026-08-07-065329-schema-lookup-was-read-in-the-tool-list-and.md
**Priority:** normal

`typo3_schema_lookup` returns `notnull` and `default` per derived column, and
its description says neither word. A patch author held exactly that question for
most of a session — is the column this TCA type produces nullable, what default
does it carry — read the tool in the list, and never fetched its schema, because
"schema lookup" read as being about which fields a table has. Put the two fields
in the description in the words the question is asked in. Say the boundary in
the same breath, because the assumption that kept the tool shut a second time
was right: it describes what TYPO3 would derive for a table in the booted
installation, so it answers nothing about a table that exists only inside a
functional-test instance, and nothing about a TCA type in the abstract. That
second half is the gap `D-KNW-063` takes on, and this card is only the wording —
do not let it grow into the corpus work. `bin/cli tools:check` holds the
reference to what the registry declares, and `bin/cli tools:index` rewrites it.
