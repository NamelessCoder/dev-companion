# Settle what `D-GUI-1` does when the placeholder is pushed

**Serves:** decisions/

A missing release target becomes a placeholder rather than `main`, and the entry
is wrong the moment a placeholder shows up in a pushed commit — at which point
the guide would have to refuse the draft outright. This repository cannot see
other people's pushes, so the reading is what the draft itself does: check that
the placeholder is unmistakable in the rendered message rather than a plausible
branch name. What would hold that half is a `CommitMessageTest` assertion on the
marker; the pushed-commit half is evidence from a forward run and stays
unguarded until one produces it.
