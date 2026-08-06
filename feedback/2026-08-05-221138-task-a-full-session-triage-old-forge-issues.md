---
date: 2026-08-05T22:11:38+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: a full session — triage old Forge issues against a 15.0.0-dev core checkout, then carry one...

## Observation

Task: a full session — triage old Forge issues against a 15.0.0-dev core checkout, then carry one of them to a finished, checked, committed patch. It ran through two skills and roughly seventy tool calls, and it finished without me ever reading one of the typo3://guides documents whole.

My client did not show me a resource list. It exposed ListMcpResourcesTool, ReadMcpResourceTool and ReadMcpResourceDirTool as deferred tools — names only, schemas not loaded — and I never loaded them, because nothing in the session pointed at them. The server's own instructions do say that typo3_server_scope names "the whole procedures served as typo3://guides resources, which your client may not list at all". I read that line at the start and did not act on it: I assumed the tool list plus ToolSearch was the whole surface, and that assumption did not hold.

The cost is concrete rather than hypothetical. I met the guides only as excerpts: typo3_rule_lookup returned single headings out of core/contribution/commit-messages ("Release Targets", "Changelog Files") and core/contribution/gerrit-workflow ("Push a Private or Work in Progress Change"), each carrying its own uri field. Those excerpts were good and sufficient for what I asked. But I asked three separate rule_lookup queries to assemble what one document covers end to end — what a bugfix owes, which branches it targets, whether it needs a changelog entry, how the push works — and I burned three round trips on typo3_commit_message_guide over a subject-length rule that the commit-messages document very likely states outright. Reading that one page whole at the start of the patch work would plausibly have replaced five calls.

The page I wanted and did not know I could ask for by name is exactly core/contribution/commit-messages.

## Query

Never called: typo3_server_scope, and no MCP resource was ever listed or read. The guide URIs typo3://guides/core/contribution/commit-messages and typo3://guides/core/contribution/gerrit-workflow reached me only as documentId/uri fields inside typo3_rule_lookup answers.

## Suggestion

Make the guides reachable from inside the flow rather than only from a list the client may never render. Two cheap options, not exclusive. First: when typo3_rule_lookup returns headings out of a document, say in the answer that the whole document exists and how to get it — the uri is already in the payload, but nothing marks it as fetchable or says what else is in it. A line such as "this is 1 of 7 headings in typo3://guides/core/contribution/commit-messages; read it whole for the rest" would have turned my second rule_lookup into a page read. Second: have the skills that depend on a procedure name its guide URI at the point of use — typo3-core-patch-development sends the reader to typo3_rule_lookup for the Gerrit workflow and for the changelog obligations, and could name the document instead of the search. As it stands the resources are discoverable only by calling typo3_server_scope, and a session with a working answer in hand has no reason to.
