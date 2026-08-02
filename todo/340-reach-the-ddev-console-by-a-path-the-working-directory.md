# Reach the DDEV console by a path the working directory cannot move

**Serves:** decisions/, R-DIS-003

`Typo3Cli::consoleInDdev()` returns `['ddev', 'exec', '--', $binary]` with
`$binary` relative to the installation root, and `D-DIS-002` recorded on
2026-08-01 that this is exit 127 in a project whose `working_dir.web` is not the
project root. Both `/var/www/html/<binary>` and `$DDEV_APPROOT/<binary>` were
measured to answer in either working directory, and the second guesses nothing —
DDEV sets `DDEV_APPROOT` in the container and `ddev exec` hands its arguments to
a bash that expands it. Settle first, from DDEV's own release notes or
repository, which version `DDEV_APPROOT` can be relied on from, and what the
call does below that version: a form resolving to `/.build/bin/typo3` on an
older DDEV is worse than the relative path it replaces, because it fails where
the relative one worked. Then change the invocation and give `Typo3CliTest` the
case, reading the reason a caller gets when the console cannot be reached at all.
