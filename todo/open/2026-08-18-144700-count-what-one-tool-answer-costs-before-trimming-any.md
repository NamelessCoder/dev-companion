# Count what one tool answer costs before trimming any

**Serves:** D-FBK-020, feedback/
**Priority:** normal
**Run:** bin/cli tools:record

Three open feedbacks say the same thing from three sides and none of them is a
number for the whole surface: `availableHints` is 78 percent of what
[typo3_hint_lookup transfers](../../feedback/2026-08-17-212300-availablehints-is-78-percent-of-everything-hint.md),
[every tool arrives deferred](../../feedback/2026-08-18-074627-every-tool-arrives-deferred-so-each-first-use.md)
so each first use costs a schema fetch, and
[thirteen of roughly thirty round trips](../../feedback/2026-08-18-081228-where-this-session-s-round-trips-went-13-of.md)
in one session answered a single uncovered question. `tools:record` already
calls every tool against a checkout and writes what came back to
`documentation/server/tools/`, so the corpus to measure is on disk. Add
`tools:measure` beside it, reading those recorded answers and printing the text
bytes and the data bytes per tool, worst first, the way `prose:check` prints the
long sentences — then the trimming starts at the top of a list instead of at
whichever tool a session happened to notice.
