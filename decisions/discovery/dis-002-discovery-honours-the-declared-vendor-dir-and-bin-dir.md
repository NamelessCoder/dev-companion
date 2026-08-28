---
id: D-DIS-002
title: Discovery honours the declared vendor-dir and bin-dir
date: 2026-07-29
status: revoked
revokedBy: D-DIS-007
---

# D-DIS-002 — Discovery honours the declared vendor-dir and bin-dir

**The packages and the console are found through the `vendor-dir` and `bin-dir`
the root `composer.json` declares, rather than through their default paths.**

Closes the feedback about `.build/bin/typo3` being unreachable and about the
extension checkout not being recognised as an installation at all.

## Evidence

- `bootstrap_package` on this machine — 21 packages found, console resolved as
  `ddev exec -- .build/bin/typo3` on PHP 8.5, and the 29
  `content-bootstrappackage-*` icons that were previously reported as
  non-existent.

## Assumed

- What the root `composer.json` declares is enough to find both the packages and
  the console. Composer's `config.vendor-dir` and `config.bin-dir` are the only
  two ways either moves in practice, and everything else — DDEV, the interpreter
  choice — was already right and simply never got a binary.
- Invoking the console through a path relative to the installation root works
  inside DDEV as it does on the host.

## Wrong if

- An installation whose console is invoked from somewhere other than the root —
  a DDEV project whose container working directory is the docroot rather than
  the project root would need an absolute path or a `cd`. Also an absolute
  `bin-dir`, which is accepted by Composer and ignored here.

## Revoked on 2026-08-01

Both halves of the **Wrong if** were run and the second **Assumed** is false: a
relative path does not work inside DDEV as it does on the host. The server never
reads `working_dir` and invokes `ddev exec` blind, so from a moved working
directory the same call is exit 127. It worked as long as nothing had moved it,
which is the default and not a guarantee. The absolute `bin-dir` half happened
as written and the decision held there — what was missing is that absolute is a
spelling of the same directory, so one below the root is expressed relative to
it now, which is the form both DDEV and the host need. `R-DIS-003` holds both.

## Since then

The repair is measured and not yet made. Both `/var/www/html/.build/bin/typo3`
and `$DDEV_APPROOT/.build/bin/typo3` answer in both working directories, and the
second is the one that guesses nothing: DDEV sets `DDEV_APPROOT=/var/www/html`
in the container itself, and `ddev exec` hands its arguments to the container's
bash, which expands it. What that still needs is the DDEV version the variable
can be relied on from, because a form that silently resolves to
`/.build/bin/typo3` on an older one is worse than the relative path it replaces.
It is v1.24.5, which makes the mount the form that guesses nothing and the
variable the one with a version on it — made on 2026-08-02, with the reading in
`D-DIS-007`.
