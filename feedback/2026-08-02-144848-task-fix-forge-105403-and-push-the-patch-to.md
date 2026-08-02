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

What the push actually required knowing, in the order it mattered:

- The remote is asymmetric. "origin" fetches from git@github.com:TYPO3/typo3.git and pushes to ssh://<user>@review.typo3.org:29418/Packages/TYPO3.CMS.git via remote.origin.pushurl. A session that reads only the fetch URL concludes the project lives on GitHub and would look for a pull request workflow that does not exist. .gitreview carries host, port, project and defaultbranch=main.
- The refspec is HEAD:refs/for/main, with %private appended for a private change. Not a branch push.
- Exactly one commit goes up, and it must stay one commit through every revision — amended, never appended to. The Change-Id trailer is what binds a new patch set to the existing review; losing it opens a second unrelated change and orphans the votes on the first. I amended three times this session and had to carry Change-Id: I9e26... forward by reading it out of git log -1 --format=%B each time, because I was passing a message file rather than editing in place.
- The commit-msg hook that generates and preserves the Change-Id lives in the common git dir, and composer gerrit:setup installs it. It also enforces the 72-character body limit and rejected my first attempt.
- The push works unchanged from a git worktree: HEAD resolves to the worktree commit, and because the parent was already on origin/main only the one commit was sent. The user asked explicitly whether the command was right from the worktree, which is a reasonable doubt and one the server could settle.
- Not mechanical but part of pushing: Forge #105403 is Closed. An open Gerrit change hanging off a closed issue needs the issue reopened.

The related feedback I filed about Forge and Gerrit read access covers getting information out of those services; this is the write direction, and it is the one where a mistake is public and hard to take back.

## Query

Task text: "wir wollen das dieser patch als private nach gerrit gepushed wird". Established by hand: git remote -v, git config --get-regexp remote., cat .gitreview, ls .git/hooks/commit-msg, then git push origin HEAD:refs/for/main%private from a git worktree

## Suggestion

Document the core contribution push as a first-class part of the core workflow, ideally in the core-development skill I asked for in a separate feedback, and reachable from typo3_project_scope when kind is "core-checkout": the asymmetric origin (read GitHub, write review.typo3.org) and how to read it from remote.origin.pushurl and .gitreview, the HEAD:refs/for/<branch> refspec with the %private and %wip variants, one-commit-amended-forever with the Change-Id carried through every amend and how to recover it, that composer gerrit:setup installs the commit-msg hook that both generates the Change-Id and enforces the 72-character limit, that the refspec is unchanged from a git worktree, and the reminder to reopen a closed Forge issue before a change is pushed against it. typo3_commit_message_guide is the natural place to warn that a Change-Id in a message it is asked to check must be preserved — it already keeps unknown trailers, but it does not say that this one is load-bearing.
