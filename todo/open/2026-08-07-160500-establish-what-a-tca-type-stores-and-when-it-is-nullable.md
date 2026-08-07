# Establish what a TCA type stores and when it is nullable, then write it

**Serves:** feedback/2026-08-07-065228-tca-type-datetime-stores-null-as-an-empty-value.md, feedback/2026-08-07-065342-documentation-lookup-has-no-page-for-extbase.md
**Priority:** high

Read the storage side of a TCA type in the core checkouts and write what holds,
starting with `type=datetime` because that is where it was hit and where a wrong
verdict reached a user. `bin/cli checkouts:update` first — all four are missing
in this working directory, which is why this is queued rather than answered. The
four places the reporting session names are where the reading starts and not
what it concludes: `DateTimeFactory::fromDatabase()`,
`DateTimeFieldType::isNullable()` (it claims `nullable` defaults to false for an
integer column and true for a `dbType` column),
`QueryHelper::transformDateTimeToDatabaseValue()`, and
`Backend::insertObject()`, which is said to omit a null property from the INSERT
so the schema default decides what is stored. Check each on both sides of a
version boundary and name the checkout in the commit. The statement the corpus
is missing, if the reading confirms it, is that a non-nullable `type=datetime`
column never holds SQL NULL — it stores 0 or the zero-date literal, which is
mapped back to null on read, so a property reading null does not mean the column
is NULL.
`bin/cli hints:probe "TCA datetime nullable null empty value stored in the database"`
reaches only `tca-formengine` today, on a weak match. Settle at the same time
whether the manual really has no page for `equals($property, null)`, because
that decides whether the second feedback is a corpus gap or a routing one —
`D-KNW-063` carries both.
