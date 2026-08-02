# How a datamap writes an inline relation, and what the parent column holds

**Serves:** feedback/2026-08-01-003216-lacked-datahandler-knowledge-and-worked-around.md
**Priority:** normal
**Branch:** todo/how-a-datamap-writes-an-inline-relation
**Claimed:** 2026-08-02

Step 1a of the ladder, on the evidence in
[`D-KNW-018`](../../decisions/knowledge/knw-018-what-a-datamap-does-to-a-relation-field-is-a-gap-this-server-owns.md):
`datahandler-persistence` says how a scalar field is written through a datamap
and stops there, so a session seeding an element with inline children has
nothing for the first relation it reaches. Read
`DataHandler::checkValue_inline_processDBdata()` and
`checkValue_group_select_processDBdata()` together with
`RelationHandler::writeForeignField()` and `countItems()` on `.checkouts/12.4`,
`.checkouts/13.4` and `.checkouts/14.3` for what the datamap value of an inline
field is, what `foreign_field` writes onto the child, and what the parent's own
column ends up holding — then write it as one statement beside the datamap
statement on the `datahandler-persistence` hint in
`knowledge/architecture-hints/php.json`, with a version range only where the
three majors differ, and a requirement for what has to keep holding.
