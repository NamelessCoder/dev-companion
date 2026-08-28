---
date: 2026-08-28T00:14:09+00:00
category: idea
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_extension_describe, typo3_task_guide, typo3_hint_lookup, typo3_changelog_lookup, typo3_commit_message_guide
directory: /home/benji/projects/bootstrap_package
---

# What carried this session: coreConstraint, commands marked check/change, omittedHints, guides in ...

## Observation

Task: review PR #1621 against bk2k/bootstrap-package, add functional regression tests, cherry-pick to BP_16_0. Recording what worked, concretely, since a later change could take any of it away without anyone noticing.

1. project_describe returning coreConstraint "^13.4 || ^14.3" beside typo3Version "14.3.6". This made the declared-range gap the first thing I knew, before opening a file. Every version claim in my review therefore reads "verified on 14.3, read for 13.4" instead of being stated flat. Without that one field I would have reported a finding established on one major as a package-wide one, which is the specific error this field prevents.

2. commands[].runs marked check / change / unknown. This is what let a review told to change nothing still run cgl:ci, phpstan and phplint, while correctly holding back `composer cgl` and `npm --prefix Build run build`. I used that classification verbatim to decide what to run; it is not derivable from a script name.

3. extension_describe reporting artifacts.tests ["Functional","Packages","Unit"] and artifacts.manual. When the request later turned into "add tests", I did not have to establish whether a harness existed — I already knew there was a functional layer and a fixture package.

4. task_guide's omittedHints naming ids rather than counting them. `backport-syntax-floor` came back that way during the backport task; I fetched it by id and it told me to read the PHP floor off the target branch's own composer.json rather than remember it. I did exactly that (BP_16_0: ">=8.2.0", same as master, so the test's `fn () =>` parses). I would never have searched for that hint in words.

5. changelog_lookup with a bare identifier as the query. "getFileAbsFileName" returned the single Breaking entry from 8.0 and nothing since, which is what let me state that the relative-path semantics have not moved across the declared range instead of guessing at it.

6. The `guides` array inside project_describe's answer. My client renders no MCP resource list at all — I never saw one. That array is the only reason I know those documents exist, which is exactly what the server instructions claim it is for. It worked as designed.

7. hint_lookup id=project-extension-tests warning that ddev exec fails from a git worktree because .ddev/config.yaml is tracked. I read that before choosing where to verify the backport and ran the suite in the main checkout instead of losing time on a worktree that could not reach the container.

8. commit_message_guide with workflow="project": it checked a contributor's message and drafted mine. Its summary-length-preferred warning fired on a 64-character subject; the repository's own log routinely runs 67 to 79, so I overrode it. That is the right division — the tool states the preference, the log decides.

## Query

Whole session. Calls in order: typo3_project_describe(); typo3_extension_describe(extension="bootstrap_package"); typo3_task_guide(changeType="audit"); typo3_changelog_lookup(query="getFileAbsFileName"); typo3_commit_message_guide(workflow="project", message=<contributor's message>); typo3_task_guide(changeType="test"); typo3_hint_lookup(id="project-extension-tests"); typo3_commit_message_guide(workflow="project", changeType="TASK", summary=..., body=...); typo3_task_guide(changeType="bugfix", backport); typo3_hint_lookup(id="backport-syntax-floor").

## Suggestion

Nothing to change. Filed so that coreConstraint, commands[].runs, omittedHints-by-id and the guides array are visible as load-bearing if any of them is ever considered for trimming — each of the four changed what I did, not merely what I knew.
