---
date: 2026-08-02T22:28:17+00:00
category: tool-gap
status: open
model: claude-opus-5
directory: /home/benji/projects/typo3-cms
---

# Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', run...

## Observation

Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', run 2026-08-03 in /home/benji/projects/typo3-cms against one unpushed WIP core patch. The session produced a good review and did not ask this server anything. 23 tool calls in 256 seconds: 22 Bash, 1 Read, 0 server calls, 0 skills activated. This was not a launch failure — the client's MCP log for the session records 'Successfully connected (transport: stdio) in 89ms', both input-schema normalisations, and 'STDIO connection closed after 256s (cleanly)', so every tool was registered and available the whole time. The reason nothing routed there is that none of the seven published skills is core-shaped: conformance, testing, documentation, release and upgrade are extension work, backend-module and content-element are site or extension work. A core contributor reviewing their own patch before pushing to Gerrit meets no skill, and nothing in the client tells them a TYPO3 knowledge server is attached. Second half of the same observation: the review's only executed check was the host's 'php -l' on the changed file, and it proposed no project-owned command anywhere, although the core repository ships Build/Scripts/runTests.sh as a tracked file and that is what core verifies a patch with. typo3_project_scope returns the declared commands of a checkout and was not called. This is the fourth recorded forward run in a row whose answer executes no project-owned command and does not say so; the three before it were REVIEW-02 in extension checkouts.

## Query

Review the current changes in this TYPO3 core checkout. Tell me what is wrong, missing, or not ready for review, in priority order. Do not change files.

## Suggestion

Give the core-contribution audience a skill, so a core checkout is not the one place where an agent meets this server by accident or not at all. A skill named for reviewing or preparing a core patch would be the routing that is missing, and its first ordered step is the one this run skipped: read the declared commands from typo3_project_scope and name the narrowest one before recommending a broader suite.
