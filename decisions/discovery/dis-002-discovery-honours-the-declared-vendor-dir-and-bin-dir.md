---
id: D-DIS-002
title: Discovery honours the declared vendor-dir and bin-dir
date: 2026-07-29
status: revoked
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

Both halves of the **Wrong if** were run, and the second **Assumed** is false.
Measured on `syntax` (DDEV 1.25.1, TYPO3 14.3.0, docroot `.build/public`), with
the container's working directory moved for the length of one call by
`ddev exec -d` rather than by reconfiguring the project: the server never reads
`working_dir` — it takes `status` and `php_version` out of `ddev describe -j`
and then invokes `ddev exec -- <binary>` blind — so the two are the same state
for the command that runs. From the project root the call answers
`TYPO3 CMS 14.3.0`; from `/var/www/html/.build/public` it is
`bash: .build/bin/typo3: No such file or directory`, exit 127. A relative path
does not work inside DDEV as it does on the host. It works as long as nothing
has moved the working directory, which is the default and not the guarantee the
entry took it for. The absolute `bin-dir` half of the same **Wrong if** happened
as written and the decision held there. Composer 2.9.5 accepts one and installs
the binaries there — a fixture project declaring
`"bin-dir": "<root>/.build/bin"` got its console at exactly that path — and this
server then found no console at all and named only the two defaults. What was
missing is that absolute is a spelling of the same directory. One below the root
is now expressed relative to it, which is the form both DDEV and the host need,
and `autoloader()` reads an absolute `vendor-dir` the same way. One outside the
root still has no usable form and is named in the reason with
`TYPO3_DEV_COMPANION_CONSOLE`. `R-DIS-003` holds both.

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
