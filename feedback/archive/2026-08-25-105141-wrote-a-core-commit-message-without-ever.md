---
date: 2026-08-25T10:51:41+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Wrote a core commit message without ever calling the tool that owns it

## Observation

Task: the opening request of the session was literally "ich brauche eine commit message für diesen change und auch die targets" against a staged diff in the core checkout.

I produced the full message — subject tag, prose body, `Resolves:`, `Releases:`, `Signed-off-by:`, the note that the hook adds the `Change-Id` — and made zero typo3_* calls in that turn. `typo3_commit_message_guide` is named in this server's own instruction block, under "the commit message, in your own repository as much as in the core". I read that block and still did not call it.

Two reasons, both structural rather than a judgement about the tool:

1. Every typo3_* tool arrived in this session deferred: names only, no schemas. Calling one costs a `ToolSearch` round trip first. At turn one, with a question I believed I could answer, that extra hop was enough to keep me from it. The first typo3_* call of the session happened only in turn two, and only because `typo3-core-patch-checkout` instructed it.

2. The repository's own `AGENTS.md` was already in my context and carries a complete commit-message section — subject tags, the 72-character rule, the footer order, the "every commit needs its own Forge issue" rule. It was sufficient-looking, so I used it.

The cost was real and I only found it later. My first answer recommended `Releases: main, 14.3, 13.4`, derived from `git for-each-ref` branch dates alone. In turn two, `typo3_rule_lookup(documentId="core/contribution/gerrit-workflow")` told me a bug fix or task goes to main plus the one line back, and that naming an older maintained line is a claim about severity rather than about where the defect is. I revised to `main, 14.3`. The right answer was one call away in turn one and I gave a wrong one instead.

For the record of what else I never put to this server in a session that ran nearly a hundred tool calls: typo3_forge_lookup (I queried forge.typo3.org with curl against the JSON API, because AGENTS.md documents that route), typo3_hint_lookup, typo3_component_lookup, typo3_fluid_namespace_list, typo3_label_lookup, typo3_icon_lookup, typo3_changelog_lookup, typo3_documentation_lookup, typo3_extension_describe, typo3_system_extension_lookup, typo3_server_scope, typo3_snapshot_scope. Six MCP calls in total against roughly ninety Bash calls.

## Query

First user turn: "ich brauche eine commit message für diesen change und auch die targets below in wleche das backgeportet werden sollte", against `git status` showing seven staged files replacing legacy docs.typo3.org URLs. Answered with no typo3_* call; typo3_commit_message_guide never invoked in the session.

## Suggestion

Nothing here is a defect in the tool — its answer was never seen. What would change the outcome is reducing the cost of the first call. If the server can influence how a client surfaces it, the three tools that answer the questions a core session opens with — typo3_commit_message_guide, typo3_gerrit_lookup, typo3_project_describe — are the ones worth having schemas loaded for from the start rather than behind a search.

Second, the instruction block could say what the guide adds beyond a repository's own AGENTS.md or CONTRIBUTING.md, because that comparison is what a session actually makes. In this repository AGENTS.md carries the commit rules in full, so "read the guide too" reads as redundant unless it says what it settles that the repo file does not — the `Releases:` policy would have been exactly that, and it is not obvious that a commit-message guide owns it.
