# Measure the next run of ten against what the last one cost

**Serves:** decisions/
**Priority:** normal
**Branch:** todo/measure-the-next-run-of-ten-against-what-the-last-one-cost
**Claimed:** 2026-08-02

Read the next run of ten out of the transcripts and write the four numbers into
a **Confirmed on** or **Revoked on** section of
[`D-FBK-020`](../../decisions/feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md),
which is the one thing nothing here can do for itself. One JSONL file per
session below `~/.claude/projects/`, in a directory named after the worktree the
session ran in — the run of 2026-08-02 is 82 of them under
`-home-benji-projects-typo3-cms-mcp--worktrees-*`, and it is the baseline: 66
tool calls per session, 0 of 5414 issued beside another, 2092 of the 4046 `bash`
calls spent on `cat`, `sed`, `grep` and `ls`, and 8.75 million cached input
tokens read back per session. Count the same four on the new run, from
`message.content[].type == 'tool_use'` and `message.usage` on the `assistant`
lines. Same calls per session, or fewer calls at the same tokens, is the
**Wrong if** happening rather than a rule that did not take.
