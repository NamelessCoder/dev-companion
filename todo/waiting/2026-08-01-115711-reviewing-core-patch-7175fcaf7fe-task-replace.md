# Reviewing core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SVG place...

**Serves:** feedback/2026-08-01-115711-reviewing-core-patch-7175fcaf7fe-task-replace.md, decisions/
**Priority:** normal
**Waiting on:** a caller who states a change type and describes a review — is
    that authoring work seen from the reviewer's side, as `D-GUI-006` assumed,
    or a reviewer naming the type of the patch under review, as this feedback
    turned out to be? Three shapes: leave it, and this feedback's own call keeps
    being answered with the Gerrit steps; let the review words win over a stated
    type, which re-opens what `D-GUI-006` decided and would answer "review the
    patch that deprecates X" as a review; or answer both, stating the review
    shape and appending what the stated type owes, which is the only one that
    costs no caller a step and the only one nothing here has judged. Putting the
    card back is an answer too: the feedback's other half is served, and what is
    left is one call shape.

The consolidation this card carried is done, on
`todo/reviewing-core-patch-7175fcaf7fe-task-replace`. The card for `R-GUI-006`
had already landed as `faa2616`, so the branch of this card that would have
edited it does not apply; `TaskGuide::answer` was re-run with the feedback's own
arguments, the review shape came back naming no removal, and `R-GUI-010` now
states the removal surface in the `audit` intent. `D-GUI-004` carries both
readings under **Since then**.

What is left is one question and no work behind it. The feedback is not archived
because its own call — task "review the core patch replacing GD-based error
thumbnails with a static SVG placeholder", `changeType` cleanup — still comes
back recognized as Patch submission alone, with "Keep the patch focused", the
test-coverage item and the two Gerrit steps. Nothing about that is a defect: a
stated change type overrules the words of the task by `D-GUI-006`, so the
`audit` intent is filtered out before the checklist is assembled, in the block
above `$confirmed` in `src/Tool/TaskGuide.php`. Which reading of that caller is
right is the question above, and archiving the feedback is what follows from it
either way.
