---
date: 2026-08-25T11:48:19+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup, typo3_reference_list, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# the resource list was never shown to the client, so no document was read whole

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

I read no document of this server whole, and my client never showed me the list. Reporting the mechanism, since the debrief says a session can finish without learning the resources exist — that is exactly what happened, and there is a specific reason.

In this client the MCP resource tools are deferred: ListMcpResourcesTool, ReadMcpResourceTool and ReadMcpResourceDirTool appeared to me only as bare names in a system reminder, with no schemas. Calling one requires first spending a ToolSearch round trip to fetch its schema. So the cost of finding out whether a relevant document exists is: guess that it might, spend a call to load a listing tool, spend a second call to list, spend a third to read. Against a checkout I could grep in one call, that never won. The 40-odd tool names in that deferred list also gave me no signal that this particular server had documents behind it as opposed to only lookups.

typo3_server_scope has the same problem from the other side. Its description is good — "Start here when it is unclear whether this server can answer a question at all, or which of the lookups is the right one" — and I did not call it, because I never felt unclear. I felt certain, and I was largely right about the checkout and wrong about the conventions. Certainty is what suppresses an orientation call, so an orientation tool that waits to be asked will systematically miss the sessions that need it most. Mine is a clean example: I was confident enough to declare a commit message ready that broke a rule I had never looked up.

The page I wanted, and would have read whole had I known it existed: something called "Commit message rules for core patches" or "Anatomy of a core commit" — the 52-character subject preference, the 72-character body limit, the trailer order, what the commit-msg hook does and does not enforce, and the policy on which release lines a bugfix goes to. In this session I assembled precisely that page myself out of AGENTS.md, .git/hooks/commit-msg, two typo3_commit_message_guide calls and three separate git-log statistics runs (subject lengths across 400 commits, Signed-off-by frequency across 200, Releases lines across the last 40 [BUGFIX] commits touching Build/).

## Query

No resource was listed or read. ListMcpResourcesTool and ReadMcpResourceTool were present only as deferred tool names requiring a ToolSearch round trip to load; neither was loaded. typo3_server_scope was also never called.

## Suggestion

Do not rely on the resource list being visible: in at least this client it costs three round trips to discover and is behind an unschema'd deferred tool. Name the important documents inside the tool answers that touch their subject — typo3_commit_message_guide's response would be the natural place to say "the full commit message rules are in <document name>". Also consider that typo3_server_scope, however well described, is a tool for the unconfident, and a confidently wrong agent is the failure case worth catching; a one-line pointer riding along inside answers the agent already wanted reaches that agent, an orientation tool does not.
