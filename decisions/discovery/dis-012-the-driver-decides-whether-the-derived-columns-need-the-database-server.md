---
id: D-DIS-012
title: The driver decides whether the derived columns need the database server
date: 2026-08-04
status: open
coveredBy: []
---

# D-DIS-012 — The driver decides whether the derived columns need the database server

**`DefaultTcaSchema::enrich()` reaches the server only where the driver asks for
a version: MySQL, MariaDB and PostgreSQL do, SQLite does not.**

`D-DIS-008` settled this against a MySQL project and named a responding database
server as the condition the answer has. Every installation this repository makes
runs `pdo_sqlite`, and there the columns are derived with the database out of
reach entirely.

## Evidence

- The platform is where a connection is opened or is not.
  `Connection::getDatabasePlatform()` builds a version provider —
  `StaticServerVersionProvider` where `serverVersion` sits in the connection
  parameters, the connection itself otherwise — and hands it to
  `Driver::getDatabasePlatform()`. Read in doctrine/dbal 4.4.4, which
  `.environments/e-site-13.4`, `e-site-14.3` and `e-site-main` install.
- `AbstractSQLiteDriver` ignores that provider and returns a `SQLitePlatform`.
  `AbstractMySQLDriver` and `AbstractPostgreSQLDriver` open with
  `$versionProvider->getServerVersion()`, which is `Connection::connect()`.
- 3.10.6, which `e-site-12.4` installs, splits the same way through
  `VersionAwarePlatformDriver`: MySQL and PostgreSQL implement it, SQLite does
  not, so `detectDatabasePlatform()` asks for a version only for the first two
  and `serverVersion` in the parameters answers before any connection there too.
- TYPO3's `Core\Database\Connection` overrides neither method on 12.4 or on
  14.3, so Doctrine's path is the whole of it.
- The drivers TYPO3 has a check for are `mysqli`, `pdo_mysql`, `pdo_pgsql` and
  `pdo_sqlite` — `DatabaseCheck` on 12.4 and on 14.3, with no mssql among them.
- Measured on 2026-08-04 against `.environments/e-site-14.3`: `pdo_sqlite`, no
  `serverVersion`, and a database file at a path inside the DDEV container.
  Driven through this machine's PHP 8.3.23, whose PDO carries `mysql` alone,
  `typo3_schema_lookup` answered `pages` with its 69 derived columns. There was
  no driver on that host to open a database with.

## Decided

- The description states no precondition. It says the core is asked for the
  columns by booting the installation and that the tool says so rather than
  answering empty when it cannot, which holds on every driver. The other
  installation-backed descriptions name fallbacks and never preconditions, and a
  condition stated there makes a SQLite caller skip a call that would have
  answered.
- The per-installation truth arrives at call time instead, measured rather than
  predicted: the enrichment throws, `probe.php` reports the topic `unavailable`,
  and `SchemaLookup` answers `unsupported` carrying the exception.
- `probe.php`'s comment above the topic names this entry for the same reason,
  since it is where the connection is reached from.
- `D-DIS-008` is revoked rather than left `confirmed`. Its title and its
  statement both name a responding server as the condition, and the reader of a
  listing has the title and the status and nothing else.

- Nothing runs over the split: it needs a MySQL installation with its server
  stopped, which no environment here holds. `ToolContractTest` covers the two
  unanswerable paths and not which driver produced one.
## Assumed

- The installation runs one of the four drivers TYPO3 checks for. A connection
  configured with a `driverClass` of its own decides this for itself, and
  nothing here reads which way it decided.
- Nothing else on the probe's path opens the connection. The enrichment reaches
  it once per table, for the platform, before the field loop.

## Wrong if

- A SQLite installation loses this answer while its project is down: a DBAL
  release where `AbstractSQLiteDriver` asks the version provider, or a TYPO3
  release whose `Connection` overrides `getDatabasePlatform()` and connects.
- A MySQL, MariaDB or PostgreSQL installation whose parameters state a
  `serverVersion` loses it too. Then the provider is not what decides this and
  the split above is one condition short.
- TYPO3 gains a driver none of this covers — mssql was assumed once and is in
  neither branch — so which installations pay stops being readable from these
  four.

## Since then

Re-read on 2026-08-23 and the split still holds where it can be read. In
doctrine/dbal 4.4.4, which `e-site-13.4`, `e-site-14.3` and `e-site-main`
install, `AbstractSQLiteDriver::getDatabasePlatform()` takes the
`ServerVersionProvider` and returns a `SQLitePlatform` without asking it, where
`AbstractMySQLDriver` opens on `$versionProvider->getServerVersion()`;
`e-site-12.4` is still on 3.10.6 and splits the same way. No covered branch
overrides `getDatabasePlatform()` in `Core\Database\Connection` — 12.4, 13.4,
14.3 and `main` declare it nowhere — so Doctrine's path is still the whole of
it.

The driver list is unchanged and reads more precisely than the third bullet.
`DatabaseCheck::$databaseDriverCheckMap` carries the same four on 12.4, 14.3 and
`main`, and it is what "has a check for" means; the wider `$driverMap` beside it
names `pdo_oci`, `oci8` and `ibm_db2` as well, is `@todo`-marked for removal
with the method that reads it, and no check is registered for any of the three.
So a connection on one of those is in neither branch today and TYPO3 does not
check it either.

The second bullet is the one no environment here can produce: every one of them
is `pdo_sqlite`, and `StaticServerVersionProvider` — which is what would answer
before a connection where `serverVersion` sits in the parameters — is in both
DBAL versions. It stays a real case rather than a settled one.
