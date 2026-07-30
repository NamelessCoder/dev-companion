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

## Write the two twins the marking pass found

Serves the one note left open,
`feedback/2026-07-29-180528-project-work-needs-a-second-axis-the-repository.md`.
The pass this item used to ask for is done and its outcome is in the note:
nineteen of the twenty-two marked hints, all four marked statements and the
marked intent need no twin, and two subjects do. Both are reachable from an
extension today and hand back the core's obligation with nothing in its place.

The first is an extension's own documentation, twin of `documentation-changelog`.
The second is an extension's own asset build, twin of both
`css-source-build-boundaries` and `backend-typescript` at once, because those two
say the same thing about `.scss` and `.ts` and neither holds outside the core.

Both are written the way `project-extension-tests` was — same subject, its own
hint, and each pointing at the other. Two things the pass established and the
writing has to keep: the asset twin goes in `general.json`, not in `css.json` or
`typescript.json`, because those two sections are withheld when the task names
the frontend and a sitepackage's asset build is exactly that case; and it points
at `public-assets` and `extension-files` rather than repeating what they already
say, so what is left to write is the boundary itself. The documentation twin also
takes the project side of two marked statements that have no twin of their own —
a changed ViewHelper argument list and a retired label are public API for whoever
installed the extension, so the obligation there is a version bump and a release
note.

The next concrete step is the documentation twin, because it is the one with
nothing at all in its place: `guides.xml`, `Documentation/Index` and semantic
versioning appear nowhere in `knowledge/`. Bind what needs binding against
`.checkouts/`, and close the note in the commit that writes the second one.

What comes after it is the section below, and after that the work is whatever
the notes a session finds ask for — or, where there are none, a scenario from
`scenarios/` still marked `gap`.

---

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

### Write the client configuration instead of describing it

Would establish a requirement under `## Discovery`: that the entrypoint can
write the configuration a client needs, rather than a readme telling somebody to
paste an absolute path into JSON. Today `readme.md` asks for exactly that, twice
— standalone and as a dependency — and it is the first thing between somebody
hearing about this server and using it.

Boost detects which agents are present per platform (`command -v claude` on
Darwin and Linux, `cmd /c where claude` on Windows) plus project markers
(`.claude/`, `CLAUDE.md`), then writes each one's own file: `.mcp.json` for most,
TOML for Codex. One class per agent, three optional contracts, and the writing
is idempotent — an existing block is replaced in place and anything the user put
around it survives.

The next concrete step is a decision, not code: whether `bin/typo3-cms-mcp` may
write into the caller's project at all. It is the entrypoint that already does
instance discovery from the working directory, so it is the place that knows
where such a file would go; but everything it does today is read-only, and
`readme.md` says the process boundary is the trust boundary. Write the outcome
in `requirements.md` under `## Discovery`, and if it is yes, one subcommand
(`bin/typo3-cms-mcp install`) writing `.mcp.json` with an absolute path to
itself, refusing rather than guessing when the file exists with a different
command.

### Say what to call before the client has a reason to ask

Would establish a requirement under `## Answers`. This is the item with the most
behind it, and it is a gap in this server's own terms: a tool nobody calls
answers nothing, and every guarantee in `requirements.md` is about the answer
once the call has happened.

Boost does not rely on tool descriptions to get itself called. It writes
instructions into the agent's own guideline file — `.ai/boost/core.blade.php`
becomes a section of `CLAUDE.md` — and they are imperative: *"Always use
`search-docs` before making code changes. Do not skip this step."* An agent that
does not know `typo3_component_lookup` exists writes v15 markup into a v13
backend, and the withholding that `typo3_component_lookup` does — the thing this
server is most careful about — never runs.

There is a smaller version of this that needs no writing outside the server, and
it should be measured first: `knowledge/server-scope.json` already carries the
`instructions` clients receive at initialize time, and `routing` already says
which tool answers which question. So the next concrete step is to read those
`instructions` as an agent would and ask whether they say *when to call*, in the
imperative, for the three cases where a wrong answer is invisible until runtime
— backend markup, icon identifiers, label keys. Where they do not, that is a
`knowledge/server-scope.json` change and nothing else. Only if that is not
enough does the generated-fragment question arise, and then it is the same
decision as the item above.

### Prompts, the primitive this server does not use

Would establish a requirement under `## Guides`, which is where what a returned
draft is worth is already written down. Boost ships four prompts — upgrade
Laravel v13, upgrade Livewire v4, upgrade Inertia v3, and a code simplifier —
and they cost no context until somebody picks one, because a prompt is invoked
by the user rather than offered to the model.

`src/` has no prompt at all. The candidates are the ones a TYPO3 session
actually starts with and this server already has the material for: a v12→v13
upgrade pass, and a commit message for work already done (which
`typo3_commit_message_guide` answers, but only once somebody thinks to call it —
same problem as the item above, different half of it).

The next concrete step is to check what `mcp/sdk` v0.7.0 offers for prompts and
whether `ToolContractTest` has anything to say about a primitive that is not a
tool; then one prompt, the commit message one, because its answer already exists
and it tests the plumbing without any new knowledge.

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
