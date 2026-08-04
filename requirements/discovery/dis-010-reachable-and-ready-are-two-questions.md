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
elsewhere is reported with what that costs, in the text and in the data, and a
failing lookup repeats it where the error alone does not say it. What it costs
is stated as what the boot cannot reach — the runtime the project declares, and
the services it brings — and never as a list of tools, because which answer
meets that limit is a property of the installation rather than of the tool
asked. It is reported with **both** steps that end the state, the start and the
call after it: an answer given from the weaker source is not revised where it
stands, so a caller told only to start the project acts on that and works on
from what it was just told to stop trusting. A server already running inside
that project's DDEV web container is ready through its direct PHP and must not
be diagnosed as a host with an unreachable DDEV project merely because the
container has no nested `ddev` binary.

## From

`typo3_server_scope` reporting the console as reachable via host PHP 8.3 with
the DDEV project stopped, so five installation-backed tools were presented as
usable and nothing said where their answers would come from (2026-07-29).

The caveat written for that named the tools it believed were lost, and the
naming was measured wrong. Driven against `.environments/e-site-14.3` with its
DDEV project stopped on 2026-08-04, all seven installation-backed tools
answered, byte for byte what the same calls answered through `ddev exec` with
the project running. The host interpreter had no database driver compiled in at
all — a query from it comes back "could not find driver" — so not one of them
had put a query anywhere, and booting TYPO3 is not what a stopped project takes
away. Only `typo3_schema_lookup` asks the connection for anything, and only for
the platform, which `pdo_sqlite` supplies with no server and `pdo_mysql` fetches
by connecting unless `serverVersion` is configured (`D-DIS-012`).

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
