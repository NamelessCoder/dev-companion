---
date: 2026-08-25T11:46:36+00:00
category: idea
status: closed
closed: 2026-08-25
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# refused-trailer calls Signed-off-by an error while the core's own AGENTS.md mandates it

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

typo3_commit_message_guide returned, at level "error":

  refused-trailer: "The Signed-off-by: line is off the draft. A core commit message carries Resolves, Related, Releases and the Change-Id the hook writes, and nothing else — whatever the checkout you are working in says."

The server is factually right about practice. I checked: of the last 200 commits on main, only 3 carry a Signed-off-by line.

But the checkout it is overruling is not an arbitrary local preference. AGENTS.md is committed to the core itself (added by "[TASK] Add AGENTS.md with repository guidelines for coding agents"), it is loaded into every agent's context via CLAUDE.md, and it says, at length and with a rationale:

  "Sign off every commit — `git commit -s` appends the `Signed-off-by:` trailer, or set `git config format.signOff true` to get it automatically. It certifies that you wrote the patch, or otherwise have the right to submit it under the project's licence (Developer Certificate of Origin). The hook preserves the trailer and only ignores it when computing the `Change-Id`."

So the server's clause "whatever the checkout you are working in says" is aimed squarely at a document the core ships and instructs agents to follow. Two committed sources of core guidance contradict each other, and the server treats its side as an error rather than as a conflict.

What I did: kept the trailer, reported the contradiction to the user explicitly with the 3-of-200 figure, and left the decision to them. That felt like the honest handling, but it cost a paragraph of explanation in the final answer and left the user resolving a policy conflict I could not settle.

The practical effect on the returned artifact: because refused-trailer strips the line from the returned `message`, that field was not something I could hand over as-is, so I assembled the final message by hand.

## Query

typo3_commit_message_guide(workflow="core", message containing "Signed-off-by: ... <...>") — check returned: refused-trailer at level "error"

## Suggestion

Two things would help. First, downgrade this from "error" to a warning that names the conflict rather than asserting one side: something like "Signed-off-by is not usual in core commits (3 of the last 200 carry one). AGENTS.md in the core checkout does instruct contributors to sign off; if you are following it, keep the line." An answer that knows about the document it is overruling is far more useful than one that waves at "whatever the checkout says". Second, consider a parameter or a workflow variant that keeps the trailer, so the returned `message` stays directly usable for callers following AGENTS.md. Worth raising with the core team too: one of the two documents ought to change, and the server is well placed to notice that it is the only party seeing both.
