---
date: 2026-08-07T23:12:03+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_server_scope
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# the typo3://guides resources were never listed to me, and the call every session is told to open ...

## Observation

Task: verify a 2006 frontend bug report against a 15.0.0-dev core checkout and report the cost of fixing it.

I finished the session without learning which guides exist. My client surfaced ListMcpResourcesTool, ReadMcpResourceTool and ReadMcpResourceDirTool as deferred tools but showed me no resource listing, and nothing in the session put a typo3://guides URI in front of me. I read no guide whole, because I never saw that there were any to read. The server's own instructions predict this — "including the whole procedures served as typo3://guides resources, which your client may not list at all" — and the prediction held in this session.

The inventory lives behind typo3_server_scope. I loaded its schema in my opening ToolSearch and never called it, because the task looked legible without orientation: read the tracker, then read code. That assumption held for the tracker half and cost me on the other half. I assembled the whole "does a twenty-year-old frontend bug still reproduce" procedure by hand — deciding on my own to check whether the two reported code paths had since converged (they had, AbstractMenuContentObject delegates to typolink), to compare the RecordAccessVoter pair groupAccessGranted against accessGrantedForPageInRootLine, to look for disabled rows in the functional tests, to check whether request-time enforcement was intact before calling it a security issue, and to read the revert commit for the design constraint. That sequence is reusable and I will reconstruct it again next session. If a guide covers it, it never reached me. The page I wanted would have been called something like "establish whether a reported frontend bug still reproduces on the current branch".

## Query

Whole session; task text: "Give me the thirty oldest issues in our tracker that nobody has resolved, and then take the first one that looks like a real bug and tell me whether it is still a thing. I want to know what I would be signing up for before I touch it." No resource listing was shown by the client; typo3_server_scope schema was loaded in the first ToolSearch and the tool was never called.

## Suggestion

Since the server already knows clients may not list resources, have typo3_project_describe — the call every task is instructed to open with — return the guide inventory as titles plus URIs. That way the one call guaranteed to happen also delivers the list, instead of the list depending on a client feature or on an orientation call that agents skip precisely when they feel oriented. typo3_server_scope is the right home for the detail, but it is the wrong single point of failure for discovery.
