---
id: D-AUD-2
date: 2026-07-29
status: standing
---

# D-AUD-2 — Two profiles, because a third one would have been the same set

**A client is offered one of two profiles, `all` and `project`, and the tools
left out are named per tool rather than derived from the `provenance` of what
they answer.**

The note that asked for this described one packaging: the installation-backed
tools and the transferable domains, without the core contribution surface. The
item written from it named three profiles — `core`, `project`, `all`.

- **Decided:** two, `all` and `project`. A `core` profile would have had to
  leave the installation-backed tools out to differ from `all`, and it must not:
  a core checkout is an installation, and looking an icon identifier or a label
  up in it is exactly what writing a patch needs. Everything else made `core`
  and `all` the same set under two names.
- **Decided:** the tools left out are named per tool rather than derived from
  the `provenance` of the topics they answer. `typo3_rule_lookup` answers two
  core-only topics and two transferable ones, so no formula over the field gets
  it right; the field is the input to the decision, not the decision.
- **Decided:** the resources are not filtered, only the tools and the maps that
  route to them. A document is read when it is asked for by name; a tool is
  offered to every session whether it fits or not.
- **Assumed:** what the three omitted tools also carried and what does transfer
  is reachable without them — the commit conventions through
  `typo3_commit_message_guide` with `workflow="project"`, the backend CSS and
  subsystem conventions through `typo3_architecture_lookup`, and every prose
  document verbatim through its `typo3://core` resource.
- **Assumed:** deriving the profile from the kind of installation is right more
  often than it is wrong, and being wrong costs one environment variable. It
  does sit against R-AUD-2 — the audience is a property of the task, not of the
  directory — and the tool list cannot vary per task, so this is the one place
  the directory decides.
- **Wrong if:** someone contributes to the core from a session started in a site
  installation and then has to set `TYPO3_MCP_PROFILE=all` to get the rules back
  — or a deployment has no installation to read at all, which is where a profile
  that leaves out the installation-backed half would earn its name.
