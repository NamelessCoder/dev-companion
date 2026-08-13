---
date: 2026-08-13T23:42:08+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: bincli, todoclaim, todohome, decisionsrenumber
directory: /home/benji/projects/typo3-cms-mcp
---

# todo:claim predicts the file two sessions will both open but not the decision id every one of the...

## Observation

The task was to work the queue in parallel, three rounds of eight, each branch homed as it reported.

Five of the twenty-four branches collided on a decision id — D-KNW-071, D-ANS-079 and D-SKL-037 in the first round, D-SKL-040 twice over and D-KNW-074 in the third. Every one of them was the newest id in its group plus one, which is what each session computes from the same main.

todo:claim already does the harder half of this. It read the eight claims of round two and printed 'src/Contribution/Gerrit.php is named by Answer a change with its Change-Id and the siblings sharing it and Answer a change with the review it is in — one class two sessions are about to open', which was correct: that pair produced the only content conflict of the three rounds, four files, and the warning is what made it a thing I was waiting for rather than a surprise. The id is the cheaper prediction of the two — it needs no reading of what a todo is about, only the group each session will write into — and it is the one nothing says.

What the collisions cost, measured from this session's own transcript: 30 todo:home invocations for 24 branches, 10 decisions:renumber calls, and every renumber followed by a hand edit, because the command prints the mentions it will not move and one to two of those eight belong to the branch — a revokedBy: in another entry's front matter, a comment in a test, a paragraph in a card. That split is right and I would not change it. The point is that it runs after the fact, on a collision that was predictable at claim time.

One thing to note against the earlier report: feedback/2026-08-13-224311 said the sessions batch nothing, and three rounds now say it. 1234 calls in 1234 tool turns, none of them sent beside another — and this session, which filed that report, then spent 163 calls the same way. That card is open, so this is a second measurement of it and not a second report.

What worked and must not be broken: homing a branch the moment its session reports, rather than waiting for the round. Round two's fastest session finished in 5 minutes and its slowest in 25, and the slow one was the one whose conflict I had to resolve — so the seven others were already on main by the time it mattered, and the conflict was against a main that had absorbed them. The refusal is the other half: todo:home rebases, runs composer ci, and where it is red it merges nothing and leaves the worktree standing, which is what let five bad rebases be repaired in place rather than reverted off main.

## Query

three rounds of bin/cli todo:claim 8, each branch brought home with bin/cli todo:home <worktree> as its session reported; measured afterwards from the 24 worktree transcripts and from this session's own

## Suggestion

Have todo:claim reserve the ids as it reserves the worktrees. It already computes the claims, and the next free number per decision group is one directory listing — printing 'this branch's decisions start at D-SKL-041' per worktree in the same block as the file warning would cost nothing and be read at the moment a session starts, which is where the warning about Gerrit.php was read.

The fallback, if reserving is too strong a claim to make on a session that may write no decision at all: have todo:home run the renumber itself. It is the one place that knows both which branch it is rebasing and which side main holds, so it can move the branch's entry and then print the mentions it did not move — the same split decisions:renumber makes today, one step earlier and without the operator in the loop.

Either way the numbers above are worth keeping: 5 in 24 is a rate, and working-todos-in-parallel.rst records four runs of ten producing seven, which is the same order and is what makes this a property of the procedure rather than of a bad night.
