---
date: 2026-08-13T22:41:18+00:00
category: idea
status: closed
closed: 2026-08-14
model: claude-opus-5[1m]
tool: bincli, todoclaim, todohome, decisionsrenumber
directory: /home/benji/projects/typo3-cms-mcp
---

# todo:home refuses a decision-id collision with a raw PHPUnit failure and does not name the renumb...

## Observation

The task was to work the todo queue in parallel: eight claims, eight worktrees, eight sessions, each branch brought home with bin/cli todo:home as it reported.

Three of the eight branches collided on a decision id: two wrote D-KNW-071, two D-ANS-079, two D-SKL-037. That is the failure working-todos-in-parallel.rst predicts, and at three of eight it is not the rare case a reader of that page would price it at.

What cost the run is the shape of the refusal, not the collision. todo:home rebases, runs composer ci, sees it red and prints the PHPUnit failure verbatim:

    DecisionsTest::everyDecisionIsFoundUnderTheIdItGoesBy
    two decision files claim the same id
    Failed asserting that actual size 322 matches expected size 323.

It names neither which two files, nor which of them is already on main, nor bin/cli decisions:renumber, which is the command that repairs it and which the page documents. The message reads as a broken branch rather than as the expected outcome of the procedure the same tool set up one command earlier. I renumbered by hand three times before reading the page and finding the command — the page's own warning is that the renumbering is the dangerous half, and by-hand is exactly the half it warns about.

What worked and must not be broken: the claim-and-home cycle itself. Eight worktrees with their own composer install came up in one command; a branch came home the moment its session reported, none waiting on the others; the refusal happened before the merge, so a red branch never reached main; the waiting card was released to todo/waiting/ with the maintainer question intact; and repository:check was clean after every merge, which is what let me trust the by-hand renumbers.

## Query

bin/cli todo:claim 8, then bin/cli todo:home <worktree> for each of the eight branches as its session reported

## Suggestion

Have todo:home read a red composer ci for the collision it expects: where DecisionsTest or RequirementsTest fails on a duplicate id, print the two paths, say which one is already on main, and print the bin/cli decisions:renumber <decision> line to run in the worktree — the same shape as the DDEV refusal that prints the command that would fix it. Cheaper still: have todo:home offer to run the renumber itself, since it already knows which branch it is rebasing and which side main holds.
