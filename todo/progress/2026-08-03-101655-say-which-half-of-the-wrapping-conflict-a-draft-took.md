# Say which half of the wrapping conflict a draft took

**Serves:** feedback/2026-08-02-144315-task-fix-a-typo3-core-bug-in-the-fluid-image.md, R-GUI-007
**Priority:** normal
**Branch:** todo/say-which-half-of-the-wrapping-conflict-a-draft-took
**Claimed:** 2026-08-03

Queued on the evidence in
[`D-GUI-003`](../../decisions/guides/gui-003-the-wrapping-conflict-is-resolved-in-the-answer-rather-than-in-silence.md),
which is where the reading is: `CommitMessage::wrapBody()` recognises a block by
`preg_match('/^\s/', $line)` alone, so a hard-broken block at column 0 is
rejoined into prose with nothing in the checks saying so, while an indented one
is kept and yields a draft that `Build/git-hooks/commit-msg` refuses. Two
changes in `src/Knowledge/CommitMessage.php`, one patch. First, report every run
of caller-written lines that `wrapBody()` joined, under a code of its own beside
`body-line-too-long`, so the answer names what it reflowed. Second, raise
`body-line-too-long` to `error` under `WORKFLOW_CORE`, where the hook's
`grep -q -E '^[^#].{72}'` is what the caller actually meets, and leave it a
`warning` under `WORKFLOW_PROJECT`, where no hook runs. Then decide from those
two together whether a block following a `...:` lead-in is worth recognising at
all — with the reflow reported, a caller who wanted the breaks can indent, and
`D-GUI-003` says why that heuristic was not settled in advance. Last, the
promise: `CommitMessageGuide::description()` offers "ready to commit" and "long
URLs left intact" in one sentence, and under the core rules those cannot both
hold, so the description drops the second half and `bin/cli tools:record`
rewrites `documentation/clients/tools/typo3_commit_message_guide.md` from it.
R-GUI-007 is what the tests are written against, and archiving the feedback with
`bin/cli feedback:archive` belongs in the same commit.

The report's second half needs nothing from this card. That `no-issues-found`
came back for a message the hook then refused was the session's own
hand-restored 73-character line rather than the draft, and `R-GUI-001` is what
makes reporting on the draft alone correct.
