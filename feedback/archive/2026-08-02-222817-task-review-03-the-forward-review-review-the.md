---
date: 2026-08-02T22:28:17+00:00
category: tool-gap
status: closed
closed: 2026-08-02
model: claude-opus-5
directory: /home/benji/projects/typo3-cms
---

# Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', run...

## Observation

Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', run 2026-08-03 in /home/benji/projects/typo3-cms against one unpushed WIP core patch. The session produced a good review and did not ask this server anything. 23 tool calls in 256 seconds: 22 Bash, 1 Read, 0 server calls, 0 skills activated. This was not a launch failure — the client's MCP log for the session records 'Successfully connected (transport: stdio) in 89ms', both input-schema normalisations, and 'STDIO connection closed after 256s (cleanly)', so every tool was registered and available the whole time. The reason nothing routed there is that none of the seven published skills is core-shaped: conformance, testing, documentation, release and upgrade are extension work, backend-module and content-element are site or extension work. A core contributor reviewing their own patch before pushing to Gerrit meets no skill. What the client did deliver arrived in full and did not fire: the transcript carries the server's instructions as an `mcp_instructions_delta`, opening with 'Start every task with typo3_project_scope', and the skill listing with all seven descriptions; only the 22 tools arrived deferred, as names without descriptions.

Trimmed on 2026-08-03 by the run that judged this feedback. The second half it was filed with — that the review proposed no project-owned command, although core ships Build/Scripts/runTests.sh — is reported already, from the other side and with better evidence, in feedback/2026-08-02-144350: a core session that did call typo3_project_scope was answered with four gerrit:setup commands and no runTests.sh, and got the script's invocation syntax elsewhere. That half belongs to that feedback and is removed here rather than counted twice.

## Query

Review the current changes in this TYPO3 core checkout. Tell me what is wrong, missing, or not ready for review, in priority order. Do not change files.

## Suggestion

Give the core-contribution audience a skill, so a core checkout is not the one place where an agent meets this server by accident or not at all. A skill named for reviewing or preparing a core patch would be the order that is missing — the knowledge is here, in knowledge/documents/typo3-core-scripts.md and typo3-commit-messages.md and in the tools beside them, and nothing says in which order to ask for it. What such a skill's first step should be is not settled here: this feedback was filed saying it is `typo3_project_scope`, and feedback/2026-08-02-144350 shows that call answering a core checkout with four gerrit:setup commands and no test runner.
