---
id: D-DIS-008
date: 2026-08-02
status: open
---

# D-DIS-008 — The columns TYPO3 derives are reachable where the database server is

**`DefaultTcaSchema::enrich()` needs a booted container and a reachable database
server, and nothing else — not a populated schema, not SQL, not a log.**

`REVIEW-02` run 5 left the v13 delta-only rule for `ext_tables.sql` unraised,
"since I did not verify each column against the schema analyzer's TCA-derived
output". Whether that output can be had at all is what this settles.

## Evidence

- Read in `.checkouts/13.4` and `.checkouts/14.3`. `enrich()` is three passes:
  the ctrl-derived columns, the TCA-column-derived ones, and the MM tables. Only
  the middle one touches a connection, at `456` on 13.4 and `497` on 14.3, and
  it fetches the platform per table before the field loop.
- That platform is used in exactly one branch — `891` and `902` — a SQLite
  workaround that stores a decimal-formatted number field as a string. Every
  other column it derives is the same on every platform, and `quote()` writes
  backticks unconditionally.
- `getConnectionForTable()->getDatabasePlatform()` needs the server, not the
  schema: on MySQL and MariaDB the platform follows from the server version. So
  an installation whose database has no TYPO3 tables yet still answers, and one
  with no database at all does not.
- `enrich()` demands an incoming `Table` object per TCA table and throws
  otherwise, which is what makes the derived set readable: handed empty tables,
  what it adds is exactly the columns an `ext_tables.sql` may leave out.
- The way in exists. `probe.php` already boots the container and reads `$TCA`
  out of it, which is `D-DIS-005`. On 13.4 the class reads `$GLOBALS['TCA']`
  itself and takes no constructor argument; on 14.3 it takes a
  `TcaSchemaFactory` and defaults it through `makeInstance`.

## Decided

- Reachable, under one condition more than the other runtime answers have: a
  database server that responds. A stopped project therefore answers this with
  the `unsupported` shape the installation-backed tools already carry, and the
  reason is the one `Typo3Cli` already reports for a container that is down.
- The tool is a step of its own and is queued, bounded to the derived side: the
  columns TYPO3 adds for a table, and nothing about the live schema.

## Assumed

- The class is `@internal` on both branches and the probe would call it anyway,
  which is the same bargain `D-DIS-005` took for the registries. What makes it
  bearable is that it is asked of the installation being read rather than
  bundled: a signature that moves fails on that installation and is reported,
  rather than making a bundled statement wrong everywhere.

## Wrong if

- `enrich()` starts needing the schema rather than the server — reading a table
  that must already exist, or a doctrine platform that connects for more than
  its version. Then the answer is available only after an installation and the
  rule stays unraisable in a fresh extension checkout, which is where it is
  asked.
- The one SQLite branch grows into several, so what is derived stops being a
  property of TCA and becomes one of the platform the caller happens to run.

## Covered by

- Nothing yet: what is settled here is that the answer exists, and the todo that
  builds it is what a test can hold.
