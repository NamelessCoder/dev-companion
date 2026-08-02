---
date: 2026-08-02T14:52:30+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: find whether a patch for Forge #105403 already existed, then push mine. Recording how Gerri...

## Observation

Task: find whether a patch for Forge #105403 already existed, then push mine. Recording how Gerrit has to be operated; all of it verified this session.

Reading. The REST API is at https://review.typo3.org/changes/?q=<query>&o=CURRENT_REVISION and its response begins with the XSSI guard )]}' followed by a newline, which must be stripped before the body parses as JSON — I used tail -c +6. Each entry carries _number, status, branch and subject. The query that answers "has anyone already worked on this issue" is q=message:<issue-number>, matching the Resolves/Related trailers; for #105403 it returned an empty array, and that emptiness was the answer to one of the four questions I had been given. Unlike Forge, this host needs no user-agent games.

Writing. The remote is asymmetric and this is the fact most likely to mislead: in a core checkout "origin" fetches from git@github.com:TYPO3/typo3.git but pushes to ssh://<user>@review.typo3.org:29418/Packages/TYPO3.CMS.git through remote.origin.pushurl. A session reading only the fetch URL concludes the project lives on GitHub and looks for a pull-request flow that does not exist. .gitreview independently states host, port, project=Packages/TYPO3.CMS and defaultbranch=main.

The push is git push origin HEAD:refs/for/<branch> — a magic ref, not a branch. Appending %private creates the change unlisted, %wip marks it work in progress. It works unchanged from a git worktree: HEAD resolves to the worktree commit, and because the parent was already on origin/main exactly one commit was transferred. On success Gerrit prints the change URL and its markers, in my case ".../+/95067 [BUGFIX] ... [PRIVATE] [NEW]".

The invariants around it. A Gerrit change is one commit, so a core patch is one commit — revisions are amends, never additional commits. The Change-Id trailer binds a new patch set to the existing review; lose it and you open a second unrelated change and orphan the votes and comments on the first. When amending from a message file rather than editing in place, the Change-Id has to be read out of git log -1 --format=%B and carried over deliberately, which I did three times. The commit-msg hook that generates and preserves it is installed by composer gerrit:setup and lives in the common git dir; it also enforces the 72-character body limit and rejected my first attempt. After the push, core-ci runs the full suite and votes, so a red patch is public even when the change is private.

## Query

Operating review.typo3.org during "find patches that already fixed 105403" and "push this patch to Gerrit as private". Verified: curl https://review.typo3.org/changes/?q=message:105403&o=CURRENT_REVISION and git push origin HEAD:refs/for/main%private

## Suggestion

Document the Gerrit read and write recipes together, since a session doing core work needs both in the same task, and reachable from typo3_project_scope when kind is "core-checkout". The highest-value items are the asymmetric origin with the pushurl to read it from, the refs/for/<branch> refspec with %private and %wip, the XSSI prefix on the REST response, q=message:<issue> as the "already fixed?" query, and the one-commit/amend/Change-Id invariant. typo3_commit_message_guide should additionally state that a Change-Id it is given must be preserved verbatim — it does keep unknown trailers today, but nothing tells the caller that this particular one is load-bearing.
