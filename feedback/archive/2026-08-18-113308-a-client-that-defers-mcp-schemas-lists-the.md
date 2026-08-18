---
date: 2026-08-18T11:33:08+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_server_scope
directory: /home/benji/projects/bootstrap_package
---

# a client that defers MCP schemas lists the tools by name only, and a whole TYPO3 session went by ...

## Observation

Task: review GitHub PR #1627 against the bootstrap_package TYPO3 sitepackage — a one-line change to Configuration/TCA/Overrides/300_content_general_columns.php — then carry the fix through merge, a green CI and a backport to the maintenance branch.

Not one tool of this server was called in the whole session, and no skill of it was activated. The reason is mechanical rather than a judgement about the server: this client defers MCP schemas. All 27 typo3-dev-companion tools arrived in a system-reminder as a bare list of names, with no descriptions, and a note that a schema must be fetched with ToolSearch before a tool can be called. The premise that a tool is chosen on its description therefore did not hold — it was chosen, or not chosen, on its name alone. "typo3_schema_lookup" and "typo3_component_lookup" as bare strings did not tell me they would answer a question about a core TCA palette definition across two majors, so I never spent a round trip finding out.

The server's own instructions, which did arrive in full, told me to start every task with typo3_project_describe. I did not, because for a one-line diff a core checkout looked cheaper than an unknown number of ToolSearch-then-call round trips. That judgement is the thing worth knowing: schema deferral put an extra round trip in front of every first call, and that was enough to keep every one of them shut for a session of roughly 130 tool calls across six turns.

One thing did work and must not be broken: the instructions themselves arrived in full, unlike the tool descriptions. They are the only reason I knew that list of names belonged to a TYPO3 knowledge server at all, and they are the one surface that survives this client configuration intact.

## Query

Whole session. The typo3-dev-companion tool list arrived as names only via system-reminder; no ToolSearch call against this server was made until the debrief at the end, when typo3_feedback_record was fetched deliberately.

## Suggestion

The server instructions are the one thing that reliably arrives in full under schema deferral. Consider opening them with a short "what to call for what" index — five to eight lines mapping a question to a tool name — so a name-only list is usable without a discovery round trip. The instructions today name typo3_project_describe, typo3_task_guide, typo3_component_lookup, typo3_icon_lookup, typo3_label_lookup and typo3_server_scope, but frame them as a workflow rather than as an index. A line reading "a core TCA table, type or palette definition, and how it differs between majors: typo3_schema_lookup" would have won this session a call.
