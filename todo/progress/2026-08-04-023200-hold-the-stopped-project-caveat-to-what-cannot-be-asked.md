# Hold the stopped-project caveat to what cannot be asked

**Serves:** R-DIS-010
**Priority:** normal
**Branch:** todo/hold-the-stopped-project-caveat-to-what-cannot-be-asked
**Claimed:** 2026-08-04

The caveat `Typo3Cli` sets for a project reached through host PHP says "the
label, module and Fluid namespace lookups need the database", and a caller reads
it in `typo3_server_scope` and in the diagnosis of every `unsupported`. Against
`.environments/e-site-14.3` with its DDEV project stopped, all three answered
anyway on 2026-08-04, `answeredBy: "installation"` each, as did the icon and the
configuration lookup — the console ran on host PHP 8.3 and none of the five put
a query to the database that was not there. Drive the installation-backed tools
against that environment stopped, one call each, and write the ones that
actually fail into the sentence; where none of them does, the caveat is about
what a boot cannot reach rather than about a list of tools, and it says so
instead. `R-DIS-010` is what promises the wording, so what the measurement
leaves standing is what that requirement says too.
