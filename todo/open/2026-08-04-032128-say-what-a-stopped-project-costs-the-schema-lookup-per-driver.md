# Say what a stopped project costs the schema lookup, per driver

**Serves:** decisions/, documentation/tools/
**Priority:** normal

`D-DIS-008` decides that the derived columns are reachable "under one condition
more than the other runtime answers have: a database server that responds", and
`typo3_schema_lookup`'s own description tells the caller the core "needs its
database server to answer". Both were settled against a MySQL project, and every
environment this repository makes now runs `pdo_sqlite` — the workaround
`todo/waiting/2026-08-03-104500-report-the-setup-that-cannot-finish-on-main-and-take-the-workaround-back-out.md`
is about. Driven against
`.environments/e-site-14.3` with its DDEV project stopped on 2026-08-04 the tool
answered `pages` with the same 69 derived columns it answers running, from a
host PHP with no database driver at all, because DBAL 4.4 asks the driver for
the platform and `AbstractSQLiteDriver::getDatabasePlatform()` returns one
without consulting a server, while `AbstractMySQLDriver` reads the server
version unless `serverVersion` sits in the connection parameters. So the
condition is the driver's rather than the tool's. Read both drivers in an
installation's `vendor/doctrine/dbal` and `Connection::getDatabasePlatform()` in
`.checkouts/14.3`, then say in the decision and in the description which
installations really lose this answer with the project down — and whether the
description should name the condition at all, given that the tool answers
`unsupported` with the reason where it is not met.
