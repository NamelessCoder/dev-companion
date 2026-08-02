---
id: R-DIS-9
status: held
---

# R-DIS-9 — A negative is never remembered

**Nothing that says "there is no installation" is remembered.**

A successful resolution is memoized for the process; a failure is retried on
every call, because the caller who reads that answer is the one likely to
install, migrate or start something and ask again in the same session.

## From

A session lost to a cached negative — the agent ran `composer install`, started
DDEV, verified `bin/typo3` answered, and every tool kept reporting no
installation until the client was restarted (2026-07-29).

## Held by

- `InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`
