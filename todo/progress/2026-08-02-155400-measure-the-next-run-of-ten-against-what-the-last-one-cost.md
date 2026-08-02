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

## What the first attempt established, 2026-08-02

That session ended without committing, and not because the step is wrong: the
run it was told to measure was still going. `claude -p` has one turn and cannot
wait, so the measurement is work for after the run rather than during it, and
that is the whole of why this claim is still here.

What it settled and nothing needs to re-derive:

- **The method has to be applied to both sides.** Counting `tool_use` blocks on
  `assistant` lines over the 82 baseline sessions gives 5465 calls, 4111 `bash`,
  2614 cheap, 736.9M cache read, against the recorded 5414 / 4046 / 2092 / 718M.
  The gap is the baseline having been taken minutes before those sessions
  finished writing, plus a matcher detail: `cheap` counted at the head of the
  first segment is 2023 and counted in any segment is 2614, and the recorded
  2092 sits between them. So report the baseline recomputed under whichever
  method this run uses, beside the recorded figure. The **Wrong if** is a
  comparison and needs one method on both sides, not fidelity to a script
  nobody kept.
- **The treatment was applied.** `AGENTS.md` carries the rule, `Todo.php` puts
  it in the handover, and the launch prompt of every one of the ten carried it.
- **Measuring mid-run confirms nothing.** Sixty seconds in, the ten had 196
  calls between them.

The ten are the directories under
`~/.claude/projects/-home-benji-projects-typo3-cms-mcp--worktrees-*` holding one
JSONL each and named after this run's worktrees; the other 82 are the baseline,
and no name is in both sets. One of the ten is this todo's own worktree, whose
transcript `6ee04fe8-8d02-4101-b875-65683cefdac3.jsonl` is the aborted attempt
above — it belongs to the run and is atypical, because it read transcripts and
deliberately blocked. Any further file in that directory is the measuring
session itself and is not part of the run. Report the ten with and without it.
