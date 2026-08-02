---
id: R-DIS-023
status: open
restsOn: [D-DIS-009]
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
named per client — the same way `documentation/clients/installing.md` already
names `chat.useAgentSkills` for VS Code, and in the command's output rather than
only in a manual, because the person who can finish the install is looking at a
terminal at that moment.

## From

Two sessions in `/home/benji/projects/site-new`, on 2026-07-29 and 2026-07-31,
each with a valid `.mcp.json` written by this installer and no callable tool.
Both reached the server in the end by driving the stdio binary by hand, the
second only after it had already audited a site package without it.

## Held by

`not guarded`. Nothing reads the installer's output, and what the output would
have to say is a property of each client rather than of this package — so a
check can hold that something is said per client, and not that it is true.
