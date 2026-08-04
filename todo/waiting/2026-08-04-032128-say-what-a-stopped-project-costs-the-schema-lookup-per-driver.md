# Say what a stopped project costs the schema lookup, per driver

**Serves:** decisions/, documentation/tools/
**Priority:** normal
**Waiting on:** whether the description should name the condition at all, given
    that the tool answers `unsupported` with the reason where it is not met. Two
    readings hold. Naming it per driver — "a MySQL, MariaDB or PostgreSQL
    installation needs its database server to answer, a SQLite one does not" —
    is accurate and lets a caller skip a call it would lose; it also costs three
    clauses in every tool listing and states a Doctrine behaviour this
    repository does not own. Dropping it — "The core is asked for them by
    booting the installation; it says so rather than answering empty when it
    cannot" — is true on every driver and leaves the per-installation reason to
    arrive at call time, where it is measured rather than predicted; it costs
    the caller the warning. The recommendation is dropping it: the condition is
    the installation's rather than the tool's, no other installation-backed
    tool's description names a precondition (they name fallbacks), and a
    condition stated in the description makes a SQLite caller skip a call that
    would have answered. Either way the description is a declared contract, so
    the wording is reviewed rather than improvised. Second question, from the
    same reading: whether `D-DIS-008` is revoked with a successor or stays
    `confirmed` with the **Since then** now at its foot. Its title and its bold
    sentence name a responding database server as the condition, which is what
    the reading disproved, and every `corrected` entry — including the three
    that meant "one named part is wrong and the rest holds" — became `revoked`
    in `0b6adc1`. Revoking costs a successor entry plus
    `bin/cli decisions:index`; the recommendation is to revoke and write the
    successor, because the listing shows the title and the status and nothing
    else.

## What the reading settled, so nobody re-derives it

The condition is the driver's. `Connection::getDatabasePlatform()` builds a
version provider — `StaticServerVersionProvider` where `serverVersion` sits in
the connection parameters, the connection itself otherwise — and hands it to
`Driver::getDatabasePlatform()`. `AbstractSQLiteDriver` ignores it and returns a
`SQLitePlatform`; `AbstractMySQLDriver` and `AbstractPostgreSQLDriver` open with
`$versionProvider->getServerVersion()`, which is `Connection::connect()`. Read
in doctrine/dbal 4.4.4 and 3.10.6, the two the environments install. So a MySQL,
MariaDB or PostgreSQL installation loses this answer with its database down, a
SQLite one never does, and none of the four does where the parameters state a
`serverVersion`. Driven on 2026-08-04 against `.environments/e-site-14.3` on a
host PHP whose PDO carries `mysql` and nothing else, `typo3_schema_lookup`
answered `pages` with its 69 derived columns. `D-DIS-008` carries the whole
reading at its foot.

## What is left

- The description in `src/Tool/SchemaLookup.php`, once the question above is
  answered. As it stands it tells every caller the core "needs its database
  server to answer", which is false on every installation this repository makes.
- `documentation/tools/typo3_schema_lookup.md`, which repeats that sentence
  because it is derived from the description — regenerated rather than edited.
- The comment above the `derivedColumns` topic in `src/Installation/probe.php`
  says the same thing and names `D-DIS-008` for it.
