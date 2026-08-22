---
id: D-EVI-005
title: 'A registration nothing can reach is cleared with its database'
date: 2026-08-02
status: open
coveredBy:
  - EnvironmentsTest::aRegistrationWhoseCheckoutIsGoneHoldsNothingBack
  - EnvironmentsTest::clearingARegistrationTakesTheDatabaseThatWouldOutliveIt
---

# D-EVI-005 — A registration nothing can reach is cleared with its database

**A DDEV registration whose approot is gone is cleared by `environment:create`
rather than refused in its name, with `ddev delete` so the database it still
owns goes too.**

`D-EVI-004` made the environment creatable and left the second machine to run
the build stuck: the name is global and the directory is per checkout, so a
worktree that made an environment and was removed holds the name on behalf of a
directory nobody can visit.

## Evidence

- Measured on 2026-08-02, on this machine, before anything was changed.
  `typo3-mcp-e-site` was registered at
  `.worktrees/record-what-the-installation-backed-tools-answer-when-a-console-answers/.environments/e-site`,
  a directory that did not exist. DDEV's own `status` for it was the string
  `project directory missing`. `bin/cli environment:create E-SITE` refused,
  naming that path and saying nothing about what to do, and
  `bin/cli environment:status` said
  `missing — run bin/cli environment:create E-SITE`, which was advice that could
  not work.
- The database is a volume named after the project, not after the directory:
  `typo3-mcp-e-site-mariadb` was on the machine beside the dead registration,
  with `ddev-typo3-mcp-e-site-snapshots`. That is why a build under the same
  name after an unlist meets the tables the last installation left —
  `The selected database contains already 42 tables.`
- DDEV 1.25.1's own help separates the two commands. `stop` is "a
  non-destructive operation and will leave database contents intact", and
  `--unlist` only removes the project from the global list. `delete` "Removes
  all DDEV project information (including database)".
- `ddev delete --omit-snapshot -y typo3-mcp-e-site` was run against the
  registration whose directory was already gone: exit 0, containers removed,
  both volumes and the two built images with them. The missing directory is not
  an obstacle to it.
- `--force` reaches the settings file and nothing else. In `.checkouts/14.3` at
  `v14.3.5-81-gfaf60eea22` it is declared
  `Force settings overwrite - use this if TYPO3 has been installed already`, and
  `prepareSystemSettings($force)` is its only use in `SetupCommand`. The table
  check is `$dbNameValidator` in `selectAndImportDatabase`, which throws
  whenever `tables !== 0` and is called on the non-interactive path as much as
  the asked one. No option of the setup gets past it.
- The fix was run end to end afterwards, on a registration reproduced the same
  way — configured, started so it had a volume, then its directory removed.
  `environment:create E-SITE` cleared it and built a working installation in 32
  seconds: frontend 200, and `site:list`, `configuration:show` and
  `language:domain:search` all answering through the console.

## Decided

- The predicate is whether the registered approot is a directory, not whose
  checkout it was. An `rm -rf .environments` in this checkout leaves the same
  orphaned volume as a removed worktree does, and one rule covers both.
- The clearing is done rather than printed. `environment:create` exists to
  change the machine, every step of it is a `ddev` command printed before it
  runs, and an environment nothing can reach is not one being taken from
  anybody. Printing it would also have to print the right command, and a step
  nobody would decline is not a decision worth handing over.
- The command is `ddev delete --omit-snapshot -y`, not `ddev stop --unlist`.
  Unlisting frees the name and leaves the volume, which moves the failure three
  minutes later into the setup step, where nothing gets past it.
- Where the approot **is** there, the refusal stands unchanged. That is a live
  checkout's environment and taking it over would stop it without saying so.
- `environment:status` reports and never clears. It names the other checkout
  where one holds the name, and otherwise keeps naming the create command, which
  is now true because create clears what it can reach past.
- The promise above `build()` is corrected rather than kept. "Every one of them
  is idempotent or forced" does not hold at the setup step against a populated
  database, and the sentence `environment:create` printed on failure — "this
  command carries on from it" — was that same promise at the moment somebody
  reads it.
- Rejected: recognising TYPO3's `contains already` message in the build output.
  It is a hardcoded English string in `SetupCommand` with no translation behind
  it, and binding a guard here to it would fail silently on the day it is
  reworded. The failure message names the exception instead.
- Rejected: having the create stop where it finds a database it did not put
  there. Nothing here can ask that question before the containers are up, and
  the place it belongs is TYPO3's setup, which is not this repository's.

## Assumed

- That an approot which is gone means an environment which is wanted gone. A
  directory absent because a drive is unmounted or a worktree was moved would
  have its database deleted by this, and nothing would say so. It is weighed
  against `D-EVI-004` putting the environment in the class of things made on
  demand and re-creatable, and the build measuring 32 seconds on a warm cache.
- That the volume keeps being named after the project. It is what makes an
  unlisted name a populated database later, and it is DDEV's convention rather
  than a documented promise.

## Wrong if

- ~~A session loses a database it wanted to an approot that was only temporarily
  absent. Then the clearing needs to ask, or to keep the snapshot `delete` would
  otherwise take.~~ Priced out on 2026-08-22: the default driver is SQLite and
  the build runs `omit_containers: [db]`, so the registration holds no database
  to lose.
- `ddev delete` stops working on a project whose directory is gone, which would
  put the two-command sequence back and leave the volume to the build.
- TYPO3's setup gains a way past a populated database. Then the create has an
  option rather than a deletion, and the failure message names the wrong way
  out.

## Since then

The first **Wrong if** is struck rather than answered, because what it is about
went away. `Environments::DEFAULT_DRIVER` is `sqlite` and every environment
below `.environments/` runs `omit_containers: [db]`, so the database is a file
in the directory the approot names: clearing a registration nothing can reach
takes a name and no data, and the ask this **Wrong if** asks for would be about
nothing. `discard()` is still `ddev delete --omit-snapshot -y` for the case
where a project was built on MySQL.

The second is unfired and not testable here without taking a registration apart.
DDEV is v1.25.1 on this machine, the version the entry measured against, and the
five projects `bin/cli environment:status` reports all have their approot.

The third is an outside event and stays open. TYPO3's setup gaining a way past a
populated database is a release note rather than a reading, and where it lands
is the failure message the create prints.
