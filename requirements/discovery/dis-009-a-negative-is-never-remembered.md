---
id: R-DIS-009
title: 'A negative is never remembered'
status: held
---

# R-DIS-009 — A negative is never remembered

**Nothing short of a whole answer is remembered: neither "there is no
installation" nor a console reached outside the runtime the project declares.**

A successful resolution is memoized for the process; a failure is retried on
every call, because the caller who reads that answer is the one likely to
install, migrate or start something and ask again in the same session.

A success that carries a caveat is retried for the same reason. It is the weaker
of two answers — the console of a stopped DDEV project, reached through an
interpreter of this machine — and the stronger one arrives during the session,
by the `ddev start` the caveat asked the caller for. What it costs is one
`ddev describe -j` per call while the project is stopped, 0.25s against
`.environments/e-site-13.4` on 2026-08-04, which is what a failing resolution on
that same path already pays.

That price is paid per read, so a caller reads the state once and writes its
whole answer from it rather than resolving per sentence. Two is where that
stops: `reason()` and `caveat()` each resolve for themselves, so one resolution
answers for the invocation and a second for whichever of the two applies. A
fourth accessor handing all three back from one resolve was weighed on
2026-08-04 and not taken. The saving is 0.25s on one tool in one state, against
a fourth way to ask a class whose three accessors each mean one thing.

## From

A session lost to a cached negative — the agent ran `composer install`, started
DDEV, verified `bin/typo3` answered, and every tool kept reporting no
installation until the client was restarted (2026-07-29).

## Held by

- `InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`
- `Typo3CliTest::aStoppedProjectIsAskedAgainAfterItStarts`
- `Typo3CliTest::aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts`
- `Typo3CliTest::theScopeAnswerDescribesAStoppedProjectOncePerHalf`
- `Typo3CliTest::anUnsupportedAnswerReadsTheCaveatOnceRatherThanPerSentence`
