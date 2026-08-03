---
date: 2026-08-02T14:48:48+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_commit_message_guide, typo3_project_scope
directory: /home/benji/projects/typo3-cms
---

# Task: fix Forge #105403 and push the patch to Gerrit as a private change. Every mechanical fact n...

## Observation

Task: fix Forge #105403 and push the patch to Gerrit as a private change. Every mechanical fact needed for the push I established from the checkout or from prior knowledge; no tool here covers it, and typo3_project_scope, which is the tool that describes the repository, does not mention any of it.

Trimmed on 2026-08-03. The asymmetric origin, the HEAD:refs/for/main refspec, one-commit-amended-forever, the Change-Id that binds a patch set, composer gerrit:setup with its commit-msg hook and the 72-character limit are all answered now, by typo3_rule_lookup and typo3_commit_message_guide, and the skill typo3-core-patch-development orders the task around them. D-SKL-005 records what was re-run and what came back. What is left is the part of the push a session still cannot get from here:

- The unlisted push. The refspec is HEAD:refs/for/main with %private appended for a private change, and %private appears nowhere in the corpus. The skill tells the caller that the rule lookup has both forms and makes the visible-or-unlisted question mandatory, so the gap is reached at the one step the skill calls irreversible.
- Where the push goes. "origin" fetches from git@github.com:TYPO3/typo3.git and pushes to ssh://<user>@review.typo3.org:29418/Packages/TYPO3.CMS.git via remote.origin.pushurl; .gitreview carries host, port, project and defaultbranch=main. The corpus has the one-time set-url command and no way to read what a checkout is already pointed at. A session that reads only the fetch URL concludes the project lives on GitHub and would look for a pull request workflow that does not exist.
- The push works unchanged from a git worktree: HEAD resolves to the worktree commit, and because the parent was already on origin/main only the one commit was sent. The user asked explicitly whether the command was right from the worktree, which is a reasonable doubt and one the server could settle.
- Not mechanical but part of pushing: Forge #105403 is Closed. An open Gerrit change hanging off a closed issue needs the issue reopened.

The related feedback I filed about Forge and Gerrit read access covers getting information out of those services; this is the write direction, and it is the one where a mistake is public and hard to take back.

## Query

Task text: "wir wollen das dieser patch als private nach gerrit gepushed wird". Established by hand: git remote -v, git config --get-regexp remote., cat .gitreview, ls .git/hooks/commit-msg, then git push origin HEAD:refs/for/main%private from a git worktree

## Suggestion

Document the core contribution push as a first-class part of the core workflow, ideally in the core-development skill I asked for in a separate feedback, and reachable from typo3_project_scope when kind is "core-checkout": the HEAD:refs/for/<branch> refspec with the %private variant beside the %wip one that is already there, how to read the asymmetric origin from remote.origin.pushurl and .gitreview rather than only how to set it, that the refspec is unchanged from a git worktree, and the reminder to reopen a closed Forge issue before a change is pushed against it.
