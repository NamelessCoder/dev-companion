---
id: D-COD-002
date: 2026-08-01
status: open
---

# D-COD-002 — The upkeep CLI is a Symfony Console application

**`bin/cli` is a `symfony/console` application, one invokable class per command
below `src/Upkeep/Command/`, and a command is named `<subject>:<verb>`.**

What it replaces is a dispatcher of its own: a `Subject` interface whose
`commands()` declared a usage string, a description and a callable, a `help()`
that rendered them, and a `usage()` each command reached for by hand when an
argument was missing. It worked. What it could not do is bind an argument — a
command read `$arguments[0] ?? ''` and decided for itself what a caller who
passed nothing should be told.

## Evidence

- Written on 2026-08-01, converting all 24 commands at once. The console was
  already in the tree as a dev dependency of php-cs-fixer, so the cost was a
  `require-dev` entry rather than a new dependency. Every reading command's
  output was captured before the change and compared after it: the only
  differences are the command names and what a missing argument reports.

## Decided

- `symfony/console` in `require-dev`, because `bin/cli` is the upkeep of this
  checkout and Composer exports it as no `bin` — what it needs is not what an
  installation of this package needs. Commands are invokable classes carrying
  `#[AsCommand]`, with their arguments on the parameters of `__invoke` under
  `#[Argument]`, which is the only arrangement where what a command takes is
  declared where it is used. `Upkeep\Cli` registers every one of them and is
  the only place a command is switched on.

## Assumed

- That the console's own `list` and `help` say enough for the subjects to need
  no one-line description of their own. The `about()` line each subject carried
  is gone, and what a subject is is now the sum of what its commands say they
  do.

## Wrong if

- The console stops reading `#[Argument]` off a command's parameters at the
  moment it does now — it reads them once, before anything merges the
  application definition in, and a command it stops asking keeps every argument
  in its signature while refusing the caller who passes one.
  `UpkeepCommandTest::everyArgumentOfACommandIsOneTheConsoleBinds` is what
  notices; the fallback is `addArgument()` on each command's definition, which
  is the older API and does not depend on that moment.

## Since then

A feedback asked for a `feedback:record` command here, so that an agent could
report without "needing to invoke the PHP class directly" —
`feedback/2026-07-31-183652-the-typo3-cms-mcp-server-provides-a.md`, judged on
2026-08-02. What it runs into is the half-sentence under **Decided**. Composer
exports `bin/cli` as no `bin`, so a project that requires this package has no
such command to call at all. The session that asked was auditing a site package
in another directory. What reaches it there is `typo3_feedback_record`, and a
`tools/list` against this checkout on 2026-08-02 still offers it, twenty-third
of twenty-four. Both it and any command would be gated on the same
`Channel::isAvailable()` — a standalone checkout — so the command would answer
only where the tool already answers.

What that session actually hit is one file over in the same batch: none of this
server's tools were callable in its client at all, which
`feedback/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md` says
and which is still open. Its two feedback from 18:36 carry no `directory:`
line, and the stdio entrypoint always supplies one, since it hands `getcwd()`
to `Instance::discoverFrom()`. So they were written by reaching into this
checkout rather than by calling the server the project was configured for. A
session that reaches that far can start `bin/typo3-cms-mcp` instead, which is
what this one did twenty minutes later — and then every tool answers rather
than one. A second way in is as undiscoverable as the first was, so it is not
the lever that report names.

Whether the command is added anyway is not settled here. The todo that judged
the feedback carries the question.
