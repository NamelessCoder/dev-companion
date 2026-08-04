---
date: 2026-08-04T18:01:33+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_commit_message_guide, typo3_server_scope, listmcpresourcestool
directory: /home/benji/projects/site-new
---

# Task: "fix this project" — twelve commits over a repo-wide cleanup. Three things I did not do, wh...

## Observation

Task: "fix this project" — twelve commits over a repo-wide cleanup. Three things I did not do, which the server cannot see from the calls that were made.

typo3_commit_message_guide: called once, for commit one. It returned a well-wrapped draft and a useful warning that my summary line was 68 characters against a preferred 52. I then hand-wrote the remaining eleven messages without it. Not because the answer was bad — the wrapping and the check were both correct — but because for workflow="project" the value is a 72-character wrap and two length checks, and once I had seen the shape of an accepted message the round trip stopped paying. The tool's own description is honest that the core workflow is where the Forge issue and Releases trailer matter; on a project repo there is much less for it to add. If usage stats ever show project-workflow calls dropping off after the first, that is why.

typo3_server_scope: never called. typo3-development-installation asks for it explicitly, before anything is created, "for whether an installation and a console can be reached at all". I skipped it because typo3_project_describe had already returned a booted installation with its TYPO3 version, sites and commands, and the site was answering 200 on both frontend and backend before I activated that skill. The step is written for a repository with nothing installed; in a session that arrives at a running installation it restates what is already known.

The server's MCP resources: never enumerated. My client listed ListMcpResourcesTool and ReadMcpResourceTool among deferred tools, so the capability was visible, but nothing pointed at what this server offers through it and I never spent a call finding out. I finished a long session without learning whether there are documents there I should have read. If the answer is "several", the discovery path is currently a guess.

## Query

typo3_commit_message_guide called once with changeType=SECURITY, workflow=project, for the first of twelve commits; typo3_server_scope never called despite typo3-development-installation asking for it; the server's MCP resources never enumerated.

## Suggestion

For typo3_server_scope: let the installation skill's step name its own skip condition, the way base.md does elsewhere — "skip where typo3_project_describe already reported a booted installation". For the resources: name them where a session will see them, either in typo3_server_scope's answer or in the first tool response of a session, since a resource nobody enumerates is a document nobody reads. For the commit guide: no change; recording the usage pattern rather than a complaint.
