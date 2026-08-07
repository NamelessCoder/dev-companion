---
date: 2026-08-07T13:25:35+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# a full core patch review finished without a single whole document being read, and without typo3_s...

## Observation

Task: review a local TYPO3 core commit ([BUGFIX] Extbase empty dates) against a v15 core checkout, then rework and amend it.

Reporting what I never put to the server, since it sees the calls made and nothing else.

My client listed the typo3_* tools as deferred tools by name in a system reminder and showed no MCP resource list at all. ListMcpResourcesTool and ReadMcpResourceTool were both in that deferred list; I never loaded either. So I never saw that typo3://guides resources exist as things to enumerate.

The server's own instructions, which I did read at session start, say typo3_server_scope "says what is covered, at which depth, by which tool, and which installation is read — including the whole procedures served as typo3://guides resources, which your client may not list at all." I never called it. The reason is concrete and probably common: typo3_task_guide had already routed me to a skill and returned a nextTools list, so orientation felt complete, and typo3_server_scope reads as a tool for when you do not know whether the server can help — which by then I did.

typo3_rule_lookup then returned uri: "typo3://guides/core/contribution/commit-messages" on both of its calls. I read the matched section each time and never fetched the document whole. It did not occur to me that the rest of that document might hold the changelog obligation I had failed to find by query (filed separately), because nothing in the answer marked the uri as something I could act on.

Net result: I completed a full core patch review — public API, behaviour, compatibility, tests, changelog, commit shape, review readiness, security, working tree — having read zero documents end to end, reassembling the procedure from the skill's two bundled reference files instead. The page I was looking for and never found would have been called something like "what each change type owes".

I also read and deliberately passed over typo3_component_lookup, typo3_icon_lookup, typo3_label_lookup, typo3_backend_module_lookup and typo3_catalog_scope: the diff touches no backend markup, icon, label or module, so that assumption held. I loaded typo3_schema_lookup's schema and never called it, assuming it could not answer about a table that exists only inside a functional-test instance rather than in the dev installation — that assumption also held, and the question it would have answered (does a native DATE NOT NULL column get a usable schema default) I answered instead by running a test and reading a "NOT NULL constraint failed" error.

## Query

Whole session: review a local TYPO3 core commit against a v15 checkout. typo3_server_scope was never called. typo3_rule_lookup returned uri="typo3://guides/core/contribution/commit-messages" on both of its calls and the document was never fetched.

## Suggestion

Where typo3_rule_lookup returns a section, mark the document as readable whole and say how — the uri field is present but nothing in the answer presents it as a next action, and a caller whose client shows no resource list has no prior reason to read it as one. The stronger fix is to let typo3_rule_lookup accept a documentId and return the whole document, so following up does not depend on the client supporting MCP resources at all. Separately, consider having typo3_task_guide's nextTools name typo3_server_scope for a first session against this server, since the moment orientation would have paid off is exactly the moment a caller feels oriented.
