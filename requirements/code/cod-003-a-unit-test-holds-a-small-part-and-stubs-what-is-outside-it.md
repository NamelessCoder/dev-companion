---
id: R-COD-003
title: 'A unit test holds a small part, and stubs what is outside it'
status: held
judged: 2026-08-22
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
  and what it starts is this repository's own CLI. The one exception is the
  installation `Upkeep\Fixture` writes: what starts there is `php` over files
  this repository produced, so an answer only an installation can give is held
  on every machine rather than on the author's. It takes nothing off the `PATH`,
  which is what keeps the sentence above true of everything else.
- **Nothing is arranged for on the machine either.** Writing an executable into
  a temporary directory and putting it on the `PATH` is still a dependency on
  that directory being writable, on `chmod`, and on a `/tmp` nobody mounted
  `noexec`. A stub is handed to the code, not left where the code will find it.
- **The double is PHPUnit's.** `self::createStub()` where it only has to answer,
  `self::createMock()` where the call itself is the assertion. A hand-written
  class implementing the interface re-invents `willReturn()` and `expects()`,
  and one was written here before this said so.
- **A data provider carries the cases.** Where one behaviour has several inputs,
  they are a provider with a named case each, so a failure names the input
  rather than a position in a loop. Two loops are not that and stay loops: one
  that walks a corpus — every requirement, every hint, every file in a directory
  — and one that checks several aspects of a single result, where a provider
  would rebuild that result per aspect and say nothing more. The suite was read
  for both on 2026-08-03, and most of what looked like a case table was the
  second kind.
- **The seam is the code's, not the test's.** A class that reaches outside takes
  what it reaches through as something a caller can replace. Where there is no
  such seam, making one is part of the work rather than a reason to write the
  other kind of test.

## From

Three shapes in this repository, none of which read as a test against a running
thing and all of which were one. `Typo3CliTest` wrote a `ddev` into a temporary
directory, made it executable and prepended that directory to the `PATH`, so
`Typo3Cli::execute()` forked a real shell to reach it, and
`Typo3Cli::locateBinary()` walked the real `PATH` beside it. `TodoTest` ran
`git worktree add -b` in whatever checkout the suite was in and removed it
afterwards, and wrote its fixtures into the real `todo/` — where a run that died
in between left them, in the queue the next session reads. The third was the fix
for the first: a hand-written `FakeRunner` doing what `self::createStub()` does.

The suite runs the same with nothing but `php` and `sh` on the `PATH` now — no
git, no ddev, no docker, not even `env` — which is the whole of what this is
for, and the check worth repeating: a `PATH` that still carries `/usr/bin` says
much less than it looks like it does.

## Held by

- It is **not guarded**, deliberately. A test that reads the other tests was
  written for this and taken out again: the rule is what the practice is rather
  than what a check polices, and a suite that greps itself reports on its own
  shape instead of on this server's.
- What stands in for a guard is that the seams exist —
  `TYPO3\DevCompanion\Process\CommandRunner` for what leaves the process,
  `Todo::useDirectory()` for a queue to write into — so the cheaper way to write
  the test is the one this asks for, and what fills the seam is PHPUnit's own
  double. `D-COD-004` is where that reasoning is.
