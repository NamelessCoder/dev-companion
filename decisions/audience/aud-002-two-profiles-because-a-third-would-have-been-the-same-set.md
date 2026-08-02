---
id: D-AUD-002
date: 2026-07-29
status: revoked
revokedBy: D-AUD-004
---

# D-AUD-002 — Two profiles, because a third one would have been the same set

**A client is offered one of two profiles, `all` and `project`, and the tools
left out are named per tool rather than derived from the `provenance` of what
they answer.**

The feedback that asked for this described one packaging: the
installation-backed tools and the transferable domains, without the core
contribution surface. The item written from it named three profiles — `core`,
`project`, `all`.

## Decided

- Two, `all` and `project`. A `core` profile would have had to leave the
  installation-backed tools out to differ from `all`, and it must not: a core
  checkout is an installation, and looking an icon identifier or a label up in
  it is exactly what writing a patch needs. Everything else made `core` and
  `all` the same set under two names.
- The tools left out are named per tool rather than derived from the
  `provenance` of the topics they answer. `typo3_rule_lookup` answers two
  core-only topics and two transferable ones, so no formula over the field gets
  it right; the field is the input to the decision, not the decision.
- The resources are not filtered, only the tools and the maps that route to
  them. A document is read when it is asked for by name; a tool is offered to
  every session whether it fits or not.

## Assumed

- What the three omitted tools also carried and what does transfer is reachable
  without them — the commit conventions through `typo3_commit_message_guide`
  with `workflow="project"`, the backend CSS and subsystem conventions through
  `typo3_architecture_lookup`, and every prose document verbatim through its
  `typo3://core` resource.
- Deriving the profile from the kind of installation is right more often than
  it is wrong, and being wrong costs one environment variable. It does sit
  against R-AUD-002 — the audience is a property of the task, not of the
  directory — and the tool list cannot vary per task, so this is the one place
  the directory decides.

## Wrong if

- Someone contributes to the core from a session started in a site installation
  and then has to set `TYPO3_MCP_PROFILE=all` to get the rules back — or a
  deployment has no installation to read at all, which is where a profile that
  leaves out the installation-backed half would earn its name.

## Revoked on 2026-08-02

The rules do arrive, and the **Assumed** about what being wrong costs does not
hold. The server was driven over stdio from `/home/benji/projects/site-new` —
`E-SITE`, TYPO3 14.3.5 — with the handshake and the calls a client makes there.
It derives `project` and offers 20 tools; `typo3_rule_lookup`,
`typo3_script_lookup` and `typo3_test_run_guide` are absent. A core-shaped task
was answered as core work all the same. `typo3_task_guide`, asked about a
`QueryBuilder` bugfix pushed to Gerrit, recognised it as `Patch submission`. It
returned the Gerrit workflow prose, four `runTests.sh` checks and the core
checklist. Every route the **Assumed** named held: `typo3_commit_message_guide`
answered `workflow="core"` with the `Resolves:` and `Releases:` trailers, all
five `typo3://core` documents read including `typo3-core-rules` and
`typo3-core-scripts`, and `typo3_architecture_lookup` named no omitted tool.
What costs more than one environment variable is the answer itself. It routes
to `typo3_test_run_guide`, which this client cannot call — twice on the
patch-shaped task and six times on a test-shaped one, out of
`knowledge/architecture-hints/php.json`, two entries in
`knowledge/server-scope.json` and `TaskGuide.php:502`. `Scope::offered()`
filters those routes, and `TaskGuide` does not. It reads `Scope::read()` and
filters by the audience of the task instead, which is exactly the collision
this entry named: the task is core work and the directory is not. `R-SCO-007`
says nothing the server hands out points at a tool it does not offer, so this
is that requirement failing, and it is now `open` with a todo against it.
Nobody has met it in the wild. Ten recorded sessions in `E-SITE` called
`typo3_task_guide` eight times, and not one of the eight was core-shaped, so
the derivation was right every time it ran. The way back is one variable as
promised — `TYPO3_MCP_PROFILE=all` there offers 23 tools and drops the profile
sentence from the instructions — and the profile caveat reached all ten
sessions, so a client that reads the instructions can find it. What no
protocol-level run can show is what an agent does with a route it cannot
follow; that needs a session in `E-SITE` given core work. The second half of
the **Wrong if**, a deployment with no installation, is untouched by this run.

## Since then

The mechanism is gone rather than repaired. Profiles were weighed against what
they bought — 2,223 bytes of a 92,189-byte tool list — and deleted on
2026-08-02, so no client is offered a shorter list unless it asked for one.
What replaces them is
[`D-AUD-004`](aud-004-the-tool-list-is-not-where-the-audience-is-said.md), and the
second half of the **Wrong if** cannot be met any more: there is no profile
left for a deployment without an installation to earn.

## Since then

The second half is read, and what survives of it is what a deployment with no
installation is offered. The profile it was written against is gone
(`D-AUD-004`), so the question is no longer which set: `bin/typo3-cms-mcp` was
driven over stdio on 2026-08-02 from a directory with no TYPO3 anywhere above
it, and it offers all 23 tools, the six that can only answer from an
installation included.

Every layer says where it stands. The `installation` block of
`typo3_server_scope` reports `found: false`, the seven directories the search
walked, and a console that is `reachable: false` with the reason; its closing
prose names what cannot be answered here and both environment variables as the
way out. `typo3_project_scope`, `typo3_icon_lookup` and `typo3_label_lookup`
each answer with the `unsupported` shape — `cause: no-installation`, the same
searched list, and text that opens "This is not answerable here, which is not
the same as an empty answer" and routes to `typo3_server_scope`. Nothing is
withheld and nothing pretends.

The one thing that does not know where it is, is the `instructions`: they open
with "Start every task with typo3_project_scope", which here answers
`unsupported`. That is left as it is. The string is the same for every session
by `D-AUD-003`, the tool it names is what reports the state, and a client that
follows it learns in one call what it has and how to name an installation — which
is a shorter route than a sentence hedged for a case most sessions are not in.
