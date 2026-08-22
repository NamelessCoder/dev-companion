---
id: D-AUD-005
title: An exclusion naming no tool is reported on stderr, and the server starts
date: 2026-08-04
status: open
coveredBy:
  - ExcludedToolsTest::aNameNoToolAnswersToIsReportedAndTheRealOneBesideItStillExcludes
  - EntrypointTest::anExcludedNameNoToolAnswersToIsSaidOnStderrAndStdoutStaysProtocol
  - InstallerTest::installKeepsWhatTheCallerPutInTheEntryAndRewritesOnlyTheCommand
---

# D-AUD-005 — An exclusion naming no tool is reported on stderr, and the server starts

**An excluded name that no tool answers to is written to stderr at startup, and
the server starts with the rest of the list.**

`TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` was read out of the caller's environment and
compared to nothing, so a name that matched no tool took nothing away and said
so nowhere.

## Evidence

- `a4470ee` renamed typo3_project_scope to `typo3_project_describe` and
  typo3_extension_scope to `typo3_extension_describe`. Started on 2026-08-04
  with `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_project_scope`, the server
  offered 25 tools including `typo3_project_describe`, and neither side said the
  exclusion had stopped applying. The same hole swallows every typo in that
  variable.
- stdout is the protocol: the transport writes one JSON-RPC line per response
  and the client parses each line, so anything else printed there is a
  corruption the client reports as a broken server. stderr is the other stream a
  started server has, and it is where
  [`src/bootstrap.php`](../../src/bootstrap.php) already puts the one startup
  failure this binary has, and where
  [driving-a-session.md](../../documentation/contributing/driving-a-session.rst)
  reads a stalled session off.
- An MCP log notification is not a channel here. There is no session until the
  client has sent `initialize`, the list is read before that, and a client that
  never sets a logging level is sent nothing.
- What an exclusion that fails to apply costs is context. Every tool the list
  can take away is read-only; the one tool that writes, `typo3_feedback_record`,
  is appended past the filter in `Registry::offered()` and cannot be excluded at
  all today.
- The same silence had a second route, measured on 2026-08-04 in a fixture
  project: `install` and `update` replaced the whole server entry, so an `env`
  block a caller had written into `.mcp.json` — the only place a client
  configuration can carry `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` — was gone after
  the next run, and the tools came back. Nothing this package writes ever names
  the variable, so the installer was not writing exclusions; it was deleting
  them.

## Decided

- The unknown names are written once, before the transport starts, naming the
  variable that carried them and the tool that lists what is offered.
- A warning rather than a refusal. Exiting on a stale variable trades one wrong
  word for every tool, and a client launches this process without the person who
  wrote that word being there to read the exit code.
- No alias: the old names do not keep excluding the renamed tools. `7553cb3` and
  `a4470ee` both renamed without one, an alias is a second name for one thing
  that has to be carried forever, and what it would buy is a configuration the
  caller now has to correct exactly once.
- No suggested spelling beside the unknown name. A nearest match computed by
  edit distance is a guess, and a guess in a startup diagnostic is acted on by
  somebody who cannot check it — the reason `Installer::REMAINING` says a
  client's documentation is silent rather than what the answer probably is.
- The setup commands own the command in an entry and nothing else: what a JSON
  entry already carries beside `type`, `command`, `args` and `enabled` is
  written back, so an exclusion in `env` survives an `install` or an `update`.
  The TOML section the two `.toml` clients get is still rewritten whole, which
  is a card rather than a second half-fix, because that path replaces text it
  never parsed.
- `ExcludedTools::all()` stays what the caller wrote. Trimming it to the names
  that are real would also change what `typo3_server_scope` reports and what the
  initialize instructions claim, which is the in-band half of this and belongs
  to whoever owns those answers.

## Assumed

- A client's stderr reaches somebody. The clients the recorded runs use capture
  it into a session log, which is what makes it the channel a stalled call is
  read off; a client that discards it leaves this warning unread.
- The registry, asked while the list answers empty, is every tool this server
  has. It is the offered list, so in a checkout without the feedback channel an
  excluded `typo3_feedback_record` is reported too — which is why the sentence
  says the server does not offer the name rather than that no such tool exists.

## Wrong if

- The warning names a tool that is real. That would mean the registry was asked
  in a state where a tool is missing for a reason of its own, and the caller is
  being told their correct configuration is wrong.
- An exclusion ever guards something other than context — a tool that writes, or
  one that reaches outside on its own. Then a list that silently matched nothing
  is a refusal rather than a warning, and this entry is the wrong default.
- A session turns up where the renamed tool came back and nobody saw the line,
  because the client captured stderr and showed it to no one. The report then
  has to be in-band, where the agent reads it.

## Since then

"The one tool that writes" in the **Evidence** and in the second **Wrong if**
means two things, and this entry was read as if it meant one.
`typo3_feedback_record` writes into this checkout and never into the caller's
installation, which is where the read-only posture lives, so its being appended
past the filter is not the hole that sentence describes. It is a named exception
—
[`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md),
under
[`D-FBK-042`](../feedback/fbk-042-the-read-only-boundary-is-the-installation-and-the-channel-writes-on-this-side-of-it.md),
2026-08-04. Read with the installation meaning, the **Wrong if** stands and has
not fired.

What is left of that reading is this entry's own open half, and it turns out to
reach further than the renamed tools. Measured on 2026-08-04 with
`TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_feedback_record` in this checkout: 26
tools offered including that one, while `typo3_server_scope` reported it under
`excludedTools.names` and the initialize instructions opened
"typo3_feedback_record is left out of your tool list". A name that is real but
unexcludable tells the client the same falsehood an unknown one does, and
`ExcludedTools::all()` staying what the caller wrote is what both have in
common.

That half was closed the same day by
[`D-AUD-006`](aud-006-the-server-reports-the-exclusion-that-happened-and-the-installer-keeps-the-line-it-did-not-write.md),
which trims `all()` to what the offered list is actually missing and reports the
rest as having taken nothing away. Two bullets here are superseded by it: the
last of **Decided**, which left `all()` as the caller wrote it, and the one
above it, which left the TOML section rewritten whole. The third **Wrong if**
was not waited for — the report is in-band now as well, under
`excludedTools.ignored`, so a client that captures stderr and shows it to nobody
is no longer the case that loses it. What this entry still holds on its own is
the statement: the warning is a warning and the server starts.
