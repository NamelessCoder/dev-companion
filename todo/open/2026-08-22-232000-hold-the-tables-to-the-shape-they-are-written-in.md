# Hold the tables to the shape they are written in

**Serves:** D-DOC-001
**Priority:** normal
**Run:** bin/cli prose:check

`bin/cli prose:check` counts the sentences and the comments and says nothing
about a table, and `D-DOC-001` says a table is padded to the width of its widest
cell so a column can be scanned unrendered. On 2026-08-22, 18 of the 19 tables
in this repository carried a row whose pipes stood in a different column from
the separator's. The rule is right and nothing holds it, which is that entry's
first **Wrong if** in its own words.

What the check reads is one block at a time: the separator row says where the
pipes belong, and every row of the block has them in the same columns. It
reports rather than fails, the way the sentence count does, because a table
inside a fenced code block is not one and the reading has to skip a fence.

The second half is a judgement rather than a count and belongs beside it. A cell
that will not fit on a line means the content is a list — `D-DOC-001`'s second
**Decided** bullet — and the widest rows in the corpus are 312 characters, in
the mapping tables of `D-FBK-021`. Whether those become lists is what the entry
says has to be said where the exception is taken, so the sweep names them and
the reading decides one at a time.

A rewriter is the other half of the same work and is what `bin/cli prose:format`
already is for prose: padding is mechanical, and a report that names 18 tables
nobody will pad by hand buys nothing.
