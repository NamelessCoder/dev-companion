---
id: D-COD-004
date: 2026-08-03
status: open
---

# D-COD-004 — What leaves this process goes through one seam

**A call that leaves this process goes through an interface a caller can
replace.**

A command, an executable lookup, an HTTP request: a unit test stubs each of
them instead of arranging for the real thing.

## Evidence

- `Typo3CliTest` proved an invocation by building one. It wrote a `ddev` script
  into `sys_get_temp_dir()`, `chmod`ed it to 0755, prepended the directory to
  `PATH`, and let `Typo3Cli::execute()` fork a shell to reach it. One of its
  two cases went further and reproduced DDEV's own argument joining through
  `bash -c`, to show that `--regex=/(save)/i` survives. No DDEV had to be
  installed, which is why this passed for a mock for as long as it did.
- The same class walked the real `PATH` in `locateBinary()`, so whether the
  test saw a `ddev` at all was a property of the machine running the suite.
- `proc_open` was written twice, in `Typo3Cli::execute()` and in
  `Environments::run()`, with the same three things got right in both — stdin
  never inherited, the exit code read off `proc_get_status()` rather than
  `proc_close()`, both streams drained before the close — and the reasons
  written down in only one of them.
- One class reaches HTTP, `Http\Fetch`, and no test in this repository makes an
  external request today.

## Decided

- `Typo3CmsMcp\Process\CommandRunner` is the seam: `run()` for a command,
  `locate()` for whether the machine has an executable. Both are the same
  boundary — asking whether `ddev` exists is asking the machine — so putting
  them on one interface is what lets a test stub the whole question.
- `SystemRunner` is the only implementation outside a test, and it is where the
  three `proc_open` details now live once.
- `Typo3Cli::useRunner()` and `Environments::useRunner()` are how a test hands
  one in. Static, because both classes are; and `forget()` deliberately does
  not reset it, because a test drops the memoized resolution between
  installations and would otherwise drop the stub with it.
- `FakeRunner` answers from a table keyed by the command line, longest prefix
  winning, and records what it was asked. A command nothing was written for
  answers as one that could not be started, because a silent empty success is
  how a test passes over a call it never meant to allow.
- Rejected: a test that reads the other tests and fails where one starts a
  process. It was written, it worked, and it was taken out — the rule is the
  practice rather than something a check polices, and the seams are what make
  the asked-for shape the cheaper one to write.

## Assumed

- That the two `useRunner()` seams are enough, because they are the two places
  that leave the process for a command. `Http\Fetch` is the third boundary and
  has no seam yet; nothing needs one until a test does.

## Wrong if

- A stub drifts from what the real thing does, and a test goes on passing
  against a `ddev describe -j` that no DDEV has answered that way for a year.
  Nothing here checks a stub against the thing it stands for.
- The static seam leaks between tests, because one forgets to put it back and
  the next is answered by a table written for something else.
- A third caller writes its own `proc_open` rather than taking the runner,
  which is the state this replaced and which nothing prevents.

## Covered by

- `Typo3CliTest::everyArgumentReachesTheContainerInTheFormThatSurvivesItsShell`
- `Typo3CliTest::theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove`
