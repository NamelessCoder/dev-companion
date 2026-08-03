# Reviewing core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SVG place...

**Serves:** feedback/2026-08-01-115711-reviewing-core-patch-7175fcaf7fe-task-replace.md, R-GUI-006
**Priority:** normal
**Branch:** todo/reviewing-core-patch-7175fcaf7fe-task-replace
**Claimed:** 2026-08-03

Judged and trimmed on 2026-08-03; `D-GUI-004` carries the reading and the
re-run, and nothing of it is repeated here. What is left is one consolidation,
and it is `normal` because this card is the only place on the board that says
`R-GUI-006` has two instances rather than one. `R-GUI-006` already has a card of
its own —
`2026-08-02-133108-give-the-task-guide-a-shape-for-a-task-that-changes-nothing`
— which was another session's claim when this was written, so nothing here was
written into it. Once the branches are home: where that card is still on the
board, name this feedback in its `**Serves:**` line, take it off `low` for the
second instance, and delete this file; where it has landed, re-run
`TaskGuide::answer` with the feedback's own arguments — they are in `D-GUI-004`
— and archive the feedback in the same commit if the brief now states the
public-API surface.
