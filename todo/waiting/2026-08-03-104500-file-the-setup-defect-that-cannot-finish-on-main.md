# File the setup defect that cannot finish on `main`

**Serves:** scenarios/
**Priority:** normal
**Waiting on:** somebody with an authenticated Forge account pasting the report
    below. This repository holds credentials for none, and nothing here can
    produce one. Answered on 2026-08-04: the person who queued the card files
    it, so a session reaching this is not the first to look at it and re-reading
    the defect buys nothing.

The report is written and verified, and the reading behind it is on `D-EVI-006`.
What is left is one act outside this checkout, and the issue number coming back
is what closes the card.

The second half this card opened with is gone rather than deferred. It asked for
the sqlite driver to be reversed as a workaround with a date on it;
`Environments::DEVELOPMENT_DRIVER` was removed in `c27f8bd` on the day the card
was written, and every covered line has been on sqlite since, for reasons the
defect did not buy. Put to the maintainer on 2026-08-04, the answer was that
sqlite stays — recorded on `D-EVI-006`, whose **Wrong if** now watches what is
actually carried rather than a workaround nobody has.

## The report, ready to file

**Subject:** Install: database selection fails on MySQL and MariaDB since the
Doctrine introspection change

`SetupDatabaseService::getDatabaseList()` builds a connection with `dbname`
unset on purpose — so that a wrong database name can still be corrected — and
then asks its schema manager which databases there are. Since #110258 that call
is `introspectDatabaseNames()`, which goes through
`AbstractSchemaManager::createSchemaProvider()` and constructs
`MySQLMetadataProvider`. That constructor runs `SELECT DATABASE()`
unconditionally and throws `DatabaseRequired` where the result is null, which it
always is on a connection with no database selected.

The provider's own `getAllDatabaseNames()` reads `information_schema.SCHEMATA`
and needs no current database. Only the constructor does, so the listing this
call site wants is available and the object that would produce it cannot be
built.

Two call sites reach it:

- `SetupCommand::selectAndImportDatabase()`, for every driver but `pdo_sqlite`,
  before anything is written. It catches the DBAL exception, prints *A database
  is required for the method:
  `Doctrine\DBAL\Platforms\MySQL\MySQLMetadataProvider::__construct`* and
  returns `Command::FAILURE`, so `vendor/bin/typo3 setup` cannot complete
  against MySQL or MariaDB by any option it offers.
- `InstallerController::showDatabaseSelectAction()`, which catches it into
  `errors` and renders the step with an empty database list, so the browser
  installer offers no database to pick.

Affected: the `main`, `14.3` and `13.4` branches. No release carries it yet, so
an installation made from a released tag does not see it. `12.4` is unaffected.

Reproduced on 2026-08-03 by `bin/cli environment:create E-SITE main` against
MariaDB, on `typo3/cms-install` at `af648f05bbc3` and `typo3/cms-core` at
`300245e2`, with `doctrine/dbal` 4.4.4 — the version all three branches lock as
`~4.4.3`.

## What the reading established

Forge #110258, *Use Doctrine introspection API for table and database names*,
was merged to `main`, `14.3` and `13.4` on 2026-08-02 and is what put the
refusing call there. It did not arrive with a DBAL bump and it does not live on
`main` alone: `13.4` and `14.3` lock the same `~4.4.3`, and `12.4` is on
`doctrine/dbal ^3.9` with no introspection API to move to.

Nothing on Forge reports it: five differently worded searches, including
`introspectDatabaseNames`, return #110258 itself and nothing else. Nothing in
Gerrit fixes it either — `file:SetupDatabaseService.php after:2026-08-01` is the
three merged backports of #110258 and no fourth change.

The 14.3 environment built anyway because it installs `^14.3`, and no release
carries the backport: `typo3/cms-install` v14.3.5 and v13.4.33 are both from
2026-07-14, read from packagist on 2026-08-03. The next patch release of either
line ships the failure.
