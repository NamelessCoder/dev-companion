---
date: 2026-08-04T18:02:41+00:00
category: bug
status: open
model: claude-opus-5
tool: typo3_feedback_record, typo3_feedback_list
directory: /home/benji/projects/site-new
---

# Task: filing session feedback at the end of a repo-wide TYPO3 cleanup.

## Observation

Task: filing session feedback at the end of a repo-wide TYPO3 cleanup.

I recorded a feedback naming three tools, one of which is not a typo3_* tool: "typo3_commit_message_guide, typo3_server_scope, ListMcpResourcesTool". typo3_feedback_list returns it as:

  "tool": "typo3_commit_message_guide, typo3_server_scope, listmcpresourcestool",
  "tools": ["typo3_commit_message_guide","typo3_server_scope","listmcpresourcestool"]

The mixed-case name was lowercased in both the joined string and the split array. The two snake_case names are unaffected, so this only shows on a name that carries capitals — which in practice means a tool from outside this server, referenced because a session reached for it instead of a typo3_* one.

There is a closed feedback on exactly this subject: feedback/archive/2026-08-02-150625, closed by commit e812d85 "[TASK] Keep a recorded name in the spelling it was given in". Either that fix covered a different path, or normalisation was reintroduced. Reporting it rather than assuming, since I can only see the output.

Consequence is small but real: a later `typo3_feedback_list tool="ListMcpResourcesTool"` filter would presumably need the lowercased spelling to match, and the record no longer names the tool as a client would.

Second, smaller item from the same batch: I recorded thirteen feedback in one session and one of them carries the wrong category. feedback/2026-08-04-175804 is about guides.xml scaffolding being absent from the knowledge base — that is missing-knowledge, and it is stored as bug. My error rather than the server's; noting it so triage does not read it as a malfunction.

## Query

typo3_feedback_record with tool="typo3_commit_message_guide, typo3_server_scope, ListMcpResourcesTool"; then typo3_feedback_list status=open

## Suggestion

Preserve the spelling of a recorded tool name verbatim, including for names that are not this server's own, and make the tool filter in typo3_feedback_list case-insensitive so either spelling finds it. For the miscategorised record: no action needed beyond recategorising feedback/2026-08-04-175804 from bug to missing-knowledge if that is cheap.
