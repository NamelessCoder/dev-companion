# A half answer is remembered as long as a whole one

**Serves:** R-DIS-009, R-DIS-010, META-02
**Priority:** normal

`Typo3Cli::resolve()` remembers every success, and a console reached through host
PHP while the project's DDEV is stopped is one — it carries a caveat saying
"start it with `ddev start`", and the caller who reads that and starts it is
answered from the same host PHP for the rest of the process. That is the reason
`R-DIS-009` gives for not remembering a negative, one state over: the answer is
not wrong, it is the weaker of two, and the stronger one becomes available
during the session.

It was `.environments/e-site-main` that showed it, where the interpreter was
also below what the packages require, and reading the bound out of
`vendor/composer/platform_check.php` took that installation out of the case
entirely — the stopped resolution now fails, so nothing is remembered and the
call after `ddev start` reaches DDEV
(`Typo3CliTest::aStoppedProjectNoInterpreterHereCanRunIsAskedAgainAfterItStarts`,
2026-08-04). The three released environments are not out of it: `e-site-13.4`
pins `8.2.0`, host PHP 8.3 satisfies it, and a stopped project there resolves
through host PHP, memoizes, and still answers that way after the containers come
up. `META-02`'s third **How it fails** — "the session having to be restarted
after the installation became reachable" — is what that still is.

The step is to decide whether `caveat() !== ''` is enough to make a resolution
not worth remembering, and if it is, to leave `resolve()` memoizing only the
uncaveated one and say so where `R-DIS-009` says what is never remembered. What
it costs is a `ddev describe -j` per call while a project is stopped, which is
what a failing resolution already costs today. Measure it against `e-site-13.4`
stopped, in one process, across a `ddev start` — the shape the entry above was
measured in.
