# Report the setup that cannot finish on `main`, and take the workaround back out

**Serves:** scenarios/
**Priority:** normal
**Waiting on:** who files the report below on Forge — writing to it needs an
    authenticated account and this repository holds no credentials for one — and
    whether the sqlite driver is still meant to be reversed at all, now that it
    is every line's driver for a reason the defect did not buy.

The report is written and verified; what is left is a person with a Forge
account pasting it. The second half of this card is not doable as it stands:
`Environments::DEVELOPMENT_DRIVER` was removed in `c27f8bd` on the same day this
card was written, and there is no development-only workaround left to take out.

## What the reading corrected

The defect is real and unreported, and both halves of this card's account of it
were wrong. It did not arrive with the DBAL bump and it does not live on `main`
alone: Forge #110258, *Use Doctrine introspection API for table and database
names*, was merged to `main`, `14.3` and `13.4` on 2026-08-02 and is what put
the refusing call into `getDatabaseList()`. `12.4` is on `doctrine/dbal ^3.9`,
has no introspection API to move to, and keeps the old call.

The evidence behind the old account is what misled it. `.checkouts/` was fetched
before 2026-08-02, so all three branches read there still show `listDatabases()`
— while the installed `typo3/cms-install` at `af648f05bbc3` already carried the
new one. Reading the same file in two places that disagreed about the date
produced "the same call is in 14.3 and does not refuse". `13.4` and `14.3` lock
the same `~4.4.3` as `main`; the DBAL version was never the difference.

Why the `E-SITE` on 14.3 built anyway: it installs `^14.3`, and no release
carries the backport yet — `typo3/cms-install` v14.3.5 and v13.4.33 are both
from 2026-07-14, read from packagist on 2026-08-03. The next patch release of
either line ships the failure.

Nothing on Forge reports it: five differently worded searches, including
`introspectDatabaseNames`, return #110258 itself and nothing else. Nothing in
Gerrit fixes it either — `file:SetupDatabaseService.php after:2026-08-01` is the
three merged backports of #110258 and no fourth change.

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

## What is behind the question

`D-EVI-006` carries the finding and what it costs, corrected in place. Its
**Wrong if** still says the workaround is the development line's; every line is
on sqlite since `c27f8bd`, for a reason of its own — no database container, and
`rm -rf` is the whole of taking an environment away.

So reversing is no longer one constant. It is putting all four lines back on
MariaDB, against a decision made after this card was written and on grounds this
card does not address, and `main` would not install at all until the defect is
fixed. That is why the question is asked rather than answered here.
