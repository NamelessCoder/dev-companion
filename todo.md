# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements.md](requirements.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Rules that keep it from becoming a fourth backlog:

- An item names what it serves — a requirement, a note, a scenario. An item that
  serves nothing is not a task, it is an idea, and ideas go in the note that had
  them.
- An item says what the **next concrete step** is, in enough detail that someone
  who has read nothing else can start. "Continue with the bindings" is not that;
  "bind the statements in `php.json` against `.checkouts/12.4` and `13.4`" is.
- A finished item is deleted, not ticked. What it established is already in
  `requirements.md`, and the commit is the record that it happened.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.

---

## Every session starts here: read `feedback/` again

This item is never done and is never deleted. Notes arrive while work is
happening — a session somewhere records what it was missing, and the file lands
in `feedback/` without anyone being told. And notes that were open yesterday are
often half answered by what shipped since, without their text saying so.

So, before picking anything up:

1. `typo3_feedback_list` — or read `feedback/` — for what is there now.
2. For each note, **run its own query against the current server**. A note is
   evidence about a version of this server that may no longer exist.
3. Close what is answered, trim what is half answered down to the part that is
   still open, and let a new note that changes the order move the items below.

The notes are the only input that comes from outside this repository. Everything
else here was written by someone who already knew what they meant.

---

## Standing check: has `mcp/sdk` released a newer version?

This item is never done and is never deleted either, and unlike everything else
here it serves no single requirement. That is the point: it serves the
precondition of all of them. Every answer this server gives travels over the
protocol version the SDK speaks, and on the day a client stops offering that
version, every requirement fails at once and not one of them says so. So the
check is cheap and it is standing — one command, and only actionable when the
answer has changed since the last session:

    composer outdated mcp/sdk

`mcp/sdk` is the official https://github.com/modelcontextprotocol/php-sdk. Two
different things can come back, and they are worth different amounts:

1. **A patch or minor release.** Take it. The one waiting as of 2026-07-30 is
   v0.7.1, unreleased but sitting on `main` since 27.07., and what it carries is
   stdio hardening — memory exhaustion on `fgets`, a cap on the stdout buffer.
   `bin/typo3-cms-mcp` runs `StdioTransport` and nothing else, so that release is
   worth having on its own, with no protocol reason attached.
2. **A release that speaks `2026-07-28`.** Not there yet: v0.7.0 is the newest
   release and its `ProtocolVersion` enum stops at `2025-11-25`. PR #403
   («Implement protocol version negotiation») is the vehicle to watch — it names
   the 2026-07-28 revision and splits a «handshake era» from a «modern era» that
   has no `initialize` at all. When it lands in a release, the bump here is two
   places: the constraint in `composer.json` and `PROTOCOL_VERSION` in
   `tests/Smoke/StdioServerTest.php`.

What the 2026-07-28 spec asks of servers has been read once already, so that the
next session does not read it again: almost none of it reaches this one. The
stateless core, the `Mcp-Method`/`Mcp-Name` routing headers, the authorization
hardening and the deprecation of HTTP+SSE are all HTTP concerns, and this server
has one transport and it is stdio. Roots, sampling and logging are deprecated and
were never used. Multi round-trip requests are for a server that has to ask
something back, and these tools are read-only and answer in one turn. One new
thing would be worth having: cacheable list results, `ttlMs` and `cacheScope` on
a `tools/list` that only changes when this repository releases.

The deprecation windows are twelve months, so the outer edge is around July 2027.
Before then the only thing that has to happen is a version bump behind a release
somebody else writes.

---

## What is left of one session's notes, and why they are before the twins

Written down before the work starts, because it is a change of order. Five notes
came out of one session in `/home/benji/projects/site-new` on 2026-07-29 — four at
23:43 from one task, an EXT:form form definition in a sitepackage prefilling a
field from the URL, and one at 23:51 from setting the installation up in the first
place. Each was re-run against the server as it is now, and each still held.
Three are closed: the Fluid array literal, whose measurement is in
`decisions.md` because verifying it needed the engine per major rather than the
checkouts; the functional test facts, one of which the source disproved on the
way in; and the credentials `typo3 setup` produces, which turned out to be a task
intent rather than a hint. Two are open:

- `typo3_architecture_lookup` for the EXT:form task answers `site-sets`,
  `frontend-page-rendering` and `sitepackage-layout` — correct for "a
  sitepackage", silent about the form framework. None of the 57 hint ids is about
  it.
- `typo3_changelog_lookup query="prefill"` returns nothing across 14.3 down to
  13.3, and `events-extension-points` says a hook is right "where the subsystem
  still has hook-based extension points" without naming one.

They go before the twins below, and the reason is what each input is worth. The
twins serve a note whose own category is `idea` — a synthesis of what the catalog
has no shape for, and nobody has reported being stopped by it. These are a
session that was stopped and read the answers out of `vendor/` by hand. Friction
that happened outranks a mismatch that was noticed.

## What reading laravel/boost put in the queue, and why it is below the note

These four items come from one outside input that is not a note: a comparison,
on 2026-07-30, with [laravel/boost](https://github.com/laravel/boost) — the same
idea for the same language, an MCP server that makes an agent write
framework-shaped code. That is worth writing down because it is the only reading
of somebody else's answer to this problem that this repository has.

They sit **below** the item above, and the reason is what each input is worth. A
note is evidence from a session that actually asked something and did not get
it; this is a reading of a server nobody here is using. So the note outranks it,
and none of these four jumps ahead of it.

They also serve nothing yet, which by the rule at the top of this file makes
them ideas rather than tasks. So each one names the requirement it would
establish, and the first concrete step for each is to write that entry in
`requirements.md` — accepted and **open** — or to decide against it there. Two
of them touch what the server may write outside itself, which is the promise the
opening of `readme.md` makes, so deciding against one is a real outcome and not
a failure to get to it.

The one difference that explains all four: Boost runs **inside** the
application, as `php artisan boost:mcp` behind a service provider, and gets its
knowledge to the agent **before** the first question — `boost:install` writes it
into `CLAUDE.md`. This server runs beside an installation and answers only when
called. The second half of that is the part worth reconsidering; the first is
not, and `decisions.md` says why a booting TYPO3 was never a precondition here.

### Withholding one tool without withholding half the server

Would establish a requirement under `## Scope`, next to the two profiles.
`Profile` is a boundary through the middle of this server and it has exactly two
positions; Boost is finer in two ways worth having. `Tinker::shouldRegister()`
returns `config('boost.tinker_tool_enabled', false)` — the tool that executes
code is off unless somebody turns it on — and `boost.mcp.tools.exclude` lets a
caller drop any single tool by class name.

The argument for it is already written in the `Profile` docblock: the tool list
is the first thing a client pays for. What is missing is that a caller who wants
21 of the 22 tools has no way to say so, and the one they do not want may be one
this server has no opinion about.

The next concrete step is the smaller half: an environment variable naming tools
to leave out (`TYPO3_MCP_EXCLUDE_TOOLS`), applied after the profile rather than
instead of it, with `typo3_server_scope` naming what it dropped for the same
reason it already names what the profile dropped — a shorter list than the
readme's has to have a reason a client can read. The default-off half has no
counterpart here: nothing in this server executes anything.

### Two things read and deliberately not queued

Written down so the next session does not have to read Boost again to find out
they were considered.

`search-docs` takes a `token_limit` (3,000 by default, capped at a million) and
every guideline Boost composes carries a token estimate. No tool here takes
anything of the kind, and `typo3_architecture_lookup` over a broad topic is the
one that could get large. Not queued because there is no measurement saying it
does: the honest first step is to measure the largest realistic answer, and that
is cheap enough that whoever needs it can do it then.

`get-absolute-url` exists because agents invent URLs with the wrong scheme and
port. It is a one-purpose tool against one named failure, and this server has no
tool of that shape. Not queued because no note has named the TYPO3 equivalent —
and `scenarios/` is where such a thing would be found, not here.
