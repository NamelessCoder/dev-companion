---
date: 2026-08-02T14:52:30+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: find whether a patch for Forge #105403 already existed, then push mine. Recording how Gerri...

## Observation

Task: find whether a patch for Forge #105403 already existed, then push mine. Recording how Gerrit has to be operated; all of it verified this session.

Trimmed on 2026-08-03. The reading half is answered: typo3_gerrit_lookup asks message:<issue> against review.typo3.org and hands back the changes, so the endpoint, the )]}' prefix and the query are no longer anybody's to establish — D-ANS-033 records it and this feedback is one of the four sessions behind it. typo3_commit_message_guide returns a Change-Id trailer verbatim, which was this feedback's second suggestion. The writing half — the asymmetric origin, %private, the worktree, one-commit-amended-forever — is the same four items feedback/2026-08-02-144848 was trimmed to, and one card carries them at high; nothing of it is repeated here. The 72-character limit the commit-msg hook rejected on is now attributed to that hook in knowledge/documents/typo3-gerrit-workflow.md. What is left is where the question this task opened with is asked:

- No order that writes a patch asks whether the patch exists already. The skill typo3-core-patch-development opens with "Establish the issue before you believe it" and names no call for it, and typo3_task_guide with this task's shape returns "Confirm the target TYPO3 core branch and issue context" and names neither typo3_forge_lookup nor typo3_gerrit_lookup. typo3-core-patch-review names both. The routing block of typo3_server_scope carries the pair as well, which is how this session reached them, so what is missing is the step in the skill rather than the answer.

## Query

Operating review.typo3.org during "find patches that already fixed 105403" and "push this patch to Gerrit as private". Verified: curl https://review.typo3.org/changes/?q=message:105403&o=CURRENT_REVISION and git push origin HEAD:refs/for/main%private

## Suggestion

Document the Gerrit read and write recipes together, since a session doing core work needs both in the same task, and reachable from typo3_project_scope when kind is "core-checkout". The highest-value items are the asymmetric origin with the pushurl to read it from, the refs/for/<branch> refspec with %private and %wip, the XSSI prefix on the REST response, q=message:<issue> as the "already fixed?" query, and the one-commit/amend/Change-Id invariant. typo3_commit_message_guide should additionally state that a Change-Id it is given must be preserved verbatim — it does keep unknown trailers today, but nothing tells the caller that this particular one is load-bearing.
