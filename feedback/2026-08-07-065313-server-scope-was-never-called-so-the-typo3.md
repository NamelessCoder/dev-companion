---
date: 2026-08-07T06:53:13+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# server_scope was never called so the typo3 guides resources were never discovered

## Observation

Task: verify Forge 109572 and carry it through to a committed, rebased core patch.

My client listed this server's tools as deferred schemas in a system reminder, fetched on demand with ToolSearch. It showed me no resource list at any point in the session. The server's own instructions say typo3_server_scope names what is covered and by which tool, "including the whole procedures served as typo3://guides resources, which your client may not list at all". I never called typo3_server_scope, so I finished a long session without ever learning which guides exist — the exact failure mode the instruction predicts, and it happened.

Two procedures I assembled by hand and would have wanted a guide for. First, carrying a verified Forge issue through to a pushed Gerrit change: I worked out the commit trailers, the release branches, the Change-Id hook, that the pre-commit hook dies in vendor/composer/platform_check.php on host PHP and reports a false header error, and that cglGit is the check that settles it against the commit. Second, proving a persistence behaviour across every storage variant of a TCA type: I built a six-column fixture table, a model, a repository and a DataHandler path from scratch to do it. The pages I wanted would have been called something like "core patch from a triaged issue" and "covering a TCA storage type across its variants".

## Query

no call was made — typo3_server_scope was never invoked; the client surfaced tool schemas via ToolSearch only and never a resource list

## Suggestion

If the guides are reachable only through a client resource list plus one optional tool call that nothing prompts, most sessions will never see them. Have typo3_task_guide name the applicable typo3://guides resource in its answer the way it already names skills, so a guide is offered at the moment the task is classified, rather than depending on the agent thinking to ask what exists. The server can measure this: sessions that call task_guide but never server_scope are the ones that never saw a guide.
