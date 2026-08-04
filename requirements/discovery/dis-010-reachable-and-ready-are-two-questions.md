---
id: R-DIS-010
status: held
---

# R-DIS-010 — Reachable and ready are two questions

**Reachable and ready are two questions.**

Whether a console can be invoked at all is asked of what the installation
requires rather than of what its manifest happens to state: the bound Composer
wrote into `vendor/composer/platform_check.php` is the one that stops a boot,
and a distribution that states none in `composer.json` still has one.

A console found on this machine while the project it belongs to is meant to run
elsewhere is reported with what that costs — everything that boots TYPO3 against
its database — in the text and in the data, and a failing lookup repeats it
where the error alone does not say it. A server already running inside that
project's DDEV web container is ready through its direct PHP and must not be
diagnosed as a host with an unreachable DDEV project merely because the
container has no nested `ddev` binary.

## From

`typo3_server_scope` reporting the console as reachable via host PHP 8.3 with
the DDEV project stopped, so five installation-backed tools were presented as
usable while four of them could not answer (2026-07-29).

The same report one layer down, against `.environments/e-site-main` on
2026-08-04: that console was not reachable at all. The base distribution states
neither `config.platform.php` nor `require.php`, so nothing bounded the
interpreter, host PHP 8.3 was accepted, and every boot through it died in the
platform check Composer had already written for `>= 8.5.0`.

## Held by

- `Typo3CliTest::aStoppedProjectReachedThroughHostPhpIsReportedAsTheHalfAnswerItIs`
- `Typo3CliTest::aConsoleAlreadyInsideDdevIsReadyThroughItsDirectPhp`
- `Typo3CliTest::thePhpBoundComesFromTheInstallWhereTheManifestStatesNone`
- `Typo3CliTest::anInstallWithNoPlatformCheckBoundsNothing`
