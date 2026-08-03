---
id: R-COD-001
status: held
---

# R-COD-001 — Every entrypoint is driven by a test that goes through it

**Each binary in `bin/` is run as a subprocess by a test, and a new one is not
finished until it has one.**

A unit test reaches a class at a time, and that is the half of a command it
cannot see: which arguments dispatch to it, which autoloader finds it, and what
it resolves the paths it reads from. A command can be held to every rule it has
and still be unreachable.

## From

Moving the upkeep into `src/Upkeep/` put its subjects one directory deeper, five
of them resolved the repository root as `dirname(__DIR__, 2)` from their own
file, and `bin/cli requirements:check` died on a path that no longer existed.
All 483 tests stayed green, because none of them went through `bin/cli` — a
smoke layer existed and covered the other binary alone. It was found by running
the command by hand, one commit late (2026-08-01).

## Held by

- `UpkeepTest::everyReadingCommandRuns`
- `EntrypointTest::helpNamesTheCommandsAndTheClientsTheyTake`
- `StdioServerTest::theServerAnnouncesItselfWithItsBoundary`
