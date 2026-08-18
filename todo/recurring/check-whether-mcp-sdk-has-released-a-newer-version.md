# Check whether `mcp/sdk` has released a newer version

**Serves:** requirements/
**Every:** 7 days
**Checked:** 2026-08-18
**Run:** composer outdated mcp/sdk

Take a patch or minor release on its own merit. The stdio hardening that stood
unreleased on `main` shipped as `v0.7.1` on 2026-08-10 and is what
`composer.lock` holds: three memory-exhaustion advisories in the transports, one
of them stdio's own `fgets()` reading without a length, now bounded at a
configurable 4 MiB. A release that speaks `2026-07-28` is the one to watch for
(PR #403, protocol version negotiation, still unreleased on 2026-08-18): when it
lands, the bump is the constraint in `composer.json` and `PROTOCOL_VERSION` in
`tests/Smoke/StdioServerTest.php`. It serves no single requirement because it
serves the precondition of all of them: every answer this server gives travels
over the protocol version the SDK speaks, and on the day a client stops offering
that version, every requirement fails at once and not one of them says so.
