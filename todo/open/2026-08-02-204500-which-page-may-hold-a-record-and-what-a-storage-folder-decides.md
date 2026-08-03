# Which page may hold a record, and what a storage folder decides about it

**Serves:** feedback/2026-08-01-003927-no-understanding-of-pid-storage-semantics.md
**Priority:** low

Step 1a of the ladder, on the evidence in
[`D-KNW-023`](../../decisions/knowledge/knw-023-which-page-may-hold-a-record-is-a-gap-this-server-owns.md):
`datahandler-persistence` says where a new record is positioned on a page and
nothing here says which page may hold it, so a session seeding a table of its own
has nothing when it picks the pid. Read `DataHandler::hasPermissionToInsert()`
and `isTableAllowedForThisPage()` with
`PageDoktypeRegistry::isRecordTypeAllowedForDoktype()` and
`getAllowedTypesForDoktype()`, and the `pages` TCA entries for
`DOKTYPE_SYSFOLDER` and `DOKTYPE_DEFAULT`, on `.checkouts/12.4`,
`.checkouts/13.4` and `.checkouts/14.3` — for what a storage folder allows that a
standard page refuses, what the TCA root-level capability decides for `pid = 0`,
and where the allowed list is declared on each major, `PageDoktypeRegistry` up to
13.4 and TCA `allowedRecordTypes` on 14.3. Then write it as one statement beside
the positioning-pid statement on `datahandler-persistence` in
`knowledge/architecture-hints/records.json`, with a version range only where the three
majors differ, and a requirement for what has to keep holding.
