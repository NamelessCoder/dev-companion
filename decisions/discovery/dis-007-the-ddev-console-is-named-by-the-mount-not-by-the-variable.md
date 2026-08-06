---
id: D-DIS-007
date: 2026-08-02
status: confirmed
---

# D-DIS-007 — The DDEV console is named by the mount, not by the variable

**`ddev exec` is given `/var/www/html/<binary>`, the place DDEV mounts the
project in its web container, rather than the relative path or
`$DDEV_APPROOT/<binary>`.**

`D-DIS-002` measured the relative path as exit 127 in a project whose
`working_dir.web` is the docroot, and left the repair to the queue with one
question open: which DDEV version `DDEV_APPROOT` can be relied on from in the
container.

## Evidence

- It is set from **v1.24.5** (2025-05-15). `DDEV_APPROOT=/var/www/html` is a
  literal line in the web service's `environment:` in
  `pkg/ddevapp/app_compose_template.yaml`, added by commit `8662f76` on
  2025-04-12 — "add DDEV_APPROOT variable to web container", fixing ddev#7198.
  The commit is not in v1.24.4 and is in v1.24.5, by GitHub's own compare.
- Before that the variable is the host-side project path and exists on the host
  only. In the container it expands to nothing, so
  `$DDEV_APPROOT/.build/bin/typo3` is `/.build/bin/typo3` there — a failure
  where the relative path worked.
- The same file mounts the project at `target: /var/www/html`, which is what the
  variable was set to. So the two forms name one directory, and one of them
  needs a version.

## Decided

- The literal mount. A variable whose value is a constant DDEV hardcodes buys
  nothing over the constant, and costs a version boundary this repository would
  have to carry.
- No `${DDEV_APPROOT:-/var/www/html}` either. It is the same string with a
  fallback for a case in which the fallback is the answer.
- The test drives a `ddev` on the PATH that describes a running project. No test
  run may depend on containers, and what failed here is the command that would
  be run, which is readable without one.

## Assumed

- Every DDEV project this server meets mounts its files at `/var/www/html`.
  DDEV's own template hardcodes it, so a project where it differs has overridden
  DDEV's default in `.ddev/docker-compose.*.yaml` — the case
  `TYPO3_DEV_COMPANION_CONSOLE` exists for.

## Wrong if

- A DDEV project answers `No such file or directory` for a console that is
  there, which would mean the mount is not what its own template says.
- DDEV stops mounting at that path, or starts deriving it. Then the variable is
  the right form and the version floor is whatever this repository still
  supports.

## Covered by

- `Typo3CliTest::theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove`

## Since then

The path was one half of what `ddev exec` does to an invocation. The other is
that it joins its arguments back into a line and gives that to bash inside the
container, so an argument carrying a character bash acts on never reaches the
console either. `typo3_label_lookup` builds one — `--regex=/(save)/i` for
`language:domain:search`, where the parentheses are a subshell — and it came
back exit 2 in every DDEV project, silently, as a fallback to reading the
package files. `Typo3Cli::run` now quotes for this transport and only for this
transport: the direct one has no shell between, and what
`TYPO3_DEV_COMPANION_CONSOLE` names may or may not. Measured against DDEV
v1.25.1 on 2026-08-02, by the first recording made against an installation of
this repository's own — `D-DOC-006` has that run.

- `Typo3CliTest::everyArgumentReachesTheContainerInTheFormThatSurvivesItsShell`

## Confirmed on 2026-08-02

Both halves held in a project this repository did not make. The feedback of
2026-07-31 is the same fault reported from outside, a day before it was fixed: a
session auditing an extension in `/home/benji/projects/site-new` asked
`typo3_label_lookup` for `printworks`. It read
`syntax error near unexpected token '('` beside 33 labels, and filed the console
path as broken. `E-SITE` is this repository's own installation, so a second
project is what the measurement above could not have.

The query was re-run there on 2026-08-02, through this branch's server over
stdio. `Typo3Cli::resolve()` answers
`ddev exec -- /var/www/html/vendor/bin/typo3` in that root, with no
`TYPO3_DEV_COMPANION_CONSOLE` set and no caveat — the mount path,
autodiscovered. `typo3_label_lookup` comes back `answeredBy: "installation"`
with `matchCount: 53`, the labels of `printworks_sitepackage`, and the argument
built unchanged as `--regex=/(printworks)/i`. DDEV v1.25.1, PHP 8.4 in the
container.

So the mount is reached in a second project, and a parenthesised regex now
survives the container's shell where that report says it did not. The feedback
is answered by this run and archived by the commit carrying it.
