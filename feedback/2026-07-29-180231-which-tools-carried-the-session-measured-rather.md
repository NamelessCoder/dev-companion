---
date: 2026-07-29T18:02:31+00:00
category: idea
status: open
tool: typo3_changelog_lookup, typo3_task_guide, typo3_server_scope
directory: /home/benji/projects/site-new
---

# WHICH TOOLS CARRIED THE SESSION, MEASURED RATHER THAN ASSUMED — because the routing puts them i...

## Observation

WHICH TOOLS CARRIED THE SESSION, MEASURED RATHER THAN ASSUMED — because the routing puts them in almost the opposite order.

typo3_changelog_lookup was the most valuable tool here by a distance, and it is described as if it were for looking things up after the fact: "Asking what a version changed, or whether an API is still there". For someone building on a major they have not built on before, it is the primary source, not the lookup of last resort. Everything I got right about v14 specifically came out of it or out of the changelog files it pointed me to: f:render.record, route enhancers shipped in site sets, Expression Language in limitToPages, list_type and the "General plugin" being gone, columns generated from TCA instead of ext_tables.sql, the ext_emconf deprecation. Those are the facts that separate a v14 answer from a v11 answer, and they are all in there.

typo3_task_guide, which the routing names first — "Starting a core task and looking for the applicable conventions and checks" — was consistently the thinnest answer of any call I made, at least for project work. It improved enormously over the session as the hints landed, but the pattern held: it is a router, and its value is entirely the hints behind it.

Two smaller ones in the same vein:

- typo3_label_lookup I never called once, and I invented roughly forty label keys. The guidance to use it is in typo3_server_scope's checkoutDiscovery and its routing table, both of which I read at the start and neither of which was in front of me at the moment I was writing XLF files. Same shape as the commit message guide miss I already reported: the tool exists, the pointer is in a document read once at session start, and the moment of need is hours later.
- typo3_translation_domain_lookup I did call, twice, and it was exactly right both times — computed rather than guessed, including for a file that did not exist yet. Worth saying, since I have mostly been reporting gaps.

The general shape: this server's pointers live in orientation documents that get read once. The tools that are reached repeatedly are the ones some other tool's output names.</observation>
<parameter name="suggestion">Say in typo3_changelog_lookup's own description that it is the first stop when working on a version one has not worked on recently, not only a retrospective lookup — and consider having typo3_task_guide route to it whenever targetVersion differs from the installation's previous major. More generally: put pointers in the output of the tools that are called repeatedly rather than only in typo3_server_scope, because that is where a caller is actually looking at the moment of need. typo3_task_guide's nextTools is the natural carrier — label lookup while writing labels, the commit guide before committing, the changelog on a version bump.

## Query

observation across ~40 tool calls: which tools actually carried the work, versus which one the routing table puts first
