# Take the PHP a console needs from what the installation requires

**Serves:** R-DIS-010, R-DIS-009, META-02
**Priority:** high

`Typo3Cli::requiredPhpVersion()` reads `config.platform.php` and then
`require.php` out of the installation's own `composer.json`, and the base
distribution states neither. So `viaPhp` accepted host PHP 8.3 for
`.environments/e-site-main`, whose dependencies require `>= 8.5.0` — the bound
Composer had already written into `vendor/composer/platform_check.php` — and
`typo3_server_scope` reported that console `reachable: true, via: php` while
every boot through it died on that check. Read the bound from
`vendor/composer/platform_check.php` where the manifest states none, and put the
assertion beside
`Typo3CliTest::aStoppedProjectReachedThroughHostPhpIsReportedAsTheHalfAnswerItIs`,
which is the same **From** one layer down. The second symptom goes with it:
`resolve()` remembers a success, so an invocation that cannot boot is what the
rest of the session answers from, and starting the project changes nothing until
the client is restarted. Measured in one server process on 2026-08-04 —
`via: php` and `answeredBy: "packages"` before `ddev start` and both again after
it, where a process started afterwards answered `via: ddev` and
`answeredBy: "installation"`. That is `META-02`'s third **How it fails** and the
session `R-DIS-009` was written from. With the bound read right the resolution
fails while the project is stopped, nothing is remembered, and the call after
`ddev start` reaches DDEV.
