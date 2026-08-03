# Report the setup that cannot finish on `main`, and take the workaround back out

**Serves:** scenarios/
**Priority:** normal

`vendor/bin/typo3 setup` cannot finish against MariaDB on the development line
at all, and nothing outside this repository knows.
`SetupDatabaseService::getDatabaseList()` builds a connection with `dbname`
unset on purpose — so that a wrong database name can still be corrected — and
then asks it for a schema manager. `doctrine/dbal` 4.4.4 reads
`SELECT DATABASE()` in `MySQLMetadataProvider::__construct`, gets null, and
throws `DatabaseRequired`. `SetupCommand::selectAndImportDatabase()` runs that
for every driver but sqlite, before anything is written, so no option of the
setup avoids it. Found on 2026-08-03 by building the environment rather than by
reading anything, and that is the first thing holding an installation of the
development line has bought.

Everything a report needs is established: `typo3/cms-install` at `af648f05bbc3`
and `typo3/cms-core` at `300245e2`, against the DBAL that branch locks itself
(`~4.4.3`, resolved 4.4.4). The same call is in `14.3`, where the DBAL is older
and does not refuse, so this arrived with the DBAL bump rather than with a
change to the install extension. The step is to raise it on Forge — how Forge
has to be operated is
`todo/open/2026-08-02-145217-task-assess-forge-105403.md`'s subject and is
already written down there.

Then take the workaround out. `Environments::DEVELOPMENT_DRIVER` is the whole
of it: the development line is installed on sqlite because that is the one path
`setup` completes on, and an installation on sqlite answers what a console
question asks and says nothing about what MariaDB does under the same schema.
`D-EVI-006` carries that cost as a **Wrong if**, and nothing re-checks it —
which is why this card exists rather than the entry alone.
