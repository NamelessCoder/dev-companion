---
id: R-DIS-023
title: 'An install says what is left before a tool can be called'
status: held
restsOn: [D-DIS-009]
heldBy:
  - InstallerAgentSupportTest::everyClientWithAnEntryIsToldWhatIsLeftBeforeAToolCanBeCalled
---

# R-DIS-023 — An install says what is left before a tool can be called

**`install` and `update` say what the client still has to do before a tool in
the entry they just wrote can be called, per client, in their own output.**

Writing `.mcp.json` puts the entry on disk. It does not register the server with
anything: a client that scopes project servers behind an approval has not been
asked yet, and a session that was already open when the file was written is
running against the configuration it started with. Both end with a published
skill naming eleven tools beside an entry that is entirely correct, and no tool
in the session.

What the step is belongs to the client rather than to this package, so it is
named per client — the same way `documentation/usage/installing.rst` already
names `chat.useAgentSkills` for VS Code, and in the command's output rather than
only in a manual, because the person who can finish the install is looking at a
terminal at that moment.

## From

Two sessions in `/home/benji/projects/site-new`, on 2026-07-29 and 2026-07-31,
each with a valid `.mcp.json` written by this installer and no callable tool.
Both reached the server in the end by driving the stdio binary by hand, the
second only after it had already audited a site package without it.

## Held by

What the output has to say is a property of each client rather than of this
package, so that test holds that something is said — per client, on both
commands, and on the line under the entry it is about — and not that it is true.
What each line claims is sourced per client in
[installing.md](../../documentation/usage/installing.rst), from that client's
own documentation, and a client whose documentation does not answer says so
rather than being filled in.
