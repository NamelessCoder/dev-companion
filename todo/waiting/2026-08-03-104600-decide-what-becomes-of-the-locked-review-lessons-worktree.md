# Decide what becomes of the locked review-lessons worktree

**Serves:** todo/
**Priority:** low
**Waiting on:** is the work in `.claude/worktrees/review-lessons` still wanted,
    and if it is, does it come home or stay where it is? Nothing here can
    answer it: the branch carries uncommitted changes somebody made and no
    session in this repository knows what they were for.

`git worktree list` reports a second, **locked** worktree beside the checkout:

    /home/benji/projects/typo3-cms-mcp/.claude/worktrees/review-lessons  779cbe9 [worktree-review-lessons] locked

It was made on 2026-08-02, is 114 MB, and its working tree is dirty — three
files under `requirements/task-skills/` are modified and not committed:
`skl-011`, `skl-012` and `skl-013`, all three about what a review reports and
what a finding is attributed to. The lock is why nothing has cleaned it up, and
the lock is also what says somebody meant to come back to it.

What the reading established, so the answer does not need it again:

- It is not one of the claim worktrees. Those live below `.worktrees/` and are
  named after the todo they were cut for; this one is below `.claude/` and is
  named after a subject.
- `.claude/` is not in `.gitignore`, so the directory shows as `?? .claude/` in
  every `git status` this repository runs, which is how it was noticed at all.
- Three answers, and none of them is this session's to take: the branch is
  rebased and merged like a claim; it is released and the worktree removed with
  the changes lost; or it stays and `.claude/` joins `.gitignore` so the status
  stops reporting it. The third is the only one that costs nothing and settles
  nothing.
