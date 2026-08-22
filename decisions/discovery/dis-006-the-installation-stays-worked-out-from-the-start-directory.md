---
id: D-DIS-006
date: 2026-08-01
status: open
---

# D-DIS-006 — The installation stays worked out from the directory the server was started in

**Which installation a session is in stays worked out from the directory the
server was started in, because MCP has no mechanism to be told it instead.**

The one candidate was `roots`, where a client declares the directories it
considers relevant and a server asks for them with `roots/list`. It is
**deprecated as of protocol revision `2026-07-28`** ([SEP-2577][sep]): new
implementations *should not* adopt it, and the same revision downgrades it from
a boundary to "informational guidance rather than an access-control mechanism",
which the protocol does not enforce. Under the feature lifecycle policy it stays
in the specification twelve months before it may be removed at all.


[sep]: https://github.com/modelcontextprotocol/modelcontextprotocol/pull/2577

## Assumed

- What it tells implementations to do instead is what this server already does.
  The migration named in the deprecation is "tool parameters, resource URIs, or
  server configuration", and `TYPO3_DEV_COMPANION_ROOT` is the third of those —
  a value someone decides, honoured by every entrypoint, which is why
  `R-DIS-001` restricts the derived answer and not this one.
- The walk-up is out of band and stays that way. A working directory is not a
  protocol concept, so no revision can deprecate it, and nothing in the
  specification speaks against it. What it costs is stated rather than hidden:
  `typo3_server_scope` reports the root, how it was determined, and every
  directory the search walked.
- The bundled SDK could not have sent the request anyway. `Mcp\Schema\Root`,
  `ListRootsRequest` and `RootsListChangedNotification` are declared, but
  `ClientGateway::request()` is private and the only server-to-client calls it
  exposes are `sample()` and `elicit()`. Reaching past that for a feature marked
  deprecated is work with a withdrawal date on it.

## Wrong if

- A later revision adds a way for a client to state where the session is that is
  not deprecated on arrival — the deprecation registry is where that would show.
  Also wrong if a client emerges that starts this server somewhere other than
  the session's directory, which would break the walk-up without breaking
  anything the specification promises: `R-DIS-022` is what would fail, and
  `TYPO3_DEV_COMPANION_ROOT` is what such a setup would have to state.

## Since then

Nothing settled it, and what was looked at is the half this checkout holds. The
bundled `mcp/sdk` is v0.7.1 at protocol revision `2025-11-25`, and
`ClientGateway::request()` is still private with `sample()` and `elicit()` as
the only server-to-client calls it exposes — so the third **Assumed** stands and
the request this entry declined to make is still one the SDK cannot send.

The other half of the **Wrong if** is the corpus, and it is silent. No feedback
of the archive reports the server started anywhere but the session's directory,
and none reports the root being worked out wrongly; `typo3_server_scope` reports
the root and the walk either way, which is what would make such a session say
so.

What is not read here is the deprecation registry. A revision that adds a
mechanism not deprecated on arrival is an outside event, and `bin/cli todo:list`
carries the recurring reading of what `mcp/sdk` has released, which is where a
new revision arrives first.
