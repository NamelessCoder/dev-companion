# Judge the tables that hold a cell no line fits

**Serves:** D-DOC-001
**Priority:** low
**Run:** bin/cli prose:check

The alignment is the formatter's since 2026-08-22 — `bin/cli prose:format` pads
a markdown table to the width of each column's widest cell. What is left is the
half no formatter can do. `D-DOC-001` says a cell that will not fit on a line
means the content is a list rather than a table, because what a table buys over
a list is a column that can be scanned, and `bin/cli prose:check` counts nine of
them.

Six are the mapping tables of `D-FBK-021`, whose widest cell is 239 characters:
a clause naming what a summary reported against a clause naming where it landed.
That entry's own **Decided** says the mapping is written down so nobody derives
it twice, so what a list has to keep is both halves of every row —
`- **what the summary said** — where it is`.

The other three are `decisions/answers/ans-031` at 114, `scenarios/readme.md` at
109 and `scenarios/forward/readme.md` at 105. Two of those are read by
`Scenarios::vocabulary()`, which takes the first cell of each row, so a list
there is a change to what a check reads and not only to how it looks.

One table at a time, and the answer may be that it stays: the exception has to
say so where it is taken, which is what `D-DOC-001` asks of a table whose cells
cannot be shortened.
