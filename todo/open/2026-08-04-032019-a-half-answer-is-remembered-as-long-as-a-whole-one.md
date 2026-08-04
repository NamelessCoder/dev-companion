# A half answer is remembered as long as a whole one

**Serves:** R-DIS-009, R-DIS-010, META-02
**Priority:** low

`Typo3Cli::resolve()` no longer remembers a caveated resolution, and `R-DIS-009`
says so — a console reached through an interpreter of this machine while the
project's DDEV is stopped is the weaker of two answers, and the stronger one
arrives with the `ddev start` the caveat asked for. Measured in one process
against `.environments/e-site-13.4` stopped on 2026-08-04, which pins `8.2.0`
and is satisfied by host PHP 8.3: before, the first resolution came back
`via=php` with the caveat and `ddev start` changed nothing for the rest of the
process; after, the call following the start comes back `via=ddev` with no
caveat, and is memoized from there
(`Typo3CliTest::aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts`).

What is left is what it costs the one tool a caller reaches in that state.
`ddev describe -j` on a stopped project is 0.25s and a whole resolution is
0.44s, both measured there; `typo3_server_scope` enters `resolve()` six times —
`resolve()`, `reason()` and `caveat()`, in the text half and again in the data
half — so that answer went from 0.44s to 2.615s while the project is stopped.
Reading the console state once into locals in `src/Tool/ServerScope.php` and
answering both halves from it changes nothing a caller sees and brings it back
to one `ddev describe -j` per call. That is the next step.

The alternative, if the memo is wanted back: memoize the caveated resolution as
before and drop it in `Registry::call`'s `finally` beside `Typo3Runtime::forget()`
and `Icons::forget()`, which is the lifetime `D-DIS-011` already gives what was
read from the installation. It costs one `ddev describe -j` per tool call rather
than per resolution, and moves the guarantee out of `Typo3Cli` into the
registry, so the upkeep commands that call `Typo3Cli` directly keep a stale
caveated resolution for their process. Not taken, because the step above buys
the same cost without moving it.
