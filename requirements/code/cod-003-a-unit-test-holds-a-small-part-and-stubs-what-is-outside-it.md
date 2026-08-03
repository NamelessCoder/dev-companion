---
id: R-COD-003
status: held
restsOn: [D-COD-004]
---

# R-COD-003 — A unit test holds a small part, and stubs what is outside it

**A unit test exercises one small part and nothing outside this process.**

What the part reaches outside — a console, a container, an HTTP endpoint, an
executable on the machine — is stubbed at a seam in the code. It is never
started, and never arranged for on the machine either.

Four things follow from it, and the fourth is the one that reaches the code
rather than the test:

- **Nothing is started.** No unit test runs a process, opens a socket, or waits
  on something being up. `tests/Smoke/` is where a subprocess is the subject,
  and what it starts is this repository's own CLI.
- **Nothing is arranged for on the machine either.** Writing an executable into
  a temporary directory and putting it on the `PATH` is still a dependency on
  that directory being writable, on `chmod`, and on a `/tmp` nobody mounted
  `noexec`. A stub is handed to the code, not left where the code will find it.
- **A data provider carries the cases.** Where one behaviour has several
  inputs, they are a provider with a named case each, so a failure names the
  input rather than a position in a loop.
- **The seam is the code's, not the test's.** A class that reaches outside
  takes what it reaches through as something a caller can replace. Where there
  is no such seam, making one is part of the work rather than a reason to write
  the other kind of test.

## From

Two shapes in this repository, both of which passed for a mock. `Typo3CliTest`
wrote a `ddev` into a temporary directory, made it executable and prepended
that directory to the `PATH`, so `Typo3Cli::execute()` forked a real shell to
reach it; `Typo3Cli::locateBinary()` walked the real `PATH` beside it. Neither
needed DDEV to be installed, so neither read as a test against a running thing
— and both were exactly that, one layer down.

## Held by

- It is **not guarded**, deliberately. A test that reads the other tests was
  written for this and taken out again: the rule is what the practice is rather
  than what a check polices, and a suite that greps itself reports on its own
  shape instead of on this server's.
- What stands in for a guard is that the seams exist —
  `Typo3CmsMcp\Process\CommandRunner` for what leaves the process,
  `Typo3CmsMcp\Tests\Support\FakeRunner` for what a test hands in — so the
  cheaper way to write the test is the one this asks for. `D-COD-004` is where
  that reasoning is.
