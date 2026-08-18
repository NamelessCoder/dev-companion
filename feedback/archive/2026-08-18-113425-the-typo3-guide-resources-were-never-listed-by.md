---
date: 2026-08-18T11:34:25+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_server_scope
directory: /home/benji/projects/bootstrap_package
---

# the typo3:// guide resources were never listed by the client and the session ended without learni...

## Observation

Task: review PR #1627 against the bootstrap_package sitepackage, then merge it, fix a separate frontend build failure, and backport both to the maintenance branch.

The server's own instructions warn that whole procedures are served as typo3:// guide resources "which your client may not list at all". That is exactly what happened. No resource listing was ever shown to me at any point in six turns. ListMcpResourcesTool, ReadMcpResourceTool and ReadMcpResourceDirTool were present in the deferred tool list as bare names, and I called none of them, so I finished the session without knowing which guides exist or what any of them is called. I read no resource whole, because I did not know there was a list to ask for — the instructions mention the resources exist but name none of them.

Where I assembled a procedure myself instead, it was for two things. First, reviewing an incoming pull request against an extension repository; the page I wanted would have been called something like "Review a pull request against an extension". Second, the repository's release and backport workflow, which I got from the human mid-session rather than from any document; that page would have been called something like "Release branches and backporting in a project package". Both are filed separately as their own findings — what this one reports is that I had no way to discover whether either already existed as a guide.

## Query

Whole session, six turns. No MCP resource listing surfaced to the model; ListMcpResourcesTool and ReadMcpResourceTool present in the deferred tool list as names only and never called.

## Suggestion

Since the instructions already anticipate this failure, consider naming the guides in the instructions themselves — a plain list of titles is cheap to carry and survives any client that drops the resource list. Without it, the guides are reachable only by a model that thinks to enumerate MCP resources unprompted, which this session did not, and neither will most sessions under schema deferral where enumerating costs a round trip against an unknown payoff.
