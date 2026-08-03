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
- `proc_open` was written three times: `Typo3Cli::execute()`,
  `Environments::run()` and `Checkouts::run()`. Two of them get the same three
  things right — stdin not inherited, the exit code read off
  `proc_get_status()` rather than `proc_close()`, both streams drained before
  the close — and the reasons are written down in one. The third differs on
  purpose: git wants the stdin it inherits.
- `TodoTest` was the worse of the two offenders and the one no search for
  `proc_open` in a test file would have found, because it went through
  `Checkouts::run()`. To hold `Todo::linked()` it ran `git worktree add -b` in
  whatever checkout the suite was in, asserted, and then ran `worktree remove
  --force` and `branch -D` in a `finally`. A unit test that writes to the
  developer's repository.
- HTTP was already behind a seam. `Http\Fetch` takes a
  `(\Closure(string): ?string)|null $transport` in its constructor and its own
  docblock says every test passes one, which is what keeps the suite the same
  offline. It is a closure rather than this interface because it is a different
  boundary, and nothing is gained by making it the same one.

## Decided

- `Typo3CmsMcp\Process\CommandRunner` is the seam: `run()` for a command,
  `locate()` for whether the machine has an executable. Both are the same
  boundary — asking whether `ddev` exists is asking the machine — so putting
  them on one interface is what lets a test stub the whole question.
- `SystemRunner` is the only implementation outside a test, and it is where the
  three `proc_open` details now live once.
- `Typo3Cli::useRunner()`, `Environments::useRunner()` and
  `Checkouts::useRunner()` are how a test hands one in. Static, because all
  three classes are; and `Typo3Cli::forget()` deliberately does not reset it,
  because a test drops the memoized resolution between installations and would
  otherwise drop the stub with it. Putting it back belongs to the test's own
  teardown, which is where `QueuedTodo` does it.
- Inheriting stdin is a parameter on `run()` rather than a second
  implementation. git wants it, the console and the build must not have it, and
  one flag says which is which where two classes said it by being two classes.
- What a test hands in is `self::createStub(CommandRunner::class)`, configured
  with `willReturn()`, `willReturnCallback()` or `willReturnOnConsecutiveCalls()`
  as the case needs. A `FakeRunner` class was written for this first, with a
  table keyed by the command line and a `commands()` recorder — which is
  `willReturnCallback()` and `expects()->with()` written again by hand. It was
  deleted.
- `Todo::useDirectory()` is the same seam for the queue: the cases that hold
  claiming and releasing write into a `todo/` of their own below the system
  temporary directory. A todo's path is relative to the root that directory
  sits in, so the redirect carries the root with it.
- Rejected: a test that reads the other tests and fails where one starts a
  process. It was written, it worked, and it was taken out — the rule is the
  practice rather than something a check polices, and the seams are what make
  the asked-for shape the cheaper one to write.

## Assumed

- That the three `useRunner()` seams are all of them, because a sweep of
  `tests/Unit` and `tests/Contract` on 2026-08-03 found no other call that
  starts a process, opens a socket, writes an executable or touches `PATH`.
  What that sweep cannot say is that the next one will not be written.

## Wrong if

- A stub drifts from what the real thing does, and a test goes on passing
  against a `ddev describe -j` that no DDEV has answered that way for a year.
  Nothing here checks a stub against the thing it stands for.
- The static seam leaks between tests, because one forgets to put it back and
  the next is answered by a table written for something else.
- A fourth caller writes its own `proc_open` rather than taking the runner,
  which is the state this replaced and which nothing prevents — deliberately,
  because the check that would prevent it is the one `R-COD-003` declines.

## Covered by

- `Typo3CliTest::everyArgumentReachesTheContainerInTheFormThatSurvivesItsShell`
- `Typo3CliTest::theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove`
- `TodoTest::aWorktreeIsToldApartFromTheCheckoutItWasCutFrom`
- `TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim`
