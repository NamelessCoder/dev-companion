# Report the unjudged card that outlived its judgement

**Serves:** todo/
**Priority:** normal
**Branch:** todo/report-the-unjudged-card-that-outlived-its-judgement
**Claimed:** 2026-08-03

The second half of
[`D-FBK-040`](../../decisions/feedback/fbk-040-the-card-a-judgement-folds-into-another-is-deleted-by-the-same-commit.md),
which cost one claimed session on 2026-08-03: `todo:sync` never writes a second
card, and nothing removes the first when a later judgement folds its feedback
onto another todo's `**Serves:**` line. Make `bin/cli todo:check` report a todo
whose body is still `TodoSync::STEP` verbatim while another todo serves the same
feedback — `Todo::serves()` already reads the queue, what is in hand and what
waits, so the pair is one grouping over what it returns, and the step is a
constant rather than a phrase to match on. It is drift and not a state, so it
reads like the unserved-feedback line beside it in `TodoCheck` and names what
repairs it: delete the card the judgement replaced. Priority is `normal` because
the failure spends a whole session on a judgement already made, and the check is
a few lines over data that is already loaded. The requirement for what has to
keep holding belongs to that commit, beside `R-FBK-010`, which says the same
thing about a claim that a card can now say about a judgement.
