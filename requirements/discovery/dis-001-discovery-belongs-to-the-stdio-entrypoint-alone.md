---
id: R-DIS-001
title: 'Discovery belongs to the stdio entrypoint alone'
status: held
heldBy:
  - InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance
---

# R-DIS-001 — Discovery belongs to the stdio entrypoint alone

**The installation is never derived from `getcwd()` on its own: the directory a
search starts from is handed in, and only the stdio entrypoint hands one in.**

`Instance` keeps that directory private and null. `discoverFrom()` is its only
setter, `describe()` walks up from whatever it holds, and with nothing handed in
there is no directory to walk from and nothing is found. That is what a
request-serving endpoint has to get: it has no such relationship to its callers,
and its document root may itself sit inside an installation.

Naming the root outright with `TYPO3_DEV_COMPANION_ROOT` is a decision rather
than a derivation, so it holds for every entrypoint and is not what this
restricts.

The one call is in `Server\Entrypoint`, which `bin/typo3-dev-companion` runs and
nothing else does.
