---
date: 2026-08-01T12:23:26+00:00
category: tool-gap
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348). To revi...

## Observation

Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348). To review a core patch I had to retrieve the changed paths and the commit message from the checkout with repeated path-filtered `git show` calls, each re-printing the full commit header. The server's own contract demands exactly this data as input — typo3_test_run_guide takes the changed paths and narrows the suites by them, typo3_commit_message_guide takes the commit message — but typo3_server_scope explicitly refuses to supply it ("determine the changed paths yourself; this server never reads the checkout"). So the server creates demand for data that only lives in the checkout and offloads the retrieval to shell work, which is where the duplicate output was spent.

## Query

Review patch: [TASK] Deprecate AssetCollector media handling

## Suggestion

Add a checkout-git-metadata tool (e.g. typo3_change_scope): given a commit hash or the working tree, return the changed paths, a diff stat, and the commit message, attributed as read from the checkout. It matches how typo3_project_scope and typo3_extension_scope already read project files, and it feeds typo3_test_run_guide's paths and typo3_commit_message_guide's message directly, removing the repeated `git show` calls a core patch review currently needs.
