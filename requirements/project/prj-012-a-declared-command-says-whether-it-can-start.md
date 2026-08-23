---
id: R-PRJ-012
title: 'A declared command says whether it can start'
status: held
restsOn: [D-ANS-086]
heldBy:
  - ProjectTest::aBoundTheInterpreterClearsIsSaidToBeClearedAndNotLeftOut
  - ProjectTest::aDeclaredCommandSaysWhetherItStartsOnThePhpThatWouldRunIt
  - ProjectTest::anInstallThatBoundsNothingIsSaidToBoundNothing
  - ProjectTest::whereNothingConfiguresAnEnvironmentTheBoundIsStated
---

# R-PRJ-012 — A declared command says whether it can start

**The project answer states the PHP bound Composer wrote into the install, and
where the interpreter that would run the declared commands stands against it.**

`R-PRJ-007` says what running a command does to the sources. Whether it gets
that far is the second question, and the bound is what answers it:
`composer install` writes the lowest PHP every package it installed accepts into
`composer/platform_check.php` below the vendor directory, the autoloader
includes it, and under it a command aborts there before its own tool starts.

A number of its own beside the three already in the answer, because no manifest
field carries it — a fixer required for development alone raises it above
everything the project itself declares. Absent means no bound: Composer leaves
the file out where nothing requires a PHP version and deletes it where
`platform-check` is off.

Where this repository configures an environment, the version that environment
states is what the bound is held against. Where it configures none, the commands
run in the caller's own shell, whose PHP nothing here reads — so the bound is
stated with `php -v` as what settles it, and no interpreter is discovered.

Read from these files like everything else in that answer. Nothing was executed
on any of these versions, and only `major.minor` is compared, because that is
the depth an environment states its own version at.

## From

`feedback/2026-08-18-113412` (2026-08-18), a sitepackage checkout. The session
was offered `composer cgl:ci` as a check a review may run, it aborted in
`.build/vendor/composer/platform_check.php` — *requires a PHP version >= 8.4.0.
You are running 8.3.23* — before the fixer started, and the answer that had
marked it a check carried nothing that could be read for it. The session looked
for a `php8.4` on the machine, found none, and gave the check to GitHub Actions.
The same mechanism twice more in two other kinds of checkout:
`feedback/archive/2026-08-07-125950` and `D-KNW-036`.
