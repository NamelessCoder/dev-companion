# Check whether `mcp/sdk` has released a newer version

**Serves:** requirements/
**Every:** 7 days
**Checked:** 2026-08-01
**Run:** composer outdated mcp/sdk

Take a patch or minor release on its own merit — v0.7.1 sits unreleased on
`main` since 27.07. and carries stdio hardening, which is worth having for a
server whose only transport is stdio. A release that speaks `2026-07-28` is the
one to watch for (PR #403, protocol version negotiation): when it lands, the
bump is the constraint in `composer.json` and `PROTOCOL_VERSION` in
`tests/Smoke/StdioServerTest.php`. It serves no single requirement because it
serves the precondition of all of them: every answer this server gives travels
over the protocol version the SDK speaks, and on the day a client stops offering
that version, every requirement fails at once and not one of them says so.
