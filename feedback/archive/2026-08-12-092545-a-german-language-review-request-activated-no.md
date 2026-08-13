---
date: 2026-08-12T09:25:45+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_project_describe, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# a German-language review request activated no skill, and the whole session ran with no entry poin...

## Observation

Trimmed on 2026-08-14 to the part that is left. The language half is answered:
`D-SKL-033` weighed German trigger words in the descriptions and decided against
them, on a listing that arrived whole with this skill's description
byte-identical to the published one. This session is written into that entry as
the second of the shape, and it removes the other cause the earlier report
offered — the request here named the change on the review server, and the skill
stayed shut anyway. What is left is the suggestion below, judged as `D-SKL-038`.

Task: review Gerrit change 95169 against the core checkout and say whether it is breaking. My transcript is complete from the first user message; nothing is summarised away.

Zero skills activated in this session. Both typo3-core-patch-review and typo3-core-patch-checkout were listed to me and both matched the task literally — I reviewed a patch set somebody else pushed, and I fetched it into a checkout and backed out again. Neither opened. There is already a closed feedback on exactly this (2026-08-10, "a review request quoting the skill's own trigger words did not activate the skill", closed by 6d5aaa57 "[TASK] Judge a skill that was listed and stayed shut"), so this is a recurrence and the fix did not take.

The new dimension is the language. The request was German. The only English token in it is the loanword "review"; the decisive half — "und sag mir ob der breaking ist" — is German, and so is the follow-up "bitte scdhau dir an welche auswirkungen das hat", which is also misspelled. Skill descriptions are English prose, and I match a request against them as text. A German request with one English loanword clears none of the phrasing in "Review a TYPO3 core patch — your own before you push it, or somebody else's patch set". The server's own instructions already tell me to translate before querying its tools; nothing tells me the same holds for deciding whether a skill applies, and skill selection happens before I have read any server instruction.

The consequence was not only a missing checklist. With no skill open, nothing routed me to typo3_project_describe (I loaded its schema in my first ToolSearch call and then never called it, despite the server instruction "Start every task with typo3_project_describe"), nothing routed me to typo3_task_guide, and I never learned typo3_server_scope or the typo3:// guide resources exist — my client did not list MCP resources at all, so I finished a full patch review without knowing what the server offers as whole documents. I reached typo3_gerrit_lookup and typo3_forge_lookup by guessing from the deferred-tool name list, not from any routing.

What I did instead of the checkout skill: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/69/95169/2, then git show FETCH_HEAD. That worked on the first try only because typo3_gerrit_lookup handed me both the ref and the remote. I would do the same again next session.

## Query

User request, verbatim: "bitte review mir 95169: [BUGFIX] Add link parsing in RTE figcaption | https://review.typo3.org/c/Packages/TYPO3.CMS/+/95169 und sag mir ob der breaking ist" — followed mid-turn by "bitte scdhau dir an welche auswirkungen das hat" (sic).

## Suggestion

Make the no-skill path survivable: if typo3_gerrit_lookup is the first call of a session, its answer is the one place that knows a patch review has begun — it could carry a line naming typo3-core-patch-review and typo3_server_scope, the way project_describe's tail already names the guides.
